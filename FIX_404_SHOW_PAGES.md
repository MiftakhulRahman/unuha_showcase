# 🔧 404 FIX - Dosen/Mahasiswa Show Pages

## Issue
Show pages for Dosen and Mahasiswa were returning 404 Not Found errors.

## Root Cause
The resource routes use parameter names like `{dosen}` and `{mahasiswa}`, but the controllers expect `User` model instances. Without explicit route model binding, Laravel couldn't properly resolve the route parameters.

## Solution
Added explicit route model binding in `routes/web.php`:

```php
Route::bind('dosen', function ($value) {
    return \App\Models\User::findOrFail($value);
});
Route::bind('mahasiswa', function ($value) {
    return \App\Models\User::findOrFail($value);
});
```

This tells Laravel to:
1. When it sees `{dosen}` in a route URL, convert the ID to a `User` model
2. When it sees `{mahasiswa}` in a route URL, convert the ID to a `User` model
3. If the user ID doesn't exist, throw a 404

## Implementation Details
- Location: `routes/web.php` (lines 33-40)
- Scope: Inside the admin routes group
- Works with: Implicit route model binding
- Security: Uses `findOrFail()` for safe lookups

## Testing
✅ Route model binding configured
✅ Show routes accessible: `/admin/dosen/{id}` and `/admin/mahasiswa/{id}`
✅ Controllers' `abort_if()` checks still validate role
✅ Build successful: `npm run build` ✓

## How It Works
1. User clicks Edit icon on Dosen list → goes to `/admin/dosen/{id}`
2. Route model binding converts `{id}` to User model
3. Controller receives User instance as parameter
4. `abort_if($user->role !== 'dosen', 404)` validates it's a dosen
5. Show page renders with user data

## Status
✅ **FIXED** - Show pages now working correctly
