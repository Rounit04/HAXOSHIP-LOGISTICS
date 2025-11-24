<?php
// Quick check of .env file
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    echo "=== .env File Contents (Database Section) ===\n\n";
    
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, 'DB_') === 0) {
            // Hide password value
            if (strpos($line, 'DB_PASSWORD=') === 0) {
                $value = substr($line, 12);
                echo "DB_PASSWORD=" . (empty($value) ? '(empty)' : str_repeat('*', strlen($value))) . "\n";
            } else {
                echo $line . "\n";
            }
        }
    }
    
    echo "\n=== Checking if DB_DATABASE is set ===\n";
    if (strpos($content, 'DB_DATABASE=') !== false) {
        preg_match('/DB_DATABASE=(.+)/', $content, $matches);
        $value = trim($matches[1] ?? '');
        if (!empty($value) && $value !== 'not set') {
            echo "✅ DB_DATABASE is set to: $value\n";
        } else {
            echo "❌ DB_DATABASE is not properly set\n";
        }
    } else {
        echo "❌ DB_DATABASE line not found in .env file\n";
    }
} else {
    echo "❌ .env file not found!\n";
}

