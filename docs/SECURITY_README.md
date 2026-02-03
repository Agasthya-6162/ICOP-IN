# ICOP Admin - Secure Authentication System

## 🔐 Security Features Implemented

Your ICOP admin panel now includes enterprise-grade security features:

✅ **Two-Factor Authentication (2FA/TOTP)**
✅ **Strong Password Policy** (12+ characters with complexity requirements)
✅ **Password History** (prevents reuse of last 5 passwords)
✅ **Account Lockout** (progressive delays after failed login attempts)
✅ **Session Management** (timeout, device tracking, multi-session support)
✅ **Brute Force Protection** (IP-based rate limiting)
✅ **Security Audit Logging** (all authentication events tracked)
✅ **Secure Password Reset** (time-limited tokens)
✅ **Backup Codes** (for 2FA recovery)
✅ **Device Fingerprinting** (detect suspicious logins)

---

## 📋 Installation Instructions

### Step 1: Install Security Schema

Run the installation script to create the necessary database tables:

```bash
cd c:\xampp\htdocs\In-ICOP\backend
php install_security.php
```

This will create the following tables:
- `two_factor_auth` - Stores 2FA secrets and backup codes
- `password_history` - Tracks password history
- `active_sessions` - Manages user sessions
- `security_settings` - Security configuration
- `security_audit_log` - Security event logging
- `trusted_devices` - Remember me functionality

### Step 2: Create a Secure Admin User

Create a new admin user with a strong password:

```bash
php create_secure_admin.php
```

Follow the prompts to:
1. Enter username
2. Enter full name and email
3. Create a strong password (minimum 12 characters)
4. Optionally enable 2FA

**Important:** Save the backup codes in a secure location!

### Step 3: Login and Setup 2FA

1. Navigate to `http://localhost/In-ICOP/admin/`
2. Login with your new credentials
3. If 2FA is enabled, you'll be prompted for a code
4. Go to **Security Settings** to manage your account

---

## 🔑 Password Requirements

All passwords must meet the following criteria:

- **Minimum length:** 12 characters
- **Uppercase letters:** At least one (A-Z)
- **Lowercase letters:** At least one (a-z)
- **Numbers:** At least one (0-9)
- **Special characters:** At least one (!@#$%^&*)
- **Not a common password:** System blocks commonly used passwords
- **Not in history:** Cannot reuse last 5 passwords

---

## 📱 Two-Factor Authentication Setup

### Using Google Authenticator (or similar app):

1. Go to **Security Settings** → **Two-Factor Authentication**
2. Click **Enable 2FA**
3. Scan the QR code with your authenticator app
4. Enter the 6-digit code to verify
5. **Save your backup codes** in a secure location

### Supported Authenticator Apps:
- Google Authenticator
- Microsoft Authenticator
- Authy
- 1Password
- LastPass Authenticator

### Backup Codes:
- You receive 10 backup codes when enabling 2FA
- Each code can only be used once
- Use them if you lose access to your authenticator app
- Download and store them securely

---

## 🛡️ Security Settings

Access security settings at: `/admin/security-settings.php`

### Features Available:

**Account Information**
- View username, email, and last login
- See account status

**Two-Factor Authentication**
- Enable/disable 2FA
- View remaining backup codes
- Generate new backup codes

**Password Management**
- Change password
- View last password change date
- Password strength validation

**Active Sessions**
- View all active sessions
- See device and IP information
- Logout specific sessions
- Logout all other devices

---

## 🔒 Login Security

### Account Lockout Policy:
- **Maximum attempts:** 5 failed login attempts
- **Lockout duration:** 15 minutes
- **Automatic unlock:** After lockout period expires

### Session Management:
- **Session timeout:** 30 minutes of inactivity
- **Remember me:** 30 days (optional)
- **Device fingerprinting:** Detects session hijacking attempts
- **Multi-session support:** Login from multiple devices

---

## 📊 Security Audit Log

All security events are logged in the `security_audit_log` table:

- Login attempts (success/failure)
- 2FA verification attempts
- Password changes
- Account lockouts
- Session creation/destruction
- Security settings changes

Access logs through the database or create an admin interface to view them.

---

## 🚨 Troubleshooting

### "Account is locked" message:
- Wait 15 minutes for automatic unlock
- Contact system administrator if urgent

### Lost authenticator app:
- Use a backup code to login
- Disable 2FA from Security Settings
- Re-enable 2FA with a new device

### Forgot password:
- Use the "Forgot Password" link on login page
- Check email for reset link
- Link expires after 1 hour

### Session expired:
- Sessions expire after 30 minutes of inactivity
- Use "Remember me" to extend session to 30 days
- Login again to create a new session

---

## 🔧 Configuration

Security settings can be modified in the `security_settings` table:

```sql
-- View current settings
SELECT * FROM security_settings;

-- Update a setting (example: change session timeout to 1 hour)
UPDATE security_settings 
SET setting_value = '3600' 
WHERE setting_key = 'session_timeout';
```

### Available Settings:

| Setting | Default | Description |
|---------|---------|-------------|
| `max_login_attempts` | 5 | Failed attempts before lockout |
| `lockout_duration` | 900 | Lockout duration in seconds (15 min) |
| `session_timeout` | 1800 | Session timeout in seconds (30 min) |
| `password_min_length` | 12 | Minimum password length |
| `password_expiry_days` | 90 | Password expiration (0 = never) |
| `password_history_count` | 5 | Number of passwords to remember |
| `session_remember_days` | 30 | Remember me duration in days |

---

## 🧹 Maintenance

### Cleanup Expired Sessions:

Run the cleanup procedure daily via cron:

```bash
# Add to crontab (Linux/Mac)
0 2 * * * mysql -u root -p icop_website -e "CALL cleanup_expired_sessions();"
```

Or run manually:

```sql
CALL cleanup_expired_sessions();
```

This will:
- Delete expired sessions
- Remove expired trusted devices
- Clean old login attempts (>30 days)
- Remove old audit logs (>180 days)
- Delete used password reset tokens (>7 days)

---

## 📝 API Endpoints

The authentication API is available at `/backend/api/auth_api.php`:

### Available Actions:

- `verify-2fa` - Verify 2FA code during login
- `setup-2fa` - Initialize 2FA for user
- `activate-2fa` - Activate 2FA after verification
- `disable-2fa` - Disable 2FA (requires password)
- `change-password` - Change user password
- `get-sessions` - Get active sessions
- `logout-session` - Logout specific session
- `logout-all-devices` - Logout all devices
- `get-2fa-status` - Get 2FA status

---

## 🎯 Best Practices

1. **Enable 2FA** for all admin accounts
2. **Use strong passwords** - minimum 12 characters
3. **Change passwords regularly** - every 90 days
4. **Monitor security logs** - check for suspicious activity
5. **Keep backup codes safe** - store in password manager
6. **Logout when done** - don't leave sessions active
7. **Use "Remember me" carefully** - only on trusted devices
8. **Review active sessions** - logout unknown devices

---

## 🆘 Support

For issues or questions:

1. Check the troubleshooting section above
2. Review security audit logs
3. Contact your system administrator
4. Check database for error messages

---

## 📄 Files Created

### Database:
- `/backend/database/security_schema.sql` - Security tables schema

### Backend Classes:
- `/backend/classes/SecurityManager.php` - Password & security validation
- `/backend/classes/TwoFactorAuth.php` - 2FA implementation
- `/backend/classes/SessionManager.php` - Session management

### API:
- `/backend/api/auth_api.php` - Authentication endpoints

### Admin Pages:
- `/admin/index.php` - Enhanced login with 2FA
- `/admin/setup-2fa.php` - 2FA setup interface
- `/admin/security-settings.php` - Security dashboard
- `/admin/logout.php` - Enhanced logout

### Utilities:
- `/backend/install_security.php` - Schema installation script
- `/backend/create_secure_admin.php` - Admin user creation script

---

## ⚠️ Important Notes

1. **Backup your database** before running the installation script
2. **Save backup codes** when enabling 2FA
3. **Test the system** before deploying to production
4. **Update admin passwords** from the default admin/admin123
5. **Enable HTTPS** in production for secure communication
6. **Set DEBUG_MODE to false** in production

---

## 🎉 You're All Set!

Your ICOP admin panel is now secured with enterprise-grade authentication. Login and explore the new security features!

**Default admin credentials (if not changed):**
- Username: `admin`
- Password: `admin123`

**⚠️ IMPORTANT:** Change the default password immediately after first login!
