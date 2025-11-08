<?php
/**
 * Quick Fix Script - Change Session Driver to File
 * 
 * Run: php fix_session.php
 */

echo "==========================================\n";
echo "   Fixing Session Driver Configuration\n";
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
    
    // Check if this is the SESSION_DRIVER line
    if (strpos($lineTrimmed, 'SESSION_DRIVER=') === 0) {
        // Check if it's set to database
        if (strpos($lineTrimmed, 'SESSION_DRIVER=database') !== false || 
            strpos($lineTrimmed, 'SESSION_DRIVER= database') !== false) {
            echo "Found: $lineTrimmed\n";
            echo "Changing to: SESSION_DRIVER=file\n\n";
            $newLines[] = "SESSION_DRIVER=file";
            $updated = true;
        } else {
            $newLines[] = $originalLine;
        }
    } else {
        $newLines[] = $originalLine;
    }
}

// If SESSION_DRIVER wasn't found, add it
if (!$updated) {
    echo "SESSION_DRIVER not found in .env file.\n";
    echo "Adding: SESSION_DRIVER=file\n\n";
    
    // Add at the end
    $newLines[] = "";
    $newLines[] = "# Session Configuration";
    $newLines[] = "SESSION_DRIVER=file";
    $updated = true;
}

// Write back to .env
file_put_contents($envPath, implode("\n", $newLines));

if ($updated) {
    echo "✅ .env file updated successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Clear config cache: php artisan config:clear\n";
    echo "2. Restart your web server\n";
    echo "3. Refresh your browser\n\n";
} else {
    echo "⚠️  SESSION_DRIVER is already set to 'file' or different value.\n";
    echo "Current value: " . (isset($lines[array_search('SESSION_DRIVER', $lines)]) ? $lines[array_search('SESSION_DRIVER', $lines)] : 'Not found') . "\n";
}






