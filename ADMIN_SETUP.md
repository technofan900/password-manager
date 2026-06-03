# Admin Setup Guide

This guide explains how to securely promote users to admin status.

## Security Approach

The admin promotion system uses a **CLI-only command** to prevent exploitation by normal users. This means:

- ✓ Only people with direct server access can run the command
- ✓ Cannot be exploited through the web interface
- ✓ No HTTP requests can trigger admin promotion
- ✓ Cannot be automated by malicious users

## Step 1: Database Migration

First, add the `is_admin` column to the `login` table:

```bash
# Option 1: Run the SQL migration directly in phpMyAdmin or MySQL client
mysql -u username -p school_project < database/migrations/add_is_admin_to_login.sql

# Option 2: Copy and paste the SQL in phpMyAdmin > SQL tab
# See: database/migrations/add_is_admin_to_login.sql
```

## Step 2: Promote a User to Admin

Use the CLI command to promote a user:

```bash
# Promote by user ID
php cli/promote-admin.php 1

# Promote by email address
php cli/promote-admin.php user@example.com

# Remove admin status
php cli/promote-admin.php 1 --remove
```

**Important**: This command can ONLY be run from the command line with server access. It will be automatically blocked if accessed via HTTP/web browser.

## Step 3: Verify Admin Access

1. Log in with the admin user's credentials
2. Navigate to `/admin` - you should have access to admin panel
3. If you get a 403 error, the promotion didn't work - check the database column exists

## How It Works

### Database
- Adds `is_admin` boolean column to `login` table (defaults to 0)
- Existing users remain non-admin

### Authentication
- `Authenticator::login()` checks `is_admin` status
- Sets `$_SESSION['admin']` only for admin users
- Normal users cannot modify this session value

### Admin Middleware
- `Admin` middleware blocks non-admin access to protected routes
- Returns 403 if user is not admin

### CLI Tool Security
- Script only runs via command line (`php_sapi_name() === 'cli'`)
- Rejects web requests immediately
- Requires direct server access to execute
- No URL or HTTP mechanism can trigger it

## Admin-Only Routes

Add routes to `routes.php` that should be admin-only:

```php
$router->get('/admin', 'admin/index');
```

Then add Admin middleware:

```php
$router->middleware('admin')->group([
    'get' => ['/admin' => 'admin/index']
]);
```

## If You Need to Add More Admin Features

### Create Admin-Protected Routes
```php
// In routes.php
$router->middleware('admin')->group([
    'get' => [
        '/admin' => 'admin/index',
        '/admin/users' => 'admin/users',
        '/admin/settings' => 'admin/settings'
    ]
]);
```

### Check Admin Status in Code
```php
// In any controller
if (isset($_SESSION['admin']) && $_SESSION['admin']) {
    // User is admin
} else {
    // Not admin
}
```

## Troubleshooting

**Command says "Script can only be run from command line"**
- You're trying to access the script via HTTP
- Use terminal/command line instead

**User not found**
- Check the exact ID or email address
- Verify user exists in database

**Admin still can't access `/admin`**
- Verify migration was applied: `DESC login` in MySQL
- Check `is_admin` column exists and is set to 1
- Clear browser cookies/session
- Ensure middleware is applied to the route

## Security Best Practices

1. **Never** add a web form to promote admins
2. **Never** expose the CLI command through a web endpoint
3. **Always** verify user identities before promoting
4. **Log** admin promotions for audit trail
5. **Limit** CLI script access to authorized personnel only
