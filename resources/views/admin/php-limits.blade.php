<!DOCTYPE html>
<html>
<head>
    <title>PHP Upload Limits Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>PHP Upload Limits Configuration</h1>
    
    <h2>Current PHP Configuration</h2>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <th>Setting</th>
            <th>Current Value</th>
            <th>Recommended</th>
            <th>Status</th>
        </tr>
        <tr>
            <td><code>upload_max_filesize</code></td>
            <td>{{ ini_get('upload_max_filesize') }}</td>
            <td>500M</td>
            <td>
                @php
                    $current = ini_get('upload_max_filesize');
                    $currentBytes = $current ? (int)$current * (strpos(strtolower($current), 'm') !== false ? 1048576 : 1) : 0;
                    $requiredBytes = 500 * 1048576;
                @endphp
                @if($currentBytes >= $requiredBytes)
                    <span class="success">✅ OK</span>
                @else
                    <span class="error">❌ Too Low</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><code>post_max_size</code></td>
            <td>{{ ini_get('post_max_size') }}</td>
            <td>500M</td>
            <td>
                @php
                    $current = ini_get('post_max_size');
                    $currentBytes = $current ? (int)$current * (strpos(strtolower($current), 'm') !== false ? 1048576 : 1) : 0;
                @endphp
                @if($currentBytes >= $requiredBytes)
                    <span class="success">✅ OK</span>
                @else
                    <span class="error">❌ Too Low</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><code>memory_limit</code></td>
            <td>{{ ini_get('memory_limit') }}</td>
            <td>1024M</td>
            <td>
                @php
                    $current = ini_get('memory_limit');
                    $currentBytes = $current ? (int)$current * (strpos(strtolower($current), 'm') !== false ? 1048576 : 1) : 0;
                    $requiredMemory = 1024 * 1048576;
                @endphp
                @if($currentBytes >= $requiredMemory)
                    <span class="success">✅ OK</span>
                @else
                    <span class="warning">⚠️ Low</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><code>max_execution_time</code></td>
            <td>{{ ini_get('max_execution_time') }} seconds</td>
            <td>1800 seconds</td>
            <td>
                @if(ini_get('max_execution_time') >= 1800)
                    <span class="success">✅ OK</span>
                @else
                    <span class="warning">⚠️ Low</span>
                @endif
            </td>
        </tr>
    </table>

    <h2>PHP Configuration File Location</h2>
    <div class="info">
        <strong>Loaded php.ini:</strong> {{ php_ini_loaded_file() ?: 'Not found' }}<br>
        @if(php_ini_scanned_files())
            <strong>Scanned files:</strong> {{ php_ini_scanned_files() }}
        @endif
    </div>

    <h2>How to Fix</h2>
    
    @php
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');
        $uploadBytes = $uploadMax ? (int)$uploadMax * (strpos(strtolower($uploadMax), 'm') !== false ? 1048576 : 1) : 0;
        $postBytes = $postMax ? (int)$postMax * (strpos(strtolower($postMax), 'm') !== false ? 1048576 : 1) : 0;
        $needsFix = ($uploadBytes < 500 * 1048576) || ($postBytes < 500 * 1048576);
    @endphp

    @if($needsFix)
        <div class="error">
            <h3>⚠️ Action Required</h3>
            <p>Your upload limits are too low. Follow these steps:</p>
            
            <h4>Step 1: Find your php.ini file</h4>
            <p>Run this command in terminal:</p>
            <pre>php --ini</pre>
            
            <h4>Step 2: Edit php.ini</h4>
            @if(php_ini_loaded_file())
                <p><strong>Edit this file:</strong> <code>{{ php_ini_loaded_file() }}</code></p>
            @elseif(php_ini_scanned_files())
                <p><strong>Edit this file:</strong> <code>{{ php_ini_scanned_files() }}</code></p>
            @endif
            <p>Find these lines (or add them if they don't exist) and update to:</p>
            <pre>upload_max_filesize = 500M
post_max_size = 500M
memory_limit = 1024M
max_execution_time = 1800</pre>
            <p><strong>Or use the auto-fix script:</strong> Run <code>php update-php-limits.php</code> in your project root</p>
            
            <h4>Step 3: Restart your web server</h4>
            <ul>
                <li><strong>Laravel built-in server:</strong> Stop (Ctrl+C) and restart with <code>php artisan serve</code></li>
                <li><strong>Apache:</strong> <code>sudo service apache2 restart</code> or restart from XAMPP/WAMP control panel</li>
                <li><strong>Nginx + PHP-FPM:</strong> <code>sudo service php-fpm restart</code> and <code>sudo service nginx restart</code></li>
            </ul>
            
            <h4>Step 4: Verify</h4>
            <p>Refresh this page to check if the limits are updated.</p>
        </div>
    @else
        <div class="success">
            <h3>✅ Configuration is Good!</h3>
            <p>Your PHP upload limits are sufficient for large file imports (up to 500MB).</p>
        </div>
    @endif

    <div class="info">
        <h3>Quick Fix for Local Development (XAMPP/WAMP)</h3>
        <ol>
            <li>Open XAMPP/WAMP Control Panel</li>
            <li>Click "Config" next to Apache</li>
            <li>Select "PHP (php.ini)"</li>
            <li>Search for <code>upload_max_filesize</code> and change to <code>500M</code></li>
            <li>Search for <code>post_max_size</code> and change to <code>500M</code></li>
            <li>Save and restart Apache</li>
        </ol>
    </div>

    <div class="info">
        <h3>🚀 Auto-Fix Script (Recommended)</h3>
        <p>Run this command in your project root directory to automatically update php.ini:</p>
        <pre>php update-php-limits.php</pre>
        <p>This script will automatically update your php.ini file with the recommended values.</p>
        <p><strong>Note:</strong> You may need to run as Administrator on Windows.</p>
    </div>

    @if(php_ini_scanned_files() && strpos(php_ini_scanned_files(), 'scoop') !== false)
    <div class="warning">
        <h3>📦 For Scoop Users (Windows)</h3>
        <p>You're using Scoop package manager. The php.ini file location is:</p>
        <pre>{{ php_ini_scanned_files() }}</pre>
        <p><strong>Steps to fix:</strong></p>
        <ol>
            <li>Open the file: <code>{{ php_ini_scanned_files() }}</code></li>
            <li>Search for <code>upload_max_filesize</code> and change to <code>upload_max_filesize = 500M</code></li>
            <li>Search for <code>post_max_size</code> and change to <code>post_max_size = 500M</code></li>
            <li>Save the file</li>
            <li>Restart your Laravel server: Stop (Ctrl+C) and run <code>php artisan serve</code> again</li>
        </ol>
        <p><strong>Or use the auto-fix script:</strong></p>
        <pre>php update-php-limits.php</pre>
    </div>
    @endif

    <div class="warning">
        <h3>⚠️ Important Notes:</h3>
        <ul>
            <li><strong>You MUST restart your web server</strong> after editing php.ini for changes to take effect</li>
            <li>For Laravel's built-in server: Stop (Ctrl+C) and restart with <code>php artisan serve</code></li>
            <li>For XAMPP/WAMP: Restart Apache from the control panel</li>
            <li>The <code>upload_max_filesize</code> and <code>post_max_size</code> settings cannot be changed with PHP code - they must be set in php.ini</li>
        </ul>
    </div>

    <p><a href="{{ route('admin.shipping-charges.create') }}">← Back to Create Shipping Charge</a></p>
</body>
</html>

