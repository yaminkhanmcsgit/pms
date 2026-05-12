## 🚨 CRITICAL: Laravel 502 Bad Gateway Fix - Complete Guide

### **Problem:**
"Class "Facade\Ignition\IgnitionServiceProvider" not found" error on cPanel

### **Root Cause:**
Laravel Sanctum requires Ignition, but Ignition is a dev dependency that gets excluded in production, causing the error.

### **Solution Steps:**

#### **1. Download Fresh Vendor Directory**
- Download this `vendor_production.zip` file to your local machine
- This contains all production dependencies with proper autoloader

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

#### **4. Run Migration (Create this file)**
Create `public_html/admin/run_migration.php`:
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Running Sessions Migration</h2>";
try {
    $migrationPath = __DIR__ . '/database/migrations/2026_05_07_070436_create_sessions_table.php';
    if (file_exists($migrationPath)) {
        echo "✓ Migration file found<br>";
        exec("php artisan migrate --path=database/migrations/2026_05_07_070436_create_sessions_table.php 2>&1", $output, $exitCode);
        if ($exitCode === 0) {
            echo "✓ Migration completed successfully!<br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        } else {
            echo "❌ Migration failed with exit code: $exitCode<br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        }
    } else {
        echo "❌ Migration file not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
echo "<br><a href='../'>Back to Application</a>";
```

- Visit `yoursite.com/admin/run_migration.php` in browser
- Delete the file after successful migration

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
- `vendor_production.zip` → Extract in public_html/admin/
- Updated `.env` file
- Updated `.htaccess` file
- `run_migration.php` → Run once, then delete