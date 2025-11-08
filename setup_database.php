<?php
/**
 * Database Setup Helper Script
 * 
 * This script helps you configure your Laravel database connection.
 * Run: php setup_database.php
 */

echo "==========================================\n";
echo "   Laravel Database Setup Helper\n";
echo "==========================================\n\n";

// Check if .env file exists
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ .env file not found!\n";
    echo "Creating .env file from .env.example...\n";
    
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', $envPath);
        echo "✅ .env file created!\n\n";
    } else {
        echo "❌ .env.example not found. Please create .env file manually.\n";
        exit(1);
    }
}

echo "Current Database Configuration:\n";
echo "--------------------------------\n";

// Read .env file
$envContent = file_get_contents($envPath);
$lines = explode("\n", $envContent);

$dbConfig = [
    'DB_CONNECTION' => 'sqlite',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => '',
    'DB_USERNAME' => '',
    'DB_PASSWORD' => '',
];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }
    
    if (strpos($line, '=') !== false) {
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if (isset($dbConfig[$key])) {
            $dbConfig[$key] = $value;
        }
    }
}

echo "Database Connection: " . ($dbConfig['DB_CONNECTION'] ?: 'Not set') . "\n";
echo "Database Host: " . ($dbConfig['DB_HOST'] ?: 'Not set') . "\n";
echo "Database Port: " . ($dbConfig['DB_PORT'] ?: 'Not set') . "\n";
echo "Database Name: " . ($dbConfig['DB_DATABASE'] ?: 'Not set') . "\n";
echo "Database Username: " . ($dbConfig['DB_USERNAME'] ?: 'Not set') . "\n";
echo "Database Password: " . ($dbConfig['DB_PASSWORD'] ? '***' : 'Not set') . "\n\n";

// Interactive setup
if ($dbConfig['DB_CONNECTION'] === 'sqlite' || empty($dbConfig['DB_DATABASE'])) {
    echo "It looks like you're using SQLite or haven't configured a SQL database.\n";
    echo "Would you like to configure MySQL/MariaDB connection? (yes/no): ";
    
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($response) === 'yes' || strtolower($response) === 'y') {
        echo "\nLet's set up your MySQL/MariaDB connection:\n";
        echo "--------------------------------------------\n";
        
        echo "Database Host [127.0.0.1]: ";
        $handle = fopen("php://stdin", "r");
        $host = trim(fgets($handle));
        $host = $host ?: '127.0.0.1';
        fclose($handle);
        
        echo "Database Port [3306]: ";
        $handle = fopen("php://stdin", "r");
        $port = trim(fgets($handle));
        $port = $port ?: '3306';
        fclose($handle);
        
        echo "Database Name: ";
        $handle = fopen("php://stdin", "r");
        $database = trim(fgets($handle));
        fclose($handle);
        
        if (empty($database)) {
            echo "❌ Database name is required!\n";
            exit(1);
        }
        
        echo "Database Username: ";
        $handle = fopen("php://stdin", "r");
        $username = trim(fgets($handle));
        fclose($handle);
        
        echo "Database Password: ";
        $handle = fopen("php://stdin", "r");
        $password = trim(fgets($handle));
        fclose($handle);
        
        // Update .env file
        $newEnvContent = $envContent;
        
        // Update or add database configuration
        $newLines = [];
        $foundDbSettings = false;
        
        foreach ($lines as $line) {
            $originalLine = $line;
            $lineTrimmed = trim($line);
            
            if (strpos($lineTrimmed, 'DB_CONNECTION=') === 0) {
                $newLines[] = "DB_CONNECTION=mysql";
                $foundDbSettings = true;
            } elseif (strpos($lineTrimmed, 'DB_HOST=') === 0) {
                $newLines[] = "DB_HOST=$host";
                $foundDbSettings = true;
            } elseif (strpos($lineTrimmed, 'DB_PORT=') === 0) {
                $newLines[] = "DB_PORT=$port";
                $foundDbSettings = true;
            } elseif (strpos($lineTrimmed, 'DB_DATABASE=') === 0) {
                $newLines[] = "DB_DATABASE=$database";
                $foundDbSettings = true;
            } elseif (strpos($lineTrimmed, 'DB_USERNAME=') === 0) {
                $newLines[] = "DB_USERNAME=$username";
                $foundDbSettings = true;
            } elseif (strpos($lineTrimmed, 'DB_PASSWORD=') === 0) {
                $newLines[] = "DB_PASSWORD=$password";
                $foundDbSettings = true;
            } else {
                $newLines[] = $originalLine;
            }
        }
        
        // If database settings weren't found, add them
        if (!$foundDbSettings) {
            $newLines[] = "\n# Database Configuration";
            $newLines[] = "DB_CONNECTION=mysql";
            $newLines[] = "DB_HOST=$host";
            $newLines[] = "DB_PORT=$port";
            $newLines[] = "DB_DATABASE=$database";
            $newLines[] = "DB_USERNAME=$username";
            $newLines[] = "DB_PASSWORD=$password";
        }
        
        file_put_contents($envPath, implode("\n", $newLines));
        
        echo "\n✅ Database configuration updated!\n\n";
        
        // Test connection
        echo "Testing database connection...\n";
        
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            echo "✅ Database connection successful!\n\n";
            
            echo "Next steps:\n";
            echo "1. Run migrations: php artisan migrate\n";
            echo "2. (Optional) Seed database: php artisan db:seed\n";
            echo "3. Clear config cache: php artisan config:clear\n";
            
        } catch (PDOException $e) {
            echo "❌ Database connection failed!\n";
            echo "Error: " . $e->getMessage() . "\n\n";
            echo "Please check:\n";
            echo "1. MySQL/MariaDB is running\n";
            echo "2. Database name exists\n";
            echo "3. Username and password are correct\n";
            echo "4. Host and port are correct\n";
        }
    } else {
        echo "\nSetup cancelled.\n";
    }
} else {
    echo "✅ Database is already configured!\n";
    echo "Testing connection...\n";
    
    try {
        $host = $dbConfig['DB_HOST'] ?: '127.0.0.1';
        $port = $dbConfig['DB_PORT'] ?: '3306';
        $database = $dbConfig['DB_DATABASE'];
        $username = $dbConfig['DB_USERNAME'];
        $password = $dbConfig['DB_PASSWORD'];
        
        if (empty($database)) {
            echo "❌ Database name is not set!\n";
            exit(1);
        }
        
        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        echo "✅ Database connection successful!\n";
        
    } catch (PDOException $e) {
        echo "❌ Database connection failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\n";






