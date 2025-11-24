<?php
/**
 * Quick PHP Limits Checker
 * Run this from command line: php check-php-limits.php
 */

echo "\n=== PHP Upload Limits Check ===\n\n";

$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');
$memoryLimit = ini_get('memory_limit');
$maxExecution = ini_get('max_execution_time');

echo "Current Settings:\n";
echo "  upload_max_filesize: {$uploadMax}\n";
echo "  post_max_size: {$postMax}\n";
echo "  memory_limit: {$memoryLimit}\n";
echo "  max_execution_time: {$maxExecution} seconds\n\n";

$phpIniPath = php_ini_loaded_file();

if ($phpIniPath) {
    echo "✅ PHP Configuration File Found:\n";
    echo "   {$phpIniPath}\n\n";
    
    echo "To fix the upload limit issue:\n";
    echo "1. Open this file in a text editor (as Administrator)\n";
    echo "2. Search for 'upload_max_filesize' and change it to: upload_max_filesize = 500M\n";
    echo "3. Search for 'post_max_size' and change it to: post_max_size = 500M\n";
    echo "4. Save the file\n";
    echo "5. Restart your web server\n\n";
    
    // Check if file is writable
    if (is_writable($phpIniPath)) {
        echo "⚠️  The php.ini file is writable. Would you like me to update it? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) === 'y') {
            $content = file_get_contents($phpIniPath);
            
            // Update upload_max_filesize
            if (preg_match("/^;?\s*upload_max_filesize\s*=/m", $content)) {
                $content = preg_replace("/^;?\s*upload_max_filesize\s*=.*$/m", "upload_max_filesize = 500M", $content);
            } else {
                $content .= "\nupload_max_filesize = 500M\n";
            }
            
            // Update post_max_size
            if (preg_match("/^;?\s*post_max_size\s*=/m", $content)) {
                $content = preg_replace("/^;?\s*post_max_size\s*=.*$/m", "post_max_size = 500M", $content);
            } else {
                $content .= "\npost_max_size = 500M\n";
            }
            
            // Update memory_limit
            if (preg_match("/^;?\s*memory_limit\s*=/m", $content)) {
                $content = preg_replace("/^;?\s*memory_limit\s*=.*$/m", "memory_limit = 1024M", $content);
            } else {
                $content .= "\nmemory_limit = 1024M\n";
            }
            
            if (file_put_contents($phpIniPath, $content)) {
                echo "\n✅ php.ini updated successfully!\n";
                echo "⚠️  IMPORTANT: You must restart your web server for changes to take effect.\n";
                echo "   - If using 'php artisan serve', stop it (Ctrl+C) and restart\n";
                echo "   - If using XAMPP/WAMP, restart Apache from control panel\n";
            } else {
                echo "\n❌ Failed to write to php.ini. Please edit manually.\n";
            }
        }
    } else {
        echo "⚠️  The php.ini file is not writable. Please edit it manually as Administrator.\n\n";
    }
} else {
    echo "❌ PHP configuration file not found.\n";
    echo "   Try running: php --ini\n";
    echo "   This will show you where your php.ini file is located.\n\n";
}

echo "\nAfter updating php.ini, restart your server and try uploading again.\n\n";

