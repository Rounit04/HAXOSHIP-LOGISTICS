# How to Reset MySQL Root Password on Windows

## Method 1: Try Empty Password (XAMPP/WAMP Default)

**If you're using XAMPP or WAMP, the root password is often empty by default.**

### Quick Test:
1. Open your `.env` file
2. Set `DB_PASSWORD=` (empty, no password)
3. Save and test: `http://127.0.0.1:8000/check-migration-status`

If this works, you're done! 🎉

---

## Method 2: Reset Password Using Skip Grant Tables (Works Always)

### Step 1: Stop MySQL Service

**For XAMPP:**
1. Open XAMPP Control Panel
2. Click "Stop" next to MySQL

**For WAMP:**
1. Right-click WAMP icon in system tray
2. Select "Stop Service" → "MySQL"

**For Standalone MySQL:**
1. Press `Win + R`, type `services.msc`, press Enter
2. Find "MySQL" service, right-click → "Stop"

### Step 2: Create MySQL Reset File

1. Open Notepad
2. Paste this command (replace `YourNewPassword123` with your desired password):
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'YourNewPassword123';
   FLUSH PRIVILEGES;
   ```
3. Save as: `C:\mysql-init.txt` (Save as type: "All Files")

**Important:** Replace `YourNewPassword123` with your actual desired password!

### Step 3: Start MySQL with Reset File

**For XAMPP:**
1. Open Command Prompt as Administrator
2. Navigate to MySQL bin directory (usually):
   ```cmd
   cd C:\xampp\mysql\bin
   ```
3. Run:
   ```cmd
   mysqld --init-file=C:\mysql-init.txt --console
   ```
4. Wait until you see "ready for connections"
5. Press `Ctrl+C` to stop it

**For WAMP:**
1. Open Command Prompt as Administrator
2. Navigate to MySQL bin directory (usually):
   ```cmd
   cd C:\wamp64\bin\mysql\mysql8.0.xx\bin
   ```
   (Replace `mysql8.0.xx` with your MySQL version)
3. Run:
   ```cmd
   mysqld --init-file=C:\mysql-init.txt --console
   ```
4. Wait until you see "ready for connections"
5. Press `Ctrl+C` to stop it

**For Standalone MySQL:**
1. Open Command Prompt as Administrator
2. Navigate to MySQL bin directory (usually):
   ```cmd
   cd "C:\Program Files\MySQL\MySQL Server 8.0\bin"
   ```
3. Run:
   ```cmd
   mysqld --init-file=C:\mysql-init.txt --console
   ```
4. Wait until you see "ready for connections"
5. Press `Ctrl+C` to stop it

### Step 4: Delete the Reset File (Important for Security!)

```cmd
del C:\mysql-init.txt
```

### Step 5: Start MySQL Normally

**For XAMPP/WAMP:**
- Start MySQL from Control Panel

**For Standalone MySQL:**
- Start MySQL service from Services manager

### Step 6: Update Your .env File

1. Open `.env` file
2. Update the password:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=haxo_shipping
   DB_USERNAME=root
   DB_PASSWORD=YourNewPassword123
   ```
   (Use the password you set in Step 2)

3. Save the file

### Step 7: Clear Config Cache

Visit: `http://127.0.0.1:8000/clear-config-cache`

Or run:
```cmd
php artisan config:clear
```

### Step 8: Test Connection

Visit: `http://127.0.0.1:8000/check-migration-status`

It should now show `connected` status! ✅

---

## Method 3: Reset via phpMyAdmin (If You Can Access It)

If you can access phpMyAdmin without password:

1. Open phpMyAdmin: `http://127.0.0.1/phpmyadmin`
2. Click on "User accounts" tab
3. Find `root@localhost`
4. Click "Edit privileges"
5. Scroll down and click "Change password"
6. Enter new password and confirm
7. Click "Go"
8. Update your `.env` file with the new password

---

## Method 4: Use MySQL Workbench (If Installed)

1. Open MySQL Workbench
2. Try to connect with current credentials
3. If it fails, you might need to reset using Method 2 first
4. Once connected, you can change password from "Server" → "Users and Privileges"

---

## Quick Summary for XAMPP (Easiest)

**Most likely, your XAMPP MySQL root password is just empty:**

1. Open `.env` file
2. Set: `DB_PASSWORD=`
3. Save file
4. Run: `php artisan config:clear`
5. Test: Visit `http://127.0.0.1:8000/check-migration-status`

**If empty password doesn't work, then use Method 2 above.**

---

## Troubleshooting

### "Access Denied" Still Appearing?
- Make sure MySQL service is restarted after reset
- Double-check `.env` file has correct password
- Clear config cache: `php artisan config:clear`
- Try restarting your web server

### Can't Find MySQL bin Directory?
- Search your computer for `mysqld.exe`
- Common locations:
  - `C:\xampp\mysql\bin\`
  - `C:\wamp64\bin\mysql\mysql*\bin\`
  - `C:\Program Files\MySQL\MySQL Server *\bin\`

### Still Not Working?
- Make sure you're running Command Prompt as Administrator
- Check if MySQL service is running
- Try using `localhost` instead of `127.0.0.1` in `.env`
- Verify MySQL port is 3306

---

## After Successfully Resetting

Once your password is reset and working:

1. **Update `.env` file** with new password
2. **Clear config cache**: `php artisan config:clear`
3. **Run migrations**: `http://127.0.0.1:8000/run-migrations`
4. **Verify**: Check phpMyAdmin to see all tables created

Good luck! 🚀

