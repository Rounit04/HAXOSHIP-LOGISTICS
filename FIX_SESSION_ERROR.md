# Fix Session Error - "could not find driver"

## Problem
The error occurs because:
1. Your web server PHP doesn't have MySQL extension enabled
2. Laravel is configured to use "database" session driver
3. The sessions table needs MySQL connection which isn't available

## Solution: Change Session Driver to File

### Option 1: Quick Fix - Use File Sessions (Recommended)

Edit your `.env` file and change:

```env
SESSION_DRIVER=database
```

**To:**

```env
SESSION_DRIVER=file
```

Then clear config cache:
```bash
php artisan config:clear
```

This will use file-based sessions instead of database sessions.

---

### Option 2: Enable MySQL for Web Server PHP

If you want to keep using database sessions, you need to enable MySQL extension for your web server PHP (not just CLI PHP).

**For XAMPP/WAMP:**
1. Find your web server PHP installation (usually different from CLI PHP)
2. Open `php.ini` for web server PHP
3. Enable `extension=pdo_mysql` and `extension=mysqli`
4. Restart Apache/web server

**For built-in PHP server:**
The built-in PHP server uses the same PHP as CLI, so it should work. Try restarting the server.

---

## Quick Fix Steps:

1. **Open `.env` file** in project root
2. **Find this line:**
   ```
   SESSION_DRIVER=database
   ```
3. **Change it to:**
   ```
   SESSION_DRIVER=file
   ```
4. **Save the file**
5. **Run this command:**
   ```bash
   php artisan config:clear
   ```
6. **Restart your web server** (if using built-in server, restart it)

---

## After Fixing:

Refresh your browser at `http://127.0.0.1:8000` - the error should be gone!






