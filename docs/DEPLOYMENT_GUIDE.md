# ICOP Website Deployment Guide

This guide will help you deploy the Indira College of Pharmacy website to a cPanel hosting environment.

## 1. File Upload
1.  Compress all files in the project folder into a ZIP file (excluding `.git`, `node_modules`, etc.).
2.  Log in to your cPanel File Manager.
3.  Upload the ZIP file to `public_html` (or your subdomain folder).
4.  Extract the ZIP file.

## 2. Database Setup
1.  Log in to cPanel and go to **MySQL Database Wizard**.
2.  Create a new database (e.g., `icop_website`).
3.  Create a new database user and generate a strong password.
4.  Add the user to the database with **ALL PRIVILEGES**.
5.  Go to **phpMyAdmin** in cPanel.
6.  Select your new database.
7.  Click **Import** tab.
8.  Upload the `database.sql` file found in the root directory.
9.  Click **Go** to import the tables and sample data.

## 3. Configuration
1.  Go to `backend/` directory in File Manager.
2.  Rename `config.php` to `config.local.php` (as a backup).
3.  Rename `config.production.php` to `config.php`.
4.  Edit the new `config.php` file:
    *   Update `DB_NAME`, `DB_USER`, and `DB_PASS` with the values from Step 2.
    *   Update `SITE_URL` to your actual domain (e.g., `https://icop.edu.in`).
    *   Set `DEBUG_MODE` to `false` if it isn't already.

## 4. Permissions
Ensure the `uploads/` directory and its subdirectories (`banners`, `gallery`, `notices`, `results`, `syllabus`, etc.) have write permissions (usually 755 or 777 depending on server config).
If they don't exist, the system will try to create them, but it's better to create them manually if permission errors occur.

## 5. Security
*   Change the default admin password immediately after logging in.
*   Ensure the `.htaccess` file is present in the root directory to protect sensitive files.

## 6. Testing
1.  Visit your website URL.
2.  Check if the homepage loads without errors.
3.  Go to `/admin` and log in with default credentials (`admin` / `admin123`).
4.  Test uploading a notice or banner to ensure file permissions are correct.

---
**Deployment Ready!**
