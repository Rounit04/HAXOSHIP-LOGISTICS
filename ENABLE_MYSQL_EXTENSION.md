# Enable MySQL Extension in PHP

## Issue
You're getting the error: "could not find driver" when trying to connect to MySQL.

This means PHP's MySQL extension (PDO MySQL) is not enabled.

## Solution: Enable PDO MySQL Extension

### Step 1: Find your php.ini file

Your PHP configuration file is located at:
```
C:\Users\rouni\scoop\apps\php\current\cli\php.ini
```

### Step 2: Open php.ini file

1. Open File Explorer
2. Navigate to: `C:\Users\rouni\scoop\apps\php\current\cli\`
3. Open `php.ini` file with a text editor (Notepad++, VS Code, etc.)
4. Make sure you have administrator privileges

### Step 3: Enable MySQL extensions

Find these lines in your `php.ini` file (use Ctrl+F to search):

Look for lines that start with `;extension=`

**Enable these extensions by removing the semicolon (;) at the beginning:**

```ini
;extension=pdo_mysql
;extension=mysqli
```

**Change them to:**

```ini
extension=pdo_mysql
extension=mysqli
```

**OR if you see these lines (with different names):**

```ini
;extension=php_pdo_mysql.dll
;extension=php_mysqli.dll
```

**Change them to:**

```ini
extension=php_pdo_mysql.dll
extension=php_mysqli.dll
```

### Step 4: Check if extension files exist

Make sure these files exist in your PHP extension directory:

1. Navigate to: `C:\Users\rouni\scoop\apps\php\current\ext\`
2. Look for these files:
   - `php_pdo_mysql.dll` (or `pdo_mysql.dll`)
   - `php_mysqli.dll` (or `mysqli.dll`)

If these files don't exist, you may need to install them or use a different PHP installation.

### Step 5: Save and Restart

1. Save the `php.ini` file
2. Restart your terminal/command prompt
3. Test by running: `php -m | findstr -i pdo_mysql`

### Step 6: Verify Extension is Loaded

Run this command:
```bash
php -m
```

You should see `pdo_mysql` and `mysqli` in the list.

### Step 7: Test Database Connection

After enabling the extension, try again:
```bash
php artisan migrate:status
```

## Alternative: Check if Extension Files Exist

If the extension files don't exist, you may need to:

1. **Download PHP with MySQL support** (if using Scoop)
   ```bash
   scoop install mysql
   ```

2. **Or use XAMPP/WAMP** which comes with MySQL extensions pre-installed

## Quick Fix Commands

If you're using Scoop package manager:

```bash
# Check if MySQL extension is available
php -m | findstr mysql

# If not found, you may need to install MySQL extension
# Or use a different PHP installation that has MySQL support
```

## Still Having Issues?

If you're still having trouble:

1. **Use XAMPP/WAMP** which includes PHP with MySQL support
2. **Or install MySQL extension manually** for your PHP version
3. **Check PHP version compatibility** - Make sure your PHP version supports MySQL

## Verify After Fixing

Run these commands to verify:

```bash
# Check if extension is loaded
php -m | findstr pdo_mysql

# Check PHP info
php -i | findstr pdo_mysql

# Test connection
php artisan migrate:status
```






