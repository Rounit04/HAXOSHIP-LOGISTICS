<?php
/**
 * Fix Database Configuration in .env File
 * Run: php fix-env-database.php
 */

echo "==========================================\n";
echo "   Fixing Database Configuration\n";
echo "==========================================\n\n";

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

// Read .env file
$envContent = file_get_contents($envPath);
$lines = explode("\n", $envContent);

$updated = false;
$newLines = [];

foreach ($lines as $line) {
    $originalLine = $line;
    $lineTrimmed = trim($line);
    
    // Skip empty lines and comments for now
    if (empty($lineTrimmed) || strpos($lineTrimmed, '#') === 0) {
        $newLines[] = $originalLine;
        continue;
    }
    
    // Check each database setting
    if (strpos($lineTrimmed, 'DB_DATABASE=') === 0) {
        if (strpos($lineTrimmed, 'DB_DATABASE=not set') !== false || 
            strpos($lineTrimmed, 'DB_DATABASE=') === 0 && substr($lineTrimmed, 12) === '' ||
            strpos($lineTrimmed, 'DB_DATABASE=database/database.sqlite') !== false) {
            echo "Found: $lineTrimmed\n";
            echo "Changing to: DB_DATABASE=haxo_shipping\n\n";
            $newLines[] = "DB_DATABASE=haxo_shipping";
            $updated = true;
        } else {
            $newLines[] = $originalLine;
        }
    } elseif (strpos($lineTrimmed, 'DB_PASSWORD=') === 0) {
        // Set password to empty if it has a value
        $currentPassword = substr($lineTrimmed, 12);
        if (!empty($currentPassword) && $currentPassword !== 'not set') {
            echo "Found: DB_PASSWORD=" . str_repeat('*', strlen($currentPassword)) . "\n";
            echo "Changing to: DB_PASSWORD=\n\n";
            $newLines[] = "DB_PASSWORD=";
            $updated = true;
        } else {
            $newLines[] = $originalLine;
        }
    } elseif (strpos($lineTrimmed, 'DB_CONNECTION=') === 0) {
        // Ensure it's set to mysql
        if (strpos($lineTrimmed, 'DB_CONNECTION=sqlite') !== false) {
            echo "Found: $lineTrimmed\n";
            echo "Changing to: DB_CONNECTION=mysql\n\n";
            $newLines[] = "DB_CONNECTION=mysql";
            $updated = true;
        } else {
            $newLines[] = $originalLine;
        }
    } else {
        $newLines[] = $originalLine;
    }
}

// Check if DB_DATABASE was never found and needs to be added
$hasDatabase = false;
foreach ($newLines as $line) {
    if (strpos(trim($line), 'DB_DATABASE=') === 0) {
        $hasDatabase = true;
        break;
    }
}

if (!$hasDatabase) {
    echo "DB_DATABASE not found. Adding it...\n\n";
    // Find a good place to insert it (after DB_PORT or DB_HOST)
    $inserted = false;
    $finalLines = [];
    foreach ($newLines as $index => $line) {
        $finalLines[] = $line;
        if (strpos(trim($line), 'DB_PORT=') === 0 && !$inserted) {
            $finalLines[] = "DB_DATABASE=haxo_shipping";
            $inserted = true;
            $updated = true;
        }
    }
    if (!$inserted) {
        $finalLines[] = "";
        $finalLines[] = "# Database Configuration";
        $finalLines[] = "DB_DATABASE=haxo_shipping";
        $updated = true;
    }
    $newLines = $finalLines;
}

// Write back to .env
if ($updated) {
    file_put_contents($envPath, implode("\n", $newLines));
    echo "✅ .env file updated successfully!\n\n";
    echo "Changes made:\n";
    echo "  - DB_CONNECTION=mysql\n";
    echo "  - DB_DATABASE=haxo_shipping\n";
    echo "  - DB_PASSWORD= (empty)\n\n";
    echo "Next steps:\n";
    echo "1. Clear config cache: php artisan config:clear\n";
    echo "2. Test connection: http://127.0.0.1:8000/check-migration-status\n";
    echo "3. Run migrations: http://127.0.0.1:8000/run-migrations\n\n";
} else {
    echo "✅ .env file is already configured correctly!\n";
    echo "No changes needed.\n\n";
}

