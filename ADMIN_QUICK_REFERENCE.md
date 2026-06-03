# 🔐 Admin Operations - Quick Reference

## Promote User to Admin

```bash
# By user ID
php cli/promote-admin.php 1

# By email address
php cli/promote-admin.php admin@school-project.test
```

## Remove Admin Status

```bash
php cli/promote-admin.php 1 --remove
```

## Check if User is Admin

**In Database:**
```sql
SELECT username, email, is_admin FROM login WHERE id = 1;
```

**Expected Output for Admin:**
```
username  | email              | is_admin
----------|--------------------|---------
john      | john@example.com   | 1
```

**In PHP Code:**
```php
if (isset($_SESSION['admin']) && $_SESSION['admin']) {
    echo "User is admin";
} else {
    echo "User is not admin";
}
```

## Access Admin Dashboard

Navigate to: `http://yoursite.com/admin`

**Requirements:**
- User must be logged in
- User must have `is_admin = 1` in database
- Admin middleware will grant access automatically

## Create New Admin Routes

### 1. Add Route (routes.php)
```php
$router->get("/admin/users", "admin/users.php")->only("admin");
```

### 2. Create Controller (Http/controllers/admin/users.php)
```php
<?php
use Core\App;
use Core\Database;

$users = App::resolve(Database::class)->query("SELECT * FROM login")->get();
view('admin/users', ['users' => $users]);
```

### 3. Create View (views/admin/users.view.php)
```php
<?php include 'partials/header.php'; ?>
<h1>Users Management</h1>
<!-- Your admin UI here -->
<?php include 'partials/footer.php'; ?>
```

## Common Commands

| Command | Effect |
|---------|--------|
| `php cli/promote-admin.php 1` | Make user ID 1 an admin |
| `php cli/promote-admin.php user@test.com` | Make this user an admin |
| `php cli/promote-admin.php 1 --remove` | Remove admin from user ID 1 |

## Verify Admin Setup

```bash
# 1. Check migration was applied
mysql -u root school_project -e "DESC login" | grep is_admin

# 2. Check user is admin
mysql -u root school_project -e "SELECT id, username, is_admin FROM login LIMIT 5"

# 3. Log in as admin and visit /admin
# Should see admin dashboard
```

## Troubleshooting Commands

```bash
# Test CLI script works
php cli/promote-admin.php

# Should output:
# Usage: php cli/promote-admin.php <user_id_or_email> [--remove]
# Examples:
#   php cli/promote-admin.php 1
#   php cli/promote-admin.php user@example.com
#   php cli/promote-admin.php 1 --remove

# List all users with admin status
mysql -u root school_project -e "SELECT id, username, email, is_admin FROM login"

# Check specific user
mysql -u root school_project -e "SELECT * FROM login WHERE id = 1\G"
```

## Security Checklist

- [ ] Database migration applied (`is_admin` column exists)
- [ ] Promoted at least one admin user via CLI
- [ ] Admin user can access `/admin` route
- [ ] Non-admin users are redirected from `/admin`
- [ ] CLI script only works from command line (not web)
- [ ] Admin middleware is applied to all admin routes
- [ ] Regular admins removed after leaving project
- [ ] Audit trail of admin actions (implement if needed)

## Need Help?

See:
- **ADMIN_SETUP.md** - Complete setup guide
- **ADMIN_IMPLEMENTATION.md** - Technical details
- **Core/Middleware/Admin.php** - Admin middleware code
- **cli/promote-admin.php** - Admin promotion CLI tool
