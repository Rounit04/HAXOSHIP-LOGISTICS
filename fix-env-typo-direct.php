<?php
/**
 * Fix .env file - Remove typo and ensure correct DB_DATABASE
 */

$envPath = __DIR__ . '/.env';
$content = file_get_contents($envPath);

// Split into lines
$lines = explode("\n", $content);
$newLines = [];
$hasDbDatabase = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Skip the typo line
    if (preg_match('/^DB_DATABSE=/', $trimmed)) {
        continue;
    }
    
    // Track if we have DB_DATABASE correctly set
    if (preg_match('/^DB_DATABASE=haxo_shipping$/', $trimmed)) {
        $hasDbDatabase = true;
        $newLines[] = $line;
    } else {
        $newLines[] = $line;
    }
}

// Ensure DB_DATABASE is set if it wasn't found
if (!$hasDbDatabase) {
    // Find where to insert it (after DB_HOST or DB_PORT)
    $inserted = false;
    $finalLines = [];
    foreach ($newLines as $line) {
        $finalLines[] = $line;
        if (strpos(trim($line), 'DB_PORT=') === 0 && !$inserted) {
            $finalLines[] = 'DB_DATABASE=haxo_shipping';
            $inserted = true;
        }
    }
    if (!$inserted) {
        $finalLines[] = 'DB_DATABASE=haxo_shipping';
    }
    $newLines = $finalLines;
}

// Write back
file_put_contents($envPath, implode("\n", $newLines));

echo "✅ Fixed .env file!\n";
echo "Removed DB_DATABSE (typo) and ensured DB_DATABASE=haxo_shipping exists.\n\n";

