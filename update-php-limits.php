<?php
/**
 * PHP Upload Limits Auto-Updater
 * This script automatically updates php.ini with recommended values
 * Run from command line: php update-php-limits.php
 */

echo "\n=== PHP Upload Limits Auto-Updater ===\n\n";

// Get php.ini file location
$phpIniPath = php_ini_loaded_file();
$phpIniScanned = php_ini_scanned_files();

echo "Current PHP Configuration:\n";
echo "  upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "  post_max_size: " . ini_get('post_max_size') . "\n";
echo "  memory_limit: " . ini_get('memory_limit') . "\n";
echo "  max_execution_time: " . ini_get('max_execution_time') . " seconds\n\n";

if ($phpIniPath) {
    echo "✅ Found php.ini: {$phpIniPath}\n\n";
    
    if (is_writable($phpIniPath)) {
        echo "File is writable. Updating...\n\n";
        
        $content = file_get_contents($phpIniPath);
        $updated = false;
        
        // Update upload_max_filesize
        if (preg_match("/^;?\s*upload_max_filesize\s*=/m", $content)) {
            $content = preg_replace("/^;?\s*upload_max_filesize\s*=.*$/m", "upload_max_filesize = 500M", $content);
            $updated = true;
            echo "✅ Updated upload_max_filesize to 500M\n";
        } else {
            $content .= "\n; Upload max filesize\nupload_max_filesize = 500M\n";
            $updated = true;
            echo "✅ Added upload_max_filesize = 500M\n";
        }
        
        // Update post_max_size
        if (preg_match("/^;?\s*post_max_size\s*=/m", $content)) {
            $content = preg_replace("/^;?\s*post_max_size\s*=.*$/m", "post_max_size = 500M", $content);
            $updated = true;
            echo "✅ Updated post_max_size to 500M\n";
        } else {
            $content .= "\n; Post max size\npost_max_size = 500M\n";
            $updated = true;
            echo "✅ Added post_max_size = 500M\n";
        }
        
        // Update memory_limit if needed
        $currentMemory = ini_get('memory_limit');
        $currentMemoryBytes = parseSize($currentMemory);
        $requiredMemoryBytes = 1024 * 1024 * 1024; // 1024M
        
        if ($currentMemoryBytes < $requiredMemoryBytes) {
            if (preg_match("/^;?\s*memory_limit\s*=/m", $content)) {
                $content = preg_replace("/^;?\s*memory_limit\s*=.*$/m", "memory_limit = 1024M", $content);
                $updated = true;
                echo "✅ Updated memory_limit to 1024M\n";
            } else {
                $content .= "\n; Memory limit\nmemory_limit = 1024M\n";
                $updated = true;
                echo "✅ Added memory_limit = 1024M\n";
            }
        } else {
            echo "ℹ️  memory_limit is already sufficient\n";
        }
        
        // Update max_execution_time if needed
        $currentExecution = ini_get('max_execution_time');
        if ($currentExecution < 1800) {
            if (preg_match("/^;?\s*max_execution_time\s*=/m", $content)) {
                $content = preg_replace("/^;?\s*max_execution_time\s*=.*$/m", "max_execution_time = 1800", $content);
                $updated = true;
                echo "✅ Updated max_execution_time to 1800\n";
            } else {
                $content .= "\n; Max execution time\nmax_execution_time = 1800\n";
                $updated = true;
                echo "✅ Added max_execution_time = 1800\n";
            }
        } else {
            echo "ℹ️  max_execution_time is already sufficient\n";
        }
        
        if ($updated) {
            if (file_put_contents($phpIniPath, $content)) {
                echo "\n✅ Successfully updated php.ini!\n";
                echo "\n⚠️  IMPORTANT: You must restart your web server for changes to take effect.\n";
                echo "   - Laravel built-in server: Stop (Ctrl+C) and restart with 'php artisan serve'\n";
                echo "   - XAMPP/WAMP: Restart Apache from control panel\n";
                echo "   - Other servers: Restart your web server\n";
            } else {
                echo "\n❌ Failed to write to php.ini. Please run as Administrator.\n";
            }
        }
    } else {
        echo "❌ File is not writable. Please run this script as Administrator.\n";
        echo "\nManual Update Instructions:\n";
        echo "1. Open: {$phpIniPath}\n";
        echo "2. Find and update these lines:\n";
        echo "   upload_max_filesize = 500M\n";
        echo "   post_max_size = 500M\n";
        echo "   memory_limit = 1024M\n";
        echo "   max_execution_time = 1800\n";
        echo "3. Save the file\n";
        echo "4. Restart your web server\n";
    }
} else {
    echo "⚠️  php.ini not found in loaded location.\n";
    if ($phpIniScanned) {
        echo "Scanned files: {$phpIniScanned}\n";
        echo "\nTry editing the scanned file or find your web server's php.ini:\n";
        echo "1. Run: php --ini (to see all php.ini locations)\n";
        echo "2. For web server, check your web server configuration\n";
        echo "3. For Laravel built-in server, edit the CLI php.ini\n";
    }
}

function parseSize($size) {
    $size = trim($size);
    if (empty($size)) return 0;
    
    $last = strtolower($size[strlen($size)-1]);
    $value = (int) $size;
    
    switch($last) {
        case 'g': $value *= 1024;
        case 'm': $value *= 1024;
        case 'k': $value *= 1024;
    }
    
    return $value;
}

echo "\n";

