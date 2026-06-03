# 🔐 Secure Admin Promotion System - Implementation Summary

## What Was Created

A **CLI-only admin promotion system** that prevents normal users from becoming admins through the web interface.

## Security Features

✅ **CLI-Only Command** - Can only be executed from server terminal/command line
✅ **Web Access Blocked** - Automatic rejection if accessed via HTTP/browser
✅ **Database-Backed** - Uses `is_admin` column in `login` table
✅ **Session-Based** - Admin status stored securely in PHP sessions
✅ **Middleware Protected** - `/admin` route protected by Admin middleware

## Files Created/Modified

### 📁 New Files
1. **`cli/promote-admin.php`** - CLI tool to promote/demote users
2. **`database/migrations/add_is_admin_to_login.sql`** - Database migration
3. **`ADMIN_SETUP.md`** - Complete setup and usage guide
4. **`views/admin/dashboard.view.php`** - Admin dashboard view

### 🔧 Modified Files
1. **`Core/Authenticator.php`** - Updated to check and set admin status
2. **`routes.php`** - Changed admin route middleware from "guest" to "admin"
3. **`Http/controllers/admin/index.php`** - Populated with dashboard logic

## Quick Start

### Step 1: Apply Database Migration
```bash
# Option A: Via MySQL command line
mysql -u root -p school_project < database/migrations/add_is_admin_to_login.sql

# Option B: Copy-paste SQL into phpMyAdmin
# See: database/migrations/add_is_admin_to_login.sql
```

### Step 2: Promote a User to Admin
```bash
# By ID
php cli/promote-admin.php 1

# By email
php cli/promote-admin.php user@example.com

# Remove admin status
php cli/promote-admin.php 1 --remove
```

### Step 3: Test Admin Access
1. Log in with the admin user
2. Navigate to `/admin`
3. You should see the admin dashboard

## How It Works

```
User Login Flow
│
├─ User enters credentials
│
├─ Authenticator::attempt() verifies password
│
├─ Database query fetches user + is_admin column
│
├─ Authenticator::login() called with user data
│
├─ If is_admin = 1: $_SESSION['admin'] = true
│
└─ User navigates to /admin
   │
   └─ Admin middleware checks $_SESSION['admin']
      ├─ If true: Access granted
      └─ If false: Redirects to home
```

## Key Security Points

### Why CLI-Only?

```php
// At the top of cli/promote-admin.php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This script can only be run from the command line.\n";
    exit(1);
}
```

- `php_sapi_name()` returns 'cli' only for command-line execution
- Returns 'cgi-fcgi' or 'fpm-fcgi' for web requests
- **Impossible** for a normal user to trigger via web browser or request

### Why No Web Form?

A web form to promote admins would allow:
- ❌ XSS attacks to promote attackers
- ❌ CSRF attacks to auto-promote users
- ❌ Social engineering (trick admins into clicking promote link)
- ❌ Normal users finding/exploiting the endpoint

### Why Session-Based?

```php
// Session cannot be modified by users
if (isset($user['is_admin']) && $user['is_admin']) {
    $_SESSION['admin'] = true;  // Set server-side only
}
```

- Server-side only (cannot be edited by client)
- Regenerated on every login
- Destroyed on logout
- Cannot be faked or replayed

## Admin Middleware

```php
// Core/Middleware/Admin.php
public function handle() {
    if (! $_SESSION['admin'] ?? false) {
        header('location: /');
        exit();
    }
}
```

- Runs before every admin-protected route
- Checks for `$_SESSION['admin']` flag
- Redirects non-admins to home page

## What Admins Can Do

Currently, the admin dashboard displays:
- Total users count
- Total admins count
- Total stored passwords
- Total folders
- Recent user registrations

### To Add More Admin Features

Create new protected routes:

```php
// In routes.php
$router->get('/admin/users', 'admin/users.php')->only('admin');
$router->get('/admin/logs', 'admin/logs.php')->only('admin');
$router->post('/admin/ban-user', 'admin/ban.php')->only('admin');
```

Create corresponding controllers:

```php
// Http/controllers/admin/users.php
$db = App::resolve(Database::class);
$users = $db->query("SELECT * FROM login")->get();
view('admin/users', ['users' => $users]);
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Script can only be run from command line" | Use terminal instead of browser |
| User not found | Check exact ID or email spelling |
| Admin can't access `/admin` | Verify `is_admin` column exists: `DESC login` in MySQL |
| Session not persisting | Clear browser cookies and try again |

## Testing the System

### Test 1: Normal User Cannot Promote
```bash
# As normal user, try to access:
curl http://localhost/cli/promote-admin.php?user=hack
# Expected: Forbidden (403)
```

### Test 2: Only CLI Works
```bash
# Only this works:
php cli/promote-admin.php 2
# Expected: User promoted successfully
```

### Test 3: Admin Can Access Dashboard
```
1. Log in with promoted admin user
2. Go to http://localhost/admin
3. See admin dashboard
```

### Test 4: Non-Admin Blocked
```
1. Log in with regular user
2. Go to http://localhost/admin
3. Redirect to home page
```

## Security Recommendations

1. **Log admin actions** - Track who promoted whom and when
2. **Require verification** - Ask for password confirmation before promotion
3. **Audit trail** - Keep historical record of admin changes
4. **Limit admins** - Only keep necessary admins
5. **Regular review** - Audit admin list monthly
6. **Secure CLI access** - Restrict server SSH access to trusted users

## Files Summary

| File | Purpose | Security |
|------|---------|----------|
| `cli/promote-admin.php` | Promote users to admin | CLI-only, blocks web access |
| `Core/Authenticator.php` | Handle login + set admin flag | Server-side session only |
| `Core/Middleware/Admin.php` | Protect admin routes | Checks session before allowing access |
| `database/migrations/add_is_admin_to_login.sql` | Add admin column | Defaults to non-admin (0) |
| `views/admin/dashboard.view.php` | Admin interface | Requires authentication |

---

For detailed setup instructions, see **ADMIN_SETUP.md**
