# 🌐 Admin CRUD API Reference & URLs

## Base URL
```
https://unuha-showcase.test/admin
```

---

## 📊 Users Management
**Base Route**: `/admin/users`

### List Users
```
GET /admin/users
GET /admin/users?search=john
GET /admin/users?role=dosen
GET /admin/users?status=active
GET /admin/users?registration=completed
GET /admin/users?search=john&role=mahasiswa&status=active&page=2
```

**Query Parameters:**
- `search`: Search by name, email, username
- `role`: Filter by role (superadmin, dosen, mahasiswa)
- `status`: Filter by status (active, inactive)
- `registration`: Filter by registration (completed, pending)
- `page`: Pagination (1, 2, 3, ...)

**Response**: Inertia renders `Admin/Users/Index` with:
```json
{
  "users": {
    "data": [...],
    "links": {...],
    "current_page": 1,
    ...
  },
  "filters": {
    "search": "john",
    "role": "mahasiswa",
    "status": "active",
    "registration": null
  }
}
```

### View Single User
```
GET /admin/users/{user_id}
```

### Edit User
```
GET /admin/users/{user_id}/edit
PUT /admin/users/{user_id}
```

### Delete Single User
```
DELETE /admin/users/{user_id}
```

### Delete Multiple Users (Bulk Delete)
```
POST /admin/users/bulk-delete
Payload: { ids: [1, 2, 3] }
```

### Reset User Password
```
POST /admin/users/{user_id}/reset-password
```

### Toggle User Status
```
POST /admin/users/{user_id}/toggle-status
```

---

## 👥 Mahasiswa Management
**Base Route**: `/admin/mahasiswa`

### List Mahasiswa
```
GET /admin/mahasiswa
GET /admin/mahasiswa?search=budi
GET /admin/mahasiswa?prodi_id=1
GET /admin/mahasiswa?angkatan=2022
GET /admin/mahasiswa?status=active
GET /admin/mahasiswa?search=budi&prodi_id=1&status=active&page=1
```

**Query Parameters:**
- `search`: Search by name, email, NIM
- `prodi_id`: Filter by program studi ID
- `angkatan`: Filter by year of entry
- `status`: Filter by status (active, inactive)
- `page`: Pagination

### View Mahasiswa Details
```
GET /admin/mahasiswa/{mahasiswa_id}
```

### Edit Mahasiswa
```
GET /admin/mahasiswa/{mahasiswa_id}/edit
PUT /admin/mahasiswa/{mahasiswa_id}
```

### Delete Mahasiswa
```
DELETE /admin/mahasiswa/{mahasiswa_id}
```

### Bulk Delete Mahasiswa
```
POST /admin/mahasiswa/bulk-delete
Payload: { ids: [1, 2, 3] }
```

---

## 👨‍🏫 Dosen Management
**Base Route**: `/admin/dosen`

### List Dosen
```
GET /admin/dosen
GET /admin/dosen?search=prof
GET /admin/dosen?prodi_id=1
GET /admin/dosen?status=active
GET /admin/dosen?search=prof&prodi_id=1&page=1
```

**Query Parameters:**
- `search`: Search by name, email, NIDN
- `prodi_id`: Filter by program studi
- `status`: Filter by status (active, inactive)
- `page`: Pagination

### View Dosen Details
```
GET /admin/dosen/{dosen_id}
```

### Edit Dosen
```
GET /admin/dosen/{dosen_id}/edit
PUT /admin/dosen/{dosen_id}
```

### Delete Dosen
```
DELETE /admin/dosen/{dosen_id}
```

### Bulk Delete Dosen
```
POST /admin/dosen/bulk-delete
Payload: { ids: [1, 2, 3] }
```

---

## 📚 Program Studi Management
**Base Route**: `/admin/prodis`

### List Program Studi
```
GET /admin/prodis
GET /admin/prodis?search=informatika
GET /admin/prodis?status=active
GET /admin/prodis?search=info&status=active&page=1
```

**Query Parameters:**
- `search`: Search by nama, kode
- `status`: Filter by status (active, inactive)
- `page`: Pagination

### Create Program Studi
```
GET /admin/prodis/create          (form)
POST /admin/prodis                (submit)
```

### View Program Studi Details
```
GET /admin/prodis/{prodi_id}
```

### Edit Program Studi
```
GET /admin/prodis/{prodi_id}/edit
PUT /admin/prodis/{prodi_id}
```

### Delete Program Studi
```
DELETE /admin/prodis/{prodi_id}
```

### Bulk Delete Program Studi
```
POST /admin/prodis/bulk-delete
Payload: { ids: [1, 2] }
```

---

## 🏷️ Kategori Management
**Base Route**: `/admin/kategoris`

### List Kategori
```
GET /admin/kategoris
GET /admin/kategoris?search=web
GET /admin/kategoris?status=active
GET /admin/kategoris?search=web&status=active&page=1
```

**Query Parameters:**
- `search`: Search by nama, slug
- `status`: Filter by status (active, inactive)
- `page`: Pagination

### Create Kategori
```
GET /admin/kategoris/create       (form)
POST /admin/kategoris             (submit)
```

### View Kategori Details
```
GET /admin/kategoris/{kategori_id}
```

### Edit Kategori
```
GET /admin/kategoris/{kategori_id}/edit
PUT /admin/kategoris/{kategori_id}
```

### Delete Kategori
```
DELETE /admin/kategoris/{kategori_id}
```

### Bulk Delete Kategori
```
POST /admin/kategoris/bulk-delete
Payload: { ids: [1, 2, 3] }
```

---

## 🛠️ Tools/Teknologi Management
**Base Route**: `/admin/tools`

### List Tools
```
GET /admin/tools
GET /admin/tools?search=laravel
GET /admin/tools?status=active
GET /admin/tools?search=laravel&status=active&page=1
```

**Query Parameters:**
- `search`: Search by nama, slug
- `status`: Filter by status (active, inactive)
- `page`: Pagination

### Create Tool
```
GET /admin/tools/create           (form)
POST /admin/tools                 (submit)
```

### View Tool Details
```
GET /admin/tools/{tool_id}
```

### Edit Tool
```
GET /admin/tools/{tool_id}/edit
PUT /admin/tools/{tool_id}
```

### Delete Tool
```
DELETE /admin/tools/{tool_id}
```

### Bulk Delete Tools
```
POST /admin/tools/bulk-delete
Payload: { ids: [1, 2, 3] }
```

---

## 📋 Bulk Delete Format

All bulk delete endpoints accept the same format:

```
POST /admin/{module}/bulk-delete

Headers:
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}

Body:
{
  "ids": [1, 2, 3, 4, 5]
}

Response:
Redirect to /{module}
with success message in session
```

---

## 🔍 Query String Examples

### Complex Filter Example - Users
```
/admin/users?search=john&role=mahasiswa&status=active&registration=completed&page=2
```

### Complex Filter Example - Mahasiswa
```
/admin/mahasiswa?search=budi+susanto&prodi_id=1&angkatan=2022&status=active&page=1
```

### Combined Search and Filter
```
/admin/dosen?search=prof.%20dr&prodi_id=2&status=inactive
```

---

## 🎯 Common Use Cases

### Find Active Users Only
```
GET /admin/users?status=active
```

### Find All Mahasiswa from Informatika
```
GET /admin/mahasiswa?prodi_id=1
```

### Find Dosen from Specific Year
```
GET /admin/mahasiswa?angkatan=2020
```

### Search and Filter Combined
```
GET /admin/mahasiswa?search=ali&prodi_id=1&status=active
```

### Pagination with Filters
```
GET /admin/users?role=dosen&page=2
```

---

## 📡 Response Status Codes

```
200 OK               - Request successful
302 Found            - Redirect (after create/update/delete)
403 Forbidden        - Not authorized (not superadmin)
404 Not Found        - Resource not found
422 Unprocessable    - Validation errors
500 Server Error     - Server-side error
```

---

## 🛡️ Authentication Requirements

All admin routes require:
1. User must be logged in
2. User must have email verified
3. User must have `role = 'superadmin'`

If not authenticated → Redirect to login  
If not authorized → HTTP 403 Forbidden

---

## 📝 Request/Response Examples

### Example: List Users with Filters
```
GET /admin/users?search=john&role=mahasiswa&status=active

Response (Inertia):
{
  "component": "Admin/Users/Index",
  "props": {
    "users": {
      "data": [
        {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com",
          "username": "johndoe",
          "role": "mahasiswa",
          "is_active": true,
          "registration_completed": true
        }
      ],
      "links": [
        {"label": "Previous", "url": null, "active": false},
        {"label": "1", "url": "/admin/users?...", "active": true},
        {"label": "2", "url": "/admin/users?page=2&...", "active": false},
        {"label": "Next", "url": "/admin/users?page=2&...", "active": false}
      ]
    },
    "filters": {
      "search": "john",
      "role": "mahasiswa",
      "status": "active",
      "registration": null
    }
  }
}
```

### Example: Bulk Delete
```
POST /admin/users/bulk-delete
Content-Type: application/json

{
  "ids": [1, 2, 3]
}

Response: HTTP 302
Location: /admin/users
Session Flash: "3 users deleted!"
```

---

## 🧪 Testing with cURL

### List Users
```bash
curl -X GET "http://localhost/admin/users?search=john" \
  -H "Accept: application/json"
```

### Bulk Delete
```bash
curl -X POST "http://localhost/admin/users/bulk-delete" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{"ids":[1,2,3]}'
```

---

## 💾 Database Queries

### Search Query (Users)
```sql
WHERE name LIKE '%john%'
   OR email LIKE '%john%'
   OR username LIKE '%john%'
```

### Filter Query (Mahasiswa)
```sql
WHERE prodi_id = 1
  AND angkatan = 2022
  AND is_active = 1
```

### Bulk Delete
```sql
DELETE FROM users WHERE id IN (1, 2, 3)
```

---

**API Version**: 1.0  
**Last Updated**: 2024-11-20  
**Status**: ✅ Production Ready
