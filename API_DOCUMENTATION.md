# API Documentation - CRUD Product & Category

## Base URL
```
http://localhost/api
```

## Authentication
Semua endpoint kecuali `GET /product/{id}` dan `GET /category/{id}` memerlukan **Bearer Token** authentication.

### 1. Login (Get Access Token)
**Endpoint:** `POST /api/login`

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "message": "Login berhasil",
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "user"
  }
}
```

---

## Product API

### 2. Get All Products
**Endpoint:** `GET /api/product`
**Auth Required:** Yes (Bearer Token)

**Response (200):**
```json
{
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Product 1",
      "quantity": 10,
      "price": 50000,
      "user_id": 1,
      "created_at": "2026-06-09T10:00:00.000000Z",
      "updated_at": "2026-06-09T10:00:00.000000Z",
      "user": {
        "id": 1,
        "name": "John Doe"
      }
    }
  ]
}
```

### 3. Get Single Product (Public)
**Endpoint:** `GET /api/product/{id}`
**Auth Required:** No

**Response (200):**
```json
{
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Product 1",
    "quantity": 10,
    "price": 50000,
    "user_id": 1,
    "created_at": "2026-06-09T10:00:00.000000Z"
  }
}
```

### 4. Create Product
**Endpoint:** `POST /api/product`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`

**Request:**
```json
{
  "name": "New Product",
  "quantity": 20,
  "price": 75000
}
```

**Response (201):**
```json
{
  "message": "Produk berhasil ditambahkan!!",
  "data": {
    "id": 2,
    "name": "New Product",
    "quantity": 20,
    "price": 75000,
    "user_id": 1,
    "created_at": "2026-06-09T10:30:00.000000Z"
  }
}
```

### 5. Update Product
**Endpoint:** `PUT /api/product/{id}`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`
**Note:** Hanya owner atau admin yang bisa update

**Request:**
```json
{
  "name": "Updated Product",
  "quantity": 15,
  "price": 60000
}
```

**Response (200):**
```json
{
  "message": "Produk berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "Updated Product",
    "quantity": 15,
    "price": 60000
  }
}
```

### 6. Delete Product
**Endpoint:** `DELETE /api/product/{id}`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`
**Note:** Hanya owner atau admin yang bisa delete

**Response (204):** No Content
```json
{
  "message": "Produk berhasil dihapus"
}
```

---

## Category API

### 7. Get All Categories
**Endpoint:** `GET /api/category`
**Auth Required:** Yes (Bearer Token)

**Response (200):**
```json
{
  "message": "Categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "product_id": 1,
      "created_at": "2026-06-09T10:00:00.000000Z",
      "product": {
        "id": 1,
        "name": "Product 1"
      }
    }
  ]
}
```

### 8. Get Single Category (Public)
**Endpoint:** `GET /api/category/{id}`
**Auth Required:** No

**Response (200):**
```json
{
  "message": "Category retrieved successfully",
  "data": {
    "id": 1,
    "name": "Electronics",
    "product_id": 1
  }
}
```

### 9. Create Category
**Endpoint:** `POST /api/category`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`

**Request:**
```json
{
  "name": "New Category",
  "product_id": 1
}
```

**Response (201):**
```json
{
  "message": "Kategori berhasil ditambahkan!!",
  "data": {
    "id": 2,
    "name": "New Category",
    "product_id": 1,
    "created_at": "2026-06-09T10:30:00.000000Z"
  }
}
```

### 10. Update Category
**Endpoint:** `PUT /api/category/{id}`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`
**Note:** Hanya admin yang bisa update

**Request:**
```json
{
  "name": "Updated Category",
  "product_id": 2
}
```

**Response (200):**
```json
{
  "message": "Kategori berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "Updated Category",
    "product_id": 2
  }
}
```

### 11. Delete Category
**Endpoint:** `DELETE /api/category/{id}`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`
**Note:** Hanya admin yang bisa delete

**Response (204):** No Content
```json
{
  "message": "Kategori berhasil dihapus"
}
```

### 12. Logout
**Endpoint:** `POST /api/logout`
**Auth Required:** Yes (Bearer Token)
**Header:** `Authorization: Bearer {access_token}`

**Response (200):**
```json
{
  "message": "Logout berhasil"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["Nama produk wajib diisi."]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Email atau password salah"
}
```

### 403 Forbidden
```json
{
  "message": "Anda tidak memiliki hak akses untuk mengupdate produk ini"
}
```

### 404 Not Found
```json
{
  "message": "Product tidak ditemukan"
}
```

### 500 Internal Server Error
```json
{
  "message": "Error creating product",
  "error": "Error details..."
}
```

---

## Testing dengan Postman

1. **Import Collection** atau buat requests secara manual
2. **Set Environment Variable** `base_url = http://localhost/api` dan `token = <access_token>`
3. **Login terlebih dahulu** untuk mendapatkan token
4. **Gunakan token** di Header untuk semua protected routes:
   ```
   Authorization: Bearer {{token}}
   ```

---

## Authorization Rules

### Product
- **CREATE/UPDATE/DELETE**: Hanya owner atau admin
- **READ**: Public (kecuali list memerlukan token)

### Category
- **CREATE**: Admin + Token
- **UPDATE/DELETE**: Admin only
- **READ**: Public (kecuali list memerlukan token)
