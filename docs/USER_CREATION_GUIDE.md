# 👤 Admin User Creation Guide - ICOP Website

## Overview

This guide provides step-by-step instructions for creating secure admin users for the ICOP website admin panel. There are **three methods** available, choose the one that suits your needs.

---

## 📋 Prerequisites

Before creating users, ensure:
- ✅ Database is installed and running
- ✅ Security schema is installed
- ✅ XAMPP/Apache and MySQL are running
- ✅ You have access to the database

---

## 🎯 Method 1: Using the CLI Script (Recommended)

This is the **easiest and most secure** method.

### Step 1: Open Command Prompt

Press `Win + R`, type `cmd`, and press Enter.

### Step 2: Navigate to Backend Directory

```bash
cd c:\xampp\htdocs\In-ICOP\backend
```

### Step 3: Run the User Creation Script

```bash
c:\xampp\php\php.exe create_secure_admin.php
```

### Step 4: Follow the Interactive Prompts

The script will ask you for the following information:

#### 4.1 Enter Username
```
Enter username: admin_john
```
- Choose a unique username
- No spaces allowed
- Only letters, numbers, and underscores

#### 4.2 Enter Full Name
```
Enter full name: John Doe
```
- Your complete name
- Will be displayed in the admin panel

#### 4.3 Enter Email
```
Enter email: john@icop.edu.in
```
- Valid email address required
- Used for password reset and notifications

#### 4.4 Create Password

The script will show password requirements:
```
Password Requirements:
- Minimum 12 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character

Enter password: ************
```

**Example of Strong Passwords:**
- ✅ `MySecure@Pass2026`
- ✅ `Admin#2026!Strong`
- ✅ `P@ssw0rd!College`
- ❌ `admin123` (too weak)
- ❌ `password` (too weak)

#### 4.5 Confirm Password
```
Confirm password: ************
```
- Re-enter the same password

#### 4.6 Enable Two-Factor Authentication
```
Enable Two-Factor Authentication? (recommended) [Y/n]: Y
```
- Press `Y` for Yes (recommended)
- Press `n` for No

### Step 5: Save Backup Codes

If you enabled 2FA, the script will generate backup codes:

```
╔════════════════════════════════════════════════════════════╗
║  IMPORTANT: Save these backup codes in a secure location  ║
╚════════════════════════════════════════════════════════════╝

Backup Codes:
  ABCD-1234
  EFGH-5678
  IJKL-9012
  MNOP-3456
  QRST-7890
  UVWX-1234
  YZAB-5678
  CDEF-9012
  GHIJ-3456
  KLMN-7890

Backup codes saved to: backup_codes_admin_john_2026-02-02_185500.txt
```

**IMPORTANT:** 
- 📝 Save these codes in a secure location
- 💾 Download the generated `.txt` file
- 🔒 Store in password manager or safe place
- ⚠️ Each code can only be used once

### Step 6: Success!

```
╔════════════════════════════════════════════════════════════╗
║  SUCCESS! Admin user created successfully                 ║
╚════════════════════════════════════════════════════════════╝

Username: admin_john
Email: john@icop.edu.in
2FA Enabled: Yes

You can now login at: http://localhost/In-ICOP/admin/
```

---

## 🗄️ Method 2: Using phpMyAdmin (Manual)

Use this method if you prefer a GUI interface.

### Step 1: Open phpMyAdmin

Navigate to: `http://localhost/phpmyadmin`

### Step 2: Select Database

Click on `icop_website` database in the left sidebar.

### Step 3: Open admin_users Table

Click on the `admin_users` table.

### Step 4: Click Insert Tab

Click the **"Insert"** tab at the top.

### Step 5: Fill in User Details

| Field | Value | Example |
|-------|-------|---------|
| `id` | Leave empty (auto-increment) | - |
| `username` | Your username | `admin_sarah` |
| `password` | **See Step 6** | - |
| `full_name` | Full name | `Sarah Johnson` |
| `email` | Email address | `sarah@icop.edu.in` |
| `role` | `admin` | `admin` |
| `is_active` | `1` | `1` |
| `last_password_change` | `NOW()` | Use function |
| `two_factor_enabled` | `0` or `1` | `0` |

### Step 6: Generate Password Hash

**IMPORTANT:** Never store plain text passwords!

#### Option A: Use Online Hash Generator
1. Go to: `https://bcrypt-generator.com/`
2. Enter your password
3. Set rounds to `10`
4. Click "Generate Hash"
5. Copy the hash (starts with `$2y$10$`)
6. Paste in the `password` field

#### Option B: Use PHP Command
```bash
c:\xampp\php\php.exe -r "echo password_hash('YourPassword123!', PASSWORD_DEFAULT);"
```

### Step 7: Click Go

Click the **"Go"** button at the bottom to insert the user.

### Step 8: Verify User Created

Click **"Browse"** tab to see your new user in the list.

---

## 💻 Method 3: Using MySQL Command Line

For advanced users who prefer SQL commands.

### Step 1: Open MySQL Command Line

```bash
cd c:\xampp\mysql\bin
mysql -u root -p icop_website
```

### Step 2: Generate Password Hash

First, generate a password hash using PHP:

```bash
c:\xampp\php\php.exe -r "echo password_hash('YourStrongPassword123!', PASSWORD_DEFAULT);"
```

Copy the output hash.

### Step 3: Insert User Record

```sql
INSERT INTO admin_users 
(username, password, full_name, email, role, is_active, last_password_change) 
VALUES 
('admin_mike', 
 '$2y$10$b6XlRF0EQHW2vmAk02UcOefbI6oKT84ywvPWuKFz.yklxNbjoGlae', 
 'Mike Wilson', 
 'mike@icop.edu.in', 
 'admin', 
 1, 
 NOW());
```

**Replace:**
- `admin_mike` with your username
- The password hash with your generated hash
- `Mike Wilson` with the full name
- `mike@icop.edu.in` with the email

### Step 4: Verify User Created

```sql
SELECT id, username, full_name, email, is_active 
FROM admin_users 
WHERE username = 'admin_mike';
```

### Step 5: Exit MySQL

```sql
EXIT;
```

---

## 🔐 Setting Up 2FA After User Creation

If you didn't enable 2FA during creation, you can enable it later.

### Step 1: Login to Admin Panel

Navigate to: `http://localhost/In-ICOP/admin/`

### Step 2: Go to Security Settings

Click on **"Security Settings"** in the admin menu.

### Step 3: Enable 2FA

1. Find the **"Two-Factor Authentication"** section
2. Click **"Enable 2FA"** button
3. You'll see a QR code

### Step 4: Scan QR Code

1. Open your authenticator app:
   - Google Authenticator
   - Microsoft Authenticator
   - Authy
   - 1Password
   - LastPass Authenticator

2. Scan the QR code displayed

3. The app will show a 6-digit code

### Step 5: Enter Verification Code

1. Enter the 6-digit code from your app
2. Click **"Verify and Enable 2FA"**

### Step 6: Save Backup Codes

**CRITICAL:** Download and save your backup codes!

- Click **"Download Backup Codes"**
- Save the file in a secure location
- Store codes in password manager
- Print and keep in safe place

---

## 🧪 Testing Your New User

### Test 1: Login

1. Go to: `http://localhost/In-ICOP/admin/`
2. Enter username and password
3. If 2FA is enabled, enter the code
4. You should see the dashboard

### Test 2: Change Password

1. Go to **Security Settings**
2. Click **"Change Password"**
3. Enter current password
4. Enter new password (meeting requirements)
5. Confirm new password
6. Click **"Change Password"**

### Test 3: View Active Sessions

1. Go to **Security Settings**
2. Scroll to **"Active Sessions"**
3. You should see your current session

---

## 🔧 Troubleshooting

### Problem: "Username already exists"

**Solution:** Choose a different username or delete the existing user first.

```sql
DELETE FROM admin_users WHERE username = 'duplicate_username';
```

### Problem: "Invalid password" when trying to login

**Solution:** 
1. Ensure password hash was generated correctly
2. Reset password using phpMyAdmin
3. Generate new hash and update:

```sql
UPDATE admin_users 
SET password = '$2y$10$NEW_HASH_HERE' 
WHERE username = 'your_username';
```

### Problem: "Account is locked"

**Solution:** Wait 15 minutes or manually unlock:

```sql
UPDATE admin_users 
SET failed_login_attempts = 0, 
    locked_until = NULL 
WHERE username = 'your_username';
```

### Problem: "2FA code not working"

**Solution:**
1. Check your device time is synchronized
2. Try the next code (codes change every 30 seconds)
3. Use a backup code instead
4. Disable and re-enable 2FA

### Problem: Lost 2FA device

**Solution:** Use a backup code to login, then:
1. Go to Security Settings
2. Disable 2FA
3. Set up 2FA again with new device

---

## 📊 User Management Best Practices

### ✅ Do's

- ✅ Use strong, unique passwords (12+ characters)
- ✅ Enable 2FA for all admin accounts
- ✅ Save backup codes securely
- ✅ Change passwords every 90 days
- ✅ Use unique usernames (not "admin")
- ✅ Deactivate users instead of deleting
- ✅ Monitor security audit logs
- ✅ Review active sessions regularly

### ❌ Don'ts

- ❌ Share admin credentials
- ❌ Use simple passwords (admin123, password)
- ❌ Reuse old passwords
- ❌ Store passwords in plain text
- ❌ Disable 2FA on production
- ❌ Use same password for multiple accounts
- ❌ Leave sessions active on public computers

---

## 🔒 Security Checklist

After creating a new admin user:

- [ ] Password meets all requirements (12+ chars, mixed case, numbers, special)
- [ ] 2FA is enabled
- [ ] Backup codes are saved securely
- [ ] Test login successful
- [ ] User can access admin dashboard
- [ ] User can change their own password
- [ ] Email is valid and accessible
- [ ] User role is set correctly
- [ ] Account is active (`is_active = 1`)
- [ ] Security audit log shows user creation

---

## 📞 Need Help?

If you encounter issues:

1. Check the **[SECURITY_README.md](file:///c:/xampp/htdocs/In-ICOP/SECURITY_README.md)** for detailed security documentation
2. Review the **[walkthrough.md](file:///C:/Users/pc/.gemini/antigravity/brain/ef970606-8a1f-4d55-94fa-92de013b8f08/walkthrough.md)** for implementation details
3. Check the `security_audit_log` table for error messages
4. Verify database tables are installed correctly

---

## 📝 Quick Reference

### Default Admin (Change Password!)
- **Username:** `admin`
- **Password:** `admin123`
- **⚠️ CHANGE IMMEDIATELY AFTER FIRST LOGIN!**

### Password Requirements
- Minimum 12 characters
- Uppercase + Lowercase
- Numbers + Special characters
- Not a common password
- Not in password history

### CLI Command
```bash
c:\xampp\php\php.exe backend\create_secure_admin.php
```

### Password Hash Generator
```bash
c:\xampp\php\php.exe -r "echo password_hash('YourPassword', PASSWORD_DEFAULT);"
```

---

**Last Updated:** February 2, 2026  
**Version:** 2.0 with Security  
**For:** ICOP Website Admin Panel
