# 📋 API Architecture & Flow Documentation

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT (Postman/Frontend)              │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP Request
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                      Laravel API Server                     │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  routes/api.php - Route Definitions                │   │
│  │  - Public routes (login, get single resource)       │   │
│  │  - Protected routes (middleware: auth:sanctum)      │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐    │
│  │  AuthApiController (Public)                        │    │
│  │  - getToken() - Generate Bearer Token              │    │
│  │  - logout() - Revoke Token                         │    │
│  └──────────────────────────────────────────────────────┘    │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐    │
│  │  ProductApiController (Protected)                  │    │
│  │  - index()   - List all products                   │    │
│  │  - store()   - Create product                      │    │
│  │  - show()    - Get single product                  │    │
│  │  - update()  - Update product                      │    │
│  │  - destroy() - Delete product                      │    │
│  └──────────────────────────────────────────────────────┘    │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐    │
│  │  CategoryApiController (Protected)                 │    │
│  │  - index()   - List all categories                 │    │
│  │  - store()   - Create category                     │    │
│  │  - show()    - Get single category                 │    │
│  │  - update()  - Update category                     │    │
│  │  - destroy() - Delete category                     │    │
│  └──────────────────────────────────────────────────────┘    │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐    │
│  │  Form Requests (Validation)                        │    │
│  │  - StoreProductRequest                             │    │
│  │  - UpdateProductRequest                            │    │
│  │  - StoreCategoryRequest                            │    │
│  │  - UpdateCategoryRequest                           │    │
│  └──────────────────────────────────────────────────────┘    │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐    │
│  │  Models & Database                                 │    │
│  │  - Product Model                                   │    │
│  │  - Category Model                                  │    │
│  │  - User Model (with HasApiTokens)                  │    │
│  └──────────────────────────────────────────────────────┘    │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐    │
│  │  Sanctum Token Storage                             │    │
│  │  - personal_access_tokens table                    │    │
│  │  - Token validation                                │    │
│  └──────────────────────────────────────────────────────┘    │
│                         │                                    │
└────────────────────────┬────────────────────────────────────┘
                         │ JSON Response
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT Response                        │
└─────────────────────────────────────────────────────────────┘
```

---

## Authentication Flow

```
User Request
    │
    ├─ POST /api/login
    │  (email, password)
    │
    ▼
┌──────────────────────────────────────┐
│ AuthApiController::getToken()        │
│                                      │
│ 1. Validate input                    │
│ 2. Auth::attempt($credentials)       │
│ 3. Get user from database            │
│ 4. Create token: createToken()       │
│ 5. Return access_token + token_type  │
└──────────────────────────────────────┘
    │
    ├─ Success (200)
    │  {
    │    "access_token": "1|xxxx",
    │    "token_type": "Bearer",
    │    "user": {...}
    │  }
    │
    ├─ Invalid Credentials (401)
    │  {
    │    "message": "Email atau password salah"
    │  }
    │
    └─ Error (500)
       {
         "message": "Error saat login",
         "error": "..."
       }

Client stores token in memory/localStorage
    │
    ▼
For subsequent requests:
    │
    ├─ Header: Authorization: Bearer 1|xxxx
    │
    ▼
Sanctum Middleware validates token
    │
    ├─ Valid: Request proceeds with Auth::user()
    │
    └─ Invalid: Return 401 Unauthorized
```

---

## Request Validation Flow

```
Incoming Request
    │
    └─ POST /api/product
       {
         "name": "Product A",
         "quantity": 10,
         "price": 50000
       }
       
    ▼
┌──────────────────────────────────────┐
│ ProductApiController::store()        │
│ (StoreProductRequest $request)       │
└──────────────────────────────────────┘
    │
    ▼
┌──────────────────────────────────────┐
│ StoreProductRequest::rules()         │
│                                      │
│ Rules:                               │
│ - name: required|string|max:255     │
│ - quantity: required|numeric|min:0  │
│ - price: required|numeric|min:0     │
│                                      │
│ If validation passes:                │
│   → proceed to controller            │
│                                      │
│ If validation fails:                 │
│   → return 422 with error messages   │
└──────────────────────────────────────┘
```

---

## Authorization Flow

### Product Authorization (Owner OR Admin)
```
User wants to UPDATE product ID 5
    │
    ▼
ProductApiController::update()
    │
    ├─ Find product (ID 5)
    │
    ├─ Check: Auth::id() !== $product->user_id 
    │         && Auth::user()->role !== 'admin'
    │
    ├─ YES → 403 Forbidden (not authorized)
    │ {
    │   "message": "Anda tidak memiliki hak akses..."
    │ }
    │
    └─ NO → Proceed with update (200 OK)
```

### Category Authorization (Admin Only)
```
User wants to CREATE category
    │
    ▼
CategoryApiController::store()
    │
    ├─ Check: Auth::user()->role !== 'admin'
    │
    ├─ YES → 403 Forbidden (not authorized)
    │ {
    │   "message": "Anda tidak memiliki hak akses..."
    │ }
    │
    └─ NO → Proceed with creation (201 Created)
```

---

## Error Handling Flow

```
Request Processing
    │
    ├─ Try block
    │  │
    │  ├─ Validation passes
    │  ├─ Authorization passes
    │  ├─ Process request
    │  │
    │  └─ Exception thrown?
    │     │
    │     ├─ NO → Return success response
    │     │       (200, 201, or 204)
    │     │
    │     └─ YES → Catch block
    │             │
    │             ├─ Log error (Log::error)
    │             │
    │             ├─ Return error response
    │             │ (400, 404, 500, etc)
    │             │
    │             └─ JSON response with
    │                message + error details
    │
    └─ HTTP Response
```

---

## Data Flow Diagram - Create Product

```
┌────────────────────┐
│ Client/Postman     │
└────────────┬───────┘
             │ POST /api/product
             │ Auth: Bearer token
             │ Body: {name, quantity, price}
             ▼
┌────────────────────────────────────────┐
│ ProductApiController::store()          │
│ (StoreProductRequest $request)         │
└────────────────┬───────────────────────┘
                 │
                 ├─ Get validated data
                 │  $validated = $request->validated()
                 │
                 ├─ Add user_id from auth
                 │  $validated['user_id'] = Auth::id()
                 │
                 ├─ Create product in database
                 │  $product = Product::create($validated)
                 │
                 ├─ Log operation
                 │  Log::info('Menambah data produk via API', [...])
                 │
                 └─ Return response (201 Created)
                    {
                      "message": "Produk berhasil ditambahkan!!",
                      "data": {
                        "id": 1,
                        "name": "...",
                        "quantity": ...,
                        "price": ...,
                        "user_id": ...
                      }
                    }
                    │
                    ▼
            ┌────────────────────┐
            │ Client receives    │
            │ response + product │
            │ data               │
            └────────────────────┘
```

---

## Data Flow Diagram - Update Product

```
┌────────────────────┐
│ Client/Postman     │
└────────────┬───────┘
             │ PUT /api/product/5
             │ Auth: Bearer token
             │ Body: {name, quantity, price}
             ▼
┌────────────────────────────────────────┐
│ ProductApiController::update()         │
│ (UpdateProductRequest, int $id)        │
└────────────┬───────────────────────────┘
             │
             ├─ Find product by ID
             │  $product = Product::find($id)
             │
             ├─ Check if product exists
             │  if (!$product) → 404 Not Found
             │
             ├─ Check authorization
             │  if (user is owner OR admin)
             │     → proceed
             │  else
             │     → 403 Forbidden
             │
             ├─ Get validated data
             │  $validated = $request->validated()
             │
             ├─ Update product
             │  $product->update($validated)
             │
             ├─ Log operation
             │  Log::info('Update data produk via API', [...])
             │
             └─ Return response (200 OK)
                {
                  "message": "Produk berhasil diperbarui",
                  "data": {updated_product}
                }
                │
                ▼
        ┌────────────────────┐
        │ Client receives    │
        │ updated product    │
        └────────────────────┘
```

---

## Token Lifecycle

```
1. Login
   └─ POST /api/login
      └─ Token created in personal_access_tokens table
      └─ Token returned to client

2. Using Token
   ├─ Client sends: Authorization: Bearer {token}
   │
   ├─ Sanctum middleware validates
   │  - Check if token exists in DB
   │  - Check if token is not revoked
   │  - Check token's user association
   │
   └─ Request authenticated with Auth::user()

3. Logout
   └─ POST /api/logout
      └─ Token revoked in DB
      └─ Token no longer valid

4. Token Expiration (Optional - can be configured)
   └─ Old tokens can be purged
      └─ Client needs to re-login
```

---

## HTTP Status Codes Used

| Code | Scenario |
|------|----------|
| **200 OK** | Successful GET, PUT, or general success |
| **201 Created** | Resource successfully created |
| **204 No Content** | Successful DELETE (no response body) |
| **400 Bad Request** | Client error in request |
| **401 Unauthorized** | Missing or invalid authentication token |
| **403 Forbidden** | Authenticated but not authorized (policy check) |
| **404 Not Found** | Resource doesn't exist |
| **422 Unprocessable Entity** | Validation error |
| **500 Internal Server Error** | Server error |

---

## Logging Strategy

All operations are logged for audit trail:

### Log Location
- `storage/logs/laravel.log`

### Log Examples

**Create Product Success:**
```
[2026-06-09 10:00:00] local.INFO: Menambah data produk via API
{"list":{"id":1,"name":"Laptop",...}}
```

**Update Product Success:**
```
[2026-06-09 10:05:00] local.INFO: Update data produk via API
{"product":{"id":1,"name":"Updated Laptop",...}}
```

**Delete Product:**
```
[2026-06-09 10:10:00] local.INFO: Hapus data produk via API
{"product_id":1}
```

**Error:**
```
[2026-06-09 10:15:00] local.ERROR: Error saat menambah product via API
{"message":"Exception message..."}
```

---

## Database Tables Involved

### users (Existing)
```
id, name, email, password, role, remember_token, email_verified_at, created_at, updated_at
```

### products (Existing)
```
id, name, quantity, price, user_id (FK), created_at, updated_at
```

### kategoris (Existing)
```
id, name, product_id (FK), created_at, updated_at
```

### personal_access_tokens (Sanctum)
```
id, tokenable_type, tokenable_id, name, token (hashed), abilities, last_used_at, created_at, updated_at
```

---

## Security Measures

✅ **Token-based Authentication**
   - Uses Laravel Sanctum for secure token management
   - Tokens are hashed in database

✅ **Authorization Checks**
   - Product: owner OR admin
   - Category: admin only
   - Prevents unauthorized modifications

✅ **Input Validation**
   - All inputs validated via Form Requests
   - Custom error messages in Indonesian
   - Type checking (numeric, string, etc)

✅ **Error Handling**
   - Try-catch blocks on all operations
   - Errors logged but not exposed to client
   - Appropriate HTTP status codes

✅ **Logging**
   - All operations logged for audit
   - Can track who did what and when

---

## Scaling Considerations

For production deployment:

1. **Database Optimization**
   - Add indexes on frequently searched columns
   - Consider pagination for list endpoints

2. **Caching**
   - Cache product/category lists
   - Cache user permissions

3. **Rate Limiting**
   - Implement throttle middleware
   - Prevent API abuse

4. **Monitoring**
   - Monitor error logs
   - Track API usage patterns

5. **Documentation**
   - Maintain OpenAPI/Swagger docs
   - Use Laravel Scramble for auto-documentation

---

## Files Summary

| File | Purpose |
|------|---------|
| `routes/api.php` | API route definitions |
| `Api/ProductApiController.php` | Product CRUD logic |
| `Api/CategoryApiController.php` | Category CRUD logic |
| `Api/AuthApiController.php` | Authentication logic |
| `Http/Requests/Store*Request.php` | Input validation |
| `Http/Requests/Update*Request.php` | Input validation |
| `Models/User.php` | User model with HasApiTokens |
| `Models/Product.php` | Product model |
| `Models/Kategori.php` | Category model |

---

## Next Steps

1. ✅ Controllers created
2. ✅ Routes configured
3. ✅ Validation rules set
4. ✅ Authorization implemented
5. ⏭️ Testing (see QUICK_START_TESTING.md)
6. ⏭️ Frontend integration
7. ⏭️ Deployment to production

This architecture provides a solid, secure, and scalable API foundation! 🚀
