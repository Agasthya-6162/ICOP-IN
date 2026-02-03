# 🔧 Login Troubleshooting Guide

## Quick Fix: Reset Your Password

If you're getting "Invalid credentials" error, use this quick fix:

### Step 1: Open Command Prompt
```bash
cd c:\xampp\htdocs\In-ICOP\backend
```

### Step 2: Reset Password
```bash
c:\xampp\php\php.exe reset_password.php admin YourNewPassword123!
```

Replace:
- `admin` with your username
- `YourNewPassword123!` with your desired password (must be 12+ characters)

### Step 3: Try Login Again
Go to `http://localhost/In-ICOP/admin/` and login with the new password.

---

## Diagnostic Tool

To diagnose what's wrong with your login:

```bash
c:\xampp\php\php.exe test_login.php admin YourPassword
```

This will show you:
- ✓ If user exists
- ✓ If account is active
- ✓ If account is locked
- ✓ If password matches
- ✓ 2FA status

---

## Common Issues & Solutions

### Issue 1: "Invalid credentials" immediately

**Cause:** Password hash doesn't match

**Solution:**
```bash
# Reset password
c:\xampp\php\php.exe backend\reset_password.php admin NewPassword123!
```

### Issue 2: Account is locked

**Cause:** Too many failed login attempts

**Solution:** Reset password (this also unlocks the account)
```bash
c:\xampp\php\php.exe backend\reset_password.php admin NewPassword123!
```

Or manually unlock via SQL:
```sql
UPDATE admin_users 
SET failed_login_attempts = 0, locked_until = NULL 
WHERE username = 'admin';
```

### Issue 3: User not found

**Cause:** Username doesn't exist or is inactive

**Solution:** Check available users:
```bash
c:\xampp\php\php.exe backend\test_login.php
```

Or create new user:
```bash
c:\xampp\php\php.exe backend\create_secure_admin.php
```

### Issue 4: Password hash looks wrong

**Cause:** Password wasn't hashed properly during creation

**Solution:** Always use `password_hash()` function:
```php
$hash = password_hash('YourPassword123!', PASSWORD_DEFAULT);
```

The hash should:
- Start with `$2y$10$`
- Be 60 characters long
- Look like: `$2y$10$b6XlRF0EQHW2vmAk02UcOefbI6oKT84ywvPWuKFz.yklxNbjoGlae`

---

## Manual Database Fix

If scripts don't work, fix directly in database:

### Step 1: Generate Password Hash
```bash
c:\xampp\php\php.exe -r "echo password_hash('YourPassword123!', PASSWORD_DEFAULT);"
```

Copy the output hash.

### Step 2: Update Database
Open phpMyAdmin and run:
```sql
UPDATE admin_users 
SET password = '$2y$10$YOUR_HASH_HERE',
    failed_login_attempts = 0,
    locked_until = NULL,
    is_active = 1
WHERE username = 'admin';
```

---

## Verify Login Works

After fixing, test with diagnostic tool:
```bash
c:\xampp\php\php.exe backend\test_login.php admin YourPassword123!
```

You should see:
```
✓ User found
✓ User is active
✓ Account is not locked
✓ PASSWORD MATCHES!
✓ Login should work
```

---

## Still Not Working?

1. **Check Apache/MySQL are running**
   - Open XAMPP Control Panel
   - Start Apache and MySQL

2. **Check database connection**
   - Verify `backend/config.php` has correct DB credentials
   - Test connection in phpMyAdmin

3. **Check security classes are loaded**
   - Ensure `backend/classes/` folder exists
   - Files: `SecurityManager.php`, `TwoFactorAuth.php`, `SessionManager.php`

4. **Check PHP errors**
   - Look in `c:\xampp\apache\logs\error.log`
   - Enable error display in `backend/config.php`: `DEBUG_MODE = true`

5. **Clear browser cache and cookies**
   - Press `Ctrl + Shift + Delete`
   - Clear cookies for localhost

---

## Need More Help?

Run the full diagnostic:
```bash
c:\xampp\php\php.exe backend\test_login.php
```

This will show detailed information about your account and what's preventing login.
