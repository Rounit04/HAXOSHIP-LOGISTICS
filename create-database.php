<?php
/**
 * Create haxo_shipping Database
 * Run: php create-database.php
 */

echo "==========================================\n";
echo "   Creating haxo_shipping Database\n";
echo "==========================================\n\n";

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL successfully!\n\n";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'haxo_shipping'");
    $exists = $stmt->rowCount() > 0;
    
    if ($exists) {
        echo "⚠️  Database 'haxo_shipping' already exists!\n";
        echo "You can proceed to run migrations.\n\n";
    } else {
        // Create database
        echo "Creating database 'haxo_shipping'...\n";
        $pdo->exec("CREATE DATABASE haxo_shipping CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Database 'haxo_shipping' created successfully!\n\n";
    }
    
    echo "Next steps:\n";
    echo "1. Run migrations: http://127.0.0.1:8000/run-migrations\n";
    echo "2. Or via command: php artisan migrate\n";
    echo "3. Check tables: http://127.0.0.1:8000/check-migration-status\n\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "Password is still incorrect. Try:\n";
        echo "1. Check your .env file - set DB_PASSWORD= (empty)\n";
        echo "2. Or reset MySQL password following: RESET_MYSQL_PASSWORD.md\n\n";
    } elseif (strpos($e->getMessage(), 'Unknown database') === false) {
        echo "Manual steps to create database:\n";
        echo "1. Open phpMyAdmin: http://127.0.0.1/phpmyadmin\n";
        echo "2. Click 'New' to create a new database\n";
        echo "3. Enter database name: haxo_shipping\n";
        echo "4. Select collation: utf8mb4_unicode_ci\n";
        echo "5. Click 'Create'\n\n";
    }
}

