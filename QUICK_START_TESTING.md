# 🚀 Quick Start Guide - Testing API

## Prerequisites
- Laravel project sudah running di `http://localhost/praktikum1pwf/`
- Database sudah terseeding dengan user test
- Postman atau cURL siap digunakan

---

## Step 1: Pastikan User untuk Testing Ada

**User Default dari Seeding:**
```
Email: admin@example.com (role: admin)
Password: password

Email: user@example.com (role: user)
Password: password
```

Jika belum ada, jalankan:
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

---

## Step 2: Setup Postman Collection

### Buat Environment Variable:
```
base_url = http://localhost/praktikum1pwf/api
admin_token = (akan diisi setelah login)
user_token = (akan diisi setelah login)
```

### Import requests atau buat manual:

---

## Step 3: Test Authentication

### Login sebagai Admin
```
POST {{base_url}}/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response (copy access_token ke variable admin_token):**
```json
{
  "message": "Login berhasil",
  "access_token": "1|xxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

### Login sebagai User
```
POST {{base_url}}/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

---

## Step 4: Test Product API

### 4.1 Create Product (sebagai user)
```
POST {{base_url}}/product
Content-Type: application/json
Authorization: Bearer {{user_token}}

{
  "name": "MacBook Pro 14",
  "quantity": 5,
  "price": 20000000
}
```

✅ Expected: 201 Created
```json
{
  "message": "Produk berhasil ditambahkan!!",
  "data": {
    "id": 1,
    "name": "MacBook Pro 14",
    "quantity": 5,
    "price": 20000000,
    "user_id": 2,
    "created_at": "2026-06-09T10:00:00.000000Z"
  }
}
```

### 4.2 Get All Products (perlu token)
```
GET {{base_url}}/product
Authorization: Bearer {{user_token}}
```

✅ Expected: 200 OK dengan list products

### 4.3 Get Single Product (PUBLIC, tidak perlu token)
```
GET {{base_url}}/product/1
```

✅ Expected: 200 OK

### 4.4 Update Product (sebagai owner)
```
PUT {{base_url}}/product/1
Content-Type: application/json
Authorization: Bearer {{user_token}}

{
  "name": "MacBook Pro 15",
  "quantity": 3,
  "price": 25000000
}
```

✅ Expected: 200 OK

### 4.5 Update Product sebagai Non-Owner (should fail)
```
PUT {{base_url}}/product/1
Content-Type: application/json
Authorization: Bearer {{user_token2}}

{
  "name": "Hacked",
  "quantity": 999,
  "price": 1
}
```

❌ Expected: 403 Forbidden
```json
{
  "message": "Anda tidak memiliki hak akses untuk mengupdate produk ini"
}
```

### 4.6 Delete Product (sebagai owner)
```
DELETE {{base_url}}/product/1
Authorization: Bearer {{user_token}}
```

✅ Expected: 204 No Content

---

## Step 5: Test Category API

### 5.1 Create Category (hanya admin)
```
POST {{base_url}}/category
Content-Type: application/json
Authorization: Bearer {{admin_token}}

{
  "name": "Electronics",
  "product_id": 1
}
```

✅ Expected: 201 Created

### 5.2 Create Category sebagai User (should fail)
```
POST {{base_url}}/category
Content-Type: application/json
Authorization: Bearer {{user_token}}

{
  "name": "Fashion",
  "product_id": 2
}
```

❌ Expected: 403 Forbidden (dari policy)

### 5.3 Get All Categories
```
GET {{base_url}}/category
Authorization: Bearer {{user_token}}
```

✅ Expected: 200 OK

### 5.4 Get Single Category (PUBLIC)
```
GET {{base_url}}/category/1
```

✅ Expected: 200 OK

### 5.5 Update Category (hanya admin)
```
PUT {{base_url}}/category/1
Content-Type: application/json
Authorization: Bearer {{admin_token}}

{
  "name": "Elektronik",
  "product_id": 2
}
```

✅ Expected: 200 OK

### 5.6 Delete Category (hanya admin)
```
DELETE {{base_url}}/category/1
Authorization: Bearer {{admin_token}}
```

✅ Expected: 204 No Content

---

## Step 6: Test Error Scenarios

### 6.1 Missing Token
```
GET {{base_url}}/product
```

❌ Expected: 401 Unauthorized

### 6.2 Invalid Token
```
GET {{base_url}}/product
Authorization: Bearer invalid_token_xxxx
```

❌ Expected: 401 Unauthorized

### 6.3 Validation Error
```
POST {{base_url}}/product
Content-Type: application/json
Authorization: Bearer {{user_token}}

{
  "name": "",
  "quantity": -5,
  "price": "invalid"
}
```

❌ Expected: 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["Nama produk wajib diisi."],
    "quantity": ["Jumlah tidak boleh kurang dari 0."],
    "price": ["Harga produk harus berupa angka."]
  }
}
```

### 6.4 Resource Not Found
```
GET {{base_url}}/product/9999
```

✅ Expected: 200 OK (public endpoint)

```
GET {{base_url}}/product/9999
Authorization: Bearer {{user_token}}
```

❌ Expected: 404 Not Found

---

## Step 7: Logout

```
POST {{base_url}}/logout
Authorization: Bearer {{user_token}}
```

✅ Expected: 200 OK
```json
{
  "message": "Logout berhasil"
}
```

Token sekarang tidak valid lagi.

---

## Testing Summary Checklist

- [ ] Login berhasil dan dapat token
- [ ] Create product berhasil
- [ ] List products berhasil (dengan token)
- [ ] Get single product berhasil (tanpa token)
- [ ] Update product berhasil (sebagai owner)
- [ ] Delete product berhasil (sebagai owner)
- [ ] Create category berhasil (sebagai admin)
- [ ] List categories berhasil
- [ ] Update category berhasil (sebagai admin)
- [ ] Delete category berhasil (sebagai admin)
- [ ] Authorization check berfungsi (non-admin tidak bisa create category)
- [ ] Ownership check berfungsi (non-owner tidak bisa update/delete product)
- [ ] Validation error bekerja
- [ ] 401 error ketika tidak ada/invalid token
- [ ] 404 error ketika resource tidak ditemukan
- [ ] Logout berhasil dan token invalidated

---

## Menggunakan cURL (Alternative)

### Login
```bash
curl -X POST http://localhost/praktikum1pwf/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

### Get All Products
```bash
curl -X GET http://localhost/praktikum1pwf/api/product \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Product
```bash
curl -X POST http://localhost/praktikum1pwf/api/product \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"name":"Product Name","quantity":10,"price":50000}'
```

---

## Troubleshooting

**Q: 401 Unauthorized pada semua request**
A: Pastikan token masih valid, coba login ulang

**Q: 403 Forbidden pada create category sebagai user**
A: Normal! Hanya admin yang bisa create category

**Q: 404 Not Found**
A: Resource dengan ID tersebut tidak ada

**Q: 422 Validation Error**
A: Check dokumentasi dan pastikan data valid sesuai rules

**Q: CORS Error**
A: Jika dari frontend, pastikan API header sudah dikonfigurasi (biasanya OK untuk Postman)

---

## Next Steps

✅ API sudah siap untuk production-like testing
✅ Dokumentasi ada di API_DOCUMENTATION.md
✅ Siap untuk integrasi dengan frontend

Selamat testing! 🎉
