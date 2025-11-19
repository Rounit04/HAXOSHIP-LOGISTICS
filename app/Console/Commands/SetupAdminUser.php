<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SetupAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:setup {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup admin user with email and password, giving them all permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Find or create user
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("User found: {$user->name}");
            $user->password = Hash::make($password);
            $user->is_admin = true;
            $user->email_verified_at = now();
            $user->save();
            $this->info("User updated with admin privileges and new password.");
        } else {
            $user = User::create([
                'name' => 'Admin User',
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
            $this->info("New admin user created.");
        }

        // Give all permissions (optional, since is_admin = true gives all permissions anyway)
        $allPermissions = Permission::all();
        $permissionIds = $allPermissions->pluck('id');
        $user->belongsToMany(Permission::class, 'user_permission')->sync($permissionIds);

        $this->info("✓ Admin user setup complete!");
        $this->info("Email: {$email}");
        $this->info("Password: [set]");
        $this->info("Is Super Admin: Yes");
        $this->info("All permissions granted.");

        return 0;
    }
}
