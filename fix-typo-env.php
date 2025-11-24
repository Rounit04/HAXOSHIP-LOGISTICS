<?php
/**
 * Fix Typo in .env File
 */

echo "Fixing typo in .env file...\n\n";

$envPath = __DIR__ . '/.env';
$content = file_get_contents($envPath);

// Replace typo
$content = preg_replace('/^DB_DATABSE=/m', 'DB_DATABASE=', $content);

// Ensure DB_DATABASE is set correctly
if (strpos($content, 'DB_DATABASE=haxo_shipping') === false) {
    // Remove any incorrect lines and add correct one
    $lines = explode("\n", $content);
    $newLines = [];
    $dbDatabaseSet = false;
    
    foreach ($lines as $line) {
        if (preg_match('/^DB_DATAB?SE=/', $line)) {
            // Skip typos and duplicates
            if (strpos($line, 'DB_DATABASE=haxo_shipping') === 0) {
                $newLines[] = 'DB_DATABASE=haxo_shipping';
                $dbDatabaseSet = true;
            }
        } else {
            $newLines[] = $line;
            if (trim($line) === 'DB_PORT=3306' && !$dbDatabaseSet) {
                $newLines[] = 'DB_DATABASE=haxo_shipping';
                $dbDatabaseSet = true;
            }
        }
    }
    
    $content = implode("\n", $newLines);
} else {
    // Just fix the typo if it exists
    $lines = explode("\n", $content);
    $newLines = [];
    foreach ($lines as $line) {
        if (preg_match('/^DB_DATABSE=/', $line)) {
            // Skip the typo line
            continue;
        }
        $newLines[] = $line;
    }
    $content = implode("\n", $newLines);
}

file_put_contents($envPath, $content);
echo "✅ Fixed typo in .env file!\n";
echo "Removed DB_DATABSE (typo) and ensured DB_DATABASE=haxo_shipping is set correctly.\n\n";

