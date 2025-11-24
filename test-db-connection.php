<?php
/**
 * Quick Database Connection Test Script
 * Run: php test-db-connection.php
 */

echo "==========================================\n";
echo "   Database Connection Test\n";
echo "==========================================\n\n";

// Load .env file
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

// Read .env and parse values
$envContent = file_get_contents($envPath);
$lines = explode("\n", $envContent);
$config = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }
    
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $config[trim($key)] = trim($value);
    }
}

// Get database config
$connection = $config['DB_CONNECTION'] ?? 'not set';
$host = $config['DB_HOST'] ?? 'not set';
$database = $config['DB_DATABASE'] ?? 'not set';
$username = $config['DB_USERNAME'] ?? 'not set';
$password = $config['DB_PASSWORD'] ?? 'not set';
$port = $config['DB_PORT'] ?? '3306';

echo "Current Configuration:\n";
echo "  DB_CONNECTION: $connection\n";
echo "  DB_HOST: $host\n";
echo "  DB_PORT: $port\n";
echo "  DB_DATABASE: $database\n";
echo "  DB_USERNAME: $username\n";
echo "  DB_PASSWORD: " . ($password ? str_repeat('*', strlen($password)) : '(empty)') . "\n\n";

// Test connection with different password options
$testPasswords = [
    '' => '(empty)',
    'root' => 'root',
    'password' => 'password',
    $password => 'current from .env'
];

echo "Testing connections...\n\n";

foreach ($testPasswords as $testPass => $label) {
    if ($testPass === $password && $password) {
        // Skip if already tested
        continue;
    }
    
    echo "Trying password: $label...\n";
    
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $testPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "  ✅ SUCCESS! Password is: $label\n";
        echo "  Connection works with this password.\n\n";
        
        echo "Update your .env file:\n";
        echo "  DB_PASSWORD=$testPass\n\n";
        
        exit(0);
    } catch (PDOException $e) {
        echo "  ❌ Failed: " . $e->getMessage() . "\n";
    }
}

echo "\n❌ None of the common passwords worked.\n";
echo "\nYou need to reset your MySQL root password.\n";
echo "Please follow the guide in: RESET_MYSQL_PASSWORD.md\n\n";

