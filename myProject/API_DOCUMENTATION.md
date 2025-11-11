# Laravel RBAC REST API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
This API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

---

## Authentication Endpoints

### 1. Register User
**POST** `/auth/register`

**Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
}
```

**Response:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "created_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123..."
}
```

### 2. Login User
**POST** `/auth/login`

**Body (JSON):**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

### 3. Logout User
**POST** `/auth/logout`
*Requires Authentication*

### 4. Get Current User
**GET** `/auth/user`
*Requires Authentication*

---

## Posts Endpoints

### Public Posts

#### 1. Get All Approved Posts
**GET** `/posts`

**Query Parameters:**
- `category_id` (optional): Filter by category
- `search` (optional): Search in title, content, author
- `sort_by` (optional): Sort field (default: created_at)
- `sort_order` (optional): asc/desc (default: desc)
- `per_page` (optional): Items per page (default: 15)

#### 2. Get Single Post
**GET** `/posts/{id}`

### User Posts (Authenticated)

#### 3. Get User's Posts
**GET** `/user/posts`
*Requires Authentication*

**Query Parameters:**
- `status` (optional): Filter by status
- `include_trashed` (optional): Include soft-deleted posts
- `per_page` (optional): Items per page

#### 4. Create Post
**POST** `/user/posts`
*Requires Authentication*

**Body (Form Data):**
```
title: "My Post Title"
content: "Post content here..."
author: "Author Name"
category_id: 1
featured_image: [file] (optional)
featured_image_alt: "Alt text" (optional)
featured_image_greyscale: true/false (optional)
gallery_images[]: [files] (optional, max 10)
gallery_images_greyscale: true/false (optional)
```

#### 5. Get User's Single Post
**GET** `/user/posts/{id}`
*Requires Authentication*

#### 6. Update Post
**PUT** `/user/posts/{id}`
*Requires Authentication*

**Body (Form Data):** Same as create, plus:
```
remove_featured_image: true/false (optional)
remove_gallery_images: true/false (optional)
```

#### 7. Delete Post (Soft Delete)
**DELETE** `/user/posts/{id}`
*Requires Authentication*

### Admin Posts (Admin Only)

#### 8. Get All Posts (Admin)
**GET** `/admin/posts`
*Requires Admin Role*

**Query Parameters:**
- `include_trashed` (optional): Include soft-deleted posts
- `status` (optional): Filter by status
- `user_id` (optional): Filter by user
- `per_page` (optional): Items per page

#### 9. Create Post (Admin)
**POST** `/admin/posts`
*Requires Admin Role*

**Body (Form Data):** Same as user create, plus:
```
user_id: 1
status: "pending|approved|rejected"
```

#### 10. Get Single Post (Admin)
**GET** `/admin/posts/{id}`
*Requires Admin Role*

#### 11. Update Post (Admin)
**PUT** `/admin/posts/{id}`
*Requires Admin Role*

#### 12. Update Post Status
**PATCH** `/admin/posts/{id}/status`
*Requires Admin Role*

**Body (JSON):**
```json
{
    "status": "approved"
}
```

#### 13. Delete Post (Admin)
**DELETE** `/admin/posts/{id}`
*Requires Admin Role*

#### 14. Restore Post
**POST** `/admin/posts/{id}/restore`
*Requires Admin Role*

#### 15. Force Delete Post
**DELETE** `/admin/posts/{id}/force-delete`
*Requires Admin Role*

---

## Categories Endpoints

### 1. Get All Categories
**GET** `/categories`

**Query Parameters:**
- `include_trashed` (optional, admin only): Include soft-deleted categories

### 2. Get Single Category
**GET** `/categories/{id}`

### Admin Categories (Admin Only)

#### 3. Create Category
**POST** `/admin/categories`
*Requires Admin Role*

**Body (JSON):**
```json
{
    "name": "Technology",
    "description": "Tech related posts",
    "slug": "technology"
}
```

#### 4. Update Category
**PUT** `/admin/categories/{id}`
*Requires Admin Role*

#### 5. Delete Category
**DELETE** `/admin/categories/{id}`
*Requires Admin Role*

#### 6. Restore Category
**POST** `/admin/categories/{id}/restore`
*Requires Admin Role*

#### 7. Force Delete Category
**DELETE** `/admin/categories/{id}/force-delete`
*Requires Admin Role*

---

## Comments Endpoints

### 1. Create Comment
**POST** `/posts/{post_id}/comments`
*Requires Authentication*

**Body (JSON):**
```json
{
    "content": "This is a comment",
    "parent_id": null
}
```

### 2. Update Comment
**PUT** `/comments/{id}`
*Requires Authentication (Owner or Admin)*

**Body (JSON):**
```json
{
    "content": "Updated comment content"
}
```

### 3. Delete Comment
**DELETE** `/comments/{id}`
*Requires Authentication (Owner or Admin)*

### Admin Comments (Admin Only)

#### 4. Get All Comments (Admin)
**GET** `/admin/comments`
*Requires Admin Role*

**Query Parameters:**
- `include_trashed` (optional): Include soft-deleted comments
- `post_id` (optional): Filter by post
- `user_id` (optional): Filter by user
- `top_level_only` (optional): Only top-level comments
- `per_page` (optional): Items per page

#### 5. Restore Comment
**POST** `/admin/comments/{id}/restore`
*Requires Admin Role*

#### 6. Force Delete Comment
**DELETE** `/admin/comments/{id}/force-delete`
*Requires Admin Role*

---

## Users Endpoints (Admin Only)

### 1. Get All Users
**GET** `/admin/users`
*Requires Admin Role*

**Query Parameters:**
- `role` (optional): Filter by role
- `search` (optional): Search in name, email
- `sort_by` (optional): Sort field
- `sort_order` (optional): asc/desc
- `per_page` (optional): Items per page

### 2. Create User
**POST** `/admin/users`
*Requires Admin Role*

**Body (JSON):**
```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
}
```

### 3. Get Single User
**GET** `/admin/users/{id}`
*Requires Admin Role*

### 4. Update User
**PUT** `/admin/users/{id}`
*Requires Admin Role*

**Body (JSON):**
```json
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123",
    "role": "admin"
}
```

### 5. Delete User
**DELETE** `/admin/users/{id}`
*Requires Admin Role*

---

## Error Responses

### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### Unauthorized (401)
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
    "message": "Unauthorized. Insufficient permissions."
}
```

### Not Found (404)
```json
{
    "message": "Post not found."
}
```

---

## Postman Testing Steps

1. **Setup Environment:**
   - Create a new environment in Postman
   - Add variable `base_url` with value `http://localhost:8000/api`
   - Add variable `token` (will be set after login)

2. **Test Authentication:**
   - Register a new user
   - Login with credentials
   - Copy the token from response and set it in environment
   - Test protected endpoints

3. **Test CRUD Operations:**
   - Create categories (as admin)
   - Create posts with images
   - Update posts
   - Add comments
   - Test soft delete and restore

4. **Test Role-Based Access:**
   - Try accessing admin endpoints as regular user
   - Verify proper error responses

---

## Image Upload Notes

- **Supported formats:** JPEG, PNG, JPG, GIF, WebP
- **Max file size:** 3MB per image
- **Max gallery images:** 10 per post
- **Features:** Automatic WebP conversion, greyscale filter, thumbnails
- **Storage:** Files stored in `storage/app/public/posts/`

---

## Rate Limiting

The API includes standard Laravel rate limiting:
- **General API:** 60 requests per minute
- **Authentication:** 5 attempts per minute for login

---

## CORS

CORS is configured to allow requests from your frontend application. Update `config/cors.php` if needed.
