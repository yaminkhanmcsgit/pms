## 🚨 CRITICAL: Laravel 502 Bad Gateway Fix - Complete Guide

### **Problem:**
"Class "Facade\Ignition\IgnitionServiceProvider" not found" error on cPanel

### **Root Cause:**
Laravel Sanctum requires Ignition, but Ignition is a dev dependency that gets excluded in production, causing the error.

### **Solution Steps:**

#### **1. Download Fresh Vendor Directory**
- Download this `vendor_production.zip` file to your local machine
- This contains CLEAN production dependencies without Ignition errors

#### **2. Upload to cPanel**
- Go to cPanel → File Manager → public_html/admin/
- Delete the existing `vendor` folder completely
- Upload `vendor_production.zip`
- Extract it (this will create a fresh `vendor` folder)

#### **3. Update .env File**
In `public_html/admin/.env`:
```env
APP_ENV=production
SESSION_DRIVER=database
APP_DEBUG=false
```

#### **4. Clear Laravel Caches**
- Upload `clear_cache.php` to `public_html/admin/`
- Visit `yoursite.com/admin/clear_cache.php` in browser
- This manually deletes cache files (works on any server)
- Delete the file after successful execution

#### **5. Create Sessions Table**
- Upload `create_sessions_table.php` to `public_html/admin/`
- Visit `yoursite.com/admin/create_sessions_table.php` in browser
- This creates the sessions table using direct database connection
- Delete the file after successful execution

#### **Alternative: Simple Versions (No Facades)**
If facade initialization still fails, use:
- `clear_cache_simple.php` - Manual file deletion
- `create_sessions_simple.php` - Direct PDO connection

#### **5. Update .htaccess**
Ensure `public_html/admin/.htaccess` contains:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /admin

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### **6. PHP Settings**
In cPanel → MultiPHP Manager:
- Set PHP version to 8.1 for `/admin` directory
- Set memory_limit: 256M
- max_execution_time: 300

#### **7. Test**
- Visit your Laravel admin panel
- Try submitting a grievance form
- The 502 error should be resolved

### **If Still Having Issues:**
1. Check PHP error logs in cPanel
2. Ensure all files uploaded correctly
3. Verify file permissions (644 for files, 755 for directories)
4. Clear browser cache

### **Files to Upload:**
- `vendor_production.zip` → Extract in public_html/admin/ (CLEAN version without Sanctum/Ignition)
- `clear_cache.php` → Run once, then delete
- `create_sessions_table.php` → Run once, then delete
- Updated `.env` file

### **Important Notes:**
- Laravel Sanctum was removed to eliminate Ignition dependency issues
- If you need API authentication, you can implement it differently or add Sanctum back after deployment
- The application will work without Sanctum for basic web authentication
- Updated `.htaccess` file