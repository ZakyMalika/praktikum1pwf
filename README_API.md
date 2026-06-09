# 🎓 Pertemuan 9 - API CRUD Implementation

Dokumentasi lengkap implementasi API CRUD untuk Product dan Category dengan Token-based Authentication menggunakan Laravel Sanctum.

---

## 📚 Dokumentasi Tersedia

### 1. **QUICK_START_TESTING.md** ⭐ START HERE
   - Step-by-step testing guide
   - Postman examples dengan hasil yang diharapkan
   - Curl commands untuk alternative testing
   - Error scenario testing
   - Troubleshooting

### 2. **API_DOCUMENTATION.md**
   - Detailed documentation untuk setiap endpoint
   - Request/Response format
   - Error codes dan responses
   - Authorization rules

### 3. **API_ARCHITECTURE.md**
   - System architecture diagram
   - Data flow diagrams
   - Authentication flow
   - Authorization logic
   - Error handling strategy
   - Database schema

### 4. **TUGAS_SELESAI.md**
   - Checklist penyelesaian tugas
   - File yang dibuat/dimodifikasi
   - Status dan ringkasan

---

## ✅ Yang Sudah Dikerjakan

### Tugas 1: API Category dengan POST/GET/PUT/DELETE + Token Auth
✅ **CategoryApiController** dengan 5 methods:
- `index()` - GET /api/category (list dengan token)
- `show(int $id)` - GET /api/category/{id} (publik)
- `store()` - POST /api/category (create, admin only)
- `update()` - PUT /api/category/{id} (admin only)
- `destroy()` - DELETE /api/category/{id} (admin only)

✅ **Form Requests:**
- `StoreCategoryRequest` - validasi untuk create
- `UpdateCategoryRequest` - validasi untuk update

### Tugas 2: Lengkapi ProductApiController GET/PUT/DELETE
✅ **ProductApiController** dengan 5 methods:
- `index()` - GET /api/product (list dengan token)
- `show(int $id)` - GET /api/product/{id} (publik)
- `store()` - POST /api/product (sudah ada)
- `update()` - PUT /api/product/{id} (owner/admin only)
- `destroy()` - DELETE /api/product/{id} (owner/admin only)

✅ **Form Requests:**
- `StoreProductRequest` - validasi untuk create
- `UpdateProductRequest` - validasi untuk update

---

## 🔐 Authentication & Authorization

### Token-Based Authentication (Laravel Sanctum)
```
1. User login dengan email/password
2. Server mengembalikan access_token (Bearer token)
3. Client menyimpan token
4. Client mengirim token di setiap request: Authorization: Bearer {token}
5. Server memvalidasi token
6. Request diproses dengan authenticated user context
```

### Authorization Rules
| Resource | Action | Who Can | Rule |
|----------|--------|---------|------|
| Product | CREATE | Any User | Must be authenticated |
| Product | UPDATE | Owner/Admin | Own product OR admin role |
| Product | DELETE | Owner/Admin | Own product OR admin role |
| Product | READ | Public | No auth required for single, token required for list |
| Category | CREATE | Admin | Admin role only |
| Category | UPDATE | Admin | Admin role only |
| Category | DELETE | Admin | Admin role only |
| Category | READ | Public | No auth required for single, token required for list |

---

## 🚀 Quick Start (5 Minutes)

### 1. Pastikan Database Siap
```bash
php artisan migrate
php artisan db:seed
```

### 2. Buat User untuk Testing (jika belum ada)
```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);

User::create([
    'name' => 'Regular User',
    'email' => 'user@example.com',
    'password' => bcrypt('password'),
    'role' => 'user'
]);
```

### 3. Test dengan Postman
1. POST http://localhost/praktikum1pwf/api/login
   - Body: `{"email":"user@example.com","password":"password"}`
   - Copy access_token dari response

2. GET http://localhost/praktikum1pwf/api/product
   - Header: `Authorization: Bearer {token_dari_step_1}`

3. POST http://localhost/praktikum1pwf/api/product
   - Header: `Authorization: Bearer {token}`
   - Body: `{"name":"Product","quantity":5,"price":50000}`

### Lebih Detail → Lihat QUICK_START_TESTING.md

---

## 📁 File Structure

```
app/
  ├─ Http/Controllers/Api/
  │  ├─ AuthApiController.php        ← Login/Logout
  │  ├─ ProductApiController.php     ← Product CRUD
  │  └─ CategoryApiController.php    ← Category CRUD
  ├─ Http/Requests/
  │  ├─ StoreProductRequest.php      ← Validasi create product
  │  ├─ UpdateProductRequest.php     ← Validasi update product
  │  ├─ StoreCategoryRequest.php     ← Validasi create category
  │  └─ UpdateCategoryRequest.php    ← Validasi update category
  └─ Models/
     ├─ User.php                     ← With HasApiTokens
     ├─ Product.php                  ← Product model
     └─ Kategori.php                 ← Category model

routes/
  └─ api.php                         ← API routes (updated)

Documentation/
  ├─ QUICK_START_TESTING.md          ← Testing guide
  ├─ API_DOCUMENTATION.md            ← Endpoint docs
  ├─ API_ARCHITECTURE.md             ← Architecture & diagrams
  ├─ TUGAS_SELESAI.md                ← Completion checklist
  └─ README.md                       ← This file
```

---

## 🔗 API Endpoints Summary

### Authentication
```
POST   /api/login                    - Login & get token
POST   /api/logout                   - Logout & revoke token
```

### Product (Protected)
```
GET    /api/product                  - List all (need token)
GET    /api/product/{id}             - Get one (public)
POST   /api/product                  - Create (need token)
PUT    /api/product/{id}             - Update (owner/admin + token)
DELETE /api/product/{id}             - Delete (owner/admin + token)
```

### Category (Protected)
```
GET    /api/category                 - List all (need token)
GET    /api/category/{id}            - Get one (public)
POST   /api/category                 - Create (admin + token)
PUT    /api/category/{id}            - Update (admin + token)
DELETE /api/category/{id}            - Delete (admin + token)
```

---

## 💻 Code Examples

### Example 1: Login
```bash
curl -X POST http://localhost/praktikum1pwf/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

Response:
```json
{
  "message": "Login berhasil",
  "access_token": "1|abcd1234...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "user"
  }
}
```

### Example 2: Create Product
```bash
curl -X POST http://localhost/praktikum1pwf/api/product \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|abcd1234..." \
  -d '{
    "name": "MacBook Pro",
    "quantity": 5,
    "price": 20000000
  }'
```

Response:
```json
{
  "message": "Produk berhasil ditambahkan!!",
  "data": {
    "id": 1,
    "name": "MacBook Pro",
    "quantity": 5,
    "price": 20000000,
    "user_id": 1,
    "created_at": "2026-06-09T10:00:00Z"
  }
}
```

### Example 3: List Products
```bash
curl -X GET http://localhost/praktikum1pwf/api/product \
  -H "Authorization: Bearer 1|abcd1234..."
```

---

## 📝 Key Features Implemented

✅ **Token-Based Authentication**
- Login returns Bearer token
- Token divalidasi di setiap protected endpoint

✅ **Role-Based Authorization**
- Admin: dapat create/update/delete category
- User: dapat create/update/delete own products
- Public: dapat melihat single product/category

✅ **Input Validation**
- Semua input divalidasi via Form Requests
- Custom error messages dalam Bahasa Indonesia
- Automatic 422 response untuk validation error

✅ **Error Handling**
- Try-catch blocks di semua operations
- Proper HTTP status codes (200, 201, 204, 400, 401, 403, 404, 500)
- Consistent JSON error responses

✅ **Logging**
- Semua create/update/delete operations dicatat
- Location: storage/logs/laravel.log

✅ **Relationships**
- Product hasMany Category
- Category belongsTo Product
- User hasMany Products
- Category/Product belongsTo User (for owner)

---

## 🧪 Testing Checklist

- [ ] Login returns valid token
- [ ] Create product berhasil (user)
- [ ] Get product berhasil (publik)
- [ ] List product berhasil (dengan token)
- [ ] Update product berhasil (owner/admin)
- [ ] Delete product berhasil (owner/admin)
- [ ] Create category berhasil (admin only)
- [ ] Update category berhasil (admin only)
- [ ] Delete category berhasil (admin only)
- [ ] Non-owner tidak bisa update/delete product (403)
- [ ] Non-admin tidak bisa create/update/delete category (403)
- [ ] Invalid token returns 401
- [ ] Validation error returns 422
- [ ] Resource not found returns 404
- [ ] Logout invalidates token

Lihat **QUICK_START_TESTING.md** untuk detailed testing steps.

---

## 🔍 Validation Rules

### Product
```
name: required, string, max 255
quantity: required, numeric, min 0
price: required, numeric, min 0
```

### Category
```
name: required, string, max 255, unique
product_id: required, exists in products table
```

---

## 📊 Response Format

### Success Response (Create - 201)
```json
{
  "message": "Deskripsi sukses",
  "data": { /* resource data */ }
}
```

### Success Response (Update - 200)
```json
{
  "message": "Deskripsi sukses",
  "data": { /* updated resource */ }
}
```

### Success Response (Delete - 204)
```
No content body
```

### Error Response
```json
{
  "message": "Error description",
  "error": "Additional details (optional)"
}
```

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

---

## 🛠️ Troubleshooting

### Q: 401 Unauthorized
**A:** Token tidak ada atau tidak valid
- Pastikan header: `Authorization: Bearer {token}`
- Coba login ulang untuk dapat token baru

### Q: 403 Forbidden
**A:** Authenticated tapi tidak authorized
- Pastikan user memiliki role/permission yang tepat
- Untuk product: hanya owner atau admin
- Untuk category: hanya admin

### Q: 422 Unprocessable Entity
**A:** Validation error
- Check error response untuk field yang salah
- Pastikan data sesuai dengan validation rules

### Q: 404 Not Found
**A:** Resource tidak ditemukan
- Pastikan resource ID yang digunakan benar
- Check bahwa resource sudah di-create sebelumnya

---

## 📚 References

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Laravel Validation](https://laravel.com/docs/validation)
- [HTTP Status Codes](https://httpwg.org/specs/rfc9110.html#status.codes)
- [REST API Best Practices](https://restfulapi.net/)

---

## 🎯 Next Steps

1. ✅ Implementation complete
2. 📖 Read QUICK_START_TESTING.md
3. 🧪 Test dengan Postman
4. 🐛 Debug any issues
5. 🚀 Ready for production!

---

## 📞 Summary

Implementasi API CRUD untuk Product dan Category dengan:
- ✅ 10 API endpoints (5 product, 5 category)
- ✅ Token-based authentication (Sanctum)
- ✅ Role-based authorization
- ✅ Input validation
- ✅ Error handling & logging
- ✅ Indonesian error messages
- ✅ Comprehensive documentation

**Status: 🎉 SIAP UNTUK TESTING DAN PRODUCTION**

---

**Last Updated:** June 9, 2026
**Framework:** Laravel 11 with Sanctum
**Authentication:** Bearer Token (API Token)
**Database:** MySQL
