<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'view_dashboard', 'group' => 'dashboard', 'description' => 'Access dashboard view'],
            
            // User Management
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'group' => 'users', 'description' => 'Create, edit, delete users'],
            ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'group' => 'users', 'description' => 'Create and assign roles'],
            
            // Rate Calculator
            ['name' => 'Access Rate Calculator', 'slug' => 'access_rate_calculator', 'group' => 'rate_calculator', 'description' => 'Use rate calculator'],
            
            // Search with AWB
            ['name' => 'Search with AWB', 'slug' => 'search_awb', 'group' => 'awb', 'description' => 'Search using AWB numbers'],
            
            // Networks
            ['name' => 'View Networks', 'slug' => 'view_networks', 'group' => 'networks', 'description' => 'View network list'],
            ['name' => 'Create Networks', 'slug' => 'create_networks', 'group' => 'networks', 'description' => 'Create new networks'],
            ['name' => 'Edit Networks', 'slug' => 'edit_networks', 'group' => 'networks', 'description' => 'Edit existing networks'],
            ['name' => 'Delete Networks', 'slug' => 'delete_networks', 'group' => 'networks', 'description' => 'Delete networks'],
            
            // Transactions
            ['name' => 'View Transactions', 'slug' => 'view_transactions', 'group' => 'transactions', 'description' => 'View transaction list'],
            
            // Services
            ['name' => 'View Services', 'slug' => 'view_services', 'group' => 'services', 'description' => 'View service list'],
            ['name' => 'Create Services', 'slug' => 'create_services', 'group' => 'services', 'description' => 'Create new services'],
            ['name' => 'Edit Services', 'slug' => 'edit_services', 'group' => 'services', 'description' => 'Edit existing services'],
            ['name' => 'Delete Services', 'slug' => 'delete_services', 'group' => 'services', 'description' => 'Delete services'],
            
            // Countries
            ['name' => 'View Countries', 'slug' => 'view_countries', 'group' => 'countries', 'description' => 'View country list'],
            ['name' => 'Create Countries', 'slug' => 'create_countries', 'group' => 'countries', 'description' => 'Create new countries'],
            ['name' => 'Edit Countries', 'slug' => 'edit_countries', 'group' => 'countries', 'description' => 'Edit existing countries'],
            ['name' => 'Delete Countries', 'slug' => 'delete_countries', 'group' => 'countries', 'description' => 'Delete countries'],
            
            // Zones
            ['name' => 'View Zones', 'slug' => 'view_zones', 'group' => 'zones', 'description' => 'View zone list'],
            ['name' => 'Create Zones', 'slug' => 'create_zones', 'group' => 'zones', 'description' => 'Create new zones'],
            ['name' => 'Edit Zones', 'slug' => 'edit_zones', 'group' => 'zones', 'description' => 'Edit existing zones'],
            ['name' => 'Delete Zones', 'slug' => 'delete_zones', 'group' => 'zones', 'description' => 'Delete zones'],
            
            // Shipping Charges
            ['name' => 'View Shipping Charges', 'slug' => 'view_shipping_charges', 'group' => 'shipping', 'description' => 'View shipping charges'],
            ['name' => 'Create Shipping Charges', 'slug' => 'create_shipping_charges', 'group' => 'shipping', 'description' => 'Create shipping charges'],
            ['name' => 'Edit Shipping Charges', 'slug' => 'edit_shipping_charges', 'group' => 'shipping', 'description' => 'Edit shipping charges'],
            ['name' => 'Delete Shipping Charges', 'slug' => 'delete_shipping_charges', 'group' => 'shipping', 'description' => 'Delete shipping charges'],
            
            // Formulas
            ['name' => 'View Formulas', 'slug' => 'view_formulas', 'group' => 'formulas', 'description' => 'View formula list'],
            ['name' => 'Create Formulas', 'slug' => 'create_formulas', 'group' => 'formulas', 'description' => 'Create new formulas'],
            ['name' => 'Edit Formulas', 'slug' => 'edit_formulas', 'group' => 'formulas', 'description' => 'Edit existing formulas'],
            ['name' => 'Delete Formulas', 'slug' => 'delete_formulas', 'group' => 'formulas', 'description' => 'Delete formulas'],
            
            // AWB Upload
            ['name' => 'View AWB Uploads', 'slug' => 'view_awb_uploads', 'group' => 'awb', 'description' => 'View AWB upload list'],
            ['name' => 'Create AWB Uploads', 'slug' => 'create_awb_uploads', 'group' => 'awb', 'description' => 'Create new AWB uploads'],
            ['name' => 'Edit AWB Uploads', 'slug' => 'edit_awb_uploads', 'group' => 'awb', 'description' => 'Edit existing AWB uploads'],
            ['name' => 'Delete AWB Uploads', 'slug' => 'delete_awb_uploads', 'group' => 'awb', 'description' => 'Delete AWB uploads'],
            
            // Bookings
            ['name' => 'View Bookings', 'slug' => 'view_bookings', 'group' => 'bookings', 'description' => 'View booking list'],
            ['name' => 'Create Bookings', 'slug' => 'create_bookings', 'group' => 'bookings', 'description' => 'Create new bookings'],
            ['name' => 'Edit Bookings', 'slug' => 'edit_bookings', 'group' => 'bookings', 'description' => 'Edit existing bookings'],
            ['name' => 'Delete Bookings', 'slug' => 'delete_bookings', 'group' => 'bookings', 'description' => 'Delete bookings'],
            
            // Blogs
            ['name' => 'View Blogs', 'slug' => 'view_blogs', 'group' => 'blogs', 'description' => 'View blog list'],
            ['name' => 'Create Blogs', 'slug' => 'create_blogs', 'group' => 'blogs', 'description' => 'Create new blogs'],
            ['name' => 'Edit Blogs', 'slug' => 'edit_blogs', 'group' => 'blogs', 'description' => 'Edit existing blogs'],
            ['name' => 'Delete Blogs', 'slug' => 'delete_blogs', 'group' => 'blogs', 'description' => 'Delete blogs'],
            
            // Reports
            ['name' => 'View Reports', 'slug' => 'view_reports', 'group' => 'reports', 'description' => 'Access reports and analytics'],
            ['name' => 'Export Reports', 'slug' => 'export_reports', 'group' => 'reports', 'description' => 'Export reports'],
            
            // Banks
            ['name' => 'View Banks', 'slug' => 'view_banks', 'group' => 'banks', 'description' => 'View bank list'],
            ['name' => 'Create Banks', 'slug' => 'create_banks', 'group' => 'banks', 'description' => 'Create new banks'],
            ['name' => 'Edit Banks', 'slug' => 'edit_banks', 'group' => 'banks', 'description' => 'Edit existing banks'],
            ['name' => 'Delete Banks', 'slug' => 'delete_banks', 'group' => 'banks', 'description' => 'Delete banks'],
            ['name' => 'Bank Transfers', 'slug' => 'bank_transfers', 'group' => 'banks', 'description' => 'Transfer money between banks'],
            
            // Payments
            ['name' => 'View Payments', 'slug' => 'view_payments', 'group' => 'payments', 'description' => 'View payment list'],
            ['name' => 'Create Payments', 'slug' => 'create_payments', 'group' => 'payments', 'description' => 'Create new payments'],
            ['name' => 'Edit Payments', 'slug' => 'edit_payments', 'group' => 'payments', 'description' => 'Edit existing payments'],
            ['name' => 'Delete Payments', 'slug' => 'delete_payments', 'group' => 'payments', 'description' => 'Delete payments'],
            
            // Settings
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'group' => 'settings', 'description' => 'Modify system settings'],
            ['name' => 'Frontend Settings', 'slug' => 'frontend_settings', 'group' => 'settings', 'description' => 'Manage frontend settings'],
            
            // Payroll
            ['name' => 'View Payroll', 'slug' => 'view_payroll', 'group' => 'payroll', 'description' => 'View payroll information'],
            ['name' => 'Manage Payroll', 'slug' => 'manage_payroll', 'group' => 'payroll', 'description' => 'Manage payroll operations'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
