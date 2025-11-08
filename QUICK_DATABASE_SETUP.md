# Quick Database Setup for MySQL/MariaDB

## Step 1: Update .env File

Open your `.env` file in the root directory and find these lines:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

**Change them to:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Step 2: Example Configuration

Here's a complete example for MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haxo_shipping
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

## Step 3: Create Database

Before connecting, create your database:

**Using MySQL Command Line:**
```sql
CREATE DATABASE haxo_shipping CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Or using phpMyAdmin:**
1. Open phpMyAdmin
2. Click "New" 
3. Enter database name: `haxo_shipping`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

## Step 4: Test Connection

Run this command to test:

```bash
php artisan migrate:status
```

If successful, you'll see a list of migrations.

## Step 5: Run Migrations

Create all database tables:

```bash
php artisan migrate
```

## Step 6: Clear Config Cache

Clear Laravel's config cache:

```bash
php artisan config:clear
```

## Troubleshooting

### "Access denied for user"
- Check username and password in `.env`

### "Connection refused" or "Can't connect to MySQL server"
- Make sure MySQL/MariaDB is running
- Check if `DB_HOST` is correct (try `127.0.0.1` instead of `localhost`)
- Verify the port (default is `3306`)

### "Unknown database"
- Create the database first (see Step 3)

### "PDOException: could not find driver"
- Install PHP MySQL extension
- For Windows: Enable `extension=pdo_mysql` in `php.ini`
- For Linux: `sudo apt-get install php-mysql`

## Quick Commands Summary

```bash
# 1. Update .env file (manually edit it)
# 2. Test connection
php artisan migrate:status

# 3. Run migrations
php artisan migrate

# 4. Clear cache
php artisan config:clear
```






