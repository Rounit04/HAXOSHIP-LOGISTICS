<?php
/**
 * PHP Upload Limits Fix Script
 * 
 * This script helps you find and update your php.ini file
 * to increase upload limits for large Excel file imports.
 * 
 * Run this script from command line: php fix-upload-limits.php
 */

echo "=== PHP Upload Limits Configuration Helper ===\n\n";

// Get current PHP configuration
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');
$max_execution = ini_get('max_execution_time');

echo "Current PHP Configuration:\n";
echo "  upload_max_filesize: {$upload_max}\n";
echo "  post_max_size: {$post_max}\n";
echo "  memory_limit: {$memory_limit}\n";
echo "  max_execution_time: {$max_execution} seconds\n\n";

// Get php.ini file location
$phpIniPath = php_ini_loaded_file();
$phpIniScanned = php_ini_scanned_files();

echo "PHP Configuration File Location:\n";
if ($phpIniPath) {
    echo "  Loaded: {$phpIniPath}\n";
} else {
    echo "  Loaded: (none - using defaults)\n";
}

if ($phpIniScanned) {
    echo "  Scanned: {$phpIniScanned}\n";
}

echo "\n";

// Check if limits need to be increased
$needsUpdate = false;
$recommendations = [];

if ($this->parseSize($upload_max) < $this->parseSize('500M')) {
    $needsUpdate = true;
    $recommendations[] = "upload_max_filesize = 500M";
}

if ($this->parseSize($post_max) < $this->parseSize('500M')) {
    $needsUpdate = true;
    $recommendations[] = "post_max_size = 500M";
}

if ($this->parseSize($memory_limit) < $this->parseSize('1024M')) {
    $recommendations[] = "memory_limit = 1024M";
}

if ($max_execution < 1800) {
    $recommendations[] = "max_execution_time = 1800";
}

if ($needsUpdate) {
    echo "⚠️  ACTION REQUIRED:\n";
    echo "Your upload limits are too low for large file imports.\n\n";
    
    if ($phpIniPath && is_writable($phpIniPath)) {
        echo "Your php.ini file is writable. Would you like to update it automatically? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) === 'y') {
            $this->updatePhpIni($phpIniPath, $recommendations);
        } else {
            echo "\nManual Update Instructions:\n";
            echo "1. Open: {$phpIniPath}\n";
            echo "2. Find and update these lines:\n";
            foreach ($recommendations as $rec) {
                echo "   {$rec}\n";
            }
            echo "3. Restart your web server (Apache/Nginx)\n";
        }
    } else {
        echo "Manual Update Instructions:\n";
        if ($phpIniPath) {
            echo "1. Open: {$phpIniPath}\n";
        } else {
            echo "1. Find your php.ini file (run: php --ini)\n";
        }
        echo "2. Find and update these lines:\n";
        foreach ($recommendations as $rec) {
            echo "   {$rec}\n";
        }
        echo "3. Restart your web server (Apache/Nginx)\n";
        echo "\nFor Laravel's built-in server, restart with: php artisan serve\n";
    }
} else {
    echo "✅ Your PHP configuration looks good!\n";
    echo "Upload limits are sufficient for large file imports.\n";
}

function parseSize($size) {
    $size = trim($size);
    $last = strtolower($size[strlen($size)-1]);
    $value = (int) $size;
    
    switch($last) {
        case 'g': $value *= 1024;
        case 'm': $value *= 1024;
        case 'k': $value *= 1024;
    }
    
    return $value;
}

function updatePhpIni($filePath, $recommendations) {
    $content = file_get_contents($filePath);
    $updated = false;
    
    foreach ($recommendations as $setting) {
        list($key, $value) = explode(' = ', $setting);
        
        // Check if setting exists
        if (preg_match("/^;?\s*{$key}\s*=/m", $content)) {
            // Replace existing setting
            $content = preg_replace("/^;?\s*{$key}\s*=.*$/m", "{$key} = {$value}", $content);
            $updated = true;
        } else {
            // Add new setting at the end
            $content .= "\n{$key} = {$value}\n";
            $updated = true;
        }
    }
    
    if ($updated && file_put_contents($filePath, $content)) {
        echo "\n✅ php.ini updated successfully!\n";
        echo "⚠️  Please restart your web server for changes to take effect.\n";
    } else {
        echo "\n❌ Failed to update php.ini. Please update manually.\n";
    }
}

