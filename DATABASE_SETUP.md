# Database Setup Guide

This guide will help you connect your Laravel application to a SQL database (MySQL/MariaDB or SQL Server).

## Prerequisites

1. **MySQL/MariaDB** or **SQL Server** installed and running
2. **Database created** (you'll need to create an empty database first)
3. **Database credentials** (username, password, host, port)

## Step 1: Configure Database Connection

### For MySQL/MariaDB

Edit your `.env` file in the root directory and add/update these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Example:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haxo_shipping
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### For SQL Server

Edit your `.env` file and add/update these lines:

```env
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Example:**
```env
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=haxo_shipping
DB_USERNAME=sa
DB_PASSWORD=your_password_here
```

## Step 2: Install Required PHP Extensions

Make sure you have the required PHP extensions installed:

### For MySQL/MariaDB:
```bash
# On Ubuntu/Debian
sudo apt-get install php-mysql

# On Windows (XAMPP/WAMP)
# Enable extension=mysqli and extension=pdo_mysql in php.ini
```

### For SQL Server:
```bash
# Install SQL Server drivers for PHP
# Follow: https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server
```

## Step 3: Create Database

### Using MySQL Command Line:
```sql
CREATE DATABASE haxo_shipping CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Using phpMyAdmin:
1. Open phpMyAdmin
2. Click "New" to create a new database
3. Enter database name: `haxo_shipping`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Using SQL Server:
```sql
CREATE DATABASE haxo_shipping;
```

## Step 4: Test Connection

Run this command to test your database connection:

```bash
php artisan migrate:status
```

If you see a list of migrations, your connection is working!

## Step 5: Run Migrations

Once connected, run all migrations to create the database tables:

```bash
php artisan migrate
```

This will create all the necessary tables in your database.

## Step 6: Verify Connection

You can verify the connection by checking if you can access the database:

```bash
php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
```

If it returns a PDO object, your connection is successful!

## Common Issues and Solutions

### Issue 1: "Access denied for user"
**Solution:** Check your database username and password in `.env` file.

### Issue 2: "SQLSTATE[HY000] [2002] No connection could be made"
**Solution:** 
- Verify MySQL/SQL Server is running
- Check the `DB_HOST` and `DB_PORT` in `.env`
- Try `127.0.0.1` instead of `localhost` for MySQL

### Issue 3: "SQLSTATE[HY000] [1049] Unknown database"
**Solution:** Create the database first using the steps in Step 3.

### Issue 4: "PDOException: could not find driver"
**Solution:** Install the required PHP extension (see Step 2).

## Additional Configuration

### For Remote Database:
If connecting to a remote database, update `DB_HOST`:
```env
DB_HOST=your_remote_server_ip
```

### For Custom Port:
If your database uses a different port:
```env
DB_PORT=3307  # Example for MySQL on port 3307
```

### For SSL Connection:
Add to `.env`:
```env
DB_SSL_CA=/path/to/ca-cert.pem
```

## Next Steps

After setting up the database:
1. Run migrations: `php artisan migrate`
2. (Optional) Seed database: `php artisan db:seed`
3. Update your AWB Upload functionality to use database models instead of session storage

## Note

Currently, the application uses session-based storage for AWB data. To fully utilize the database, you'll need to:
- Create models for AWB Upload data
- Create migrations for AWB tables
- Update controllers to use Eloquent ORM instead of session storage

Would you like help with migrating the session-based storage to database models?

