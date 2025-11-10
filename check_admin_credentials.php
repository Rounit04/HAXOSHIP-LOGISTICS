<?php
/**
 * Check Admin Credentials
 * Run: php check_admin_credentials.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "==========================================\n";
echo "   Admin Panel Credentials\n";
echo "==========================================\n\n";

$users = User::all();

if ($users->isEmpty()) {
    echo "❌ No users found in the database!\n\n";
    echo "To create an admin user, run:\n";
    echo "php artisan db:seed\n\n";
    echo "Or create a user manually using:\n";
    echo "php artisan tinker\n";
    echo "Then run:\n";
    echo "User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);\n";
} else {
    echo "Available Users (Any user can login to admin panel):\n";
    echo "----------------------------------------------------\n\n";
    
    foreach ($users as $user) {
        echo "Email: " . $user->email . "\n";
        echo "Name: " . $user->name . "\n";
        echo "Password: password (default)\n";
        echo "----------------------------------------\n\n";
    }
    
    echo "\nNote: Default password for all users is 'password'\n";
    echo "You can use any user's email and password to login to admin panel.\n";
}








