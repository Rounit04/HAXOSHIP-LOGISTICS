<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FrontendSetting;
use App\Models\BookingCategory;
use App\Models\Blog;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\AwbUpload;
use App\Models\Network;
use App\Models\NetworkTransaction;
use App\Models\SupportTicket;
use App\Models\DeliveryCategory;
use App\Models\DeliveryCharge;
use App\Models\DeliveryType;
use App\Models\LiquidFragile;
use App\Models\SmsSetting;
use App\Models\SmsSendSetting;
use App\Models\GooglemapSetting;
use App\Models\MailSetting;
use App\Models\SocialLoginSetting;
use App\Models\PaymentSetup;
use App\Models\Packaging;
use App\Models\Currency;
use App\Imports\AwbUploadsImport;
use App\Imports\NetworksImport;
use App\Imports\ServicesImport;
use App\Imports\CountriesImport;
use App\Imports\ZonesImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    use ImportMethods;
    public function dashboard()
    {
        // Ensure user is logged in (should be handled by middleware, but just in case)
        if (!session()->has('admin_logged_in') || session('admin_logged_in') !== true) {
            return redirect()->route('admin.login');
        }
        
        $totalUsers = User::count();
        
        // Generate last 7 days dates for graphs
        $dates = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('d-m-Y');
            // For demo, you can replace these with actual data from your database
            $incomeData[] = rand(0, 5); // Replace with actual income data
            $expenseData[] = rand(0, 5); // Replace with actual expense data
        }
        
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'dates' => $dates,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
        ]);
    }

    public function users()
    {
        // Get all users from the database (both regular and Google OAuth users)
        $users = User::latest()->get();
        
        return view('admin.users', [
            'users' => $users,
        ]);
    }

    public function settings()
    {
        $notificationSettings = NotificationSetting::getSettings();
        return view('admin.settings', compact('notificationSettings'));
    }

    public function updateGdprCookieSettings(Request $request)
    {
        $request->validate([
            'gdpr_cookie_enabled' => 'sometimes|boolean',
            'gdpr_cookie_message' => 'nullable|string|max:1000',
            'gdpr_cookie_button_text' => 'nullable|string|max:50',
            'gdpr_cookie_decline_text' => 'nullable|string|max:50',
            'gdpr_cookie_settings_text' => 'nullable|string|max:50',
            'gdpr_cookie_position' => 'nullable|in:top,bottom',
            'gdpr_cookie_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'gdpr_cookie_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'gdpr_cookie_button_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'gdpr_cookie_expiry_days' => 'nullable|integer|min:1|max:3650',
        ]);

        $settings = FrontendSetting::getSettings();
        
        $settings->gdpr_cookie_enabled = $request->has('gdpr_cookie_enabled') && $request->gdpr_cookie_enabled == '1';
        $settings->gdpr_cookie_message = $request->gdpr_cookie_message ?? ($settings->gdpr_cookie_message ?? 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.');
        $settings->gdpr_cookie_button_text = $request->gdpr_cookie_button_text ?? ($settings->gdpr_cookie_button_text ?? 'Accept All');
        $settings->gdpr_cookie_decline_text = $request->gdpr_cookie_decline_text ?? ($settings->gdpr_cookie_decline_text ?? 'Decline');
        $settings->gdpr_cookie_settings_text = $request->gdpr_cookie_settings_text ?? ($settings->gdpr_cookie_settings_text ?? 'Settings');
        $settings->gdpr_cookie_position = $request->gdpr_cookie_position ?? ($settings->gdpr_cookie_position ?? 'bottom');
        $settings->gdpr_cookie_bg_color = $request->gdpr_cookie_bg_color ?? ($settings->gdpr_cookie_bg_color ?? '#ffffff');
        $settings->gdpr_cookie_text_color = $request->gdpr_cookie_text_color ?? ($settings->gdpr_cookie_text_color ?? '#1b1b18');
        $settings->gdpr_cookie_button_color = $request->gdpr_cookie_button_color ?? ($settings->gdpr_cookie_button_color ?? '#FF750F');
        $settings->gdpr_cookie_expiry_days = $request->gdpr_cookie_expiry_days ?? ($settings->gdpr_cookie_expiry_days ?? 365);
        
        $settings->save();

        return redirect()->route('admin.settings')->with('success', 'GDPR Cookie settings updated successfully!');
    }

    public function notificationSettings()
    {
        $settings = NotificationSetting::getSettings();
        return view('admin.notification-settings', compact('settings'));
    }

    public function updateNotificationSettings(Request $request)
    {
        $request->validate([
            'polling_interval' => 'sometimes|integer|min:5|max:300',
            'settings' => 'required|array',
            'settings.*.enabled' => 'sometimes|boolean',
            'settings.*.show_popup' => 'sometimes|boolean',
            'settings.*.show_dropdown' => 'sometimes|boolean',
            'settings.*.polling_interval' => 'sometimes|integer|min:5|max:300',
        ]);

        // Update global polling interval if provided
        if ($request->has('polling_interval')) {
            $firstSetting = NotificationSetting::where('key', 'user_login')->first();
            if ($firstSetting) {
                $firstSetting->update(['polling_interval' => (int)$request->polling_interval]);
                // Update all settings to use the same polling interval
                NotificationSetting::query()->update(['polling_interval' => (int)$request->polling_interval]);
            }
        }

        foreach ($request->settings as $key => $settingData) {
            $setting = NotificationSetting::where('key', $key)->first();
            if ($setting) {
                $updateData = [];
                
                if (isset($settingData['enabled'])) {
                    $updateData['enabled'] = $settingData['enabled'] == '1' || $settingData['enabled'] === true || $settingData['enabled'] === 'true';
                }
                if (isset($settingData['show_popup'])) {
                    $updateData['show_popup'] = $settingData['show_popup'] == '1' || $settingData['show_popup'] === true || $settingData['show_popup'] === 'true';
                }
                if (isset($settingData['show_dropdown'])) {
                    $updateData['show_dropdown'] = $settingData['show_dropdown'] == '1' || $settingData['show_dropdown'] === true || $settingData['show_dropdown'] === 'true';
                }
                if (isset($settingData['polling_interval'])) {
                    $updateData['polling_interval'] = (int)$settingData['polling_interval'];
                }
                
                if (!empty($updateData)) {
                    $setting->update($updateData);
                }
            }
        }

        return redirect()->route('admin.notification-settings')->with('success', 'Notification settings updated successfully!');
    }

    public function frontendSettings()
    {
        $settings = FrontendSetting::getSettings();
        return view('admin.frontend-settings', compact('settings'));
    }

    public function updateFrontendSettings(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_button_text' => 'nullable|string|max:50',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_description' => 'nullable|string|max:500',
            'footer_facebook_url' => 'nullable|url|max:255',
            'footer_instagram_url' => 'nullable|url|max:255',
            'footer_twitter_url' => 'nullable|url|max:255',
            'footer_skype_url' => 'nullable|url|max:255',
            'footer_google_play_url' => 'nullable|url|max:255',
            'footer_app_store_url' => 'nullable|url|max:255',
            'footer_copyright_text' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:255',
        ]);

        $settings = FrontendSetting::getSettings();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $logoPath = $request->file('logo')->store('frontend', 'public');
            $settings->logo = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($settings->banner && Storage::disk('public')->exists($settings->banner)) {
                Storage::disk('public')->delete($settings->banner);
            }
            $bannerPath = $request->file('banner')->store('frontend', 'public');
            $settings->banner = $bannerPath;
        }

        // Handle footer logo upload
        if ($request->hasFile('footer_logo')) {
            // Delete old footer logo if exists
            if ($settings->footer_logo && Storage::disk('public')->exists($settings->footer_logo)) {
                Storage::disk('public')->delete($settings->footer_logo);
            }
            $footerLogoPath = $request->file('footer_logo')->store('frontend', 'public');
            $settings->footer_logo = $footerLogoPath;
        }

        // Update other settings
        $settings->primary_color = $request->primary_color;
        $settings->secondary_color = $request->secondary_color;
        $settings->text_color = $request->text_color;
        $settings->hero_title = $request->hero_title;
        $settings->hero_subtitle = $request->hero_subtitle;
        $settings->hero_button_text = $request->hero_button_text;
        
        // Update footer settings
        $settings->footer_description = $request->footer_description;
        $settings->footer_facebook_url = $request->footer_facebook_url;
        $settings->footer_instagram_url = $request->footer_instagram_url;
        $settings->footer_twitter_url = $request->footer_twitter_url;
        $settings->footer_skype_url = $request->footer_skype_url;
        $settings->footer_google_play_url = $request->footer_google_play_url;
        $settings->footer_app_store_url = $request->footer_app_store_url;
        $settings->footer_copyright_text = $request->footer_copyright_text;
        
        // Update contact settings (only if they are provided in the request)
        if ($request->has('contact_email') || $request->has('contact_phone') || $request->has('contact_address')) {
            if ($request->has('contact_email')) {
                $settings->contact_email = $request->contact_email ?: null;
            }
            if ($request->has('contact_phone')) {
                $settings->contact_phone = $request->contact_phone ?: null;
            }
            if ($request->has('contact_address')) {
                $settings->contact_address = $request->contact_address ?: null;
            }
        }
        
        $settings->save();

        return redirect()->route('admin.frontend-settings')->with('success', 'Frontend settings updated successfully!');
    }

    public function roles()
    {
        // Get all users without roles relationship (since we're using simple role assignment)
        $users = User::latest()->get();
        $roles = ['Admin', 'Manager', 'Staff', 'User', 'Viewer']; // You can create a Role model later
        
        return view('admin.roles', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Save the role to the user
        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        // Create notification for role assignment (only if enabled)
        if (NotificationSetting::isEnabled('role_assigned')) {
            Notification::create([
                'type' => 'role_assigned',
                'title' => 'Role Assigned',
                'message' => 'Role "' . $request->role . '" has been assigned to ' . $user->name,
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'old_role' => $oldRole,
                    'new_role' => $request->role,
                ],
            ]);
        }

        return back()->with('success', 'Role assigned successfully to ' . $user->name . '!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('role')) {
            $user->role = $request->role;
        }
        $user->save();

        return redirect()->route('admin.roles')->with('success', 'User updated successfully!');
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();

        return back()->with('success', 'User verified successfully!');
    }

    public function unverifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = null;
        $user->save();

        return back()->with('success', 'User unverified successfully!');
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);
        $user->banned_at = now();
        $user->save();

        return back()->with('success', 'User banned successfully!');
    }

    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->banned_at = null;
        $user->save();

        return back()->with('success', 'User unbanned successfully!');
    }

    public function loginAsUser($id)
    {
        $user = User::findOrFail($id);
        
        // Store admin session info
        session(['admin_id' => session('admin_user_id')]);
        session(['admin_user_name' => session('admin_user_name')]);
        
        // Log in as the user
        Auth::login($user, true);
        
        return redirect()->route('dashboard')->with('success', 'Logged in as ' . $user->name . '!');
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        // Create role logic here
        // You can create a Role model and permissions table later

        return back()->with('success', 'Role created successfully!');
    }

    public function rateCalculator()
    {
        // Sample data for dropdowns - In production, fetch from database
        $countries = ['India', 'USA', 'UK', 'Canada', 'Australia', 'Germany', 'France', 'Japan'];
        $shipmentTypes = ['Dox', 'Non-Dox', 'Medicine', 'Special'];
        
        // Sample pincodes - In production, fetch from database based on country
        $pincodes = [
            'India' => ['110001', '400001', '700001', '600001', '560001', '380001'],
            'USA' => ['10001', '90001', '60601', '77001', '33101', '94101'],
            'UK' => ['SW1A 1AA', 'M1 1AA', 'B1 1AA', 'LS1 1AA', 'EC1A 1BB', 'WC1A 1AB'],
        ];

        return view('admin.rate-calculator', [
            'countries' => $countries,
            'shipmentTypes' => $shipmentTypes,
            'pincodes' => $pincodes,
        ]);
    }

    public function calculateRate(Request $request)
    {
        $request->validate([
            'shipment_type' => 'required|string',
            'origin_country' => 'required|string',
            'origin_pincode' => 'required|string',
            'destination_country' => 'required|string',
            'destination_pincode' => 'required|string',
            'weight' => 'required|numeric|min:0.1',
        ]);

        // Get zones based on pincodes
        $zones = $this->getZones();
        $originZone = null;
        $destinationZone = null;
        
        // Find origin zone
        $originZoneData = collect($zones)->first(function($zone) use ($request) {
            return ($zone['pincode'] ?? '') == $request->origin_pincode 
                && ($zone['country'] ?? '') == $request->origin_country
                && ($zone['status'] ?? '') == 'Active';
        });
        if ($originZoneData) {
            $originZone = $originZoneData['zone'] ?? null;
        }
        
        // Find destination zone
        $destinationZoneData = collect($zones)->first(function($zone) use ($request) {
            return ($zone['pincode'] ?? '') == $request->destination_pincode 
                && ($zone['country'] ?? '') == $request->destination_country
                && ($zone['status'] ?? '') == 'Active';
        });
        if ($destinationZoneData) {
            $destinationZone = $destinationZoneData['zone'] ?? null;
        }
        
        // Get all formulas and shipping charges
        $allFormulas = $this->getFormulas();
        $allShippingCharges = $this->getShippingCharges();
        $weight = (float)$request->weight;
        
        // Match shipping charges based on route and weight
        // Priority: Exact match (with zones) > Match without zones > Match by country only
        $matchingCharges = collect($allShippingCharges)->filter(function($charge) use ($request, $originZone, $destinationZone, $weight) {
            $matchesOrigin = ($charge['origin'] ?? '') == $request->origin_country;
            $matchesDestination = ($charge['destination'] ?? '') == $request->destination_country;
            $matchesShipmentType = ($charge['shipment_type'] ?? '') == $request->shipment_type;
            $matchesWeight = $weight >= (float)($charge['min_weight'] ?? 0) && $weight <= (float)($charge['max_weight'] ?? 999999);
            
            // Must match origin, destination, shipment type, and weight
            if (!$matchesOrigin || !$matchesDestination || !$matchesShipmentType || !$matchesWeight) {
                return false;
            }
            
            // Zone matching is optional - if zones are found, prefer exact zone matches
            if ($originZone && $destinationZone) {
                $matchesOriginZone = ($charge['origin_zone'] ?? '') == $originZone || ($charge['origin_zone'] ?? '') == '';
                $matchesDestinationZone = ($charge['destination_zone'] ?? '') == $destinationZone || ($charge['destination_zone'] ?? '') == '';
                return $matchesOriginZone && $matchesDestinationZone;
            }
            
            // If zones not found, match by country only
            return true;
        })->sortBy(function($charge) use ($originZone, $destinationZone) {
            // Prioritize exact zone matches
            $score = 0;
            if ($originZone && ($charge['origin_zone'] ?? '') == $originZone) {
                $score += 10;
            }
            if ($destinationZone && ($charge['destination_zone'] ?? '') == $destinationZone) {
                $score += 10;
            }
            return -$score; // Negative for descending sort
        })->values()->toArray();
        
        // Match formulas - find all applicable formulas
        // Formulas can apply based on:
        // 1. Network/Service from matching charges (if any)
        // 2. Or formulas with no specific network/service (general formulas)
        // 3. Status must be Active
        $appliedFormulas = [];
        $formulaChargeTotal = 0;
        $baseRate = 100; // Default base rate
        
        // Get networks and services from matching charges
        $chargeNetworks = collect($matchingCharges)->pluck('network')->filter()->unique()->toArray();
        $chargeServices = collect($matchingCharges)->pluck('service')->filter()->unique()->toArray();
        
        // Use first matching charge's rate as base rate if available
        if (!empty($matchingCharges)) {
            $firstCharge = $matchingCharges[0];
            $chargeRate = (float)($firstCharge['rate'] ?? 0);
            if ($chargeRate > 0) {
                $baseRate = $chargeRate;
            }
        }
        
        // Find all active formulas that could apply
        // A formula applies if:
        // 1. It has no network specified (applies to all), OR
        // 2. Its network matches one of the charge networks, OR
        // 3. There are no matching charges (show all formulas)
        $allActiveFormulas = collect($allFormulas)->filter(function($formula) {
            return ($formula['status'] ?? '') == 'Active';
        });
        
        // Filter formulas that match the context
        $applicableFormulas = $allActiveFormulas->filter(function($formula) use ($chargeNetworks, $chargeServices, $matchingCharges) {
            $formulaNetwork = $formula['network'] ?? '';
            $formulaService = $formula['service'] ?? '';
            
            // If no matching charges, show all formulas
            if (empty($matchingCharges)) {
                return true;
            }
            
            // If formula has no network/service, it applies to all
            if (empty($formulaNetwork) && empty($formulaService)) {
                return true;
            }
            
            // Check if formula network matches any charge network (or formula has no network)
            $networkMatches = empty($formulaNetwork) || in_array($formulaNetwork, $chargeNetworks);
            
            // Check if formula service matches any charge service (or formula has no service)
            $serviceMatches = empty($formulaService) || in_array($formulaService, $chargeServices);
            
            return $networkMatches && $serviceMatches;
        })->sortBy(function($formula) {
            // Sort by priority (1st, 2nd, 3rd, 4th)
            $priority = $formula['priority'] ?? '4th';
            $priorityMap = ['1st' => 1, '2nd' => 2, '3rd' => 3, '4th' => 4];
            return $priorityMap[$priority] ?? 4;
        })->values();
        
        // Calculate charges for each applicable formula
        foreach ($applicableFormulas as $formula) {
            $formulaNetwork = $formula['network'] ?? '';
            $formulaService = $formula['service'] ?? '';
            
            // Determine which network/service to associate this formula with
            $displayNetwork = 'N/A';
            $displayService = 'N/A';
            
            if (!empty($chargeNetworks) && !empty($formulaNetwork)) {
                // Use formula's network if it matches a charge network
                if (in_array($formulaNetwork, $chargeNetworks)) {
                    $displayNetwork = $formulaNetwork;
                }
            } else if (!empty($formulaNetwork)) {
                $displayNetwork = $formulaNetwork;
            } else if (!empty($chargeNetworks)) {
                $displayNetwork = $chargeNetworks[0];
            }
            
            if (!empty($chargeServices) && !empty($formulaService)) {
                if (in_array($formulaService, $chargeServices)) {
                    $displayService = $formulaService;
                }
            } else if (!empty($formulaService)) {
                $displayService = $formulaService;
            } else if (!empty($chargeServices)) {
                $displayService = $chargeServices[0];
            }
            
            // Calculate formula charge
            $calculatedCharge = 0;
            $formulaValue = (float)($formula['value'] ?? 0);
            $formulaType = $formula['type'] ?? 'Fixed';
            $formulaScope = $formula['scope'] ?? 'Flat';
            
            if ($formulaType == 'Fixed') {
                if ($formulaScope == 'per kg') {
                    $calculatedCharge = $formulaValue * $weight;
                } else {
                    $calculatedCharge = $formulaValue;
                }
            } else { // Percentage
                if ($formulaScope == 'per kg') {
                    $percentageAmount = ($baseRate * $formulaValue / 100);
                    $calculatedCharge = $percentageAmount * $weight;
                } else {
                    $calculatedCharge = $baseRate * $formulaValue / 100;
                }
            }
            
            $formulaChargeTotal += $calculatedCharge;
            
            $appliedFormulas[] = array_merge($formula, [
                'calculated_charge' => round($calculatedCharge, 2),
                'network' => $displayNetwork,
                'service' => $displayService,
            ]);
        }
        
        // Calculate weight charge (use formula total if available, otherwise default)
        $weightCharge = $formulaChargeTotal > 0 ? $formulaChargeTotal : ($weight * 10);
        
        // Calculate distance charge (default calculation)
        $distanceCharge = 20.00;
        
        // Calculate total rate
        $totalRate = $baseRate + $weightCharge + $distanceCharge;
        
        return response()->json([
            'success' => true,
            'rate' => round($totalRate, 2),
            'currency' => 'INR',
            'breakdown' => [
                'base_rate' => $baseRate,
                'weight' => $weight,
                'weight_charge' => round($weightCharge, 2),
                'distance_charge' => round($distanceCharge, 2),
                'service_type' => $request->shipment_type,
            ],
            'applied_formulas' => $appliedFormulas,
            'matching_charges' => $matchingCharges,
            'origin_zone' => $originZone,
            'destination_zone' => $destinationZone,
        ]);
    }

    // Get networks from database with session fallback
    private function getNetworks()
    {
        // Try to get from database first
        $dbNetworks = Network::all();
        
        if ($dbNetworks->isNotEmpty()) {
            // Convert to array format for backward compatibility
            return $dbNetworks->map(function($network) {
                return [
                    'id' => $network->id,
                    'name' => $network->name,
                    'type' => $network->type,
                    'opening_balance' => $network->opening_balance,
                    'status' => $network->status,
                    'bank_details' => $network->bank_details,
                    'upi_scanner' => $network->upi_scanner,
                    'remark' => $network->remark,
                ];
            })->toArray();
        }
        
        // Fallback to session
        if (session()->has('networks')) {
            return session('networks');
        }
        
        // Initialize with default networks if both are empty
        $defaultNetworks = [
            [
                'id' => 1,
                'name' => 'DTDC',
                'type' => 'Domestic',
                'opening_balance' => 5000.00,
                'status' => 'Active',
                'bank_details' => 'HDFC Bank - Account: ****1234',
                'upi_scanner' => 'dtdc@paytm',
                'remark' => 'Primary domestic network',
            ],
            [
                'id' => 2,
                'name' => 'FedEx',
                'type' => 'International',
                'opening_balance' => 10000.00,
                'status' => 'Active',
                'bank_details' => 'ICICI Bank - Account: ****5678',
                'upi_scanner' => 'fedex@upi',
                'remark' => 'International shipping partner',
            ],
        ];
        
        // Store defaults in session
        session(['networks' => $defaultNetworks]);
        
        return $defaultNetworks;
    }

    public function networks()
    {
        // Redirect to create network by default
        return redirect()->route('admin.networks.create');
    }

    public function createNetwork()
    {
        $networks = $this->getNetworks();
        return view('admin.networks.create', [
            'networks' => $networks,
        ]);
    }

    public function allNetworks(Request $request)
    {
        // Always prioritize database - start with database query
        $query = Network::query();
        
        // Search by network name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get filtered results from database
        $dbNetworks = $query->latest()->get();
        
        // Convert to array format - always use database results if they exist
        $networks = [];
        if ($dbNetworks->isNotEmpty()) {
            $networks = $dbNetworks->map(function($network) {
                return [
                    'id' => $network->id,
                    'name' => $network->name,
                    'type' => $network->type,
                    'opening_balance' => $network->opening_balance,
                    'status' => $network->status,
                    'bank_details' => $network->bank_details,
                    'upi_scanner' => $network->upi_scanner,
                    'remark' => $network->remark,
                ];
            })->toArray();
        }
        
        // Only fallback to session if database is completely empty AND no filters are applied
        // This ensures newly created networks always show up from database
        if (empty($networks) && !$request->filled('search') && !$request->filled('type') && !$request->filled('status')) {
            // Double-check database is empty (no filters applied)
            $dbCount = Network::count();
            if ($dbCount === 0) {
                // Only then use session fallback
                $networks = $this->getNetworks();
            }
        }
        
        return view('admin.networks.all', [
            'networks' => $networks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'type' => $request->type ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    /**
     * Bulk delete networks
     */
    public function bulkDeleteNetworks(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $deleted = Network::whereIn('id', $ids)->delete();
            
            // Also remove from session
            $networks = $this->getNetworks();
            $networks = array_filter($networks, function($network) use ($ids) {
                return !in_array($network['id'], $ids);
            });
            session(['networks' => array_values($networks)]);
            session()->save();
            
            return redirect()->route('admin.networks.all')
                ->with('success', "Successfully deleted {$deleted} network(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting networks: ' . $e->getMessage());
        }
    }

    public function editNetwork($id)
    {
        try {
            // Try to get from database first
            $networkModel = Network::find($id);
            
            if ($networkModel) {
                // Convert to array format for view
                $network = [
                    'id' => $networkModel->id,
                    'name' => $networkModel->name,
                    'type' => $networkModel->type,
                    'opening_balance' => $networkModel->opening_balance,
                    'status' => $networkModel->status,
                    'bank_details' => $networkModel->bank_details,
                    'upi_scanner' => $networkModel->upi_scanner,
                    'remark' => $networkModel->remark,
                ];
            } else {
                // Fallback to session
                $networks = $this->getNetworks();
                $network = collect($networks)->firstWhere('id', $id);
                
                if (!$network) {
                    return redirect()->route('admin.networks.all')->with('error', 'Network not found');
                }
            }
            
            $networks = $this->getNetworks();

            return view('admin.networks.edit', [
                'network' => $network,
                'networks' => $networks,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading network for edit: ' . $e->getMessage());
            return redirect()->route('admin.networks.all')->with('error', 'Error loading network: ' . $e->getMessage());
        }
    }

    public function storeNetwork(Request $request)
    {
        $request->validate([
            'network_name' => 'required|string|max:255',
            'network_type' => 'required|in:Domestic,International',
            'opening_balance' => 'required|numeric|min:0',
            'bank_details' => 'nullable|string',
            'upi_scanner' => 'nullable|string',
            'upi_scanner_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Convert checkbox to status string
        // Checkbox sends "1", "on", or true when checked, "0" or nothing when unchecked
        $statusValue = $request->input('status');
        // Handle various checkbox values: "1", "on", true, 1 = Active; "0", false, 0, null = Inactive
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';
        
        // Log for debugging
        \Log::info('Network creation - Status value received: ' . var_export($statusValue, true) . ', Converted to: ' . $status);

        // Handle UPI Scanner - file upload or text
        $upiScanner = '';
        if ($request->hasFile('upi_scanner_file')) {
            // Upload file
            $file = $request->file('upi_scanner_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/upi_scanners', $fileName);
            $upiScanner = 'storage/upi_scanners/' . $fileName;
        } elseif ($request->filled('upi_scanner')) {
            // Use text input
            $upiScanner = $request->upi_scanner;
        }

        // Get existing networks
        $networks = $this->getNetworks();
        
        // Ensure we have an array
        if (!is_array($networks)) {
            $networks = [];
        }
        
        // Generate new ID - handle case where networks might have string keys or be empty
        $maxId = 0;
        if (count($networks) > 0) {
            $ids = array_column($networks, 'id');
            if (!empty($ids) && is_numeric(max($ids))) {
                $maxId = max($ids);
            }
        }
        $newId = $maxId + 1;
        
        // Create new network array
        $newNetwork = [
            'id' => $newId,
            'name' => $request->network_name,
            'type' => $request->network_type,
            'opening_balance' => (float) $request->opening_balance,
            'status' => $status,
            'bank_details' => $request->bank_details ?? '',
            'upi_scanner' => $upiScanner,
            'remark' => $request->remark ?? '',
        ];
        
        // Save to database first
        try {
            $network = Network::create([
                'name' => $request->network_name,
                'type' => $request->network_type,
                'opening_balance' => (float) $request->opening_balance,
                'status' => $status,
                'bank_details' => $request->bank_details ?? '',
                'upi_scanner' => $upiScanner,
                'remark' => $request->remark ?? '',
            ]);

            // Verify it was saved
            if ($network && $network->id) {
                // Also update session for backward compatibility
                $networks[] = $newNetwork;
                session()->put('networks', $networks);
                session()->save();

                // Return JSON response for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Network created successfully!',
                        'redirect' => route('admin.networks.all')
                    ]);
                }

                return redirect()->route('admin.networks.all')->with('success', 'Network created successfully!');
            } else {
                throw new \Exception('Network was not saved to database - no ID returned');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Network creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If database save fails, still save to session as fallback
            $networks[] = $newNetwork;
            session()->put('networks', $networks);
            session()->save();
            
            $errorMessage = 'Network saved to session but database save failed: ' . $e->getMessage();
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            return redirect()->route('admin.networks.all')
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function updateNetwork(Request $request, $id)
    {
        $request->validate([
            'network_name' => 'required|string|max:255',
            'network_type' => 'required|in:Domestic,International',
            'opening_balance' => 'required|numeric|min:0',
            'bank_details' => 'nullable|string',
            'upi_scanner' => 'nullable|string',
            'upi_scanner_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Convert checkbox to status string
        // Checkbox sends "1", "on", or true when checked, "0" or nothing when unchecked
        $statusValue = $request->input('status');
        // Handle various checkbox values: "1", "on", true, 1 = Active; "0", false, 0, null = Inactive
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';

        try {
            // Find network in database
            $network = Network::findOrFail($id);
            
            // Keep opening balance fixed - do not update it
            $openingBalance = $network->opening_balance;
            
            // Handle UPI Scanner - file upload or text
            $upiScanner = $network->upi_scanner ?? ''; // Keep existing if not updated
            
            if ($request->hasFile('upi_scanner_file')) {
                // Delete old file if exists
                if (!empty($network->upi_scanner) && str_starts_with($network->upi_scanner, 'storage/')) {
                    $oldFilePath = str_replace('storage/', 'public/', $network->upi_scanner);
                    if (\Storage::exists($oldFilePath)) {
                        \Storage::delete($oldFilePath);
                    }
                }
                
                // Upload new file
                $file = $request->file('upi_scanner_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('public/upi_scanners', $fileName);
                $upiScanner = 'storage/upi_scanners/' . $fileName;
            } elseif ($request->filled('upi_scanner')) {
                // Use text input - delete old file if it was a file
                if (!empty($network->upi_scanner) && str_starts_with($network->upi_scanner, 'storage/')) {
                    $oldFilePath = str_replace('storage/', 'public/', $network->upi_scanner);
                    if (\Storage::exists($oldFilePath)) {
                        \Storage::delete($oldFilePath);
                    }
                }
                $upiScanner = $request->upi_scanner;
            }
            
            // Update network in database (opening balance is kept fixed)
            $network->update([
                'name' => $request->network_name,
                'type' => $request->network_type,
                'opening_balance' => $openingBalance, // Keep original opening balance
                'status' => $status,
                'bank_details' => $request->bank_details ?? '',
                'upi_scanner' => $upiScanner,
                'remark' => $request->remark ?? '',
            ]);
            
            // Also update session for backward compatibility
            $networks = $this->getNetworks();
            if (!is_array($networks)) {
                $networks = [];
            }
            
            $networks = array_map(function($n) use ($id, $request, $status, $openingBalance, $upiScanner) {
                if ($n['id'] == $id) {
                    return [
                        'id' => $id,
                        'name' => $request->network_name,
                        'type' => $request->network_type,
                        'opening_balance' => $openingBalance, // Keep original opening balance
                        'status' => $status,
                        'bank_details' => $request->bank_details ?? '',
                        'upi_scanner' => $upiScanner,
                        'remark' => $request->remark ?? '',
                    ];
                }
                return $n;
            }, $networks);
            
            session(['networks' => array_values($networks)]);
            session()->save();
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Network updated successfully!',
                    'redirect' => route('admin.networks.all')
                ]);
            }
            
            return redirect()->route('admin.networks.all')->with('success', 'Network updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Network update failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Error updating network: ' . $e->getMessage();
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function toggleNetworkStatus($id)
    {
        try {
            // Find network in database
            $network = Network::findOrFail($id);
            
            // Toggle status between Active and Inactive
            $network->status = $network->status === 'Active' ? 'Inactive' : 'Active';
            $network->save();
            
            // Also update session for backward compatibility
            $networks = $this->getNetworks();
            if (is_array($networks)) {
                $networks = array_map(function($n) use ($id, $network) {
                    if ($n['id'] == $id) {
                        $n['status'] = $network->status;
                    }
                    return $n;
                }, $networks);
                session(['networks' => array_values($networks)]);
                session()->save();
            }
            
            return response()->json([
                'success' => true,
                'status' => $network->status,
                'message' => 'Network status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Network status toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating network status: ' . $e->getMessage()
            ], 422);
        }
    }

    public function deleteNetwork($id)
    {
        try {
            // Try to delete from database first
            $deleted = Network::where('id', $id)->delete();
            
            if ($deleted) {
                // Also remove from session
                $networks = $this->getNetworks();
                if (!is_array($networks)) {
                    $networks = [];
                }
                
                $networks = array_filter($networks, function($network) use ($id) {
                    return $network['id'] != $id;
                });
                
                session(['networks' => array_values($networks)]);
                session()->save();
                
                return redirect()->route('admin.networks.all')->with('success', 'Network deleted successfully!');
            }
            
            // Fallback to session-based deletion
            $networks = $this->getNetworks();
            if (!is_array($networks)) {
                $networks = [];
            }
            
            $networks = array_filter($networks, function($network) use ($id) {
                return $network['id'] != $id;
            });
            
            session(['networks' => array_values($networks)]);
            session()->save();

            return redirect()->route('admin.networks.all')->with('success', 'Network deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting network: ' . $e->getMessage());
        }
    }

    // Services Management
    private function getServices()
    {
        if (session()->has('services') && !empty(session('services'))) {
            return session('services');
        }
        
        // Initialize default services if none exist
        $this->initializeDefaultServices();
        return session('services');
    }
    
    private function initializeDefaultServices()
    {
        $defaultServices = [
            [
                'id' => 1,
                'name' => 'Express',
                'network' => 'DTDC',
                'transit_time' => '24-48 Hours',
                'items_allowed' => 'Documents, Small Packages',
                'status' => 'Active',
                'remark' => 'Fast delivery service',
                'display_title' => 'E-Commerce delivery',
                'description' => 'Fast, reliable delivery solutions designed for online stores to ensure smooth order fulfillment and on-time customer satisfaction.',
                'icon_type' => 'truck',
                'is_highlighted' => false,
            ],
            [
                'id' => 2,
                'name' => 'Economy',
                'network' => 'FedEx',
                'transit_time' => '5-7 Days',
                'items_allowed' => 'All Items',
                'status' => 'Active',
                'remark' => 'Cost-effective shipping',
                'display_title' => 'Pick & Drop',
                'description' => 'Flexible pickup and drop services that make parcel movement easy, efficient, and trackable from start to finish.',
                'icon_type' => 'pickup',
                'is_highlighted' => false,
            ],
            [
                'id' => 3,
                'name' => 'Packaging',
                'network' => 'DTDC',
                'transit_time' => 'Standard',
                'items_allowed' => 'All Items',
                'status' => 'Active',
                'remark' => 'Professional packaging service',
                'display_title' => 'Packaging',
                'description' => 'Secure, professional packaging that keeps every product safe, protected, and presentable throughout transit.',
                'icon_type' => 'package',
                'is_highlighted' => false,
            ],
            [
                'id' => 4,
                'name' => 'Warehousing',
                'network' => 'DTDC',
                'transit_time' => 'Standard',
                'items_allowed' => 'Bulk Goods',
                'status' => 'Active',
                'remark' => 'Smart storage solutions',
                'display_title' => 'Warehousing',
                'description' => 'Smart storage and inventory management with real-time tracking to handle bulk goods efficiently and cost-effectively.',
                'icon_type' => 'warehouse',
                'is_highlighted' => true,
            ],
        ];
        
        session(['services' => $defaultServices]);
        session()->save();
    }

    public function services()
    {
        return redirect()->route('admin.services.create');
    }

    public function createService()
    {
        $services = $this->getServices();
        $networks = $this->getNetworks();
        return view('admin.services.create', [
            'services' => $services,
            'networks' => $networks,
        ]);
    }

    public function allServices(Request $request)
    {
        $services = $this->getServices();
        $networks = $this->getNetworks();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $services = array_filter($services, function($service) use ($searchTerm) {
                return strpos(strtolower($service['name'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply network filter
        if ($request->filled('network')) {
            $networkFilter = $request->network;
            $services = array_filter($services, function($service) use ($networkFilter) {
                return ($service['network'] ?? '') == $networkFilter;
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $services = array_filter($services, function($service) use ($statusFilter) {
                return ($service['status'] ?? '') == $statusFilter;
            });
        }
        
        // Re-index array after filtering
        $services = array_values($services);
        
        return view('admin.services.all', [
            'services' => $services,
            'networks' => $networks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'network' => $request->network ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    public function editService($id)
    {
        $services = $this->getServices();
        $service = collect($services)->firstWhere('id', $id);
        $networks = $this->getNetworks();
        
        if (!$service) {
            return redirect()->route('admin.services.all')->with('error', 'Service not found');
        }

        return view('admin.services.edit', [
            'service' => $service,
            'services' => $services,
            'networks' => $networks,
        ]);
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'transit_time' => 'required|string|max:255',
            'items_allowed' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'status' => 'nullable',
            'display_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon_type' => 'nullable|string|max:255',
            'is_highlighted' => 'nullable',
        ]);

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';
        
        // Handle is_highlighted checkbox
        $isHighlightedValue = $request->input('is_highlighted');
        $isHighlighted = ($isHighlightedValue === '1' || $isHighlightedValue === 'on' || $isHighlightedValue === true || $isHighlightedValue === 1);

        $services = $this->getServices();
        if (!is_array($services)) {
            $services = [];
        }
        
        $newId = count($services) > 0 ? max(array_column($services, 'id')) + 1 : 1;
        
        $newService = [
            'id' => $newId,
            'name' => $request->service_name,
            'network' => $request->network,
            'transit_time' => $request->transit_time,
            'items_allowed' => $request->items_allowed,
            'status' => $status,
            'remark' => $request->remark ?? '',
            'display_title' => $request->display_title ?? $request->service_name,
            'description' => $request->description ?? '',
            'icon_type' => $request->icon_type ?? 'truck',
            'is_highlighted' => $isHighlighted,
        ];
        
        $services[] = $newService;
        session(['services' => $services]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service created successfully!',
                'redirect' => route('admin.services.all')
            ]);
        }

        return redirect()->route('admin.services.all')->with('success', 'Service created successfully!');
    }

    public function updateService(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'transit_time' => 'required|string|max:255',
            'items_allowed' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'status' => 'nullable',
            'display_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon_type' => 'nullable|string|max:255',
            'is_highlighted' => 'nullable',
        ]);

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';
        
        // Handle is_highlighted checkbox
        $isHighlightedValue = $request->input('is_highlighted');
        $isHighlighted = ($isHighlightedValue === '1' || $isHighlightedValue === 'on' || $isHighlightedValue === true || $isHighlightedValue === 1);

        $services = $this->getServices();
        if (!is_array($services)) {
            $services = [];
        }
        
        $services = array_map(function($service) use ($id, $request, $status, $isHighlighted) {
            if ($service['id'] == $id) {
                return [
                    'id' => $id,
                    'name' => $request->service_name,
                    'network' => $request->network,
                    'transit_time' => $request->transit_time,
                    'items_allowed' => $request->items_allowed,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                    'display_title' => $request->display_title ?? $request->service_name,
                    'description' => $request->description ?? ($service['description'] ?? ''),
                    'icon_type' => $request->icon_type ?? ($service['icon_type'] ?? 'truck'),
                    'is_highlighted' => $isHighlighted,
                ];
            }
            return $service;
        }, $services);
        
        session(['services' => array_values($services)]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully!',
                'redirect' => route('admin.services.all')
            ]);
        }

        return redirect()->route('admin.services.all')->with('success', 'Service updated successfully!');
    }

    public function toggleServiceStatus($id)
    {
        try {
            // Get services from session
            $services = $this->getServices();
            if (!is_array($services)) {
                $services = [];
            }
            
            // Find the service by ID
            $serviceFound = false;
            $services = array_map(function($service) use ($id, &$serviceFound) {
                if ($service['id'] == $id) {
                    $serviceFound = true;
                    // Toggle status between Active and Inactive
                    $service['status'] = ($service['status'] === 'Active') ? 'Inactive' : 'Active';
                }
                return $service;
            }, $services);
            
            if (!$serviceFound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }
            
            // Save back to session
            session(['services' => array_values($services)]);
            session()->save();
            
            // Get the updated service status
            $updatedService = collect($services)->firstWhere('id', $id);
            
            return response()->json([
                'success' => true,
                'status' => $updatedService['status'],
                'message' => 'Service status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Service status toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating service status: ' . $e->getMessage()
            ], 422);
        }
    }

    public function deleteService($id)
    {
        $services = $this->getServices();
        if (!is_array($services)) {
            $services = [];
        }
        
        $services = array_filter($services, function($service) use ($id) {
            return $service['id'] != $id;
        });
        
        session(['services' => array_values($services)]);
        session()->save();
        
        return redirect()->route('admin.services.all')->with('success', 'Service deleted successfully!');
    }

    /**
     * Bulk delete services
     */
    public function bulkDeleteServices(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $services = $this->getServices();
            if (!is_array($services)) {
                $services = [];
            }
            
            // Filter out selected services
            $services = array_filter($services, function($service) use ($ids) {
                return !in_array($service['id'], $ids);
            });
            
            session(['services' => array_values($services)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.services.all')
                ->with('success', "Successfully deleted {$deletedCount} service(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting services: ' . $e->getMessage());
        }
    }

    // Countries Management
    private function getCountries()
    {
        if (session()->has('countries')) {
            return session('countries');
        }
        
        $defaultCountries = [
            [
                'id' => 1,
                'name' => 'India',
                'code' => 'IN',
                'isd_no' => '+91',
                'dialing_code' => '0091',
                'status' => 'Active',
                'remark' => 'Primary country',
            ],
            [
                'id' => 2,
                'name' => 'United States',
                'code' => 'US',
                'isd_no' => '+1',
                'dialing_code' => '001',
                'status' => 'Active',
                'remark' => 'International shipping',
            ],
        ];
        
        session(['countries' => $defaultCountries]);
        return $defaultCountries;
    }

    public function countries()
    {
        return redirect()->route('admin.countries.create');
    }

    public function createCountry()
    {
        $countries = $this->getCountries();
        return view('admin.countries.create', [
            'countries' => $countries,
        ]);
    }

    public function allCountries(Request $request)
    {
        $countries = $this->getCountries();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $countries = array_filter($countries, function($country) use ($searchTerm) {
                return strpos(strtolower($country['name'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $countries = array_filter($countries, function($country) use ($statusFilter) {
                return ($country['status'] ?? '') == $statusFilter;
            });
        }
        
        // Re-index array after filtering
        $countries = array_values($countries);
        
        return view('admin.countries.all', [
            'countries' => $countries,
            'searchParams' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    public function editCountry($id)
    {
        $countries = $this->getCountries();
        $country = collect($countries)->firstWhere('id', $id);
        
        if (!$country) {
            return redirect()->route('admin.countries.all')->with('error', 'Country not found');
        }

        return view('admin.countries.edit', [
            'country' => $country,
            'countries' => $countries,
        ]);
    }

    public function storeCountry(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'country_isd_no' => 'required|string|max:10',
            'country_dialing_code' => 'nullable|string|max:10',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';

        $countries = $this->getCountries();
        if (!is_array($countries)) {
            $countries = [];
        }
        
        $newId = count($countries) > 0 ? max(array_column($countries, 'id')) + 1 : 1;
        
        $newCountry = [
            'id' => $newId,
            'name' => $request->country_name,
            'code' => $request->country_code,
            'isd_no' => $request->country_isd_no,
            'dialing_code' => $request->country_dialing_code ?? '',
            'status' => $status,
            'remark' => $request->remark ?? '',
        ];
        
        $countries[] = $newCountry;
        session(['countries' => $countries]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Country created successfully!',
                'redirect' => route('admin.countries.all')
            ]);
        }

        return redirect()->route('admin.countries.all')->with('success', 'Country created successfully!');
    }

    public function updateCountry(Request $request, $id)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'country_isd_no' => 'required|string|max:10',
            'country_dialing_code' => 'nullable|string|max:10',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';

        $countries = $this->getCountries();
        if (!is_array($countries)) {
            $countries = [];
        }
        
        $countries = array_map(function($country) use ($id, $request, $status) {
            if ($country['id'] == $id) {
                return [
                    'id' => $id,
                    'name' => $request->country_name,
                    'code' => $request->country_code,
                    'isd_no' => $request->country_isd_no,
                    'dialing_code' => $request->country_dialing_code ?? '',
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                ];
            }
            return $country;
        }, $countries);
        
        session(['countries' => array_values($countries)]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Country updated successfully!',
                'redirect' => route('admin.countries.all')
            ]);
        }

        return redirect()->route('admin.countries.all')->with('success', 'Country updated successfully!');
    }

    public function toggleCountryStatus($id)
    {
        try {
            // Get countries from session
            $countries = $this->getCountries();
            if (!is_array($countries)) {
                $countries = [];
            }
            
            // Find the country by ID
            $countryFound = false;
            $countries = array_map(function($country) use ($id, &$countryFound) {
                if ($country['id'] == $id) {
                    $countryFound = true;
                    // Toggle status between Active and Inactive
                    $country['status'] = ($country['status'] === 'Active') ? 'Inactive' : 'Active';
                }
                return $country;
            }, $countries);
            
            if (!$countryFound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country not found'
                ], 404);
            }
            
            // Save back to session
            session(['countries' => array_values($countries)]);
            session()->save();
            
            // Get the updated country status
            $updatedCountry = collect($countries)->firstWhere('id', $id);
            
            return response()->json([
                'success' => true,
                'status' => $updatedCountry['status'],
                'message' => 'Country status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Country status toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating country status: ' . $e->getMessage()
            ], 422);
        }
    }

    public function deleteCountry($id)
    {
        $countries = $this->getCountries();
        if (!is_array($countries)) {
            $countries = [];
        }
        
        $countries = array_filter($countries, function($country) use ($id) {
            return $country['id'] != $id;
        });
        
        session(['countries' => array_values($countries)]);
        session()->save();
        
        return redirect()->route('admin.countries.all')->with('success', 'Country deleted successfully!');
    }

    /**
     * Bulk delete countries
     */
    public function bulkDeleteCountries(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $countries = $this->getCountries();
            if (!is_array($countries)) {
                $countries = [];
            }
            
            // Filter out selected countries
            $countries = array_filter($countries, function($country) use ($ids) {
                return !in_array($country['id'], $ids);
            });
            
            session(['countries' => array_values($countries)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.countries.all')
                ->with('success', "Successfully deleted {$deletedCount} country/countries.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting countries: ' . $e->getMessage());
        }
    }

    // Zones Management
    private function getZones()
    {
        if (session()->has('zones')) {
            return session('zones');
        }
        
        $defaultZones = [
            [
                'id' => 1,
                'pincode' => '110001',
                'country' => 'India',
                'zone' => 'Zone 1',
                'network' => 'DTDC',
                'service' => 'Express',
                'status' => 'Active',
                'remark' => 'Delhi zone',
            ],
            [
                'id' => 2,
                'pincode' => '400001',
                'country' => 'India',
                'zone' => 'Zone 2',
                'network' => 'Blue Dart',
                'service' => 'Economy',
                'status' => 'Active',
                'remark' => 'Mumbai zone',
            ],
        ];
        
        session(['zones' => $defaultZones]);
        return $defaultZones;
    }

    private function getZoneOptions()
    {
        return [
            'No Zone',
            'Remote',
        ] + array_map(function($i) {
            return "Zone {$i}";
        }, range(1, 60));
    }

    public function zones()
    {
        return redirect()->route('admin.zones.create');
    }

    public function createZone()
    {
        $zones = $this->getZones();
        $countries = $this->getCountries();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $zoneOptions = $this->getZoneOptions();
        return view('admin.zones.create', [
            'zones' => $zones,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
            'zoneOptions' => $zoneOptions,
        ]);
    }

    public function allZones(Request $request)
    {
        $zones = $this->getZones();
        $networks = $this->getNetworks();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $zones = array_filter($zones, function($zone) use ($searchTerm) {
                return strpos(strtolower($zone['pincode'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply network filter
        if ($request->filled('network')) {
            $networkFilter = $request->network;
            $zones = array_filter($zones, function($zone) use ($networkFilter) {
                return ($zone['network'] ?? '') == $networkFilter;
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $zones = array_filter($zones, function($zone) use ($statusFilter) {
                return ($zone['status'] ?? '') == $statusFilter;
            });
        }
        
        // Re-index array after filtering
        $zones = array_values($zones);
        
        return view('admin.zones.all', [
            'zones' => $zones,
            'networks' => $networks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'network' => $request->network ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    public function editZone($id)
    {
        $zones = $this->getZones();
        $zone = collect($zones)->firstWhere('id', $id);
        $countries = $this->getCountries();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $zoneOptions = $this->getZoneOptions();
        
        if (!$zone) {
            return redirect()->route('admin.zones.all')->with('error', 'Zone not found');
        }

        return view('admin.zones.edit', [
            'zone' => $zone,
            'zones' => $zones,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
            'zoneOptions' => $zoneOptions,
        ]);
    }

    public function storeZone(Request $request)
    {
        $request->validate([
            'pincode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'zone' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';

        $zones = $this->getZones();
        if (!is_array($zones)) {
            $zones = [];
        }
        
        $newId = count($zones) > 0 ? max(array_column($zones, 'id')) + 1 : 1;
        
        $newZone = [
            'id' => $newId,
            'pincode' => $request->pincode,
            'country' => $request->country,
            'zone' => $request->zone,
            'network' => $request->network,
            'service' => $request->service,
            'status' => $status,
            'remark' => $request->remark ?? '',
        ];
        
        $zones[] = $newZone;
        session(['zones' => $zones]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Zone created successfully!',
                'redirect' => route('admin.zones.all')
            ]);
        }

        return redirect()->route('admin.zones.all')->with('success', 'Zone created successfully!');
    }

    public function updateZone(Request $request, $id)
    {
        $request->validate([
            'pincode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'zone' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';

        $zones = $this->getZones();
        if (!is_array($zones)) {
            $zones = [];
        }
        
        $zones = array_map(function($zone) use ($id, $request, $status) {
            if ($zone['id'] == $id) {
                return [
                    'id' => $id,
                    'pincode' => $request->pincode,
                    'country' => $request->country,
                    'zone' => $request->zone,
                    'network' => $request->network,
                    'service' => $request->service,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                ];
            }
            return $zone;
        }, $zones);
        
        session(['zones' => array_values($zones)]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Zone updated successfully!',
                'redirect' => route('admin.zones.all')
            ]);
        }

        return redirect()->route('admin.zones.all')->with('success', 'Zone updated successfully!');
    }

    public function deleteZone($id)
    {
        $zones = $this->getZones();
        if (!is_array($zones)) {
            $zones = [];
        }
        
        $zones = array_filter($zones, function($zone) use ($id) {
            return $zone['id'] != $id;
        });
        
        session(['zones' => array_values($zones)]);
        session()->save();
        
        return redirect()->route('admin.zones.all')->with('success', 'Zone deleted successfully!');
    }

    /**
     * Bulk delete zones
     */
    public function bulkDeleteZones(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $zones = $this->getZones();
            if (!is_array($zones)) {
                $zones = [];
            }
            
            // Filter out selected zones
            $zones = array_filter($zones, function($zone) use ($ids) {
                return !in_array($zone['id'], $ids);
            });
            
            session(['zones' => array_values($zones)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.zones.all')
                ->with('success', "Successfully deleted {$deletedCount} zone(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting zones: ' . $e->getMessage());
        }
    }

    // Shipping Charges Management
    private function getShippingCharges()
    {
        if (session()->has('shipping_charges')) {
            return session('shipping_charges');
        }
        
        $defaultCharges = [
            [
                'id' => 1,
                'origin' => 'India',
                'origin_zone' => 'Zone 1',
                'destination' => 'India',
                'destination_zone' => 'Zone 2',
                'shipment_type' => 'Dox',
                'min_weight' => 0.01,
                'max_weight' => 5.0,
                'network' => 'DTDC',
                'service' => 'Express',
                'rate' => 100.00,
                'remark' => 'Delhi to Mumbai',
            ],
            [
                'id' => 2,
                'origin' => 'India',
                'origin_zone' => 'Zone 2',
                'destination' => 'United States',
                'destination_zone' => 'Remote',
                'shipment_type' => 'Non-Dox',
                'min_weight' => 5.0,
                'max_weight' => 10.0,
                'network' => 'Blue Dart',
                'service' => 'Economy',
                'rate' => 500.00,
                'remark' => 'Mumbai to US',
            ],
        ];
        
        session(['shipping_charges' => $defaultCharges]);
        return $defaultCharges;
    }

    private function getShipmentTypes()
    {
        return ['Dox', 'Non-Dox', 'Medicine'];
    }

    public function shippingCharges()
    {
        return redirect()->route('admin.shipping-charges.create');
    }

    public function createShippingCharge()
    {
        $shippingCharges = $this->getShippingCharges();
        $countries = $this->getCountries();
        $zones = $this->getZones();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $shipmentTypes = $this->getShipmentTypes();
        
        // Get unique zones from zones data
        $zoneOptions = collect($zones)->pluck('zone')->unique()->sort()->values()->toArray();
        if (!in_array('No Zone', $zoneOptions)) {
            array_unshift($zoneOptions, 'No Zone');
        }
        if (!in_array('Remote', $zoneOptions)) {
            $zoneOptions[] = 'Remote';
        }
        
        return view('admin.shipping-charges.create', [
            'shippingCharges' => $shippingCharges,
            'countries' => $countries,
            'zoneOptions' => $zoneOptions,
            'networks' => $networks,
            'services' => $services,
            'shipmentTypes' => $shipmentTypes,
        ]);
    }

    public function allShippingCharges(Request $request)
    {
        $shippingCharges = $this->getShippingCharges();
        $networks = $this->getNetworks();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $shippingCharges = array_filter($shippingCharges, function($charge) use ($searchTerm) {
                return strpos(strtolower($charge['origin'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($charge['destination'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply network filter
        if ($request->filled('network')) {
            $networkFilter = $request->network;
            $shippingCharges = array_filter($shippingCharges, function($charge) use ($networkFilter) {
                return ($charge['network'] ?? '') == $networkFilter;
            });
        }
        
        // Apply shipment type filter
        if ($request->filled('shipment_type')) {
            $typeFilter = $request->shipment_type;
            $shippingCharges = array_filter($shippingCharges, function($charge) use ($typeFilter) {
                return ($charge['shipment_type'] ?? '') == $typeFilter;
            });
        }
        
        // Re-index array after filtering
        $shippingCharges = array_values($shippingCharges);
        
        return view('admin.shipping-charges.all', [
            'shippingCharges' => $shippingCharges,
            'networks' => $networks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'network' => $request->network ?? '',
                'shipment_type' => $request->shipment_type ?? '',
            ],
        ]);
    }

    public function editShippingCharge($id)
    {
        $shippingCharges = $this->getShippingCharges();
        $charge = collect($shippingCharges)->firstWhere('id', $id);
        $countries = $this->getCountries();
        $zones = $this->getZones();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $shipmentTypes = $this->getShipmentTypes();
        
        // Get unique zones from zones data
        $zoneOptions = collect($zones)->pluck('zone')->unique()->sort()->values()->toArray();
        if (!in_array('No Zone', $zoneOptions)) {
            array_unshift($zoneOptions, 'No Zone');
        }
        if (!in_array('Remote', $zoneOptions)) {
            $zoneOptions[] = 'Remote';
        }
        
        if (!$charge) {
            return redirect()->route('admin.shipping-charges.all')->with('error', 'Shipping charge not found');
        }

        return view('admin.shipping-charges.edit', [
            'charge' => $charge,
            'shippingCharges' => $shippingCharges,
            'countries' => $countries,
            'zoneOptions' => $zoneOptions,
            'networks' => $networks,
            'services' => $services,
            'shipmentTypes' => $shipmentTypes,
        ]);
    }

    public function storeShippingCharge(Request $request)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'origin_zone' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'destination_zone' => 'required|string|max:255',
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Medicine',
            'min_weight' => 'required|numeric|min:0.01',
            'max_weight' => 'required|numeric|gt:min_weight',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
        ]);

        $shippingCharges = $this->getShippingCharges();
        if (!is_array($shippingCharges)) {
            $shippingCharges = [];
        }
        
        $newId = count($shippingCharges) > 0 ? max(array_column($shippingCharges, 'id')) + 1 : 1;
        
        $newCharge = [
            'id' => $newId,
            'origin' => $request->origin,
            'origin_zone' => $request->origin_zone,
            'destination' => $request->destination,
            'destination_zone' => $request->destination_zone,
            'shipment_type' => $request->shipment_type,
            'min_weight' => $request->min_weight,
            'max_weight' => $request->max_weight,
            'network' => $request->network,
            'service' => $request->service,
            'rate' => $request->rate,
            'remark' => $request->remark ?? '',
        ];
        
        $shippingCharges[] = $newCharge;
        session(['shipping_charges' => $shippingCharges]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipping charge created successfully!',
                'redirect' => route('admin.shipping-charges.all')
            ]);
        }

        return redirect()->route('admin.shipping-charges.all')->with('success', 'Shipping charge created successfully!');
    }

    public function updateShippingCharge(Request $request, $id)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'origin_zone' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'destination_zone' => 'required|string|max:255',
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Medicine',
            'min_weight' => 'required|numeric|min:0.01',
            'max_weight' => 'required|numeric|gt:min_weight',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
        ]);

        $shippingCharges = $this->getShippingCharges();
        if (!is_array($shippingCharges)) {
            $shippingCharges = [];
        }
        
        $shippingCharges = array_map(function($charge) use ($id, $request) {
            if ($charge['id'] == $id) {
                return [
                    'id' => $id,
                    'origin' => $request->origin,
                    'origin_zone' => $request->origin_zone,
                    'destination' => $request->destination,
                    'destination_zone' => $request->destination_zone,
                    'shipment_type' => $request->shipment_type,
                    'min_weight' => $request->min_weight,
                    'max_weight' => $request->max_weight,
                    'network' => $request->network,
                    'service' => $request->service,
                    'rate' => $request->rate,
                    'remark' => $request->remark ?? '',
                ];
            }
            return $charge;
        }, $shippingCharges);
        
        session(['shipping_charges' => array_values($shippingCharges)]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipping charge updated successfully!',
                'redirect' => route('admin.shipping-charges.all')
            ]);
        }

        return redirect()->route('admin.shipping-charges.all')->with('success', 'Shipping charge updated successfully!');
    }

    public function deleteShippingCharge($id)
    {
        $shippingCharges = $this->getShippingCharges();
        if (!is_array($shippingCharges)) {
            $shippingCharges = [];
        }
        
        $shippingCharges = array_filter($shippingCharges, function($charge) use ($id) {
            return $charge['id'] != $id;
        });
        
        session(['shipping_charges' => array_values($shippingCharges)]);
        session()->save();
        
        return redirect()->route('admin.shipping-charges.all')->with('success', 'Shipping charge deleted successfully!');
    }

    /**
     * Bulk delete shipping charges
     */
    public function bulkDeleteShippingCharges(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $shippingCharges = $this->getShippingCharges();
            if (!is_array($shippingCharges)) {
                $shippingCharges = [];
            }
            
            // Filter out selected shipping charges
            $shippingCharges = array_filter($shippingCharges, function($charge) use ($ids) {
                return !in_array($charge['id'], $ids);
            });
            
            session(['shipping_charges' => array_values($shippingCharges)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.shipping-charges.all')
                ->with('success', "Successfully deleted {$deletedCount} shipping charge(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting shipping charges: ' . $e->getMessage());
        }
    }

    // Formulas Management
    private function getFormulas()
    {
        if (session()->has('formulas')) {
            return session('formulas');
        }
        
        $defaultFormulas = [
            [
                'id' => 1,
                'formula_name' => 'Express Delivery Fee',
                'network' => 'DTDC',
                'service' => 'Express',
                'type' => 'Fixed',
                'scope' => 'Flat',
                'priority' => '1st',
                'value' => 50.00,
                'status' => 'Active',
                'remark' => 'Fixed fee for express delivery',
            ],
            [
                'id' => 2,
                'formula_name' => 'Weight Based Charge',
                'network' => 'Blue Dart',
                'service' => 'Economy',
                'type' => 'Percentage',
                'scope' => 'per kg',
                'priority' => '2nd',
                'value' => 10.5,
                'status' => 'Active',
                'remark' => '10.5% per kg',
            ],
        ];
        
        session(['formulas' => $defaultFormulas]);
        return $defaultFormulas;
    }

    private function getFormulaTypes()
    {
        return ['Fixed', 'Percentage'];
    }

    private function getFormulaScopes()
    {
        return ['per kg', 'Flat'];
    }

    private function getFormulaPriorities()
    {
        return ['1st', '2nd', '3rd', '4th'];
    }

    public function formulas()
    {
        return redirect()->route('admin.formulas.create');
    }

    public function createFormula()
    {
        $formulas = $this->getFormulas();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $types = $this->getFormulaTypes();
        $scopes = $this->getFormulaScopes();
        $priorities = $this->getFormulaPriorities();
        return view('admin.formulas.create', [
            'formulas' => $formulas,
            'networks' => $networks,
            'services' => $services,
            'types' => $types,
            'scopes' => $scopes,
            'priorities' => $priorities,
        ]);
    }

    public function allFormulas(Request $request)
    {
        $formulas = $this->getFormulas();
        
        // Get networks - prioritize database, fallback to session
        $dbNetworks = Network::all();
        $networks = [];
        if ($dbNetworks->isNotEmpty()) {
            $networks = $dbNetworks->map(function($network) {
                return [
                    'id' => $network->id,
                    'name' => $network->name,
                    'type' => $network->type,
                    'status' => $network->status,
                ];
            })->toArray();
        } else {
            // Fallback to session networks
            $networks = $this->getNetworks();
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $formulas = array_filter($formulas, function($formula) use ($searchTerm) {
                return strpos(strtolower($formula['formula_name'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($formula['network'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($formula['service'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply network filter
        if ($request->filled('network')) {
            $networkFilter = $request->network;
            $formulas = array_filter($formulas, function($formula) use ($networkFilter) {
                return ($formula['network'] ?? '') == $networkFilter;
            });
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $typeFilter = $request->type;
            $formulas = array_filter($formulas, function($formula) use ($typeFilter) {
                return ($formula['type'] ?? '') == $typeFilter;
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $formulas = array_filter($formulas, function($formula) use ($statusFilter) {
                return ($formula['status'] ?? '') == $statusFilter;
            });
        }
        
        // Re-index array after filtering
        $formulas = array_values($formulas);
        
        return view('admin.formulas.all', [
            'formulas' => $formulas,
            'networks' => $networks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'network' => $request->network ?? '',
                'type' => $request->type ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    public function editFormula($id)
    {
        $formulas = $this->getFormulas();
        $formula = collect($formulas)->firstWhere('id', $id);
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $types = $this->getFormulaTypes();
        $scopes = $this->getFormulaScopes();
        $priorities = $this->getFormulaPriorities();
        
        if (!$formula) {
            return redirect()->route('admin.formulas.all')->with('error', 'Formula not found');
        }

        return view('admin.formulas.edit', [
            'formula' => $formula,
            'formulas' => $formulas,
            'networks' => $networks,
            'services' => $services,
            'types' => $types,
            'scopes' => $scopes,
            'priorities' => $priorities,
        ]);
    }

    public function storeFormula(Request $request)
    {
        $request->validate([
            'formula_name' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'type' => 'required|string|in:Fixed,Percentage',
            'scope' => 'required|string|in:per kg,Flat',
            'priority' => 'required|string|in:1st,2nd,3rd,4th',
            'value' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $status = $request->has('status') && $request->status ? 'Active' : 'Inactive';

        $formulas = $this->getFormulas();
        if (!is_array($formulas)) {
            $formulas = [];
        }
        
        $newId = count($formulas) > 0 ? max(array_column($formulas, 'id')) + 1 : 1;
        
        $newFormula = [
            'id' => $newId,
            'formula_name' => $request->formula_name,
            'network' => $request->network,
            'service' => $request->service,
            'type' => $request->type,
            'scope' => $request->scope,
            'priority' => $request->priority,
            'value' => $request->value,
            'status' => $status,
            'remark' => $request->remark ?? '',
        ];
        
        $formulas[] = $newFormula;
        session(['formulas' => $formulas]);
        session()->save();

        return redirect()->route('admin.formulas.all')->with('success', 'Formula created successfully!');
    }

    public function updateFormula(Request $request, $id)
    {
        $request->validate([
            'formula_name' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'type' => 'required|string|in:Fixed,Percentage',
            'scope' => 'required|string|in:per kg,Flat',
            'priority' => 'required|string|in:1st,2nd,3rd,4th',
            'value' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $status = $request->has('status') && $request->status ? 'Active' : 'Inactive';

        $formulas = $this->getFormulas();
        if (!is_array($formulas)) {
            $formulas = [];
        }
        
        $formulas = array_map(function($formula) use ($id, $request, $status) {
            if ($formula['id'] == $id) {
                return [
                    'id' => $id,
                    'formula_name' => $request->formula_name,
                    'network' => $request->network,
                    'service' => $request->service,
                    'type' => $request->type,
                    'scope' => $request->scope,
                    'priority' => $request->priority,
                    'value' => $request->value,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                ];
            }
            return $formula;
        }, $formulas);
        
        session(['formulas' => array_values($formulas)]);
        session()->save();

        return redirect()->route('admin.formulas.all')->with('success', 'Formula updated successfully!');
    }

    public function toggleFormulaStatus($id)
    {
        try {
            // Get formulas from session
            $formulas = $this->getFormulas();
            if (!is_array($formulas)) {
                $formulas = [];
            }
            
            // Find the formula by ID
            $formulaFound = false;
            $formulas = array_map(function($formula) use ($id, &$formulaFound) {
                if ($formula['id'] == $id) {
                    $formulaFound = true;
                    // Toggle status between Active and Inactive
                    $formula['status'] = ($formula['status'] === 'Active') ? 'Inactive' : 'Active';
                }
                return $formula;
            }, $formulas);
            
            if (!$formulaFound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formula not found'
                ], 404);
            }
            
            // Save back to session
            session(['formulas' => array_values($formulas)]);
            session()->save();
            
            // Get the updated formula status
            $updatedFormula = collect($formulas)->firstWhere('id', $id);
            
            return response()->json([
                'success' => true,
                'status' => $updatedFormula['status'],
                'message' => 'Formula status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Formula status toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating formula status: ' . $e->getMessage()
            ], 422);
        }
    }

    public function deleteFormula($id)
    {
        $formulas = $this->getFormulas();
        if (!is_array($formulas)) {
            $formulas = [];
        }
        
        $formulas = array_filter($formulas, function($formula) use ($id) {
            return $formula['id'] != $id;
        });
        
        session(['formulas' => array_values($formulas)]);
        session()->save();

        return redirect()->route('admin.formulas.all')->with('success', 'Formula deleted successfully!');
    }

    /**
     * Bulk delete formulas
     */
    public function bulkDeleteFormulas(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $formulas = $this->getFormulas();
            if (!is_array($formulas)) {
                $formulas = [];
            }
            
            // Filter out selected formulas
            $formulas = array_filter($formulas, function($formula) use ($ids) {
                return !in_array($formula['id'], $ids);
            });
            
            session(['formulas' => array_values($formulas)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.formulas.all')
                ->with('success', "Successfully deleted {$deletedCount} formula(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting formulas: ' . $e->getMessage());
        }
    }

    // Search with AWB Management
    private function getAwbs()
    {
        if (session()->has('awbs')) {
            return session('awbs');
        }
        
        $defaultAwbs = [
            [
                'id' => 1,
                'origin' => 'India',
                'origin_pin' => '110001',
                'destination' => 'United States',
                'destination_pin' => '10001',
                'chr_weight' => 5.5,
                'pieces' => 2,
                'network' => 'DTDC',
                'services' => 'Express',
                'booking_amount' => 1500.00,
                'forwarding_service' => 'FedEx',
                'v_awb' => 'V123456789',
                'f_awb' => 'F987654321',
                'remark_on_booking_time' => 'Urgent delivery required',
                'remark_1' => '',
                'remark_2' => '',
                'remark_3' => '',
                'remark_4' => '',
                'remark_5' => '',
                'remark_6' => '',
                'remark_7' => '',
            ],
        ];
        
        session(['awbs' => $defaultAwbs]);
        return $defaultAwbs;
    }

    public function searchWithAwb()
    {
        return redirect()->route('admin.search-with-awb.search');
    }

    public function searchAWB(Request $request)
    {
        $awbNumber = $request->input('awb_number');
        $awb = null;
        
        if ($awbNumber) {
            $awbUploads = $this->getAwbUploads();
            // Search by AWB number in AWB Upload data
            $awb = collect($awbUploads)->first(function ($item) use ($awbNumber) {
                return strtoupper($item['awb_no']) === strtoupper($awbNumber);
            });
            
            // If AWB found, add to history
            if ($awb) {
                $this->addToHistory($awb);
            }
        }
        
        return view('admin.search-with-awb.search', [
            'awb' => $awb,
            'awbNumber' => $awbNumber ?? '',
        ]);
    }

    public function historyAWB()
    {
        $history = $this->getHistory();
        return view('admin.search-with-awb.history', [
            'history' => $history,
        ]);
    }

    private function addToHistory($awb)
    {
        $history = $this->getHistory();
        if (!is_array($history)) {
            $history = [];
        }
        
        // Check if AWB already exists in history
        $exists = collect($history)->first(function ($item) use ($awb) {
            return $item['id'] == $awb['id'];
        });
        
        if (!$exists) {
            // Add timestamp to the AWB
            $awbWithTimestamp = array_merge($awb, [
                'viewed_at' => now()->toDateTimeString(),
            ]);
            // Add to beginning of array (most recent first)
            array_unshift($history, $awbWithTimestamp);
            // Keep only last 50 items
            $history = array_slice($history, 0, 50);
            session(['awb_history' => $history]);
            session()->save();
        } else {
            // Update timestamp if exists
            $history = array_map(function ($item) use ($awb) {
                if ($item['id'] == $awb['id']) {
                    $item['viewed_at'] = now()->toDateTimeString();
                }
                return $item;
            }, $history);
            // Sort by viewed_at descending
            usort($history, function ($a, $b) {
                return strtotime($b['viewed_at'] ?? '') - strtotime($a['viewed_at'] ?? '');
            });
            session(['awb_history' => $history]);
            session()->save();
        }
    }

    private function getHistory()
    {
        if (session()->has('awb_history')) {
            return session('awb_history');
        }
        return [];
    }

    public function editAwb($id)
    {
        $awbs = $this->getAwbs();
        $awb = collect($awbs)->firstWhere('id', $id);
        $countries = $this->getCountries();
        $zones = $this->getZones();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        
        if (!$awb) {
            return redirect()->route('admin.search-with-awb.all')->with('error', 'AWB not found');
        }

        return view('admin.search-with-awb.edit', [
            'awb' => $awb,
            'awbs' => $awbs,
            'countries' => $countries,
            'zones' => $zones,
            'networks' => $networks,
            'services' => $services,
        ]);
    }

    public function storeAwb(Request $request)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'origin_pin' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_pin' => 'required|string|max:20',
            'chr_weight' => 'required|numeric|min:0',
            'pieces' => 'required|integer|min:1',
            'network' => 'required|string|max:255',
            'services' => 'required|string|max:255',
            'booking_amount' => 'required|numeric|min:0',
            'forwarding_service' => 'nullable|string|max:255',
            'v_awb' => 'required|string|max:255',
            'f_awb' => 'required|string|max:255',
            'remark_on_booking_time' => 'required|string',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string',
            'remark_4' => 'nullable|string',
            'remark_5' => 'nullable|string',
            'remark_6' => 'nullable|string',
            'remark_7' => 'nullable|string',
        ]);

        $awbs = $this->getAwbs();
        if (!is_array($awbs)) {
            $awbs = [];
        }
        
        $newId = count($awbs) > 0 ? max(array_column($awbs, 'id')) + 1 : 1;
        
        $newAwb = [
            'id' => $newId,
            'origin' => $request->origin,
            'origin_pin' => $request->origin_pin,
            'destination' => $request->destination,
            'destination_pin' => $request->destination_pin,
            'chr_weight' => $request->chr_weight,
            'pieces' => $request->pieces,
            'network' => $request->network,
            'services' => $request->services,
            'booking_amount' => $request->booking_amount,
            'forwarding_service' => $request->forwarding_service ?? '',
            'v_awb' => $request->v_awb,
            'f_awb' => $request->f_awb,
            'remark_on_booking_time' => $request->remark_on_booking_time,
            'remark_1' => $request->remark_1 ?? '',
            'remark_2' => $request->remark_2 ?? '',
            'remark_3' => $request->remark_3 ?? '',
            'remark_4' => $request->remark_4 ?? '',
            'remark_5' => $request->remark_5 ?? '',
            'remark_6' => $request->remark_6 ?? '',
            'remark_7' => $request->remark_7 ?? '',
        ];
        
        $awbs[] = $newAwb;
        session(['awbs' => $awbs]);
        session()->save();

        return redirect()->route('admin.search-with-awb.all')->with('success', 'AWB created successfully!');
    }

    public function updateAwb(Request $request, $id)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'origin_pin' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_pin' => 'required|string|max:20',
            'chr_weight' => 'required|numeric|min:0',
            'pieces' => 'required|integer|min:1',
            'network' => 'required|string|max:255',
            'services' => 'required|string|max:255',
            'booking_amount' => 'required|numeric|min:0',
            'forwarding_service' => 'nullable|string|max:255',
            'v_awb' => 'required|string|max:255',
            'f_awb' => 'required|string|max:255',
            'remark_on_booking_time' => 'required|string',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string',
            'remark_4' => 'nullable|string',
            'remark_5' => 'nullable|string',
            'remark_6' => 'nullable|string',
            'remark_7' => 'nullable|string',
        ]);

        $awbs = $this->getAwbs();
        if (!is_array($awbs)) {
            $awbs = [];
        }
        
        $awbs = array_map(function($awb) use ($id, $request) {
            if ($awb['id'] == $id) {
                return [
                    'id' => $id,
                    'origin' => $request->origin,
                    'origin_pin' => $request->origin_pin,
                    'destination' => $request->destination,
                    'destination_pin' => $request->destination_pin,
                    'chr_weight' => $request->chr_weight,
                    'pieces' => $request->pieces,
                    'network' => $request->network,
                    'services' => $request->services,
                    'booking_amount' => $request->booking_amount,
                    'forwarding_service' => $request->forwarding_service ?? '',
                    'v_awb' => $request->v_awb,
                    'f_awb' => $request->f_awb,
                    'remark_on_booking_time' => $request->remark_on_booking_time,
                    'remark_1' => $request->remark_1 ?? '',
                    'remark_2' => $request->remark_2 ?? '',
                    'remark_3' => $request->remark_3 ?? '',
                    'remark_4' => $request->remark_4 ?? '',
                    'remark_5' => $request->remark_5 ?? '',
                    'remark_6' => $request->remark_6 ?? '',
                    'remark_7' => $request->remark_7 ?? '',
                ];
            }
            return $awb;
        }, $awbs);
        
        session(['awbs' => array_values($awbs)]);
        session()->save();

        return redirect()->route('admin.search-with-awb.all')->with('success', 'AWB updated successfully!');
    }

    public function deleteAwb($id)
    {
        $awbs = $this->getAwbs();
        if (!is_array($awbs)) {
            $awbs = [];
        }
        
        $awbs = array_filter($awbs, function($awb) use ($id) {
            return $awb['id'] != $id;
        });
        
        session(['awbs' => array_values($awbs)]);
        session()->save();

        return redirect()->route('admin.search-with-awb.all')->with('success', 'AWB deleted successfully!');
    }

    // AWB Upload Management
    private function getAwbUploads()
    {
        if (session()->has('awb_uploads')) {
            return session('awb_uploads');
        }
        
        $defaultUploads = [
            [
                'id' => 1,
                'awb_no' => 'AWB123456789',
                'date_of_sale' => '2025-01-15',
                'branch' => 'Mumbai',
                'status' => 'Active',
                'booking_type' => 'International',
                'shipment_type' => 'Dox',
                'destination' => 'United States',
                'consignee_name' => 'John Doe',
                'origin_pin' => '400001',
                'destination_pin' => '10001',
                'pieces' => 2,
                'weight' => 5.5,
                'vel_weight' => 6.0,
                'chr_weight' => 6.0,
                'clearance' => 'Customs Cleared',
                'operation_remark' => 'Normal operation',
                'network' => 'DTDC',
                'service' => 'Express',
                'display_service_name' => 'Express Delivery',
                'remark_1' => '',
                'remark_2' => '',
                'remark_3' => '',
                'remark_4' => '',
                'remark_5' => '',
                'remark_6' => '',
                'remark_7' => '',
            ],
        ];
        
        session(['awb_uploads' => $defaultUploads]);
        return $defaultUploads;
    }

    private function getBookingTypes()
    {
        return ['International', 'Domestic'];
    }

    private function getShipmentTypesForUpload()
    {
        return ['Dox', 'Non-Dox', 'Other'];
    }

    public function awbUpload()
    {
        return redirect()->route('admin.awb-upload.create');
    }

    public function createAwbUpload()
    {
        $awbUploads = $this->getAwbUploads();
        $countries = $this->getCountries();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $bookingTypes = $this->getBookingTypes();
        $shipmentTypes = $this->getShipmentTypesForUpload();
        
        // Get branches from networks (or use a separate list)
        $branches = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata'];
        
        return view('admin.awb-upload.create', [
            'awbUploads' => $awbUploads,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
            'bookingTypes' => $bookingTypes,
            'shipmentTypes' => $shipmentTypes,
            'branches' => $branches,
        ]);
    }

    public function allAwbUpload(Request $request)
    {
        // Get networks and services for filter dropdowns
        $networks = $this->getNetworks();
        $services = $this->getServices();
        
        // Start query
        $query = AwbUpload::query();
        
        // Search by AWB number (most important)
        if ($request->filled('awb_number')) {
            $query->where('awb_no', 'like', '%' . $request->awb_number . '%');
        }
        
        // Search by network name
        if ($request->filled('network_name')) {
            $query->where('network_name', 'like', '%' . $request->network_name . '%');
        }
        
        // Search by service name
        if ($request->filled('service_name')) {
            $query->where('service_name', 'like', '%' . $request->service_name . '%');
        }
        
        // Get filtered results
        $awbUploads = $query->latest()->get();
        
        // If database is empty, use session data
        if ($awbUploads->isEmpty() && !$request->filled('awb_number') && !$request->filled('network_name') && !$request->filled('service_name')) {
            $awbUploads = collect($this->getAwbUploads());
        }
        
        return view('admin.awb-upload.all', [
            'awbUploads' => $awbUploads,
            'networks' => $networks,
            'services' => $services,
            'searchParams' => [
                'awb_number' => $request->awb_number ?? '',
                'network_name' => $request->network_name ?? '',
                'service_name' => $request->service_name ?? '',
            ],
        ]);
    }

    /**
     * Bulk delete AWB uploads
     */
    public function bulkDeleteAwbUpload(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $deleted = AwbUpload::whereIn('id', $ids)->delete();
            
            return redirect()->route('admin.awb-upload.all')
                ->with('success', "Successfully deleted {$deleted} AWB upload(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting AWB uploads: ' . $e->getMessage());
        }
    }

    public function editAwbUpload($id)
    {
        $awbUploads = $this->getAwbUploads();
        $upload = collect($awbUploads)->firstWhere('id', $id);
        $countries = $this->getCountries();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $bookingTypes = $this->getBookingTypes();
        $shipmentTypes = $this->getShipmentTypesForUpload();
        
        $branches = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata'];
        
        if (!$upload) {
            return redirect()->route('admin.awb-upload.all')->with('error', 'AWB Upload not found');
        }

        return view('admin.awb-upload.edit', [
            'upload' => $upload,
            'awbUploads' => $awbUploads,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
            'bookingTypes' => $bookingTypes,
            'shipmentTypes' => $shipmentTypes,
            'branches' => $branches,
        ]);
    }

    public function storeAwbUpload(Request $request)
    {
        $request->validate([
            'awb_no' => 'required|string|max:255|regex:/^[a-zA-Z0-9]+$/',
            'type' => 'required|string|in:domestic,international',
            'origin' => 'required|string|max:255',
            'origin_zone' => 'required|string|max:255',
            'origin_zone_pincode' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_zone' => 'required|string|max:255',
            'destination_zone_pincode' => 'required|string|max:20',
            'reference_no' => 'nullable|string|max:255',
            'non_commercial' => 'nullable|string|in:Yes,No',
            'consignor' => 'required|string|max:255',
            'consignor_attn' => 'required|string|max:255',
            'consignee' => 'required|string|max:255',
            'consignee_attn' => 'required|string|max:255',
            'pk' => 'required|integer|min:1',
            'actual_weight' => 'required|numeric|min:0',
            'volumetric_weight' => 'required|numeric|min:0',
            'chargeable_weight' => 'required|string|max:255',
            'network_name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
            'amour' => 'required|numeric|min:0',
            'medical_shipment' => 'nullable|string|in:Yes,No',
            'invoice_value' => 'nullable|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'is_coc' => 'nullable|boolean',
            'cod_amount' => 'nullable|numeric|min:0',
            'clearance_required' => 'nullable|string|in:Yes,No',
            'status' => 'required|string|in:publish,delivered,transit,cancelled',
            'payment_deduct' => 'nullable|string|in:Yes,No',
            'location' => 'nullable|string|max:255',
            'forwarding_service' => 'nullable|string|max:255',
            'remark_3' => 'nullable|string',
        ]);

        // Check for duplicate AWB No in database
        if (AwbUpload::where('awb_no', $request->awb_no)->exists()) {
            return redirect()->back()->withInput()->with('error', 'AWB No. already exists. Duplicate AWB numbers are not allowed.');
        }

        // Create AWB upload in database
        $awbUpload = AwbUpload::create([
            'awb_no' => $request->awb_no,
            'type' => $request->type,
            'origin' => $request->origin,
            'origin_zone' => $request->origin_zone,
            'origin_zone_pincode' => $request->origin_zone_pincode,
            'destination' => $request->destination,
            'destination_zone' => $request->destination_zone,
            'destination_zone_pincode' => $request->destination_zone_pincode,
            'reference_no' => $request->reference_no ?? null,
            'date_of_sale' => $request->date_of_sale ?? null,
            'invoice_date' => $request->invoice_date ?? null,
            'non_commercial' => $request->non_commercial ?? null,
            'consignor' => $request->consignor,
            'consignor_attn' => $request->consignor_attn,
            'consignee' => $request->consignee,
            'consignee_attn' => $request->consignee_attn,
            'goods_type' => $request->goods_type ?? null, // Backend only field
            'pk' => $request->pk ?? 1,
            'actual_weight' => $request->actual_weight,
            'volumetric_weight' => $request->volumetric_weight,
            'chargeable_weight' => $request->chargeable_weight,
            'network_name' => $request->network_name,
            'service_name' => $request->service_name,
            'amour' => $request->amour,
            'medical_shipment' => $request->medical_shipment ?? null,
            'invoice_value' => $request->invoice_value ?? null,
            'is_coc' => $request->is_coc ?? false,
            'cod_amount' => $request->cod_amount ?? 0,
            'clearance_required' => $request->clearance_required ?? null,
            'remark' => $request->remark ?? null, // Backend only field
            'status' => $request->status,
            'payment_deduct' => $request->payment_deduct ?? null,
            'location' => $request->location ?? null,
            'forwarding_service' => $request->forwarding_service ?? null,
            'forwarding_number' => $request->forwarding_number ?? null, // Backend only field
            'transfer' => $request->transfer ?? null, // Backend only field
            'transfer_on' => $request->transfer_on ?? null, // Backend only field
            'remark_1' => $request->remark_1 ?? null, // Backend only field
            'remark_2' => $request->remark_2 ?? null, // Backend only field
            'remark_3' => $request->remark_3 ?? null,
            'remark_4' => $request->remark_4 ?? null,
            'remark_5' => $request->remark_5 ?? null,
            'remark_6' => $request->remark_6 ?? null,
            'remark_7' => $request->remark_7 ?? null,
            'display_service_name' => $request->display_service_name ?? null,
            'operation_remark' => $request->operation_remark ?? null,
        ]);

        return redirect()->route('admin.awb-upload.all')->with('success', 'AWB Upload created successfully!');
    }

    public function updateAwbUpload(Request $request, $id)
    {
        $request->validate([
            'awb_no' => 'required|string|max:255|regex:/^[a-zA-Z0-9]+$/',
            'date_of_sale' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'booking_type' => 'nullable|string|in:International,Domestic',
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Other',
            'destination' => 'required|string|max:255',
            'consignee_name' => 'nullable|string|max:255',
            'origin_pin' => 'required|string|max:20',
            'destination_pin' => 'required|string|max:20',
            'pieces' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0',
            'vel_weight' => 'required|numeric|min:0',
            'chr_weight' => 'required|numeric|min:0',
            'clearance' => 'nullable|string|max:255',
            'operation_remark' => 'nullable|string',
            'network' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'display_service_name' => 'nullable|string|max:255',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string',
            'remark_4' => 'nullable|string',
            'remark_5' => 'nullable|string',
            'remark_6' => 'nullable|string',
            'remark_7' => 'nullable|string',
        ]);

        // Check for duplicate AWB No (excluding current record)
        $awbUploads = $this->getAwbUploads();
        $duplicate = collect($awbUploads)->first(function($upload) use ($request, $id) {
            return $upload['awb_no'] == $request->awb_no && $upload['id'] != $id;
        });
        if ($duplicate) {
            return redirect()->back()->withInput()->with('error', 'AWB No. already exists. Duplicate AWB numbers are not allowed.');
        }

        if (!is_array($awbUploads)) {
            $awbUploads = [];
        }
        
        $awbUploads = array_map(function($upload) use ($id, $request) {
            if ($upload['id'] == $id) {
                return [
                    'id' => $id,
                    'awb_no' => $request->awb_no,
                    'date_of_sale' => $request->date_of_sale ?? '',
                    'branch' => $request->branch ?? '',
                    'status' => $request->status,
                    'booking_type' => $request->booking_type ?? '',
                    'shipment_type' => $request->shipment_type,
                    'destination' => $request->destination,
                    'consignee_name' => $request->consignee_name ?? '',
                    'origin_pin' => $request->origin_pin,
                    'destination_pin' => $request->destination_pin,
                    'pieces' => $request->pieces,
                    'weight' => $request->weight,
                    'vel_weight' => $request->vel_weight,
                    'chr_weight' => $request->chr_weight,
                    'clearance' => $request->clearance ?? '',
                    'operation_remark' => $request->operation_remark ?? '',
                    'network' => $request->network ?? '',
                    'service' => $request->service ?? '',
                    'display_service_name' => $request->display_service_name ?? '',
                    'remark_1' => $request->remark_1 ?? '',
                    'remark_2' => $request->remark_2 ?? '',
                    'remark_3' => $request->remark_3 ?? '',
                    'remark_4' => $request->remark_4 ?? '',
                    'remark_5' => $request->remark_5 ?? '',
                    'remark_6' => $request->remark_6 ?? '',
                    'remark_7' => $request->remark_7 ?? '',
                ];
            }
            return $upload;
        }, $awbUploads);
        
        session(['awb_uploads' => array_values($awbUploads)]);
        session()->save();

        return redirect()->route('admin.awb-upload.all')->with('success', 'AWB Upload updated successfully!');
    }

    public function deleteAwbUpload($id)
    {
        try {
            // Try to delete from database first
            $deleted = AwbUpload::where('id', $id)->delete();
            
            if ($deleted) {
                return redirect()->route('admin.awb-upload.all')->with('success', 'AWB Upload deleted successfully!');
            }
            
            // Fallback to session-based deletion
            $awbUploads = $this->getAwbUploads();
            if (!is_array($awbUploads)) {
                $awbUploads = [];
            }
            
            $awbUploads = array_filter($awbUploads, function($upload) use ($id) {
                return $upload['id'] != $id;
            });
            
            session(['awb_uploads' => array_values($awbUploads)]);
            session()->save();

            return redirect()->route('admin.awb-upload.all')->with('success', 'AWB Upload deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting AWB upload: ' . $e->getMessage());
        }
    }

    /**
     * Bulk upload AWB data from Excel file
     */
    public function bulkUploadAwbUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('excel_file');
            
            // Get networks and services for validation
            $networks = $this->getNetworks();
            $services = $this->getServices();
            
            // Import Excel file
            $import = new AwbUploadsImport();
            Excel::import($import, $file);
            
            // After import, fetch service and network names from existing tables
            $uploadedAwbs = AwbUpload::whereDate('created_at', today())->get();
            
            $updated = 0;
            
            foreach ($uploadedAwbs as $awb) {
                // Fetch service name - if service_name exists in Excel, try to match with services
                if (!empty($awb->service_name)) {
                    $matchedService = collect($services)->first(function($service) use ($awb) {
                        return stripos($service['name'], $awb->service_name) !== false 
                            || stripos($awb->service_name, $service['name']) !== false;
                    });
                    
                    if ($matchedService) {
                        $awb->service_name = $matchedService['name'];
                        $awb->display_service_name = $matchedService['name'];
                    }
                }
                
                // Fetch network name - if network_name exists in Excel, try to match with networks
                if (!empty($awb->network_name)) {
                    $matchedNetwork = collect($networks)->first(function($network) use ($awb) {
                        return stripos($network['name'], $awb->network_name) !== false 
                            || stripos($awb->network_name, $network['name']) !== false;
                    });
                    
                    if ($matchedNetwork) {
                        $awb->network_name = $matchedNetwork['name'];
                    }
                }
                
                $awb->save();
                $updated++;
            }
            
            $totalImported = AwbUpload::whereDate('created_at', today())->count();
            
            return redirect()->route('admin.awb-upload.all')
                ->with('success', "Bulk upload completed! {$totalImported} records imported successfully. {$updated} records matched with existing services/networks.");
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()
                ->with('error', 'Validation errors in Excel file: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            \Log::error('AWB Upload Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Provide more helpful error message
            $errorMessage = 'Error importing Excel file. ';
            if (strpos($e->getMessage(), 'awb') !== false || strpos($e->getMessage(), 'AWB') !== false) {
                $errorMessage .= 'Please ensure your Excel file has an AWB column (can be named: AWB, AWB No., AWB No, awb, etc.) in row 2. ';
            }
            $errorMessage .= 'Error details: ' . $e->getMessage();
            
            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    // Booking Management
    private function getBookings()
    {
        if (session()->has('bookings')) {
            return session('bookings');
        }
        
        $defaultBookings = [
            [
                'id' => 1,
                'current_booking_date' => date('Y-m-d'),
                'awb_no' => 'AWB123456789',
                'shipment_type' => 'Dox',
                'booking_type' => 'International',
                'date_of_sale' => '2025-01-15',
                'consignee_name' => 'John Doe',
                'origin' => 'India',
                'origin_pin' => '400001',
                'destination' => 'United States',
                'destination_pin' => '10001',
                'chr_weight' => 6.0,
                'pieces' => 2,
                'booking_amount' => 1500.00,
                'original_booking_amount' => 1500.00,
                'network' => null,
                'remark_1' => '',
                'remark_2' => '',
                'remark_3' => '',
                'remark_4' => '',
                'remark_5' => '',
                'remark_6' => '',
                'remark_7' => '',
                'forwarding_service' => 'FedEx',
                'v_awb' => 'V123456789',
                'f_awb' => 'F987654321',
            ],
        ];
        
        session(['bookings' => $defaultBookings]);
        return $defaultBookings;
    }

    private function getShipmentTypesForBooking()
    {
        return ['Dox', 'Non-Dox', 'Other'];
    }

    public function bookings()
    {
        return redirect()->route('admin.bookings.create');
    }

    public function createBooking()
    {
        $bookings = $this->getBookings();
        $awbUploads = $this->getAwbUploads();
        $countries = $this->getCountries();
        $shipmentTypes = $this->getShipmentTypesForBooking();
        $bookingTypes = $this->getBookingTypes();
        $networks = $this->getNetworks();
        
        return view('admin.bookings.create', [
            'bookings' => $bookings,
            'awbUploads' => $awbUploads,
            'countries' => $countries,
            'shipmentTypes' => $shipmentTypes,
            'bookingTypes' => $bookingTypes,
            'networks' => $networks,
        ]);
    }

    public function allBookings(Request $request)
    {
        $bookings = $this->getBookings();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $bookings = array_filter($bookings, function($booking) use ($searchTerm) {
                return strpos(strtolower($booking['awb_no'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($booking['origin'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($booking['destination'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($booking['consignee_name'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply shipment type filter
        if ($request->filled('shipment_type')) {
            $typeFilter = $request->shipment_type;
            $bookings = array_filter($bookings, function($booking) use ($typeFilter) {
                return ($booking['shipment_type'] ?? '') == $typeFilter;
            });
        }
        
        // Re-index array after filtering
        $bookings = array_values($bookings);
        
        return view('admin.bookings.all', [
            'bookings' => $bookings,
            'searchParams' => [
                'search' => $request->search ?? '',
                'shipment_type' => $request->shipment_type ?? '',
            ],
        ]);
    }

    public function editBooking($id)
    {
        $bookings = $this->getBookings();
        $booking = collect($bookings)->firstWhere('id', $id);
        $awbUploads = $this->getAwbUploads();
        $countries = $this->getCountries();
        $shipmentTypes = $this->getShipmentTypesForBooking();
        $bookingTypes = $this->getBookingTypes();
        $networks = $this->getNetworks();
        
        if (!$booking) {
            return redirect()->route('admin.bookings.all')->with('error', 'Booking not found');
        }

        return view('admin.bookings.edit', [
            'booking' => $booking,
            'bookings' => $bookings,
            'awbUploads' => $awbUploads,
            'countries' => $countries,
            'shipmentTypes' => $shipmentTypes,
            'bookingTypes' => $bookingTypes,
            'networks' => $networks,
        ]);
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'awb_no' => 'required|string|max:255',
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Other',
            'booking_type' => 'nullable|string|max:255',
            'date_of_sale' => 'nullable|date',
            'consignee_name' => 'nullable|string|max:255',
            'origin' => 'required|string|max:255',
            'origin_pin' => 'nullable|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_pin' => 'required|string|max:20',
            'chr_weight' => 'required|numeric|min:0',
            'pieces' => 'nullable|integer|min:1',
            'booking_amount' => 'required|numeric|min:0',
            'network' => 'nullable|string|max:255',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string',
            'remark_4' => 'nullable|string',
            'remark_5' => 'nullable|string',
            'remark_6' => 'nullable|string',
            'remark_7' => 'nullable|string',
            'forwarding_service' => 'nullable|string|max:255',
            'v_awb' => 'nullable|string|max:255',
            'f_awb' => 'nullable|string|max:255',
            'dummy_number' => 'nullable|string|max:255',
        ]);

        $bookings = $this->getBookings();
        if (!is_array($bookings)) {
            $bookings = [];
        }
        
        $newId = count($bookings) > 0 ? max(array_column($bookings, 'id')) + 1 : 1;
        
        $bookingAmount = $request->booking_amount;
        $networkName = $request->network ?? null;
        
        $newBooking = [
            'id' => $newId,
            'current_booking_date' => date('Y-m-d'),
            'awb_no' => $request->awb_no,
            'shipment_type' => $request->shipment_type,
            'booking_type' => $request->booking_type ?? '',
            'date_of_sale' => $request->date_of_sale ?? '',
            'consignee_name' => $request->consignee_name ?? '',
            'origin' => $request->origin,
            'origin_pin' => $request->origin_pin ?? '',
            'destination' => $request->destination,
            'destination_pin' => $request->destination_pin,
            'chr_weight' => $request->chr_weight,
            'pieces' => $request->pieces ?? 1,
            'booking_amount' => $bookingAmount,
            'original_booking_amount' => $bookingAmount, // Store original amount
            'network' => $networkName,
            'remark_1' => $request->remark_1 ?? '',
            'remark_2' => $request->remark_2 ?? '',
            'remark_3' => $request->remark_3 ?? '',
            'remark_4' => $request->remark_4 ?? '',
            'remark_5' => $request->remark_5 ?? '',
            'remark_6' => $request->remark_6 ?? '',
            'remark_7' => $request->remark_7 ?? '',
            'forwarding_service' => $request->forwarding_service ?? '',
            'v_awb' => $request->v_awb ?? '',
            'f_awb' => $request->f_awb ?? '',
            'dummy_number' => $request->dummy_number ?? '',
        ];
        
        $bookings[] = $newBooking;
        session(['bookings' => $bookings]);
        session()->save();

        // If network is selected, record the initial booking amount as credit to the network
        if ($networkName) {
            try {
                $network = Network::where('name', $networkName)->first();
                if ($network) {
                    $network->credit(
                        $bookingAmount,
                        (string)$newId,
                        $request->awb_no,
                        'booking',
                        "Initial booking amount for AWB: {$request->awb_no}",
                        "Booking created with amount ₹{$bookingAmount}"
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error recording network transaction on booking creation: ' . $e->getMessage());
            }
        }

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'redirect' => route('admin.bookings.all')
            ]);
        }

        return redirect()->route('admin.bookings.all')->with('success', 'Booking created successfully!');
    }

    public function updateBooking(Request $request, $id)
    {
        $request->validate([
            'awb_no' => 'required|string|max:255',
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Other',
            'booking_type' => 'nullable|string|max:255',
            'date_of_sale' => 'nullable|date',
            'consignee_name' => 'nullable|string|max:255',
            'origin' => 'required|string|max:255',
            'origin_pin' => 'nullable|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_pin' => 'required|string|max:20',
            'chr_weight' => 'required|numeric|min:0',
            'pieces' => 'nullable|integer|min:1',
            'booking_amount' => 'required|numeric|min:0',
            'network' => 'nullable|string|max:255',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string',
            'remark_4' => 'nullable|string',
            'remark_5' => 'nullable|string',
            'remark_6' => 'nullable|string',
            'remark_7' => 'nullable|string',
            'forwarding_service' => 'nullable|string|max:255',
            'v_awb' => 'nullable|string|max:255',
            'f_awb' => 'nullable|string|max:255',
            'dummy_number' => 'nullable|string|max:255',
        ]);

        $bookings = $this->getBookings();
        if (!is_array($bookings)) {
            $bookings = [];
        }
        
        // Find the existing booking
        $existingBooking = collect($bookings)->firstWhere('id', $id);
        if (!$existingBooking) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            return redirect()->route('admin.bookings.all')->with('error', 'Booking not found');
        }
        
        // Get the current booking amount (before update)
        $oldPrice = $existingBooking['booking_amount'] ?? 0;
        $newPrice = $request->booking_amount;
        $oldNetworkName = $existingBooking['network'] ?? null;
        $newNetworkName = $request->network ?? null;
        $awbNo = $request->awb_no;
        
        $bookings = array_map(function($booking) use ($id, $request, $oldPrice) {
            if ($booking['id'] == $id) {
                $originalAmount = $booking['original_booking_amount'] ?? $oldPrice;
                return [
                    'id' => $id,
                    'current_booking_date' => $booking['current_booking_date'], // Keep original booking date
                    'awb_no' => $request->awb_no,
                    'shipment_type' => $request->shipment_type,
                    'booking_type' => $request->booking_type ?? '',
                    'date_of_sale' => $request->date_of_sale ?? '',
                    'consignee_name' => $request->consignee_name ?? '',
                    'origin' => $request->origin,
                    'origin_pin' => $request->origin_pin ?? '',
                    'destination' => $request->destination,
                    'destination_pin' => $request->destination_pin,
                    'chr_weight' => $request->chr_weight,
                    'pieces' => $request->pieces ?? 1,
                    'booking_amount' => $request->booking_amount,
                    'original_booking_amount' => $originalAmount, // Keep original amount
                    'network' => $request->network ?? null,
                    'remark_1' => $request->remark_1 ?? '',
                    'remark_2' => $request->remark_2 ?? '',
                    'remark_3' => $request->remark_3 ?? '',
                    'remark_4' => $request->remark_4 ?? '',
                    'remark_5' => $request->remark_5 ?? '',
                    'remark_6' => $request->remark_6 ?? '',
                    'remark_7' => $request->remark_7 ?? '',
                    'forwarding_service' => $request->forwarding_service ?? '',
                    'v_awb' => $request->v_awb ?? '',
                    'f_awb' => $request->f_awb ?? '',
                    'dummy_number' => $request->dummy_number ?? '',
                ];
            }
            return $booking;
        }, $bookings);
        
        session(['bookings' => array_values($bookings)]);
        session()->save();

        try {
            // Handle network change
            if ($oldNetworkName != $newNetworkName) {
                // If old network exists, debit the old price from it
                if ($oldNetworkName) {
                    $oldNetwork = Network::where('name', $oldNetworkName)->first();
                    if ($oldNetwork) {
                        $oldNetwork->debit(
                            $oldPrice,
                            (string)$id,
                            $awbNo,
                            'network_change',
                            "Network changed from {$oldNetworkName} - Debit original booking amount",
                            "Network changed. Original booking amount ₹{$oldPrice} debited."
                        );
                    }
                }
                
                // If new network exists, credit the new price to it
                if ($newNetworkName) {
                    $newNetwork = Network::where('name', $newNetworkName)->first();
                    if ($newNetwork) {
                        $newNetwork->credit(
                            $newPrice,
                            (string)$id,
                            $awbNo,
                            'network_change',
                            "Network changed to {$newNetworkName} - Credit new booking amount",
                            "Network changed. New booking amount ₹{$newPrice} credited."
                        );
                    }
                }
            } else {
                // Same network - handle price change
                if ($oldNetworkName && $oldPrice != $newPrice) {
                    $network = Network::where('name', $oldNetworkName)->first();
                    if ($network) {
                        $priceDifference = $newPrice - $oldPrice;
                        
                        if ($priceDifference > 0) {
                            // Price increased - credit the difference
                            $network->credit(
                                $priceDifference,
                                (string)$id,
                                $awbNo,
                                'price_change',
                                "Price increased from ₹{$oldPrice} to ₹{$newPrice} - Credit difference",
                                "Booking price increased. Difference of ₹{$priceDifference} credited."
                            );
                        } else {
                            // Price decreased - debit the difference
                            $network->debit(
                                abs($priceDifference),
                                (string)$id,
                                $awbNo,
                                'price_change',
                                "Price decreased from ₹{$oldPrice} to ₹{$newPrice} - Debit difference",
                                "Booking price decreased. Difference of ₹" . abs($priceDifference) . " debited."
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error recording network transaction on booking update: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully!',
                'redirect' => route('admin.bookings.all')
            ]);
        }

        // Create notification for order/booking update (only if enabled)
        if (NotificationSetting::isEnabled('order_updated')) {
            $booking = collect($bookings)->firstWhere('id', $id);
            Notification::create([
                'type' => 'order_updated',
                'title' => 'Order Updated',
                'message' => 'Order/Booking #' . $request->awb_no . ' has been updated',
                'data' => [
                    'booking_id' => $id,
                    'awb_no' => $request->awb_no,
                    'destination' => $request->destination,
                    'booking_amount' => $request->booking_amount,
                ],
            ]);
        }

        return redirect()->route('admin.bookings.all')->with('success', 'Booking updated successfully!');
    }

    /**
     * Bulk delete bookings
     */
    public function bulkDeleteBookings(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $bookings = $this->getBookings();
            if (!is_array($bookings)) {
                $bookings = [];
            }
            
            // Find bookings to delete before filtering to reverse transactions
            $bookingsToDelete = array_filter($bookings, function($booking) use ($ids) {
                return in_array($booking['id'], $ids);
            });
            
            // Reverse transactions for deleted bookings
            foreach ($bookingsToDelete as $booking) {
                if (isset($booking['network']) && $booking['network']) {
                    try {
                        $network = Network::where('name', $booking['network'])->first();
                        if ($network && isset($booking['booking_amount'])) {
                            $bookingAmount = $booking['booking_amount'];
                            $network->debit(
                                $bookingAmount,
                                (string)$booking['id'],
                                $booking['awb_no'] ?? null,
                                'booking',
                                "Bulk delete - Reverse booking amount",
                                "Booking deleted in bulk. Booking amount ₹{$bookingAmount} debited to reverse transaction."
                            );
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error recording network transaction on bulk booking deletion: ' . $e->getMessage());
                    }
                }
            }
            
            // Filter out selected bookings
            $bookings = array_filter($bookings, function($booking) use ($ids) {
                return !in_array($booking['id'], $ids);
            });
            
            session(['bookings' => array_values($bookings)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.bookings.all')
                ->with('success', "Successfully deleted {$deletedCount} booking(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting bookings: ' . $e->getMessage());
        }
    }

    public function deleteBooking($id)
    {
        $bookings = $this->getBookings();
        if (!is_array($bookings)) {
            $bookings = [];
        }
        
        // Find the booking before deleting to reverse transaction if needed
        $bookingToDelete = collect($bookings)->firstWhere('id', $id);
        
        $bookings = array_filter($bookings, function($booking) use ($id) {
            return $booking['id'] != $id;
        });
        
        session(['bookings' => array_values($bookings)]);
        session()->save();

        // If booking had a network, debit the booking amount to reverse the transaction
        if ($bookingToDelete && isset($bookingToDelete['network']) && $bookingToDelete['network']) {
            try {
                $network = Network::where('name', $bookingToDelete['network'])->first();
                if ($network && isset($bookingToDelete['booking_amount'])) {
                    $bookingAmount = $bookingToDelete['booking_amount'];
                    $network->debit(
                        $bookingAmount,
                        (string)$id,
                        $bookingToDelete['awb_no'] ?? null,
                        'booking',
                        "Booking deleted - Reverse booking amount",
                        "Booking deleted. Booking amount ₹{$bookingAmount} debited to reverse transaction."
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error recording network transaction on booking deletion: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.bookings.all')->with('success', 'Booking deleted successfully!');
    }

    // Transactions Management
    public function allTransactions(Request $request)
    {
        $query = NetworkTransaction::with('network')->latest();
        
        // Search by AWB, booking ID, or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('awb_no', 'like', '%' . $search . '%')
                  ->orWhere('booking_id', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('network', function($networkQuery) use ($search) {
                      $networkQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter by network
        if ($request->filled('network_id')) {
            $query->where('network_id', $request->network_id);
        }
        
        // Filter by type (credit/debit)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->paginate(50);
        $networks = Network::where('status', 'Active')->get();
        
        // Calculate totals (apply same filters for accurate totals)
        $applyFilters = function($query) use ($request) {
            if ($request->filled('network_id')) {
                $query->where('network_id', $request->network_id);
            }
            if ($request->filled('transaction_type')) {
                $query->where('transaction_type', $request->transaction_type);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        };
        
        $creditQuery = NetworkTransaction::where('type', 'credit');
        $debitQuery = NetworkTransaction::where('type', 'debit');
        
        $applyFilters($creditQuery);
        $applyFilters($debitQuery);
        
        $totalCredits = $creditQuery->sum('amount');
        $totalDebits = $debitQuery->sum('amount');
        $netBalance = $totalCredits - $totalDebits;
        
        return view('admin.transactions.all', [
            'transactions' => $transactions,
            'networks' => $networks,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
            'netBalance' => $netBalance,
            'searchParams' => [
                'search' => $request->search ?? '',
                'network_id' => $request->network_id ?? '',
                'type' => $request->type ?? '',
                'transaction_type' => $request->transaction_type ?? '',
                'date_from' => $request->date_from ?? '',
                'date_to' => $request->date_to ?? '',
            ],
        ]);
    }

    public function showTransaction($id)
    {
        $transaction = NetworkTransaction::with('network')->findOrFail($id);
        
        return view('admin.transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    // Booking Categories Management
    public function bookingCategories()
    {
        return redirect()->route('admin.booking-categories.create');
    }

    public function createBookingCategory()
    {
        $categories = BookingCategory::latest()->get();
        return view('admin.booking-categories.create', [
            'categories' => $categories,
        ]);
    }

    public function allBookingCategories(Request $request)
    {
        $query = BookingCategory::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }
        
        $categories = $query->latest()->get();
        return view('admin.booking-categories.all', [
            'categories' => $categories,
            'search' => $request->search ?? '',
        ]);
    }

    public function editBookingCategory($id)
    {
        $category = BookingCategory::findOrFail($id);
        $categories = BookingCategory::latest()->get();
        
        return view('admin.booking-categories.edit', [
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    public function storeBookingCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:network,expense,income,support,wallet,ledger',
            'requires_awb' => 'required|in:0,1',
            'status' => 'required|in:Active,In-active',
        ]);

        BookingCategory::create([
            'name' => $request->name,
            'type' => $request->type,
            'requires_awb' => (bool)$request->requires_awb,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.booking-categories.all')->with('success', 'Category successfully created!');
    }

    public function updateBookingCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:network,expense,income,support,wallet,ledger',
            'requires_awb' => 'required|in:0,1',
            'status' => 'required|in:Active,In-active',
        ]);

        $category = BookingCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'requires_awb' => (bool)$request->requires_awb,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.booking-categories.all')->with('success', 'Category updated successfully!');
    }

    public function deleteBookingCategory($id)
    {
        $category = BookingCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.booking-categories.all')->with('success', 'Booking category deleted successfully!');
    }

    public function bulkDeleteBookingCategories(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            BookingCategory::whereIn('id', $ids)->delete();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.booking-categories.all')
                ->with('success', "Successfully deleted {$deletedCount} category(ies).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting categories: ' . $e->getMessage());
        }
    }

    // Blog Management
    public function blogs()
    {
        return redirect()->route('admin.blogs.create');
    }

    public function createBlog()
    {
        $blogs = Blog::latest()->get();
        return view('admin.blogs.create', [
            'blogs' => $blogs,
        ]);
    }

    public function allBlogs()
    {
        $blogs = Blog::latest()->get();
        return view('admin.blogs.all', [
            'blogs' => $blogs,
        ]);
    }

    public function editBlog($id)
    {
        $blog = Blog::findOrFail($id);
        $blogs = Blog::latest()->get();
        
        return view('admin.blogs.edit', [
            'blog' => $blog,
            'blogs' => $blogs,
        ]);
    }

    public function storeBlog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'status' => 'required|in:published,draft',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'author' => $request->author ?? 'Admin Agent',
            'published_at' => $request->published_at ? now()->parse($request->published_at) : now(),
            'status' => $request->status,
            'views' => 0,
        ]);

        return redirect()->route('admin.blogs.all')->with('success', 'Blog created successfully!');
    }

    public function updateBlog(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'status' => 'required|in:published,draft',
        ]);

        $blog = Blog::findOrFail($id);

        $imagePath = $blog->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'author' => $request->author ?? 'Admin Agent',
            'published_at' => $request->published_at ? now()->parse($request->published_at) : $blog->published_at,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.blogs.all')->with('success', 'Blog updated successfully!');
    }

    public function deleteBlog($id)
    {
        $blog = Blog::findOrFail($id);
        
        // Delete image if exists
        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        
        $blog->delete();

        return redirect()->route('admin.blogs.all')->with('success', 'Blog deleted successfully!');
    }

    // About Us Management
    public function editAboutUs()
    {
        $settings = FrontendSetting::getSettings();
        return view('admin.about-us.edit', compact('settings'));
    }

    public function updateAboutUs(Request $request)
    {
        $request->validate([
            'about_us_content' => 'nullable|string',
        ]);

        $settings = FrontendSetting::getSettings();
        $settings->about_us_content = $request->about_us_content;
        $settings->save();

        return redirect()->route('admin.about-us.edit')->with('success', 'About Us content updated successfully!');
    }

    // Direct Entry Management
    private function getDirectEntryBookings()
    {
        return session('direct_entry_bookings', []);
    }

    public function createDirectEntry()
    {
        $bookings = $this->getDirectEntryBookings();
        $countries = $this->getCountries();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        
        return view('admin.direct-entry.create', [
            'bookings' => $bookings,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
        ]);
    }

    public function allDirectEntry(Request $request)
    {
        $bookings = $this->getDirectEntryBookings();
        $networks = $this->getNetworks();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $bookings = array_filter($bookings, function($booking) use ($searchTerm) {
                return strpos(strtolower($booking['awb_no'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($booking['origin'] ?? ''), $searchTerm) !== false ||
                       strpos(strtolower($booking['destination'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply network filter
        if ($request->filled('network')) {
            $networkFilter = $request->network;
            $bookings = array_filter($bookings, function($booking) use ($networkFilter) {
                return ($booking['network'] ?? '') == $networkFilter;
            });
        }
        
        // Apply shipment type filter
        if ($request->filled('shipment_type')) {
            $typeFilter = $request->shipment_type;
            $bookings = array_filter($bookings, function($booking) use ($typeFilter) {
                return ($booking['shipment_type'] ?? '') == $typeFilter;
            });
        }
        
        // Re-index array after filtering
        $bookings = array_values($bookings);
        
        return view('admin.direct-entry.all', [
            'bookings' => $bookings,
            'networks' => $networks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'network' => $request->network ?? '',
                'shipment_type' => $request->shipment_type ?? '',
            ],
        ]);
    }

    public function editDirectEntry($id)
    {
        $bookings = $this->getDirectEntryBookings();
        $booking = collect($bookings)->firstWhere('id', $id);
        $countries = $this->getCountries();
        $networks = $this->getNetworks();
        $services = $this->getServices();
        
        if (!$booking) {
            return redirect()->route('admin.direct-entry.all')->with('error', 'Booking not found');
        }

        return view('admin.direct-entry.edit', [
            'booking' => $booking,
            'bookings' => $bookings,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
        ]);
    }

    public function storeDirectEntry(Request $request)
    {
        // Check for duplicate AWB No
        $bookings = $this->getDirectEntryBookings();
        $existingAwb = collect($bookings)->firstWhere('awb_no', $request->awb_no);
        if ($existingAwb) {
            return redirect()->back()->withInput()->with('error', 'AWB No. already exists. Duplicate AWB numbers are not allowed.');
        }

        $request->validate([
            'awb_no' => 'required|string|max:255', // Special characters allowed
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Other',
            'origin' => 'required|string|max:255',
            'origin_pin' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_pin' => 'required|string|max:20',
            'chr_weight' => 'required|numeric|min:0.01',
            'pieces' => 'required|integer|min:1',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'booking_amount' => 'required|numeric|min:0',
            'forwarding_service' => 'required|string|max:255',
            'v_awb' => 'nullable|string|max:255',
            'f_awb' => 'nullable|string|max:255',
            'dummy_number' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
        ]);

        $bookings = $this->getDirectEntryBookings();
        if (!is_array($bookings)) {
            $bookings = [];
        }
        
        $newId = count($bookings) > 0 ? max(array_column($bookings, 'id')) + 1 : 1;
        
        $bookingAmount = $request->booking_amount;
        $networkName = $request->network ?? null;
        
        $newBooking = [
            'id' => $newId,
            'current_booking_date' => date('Y-m-d'),
            'awb_no' => $request->awb_no,
            'shipment_type' => $request->shipment_type,
            'origin' => $request->origin,
            'origin_pin' => $request->origin_pin,
            'destination' => $request->destination,
            'destination_pin' => $request->destination_pin,
            'chr_weight' => $request->chr_weight,
            'pieces' => $request->pieces,
            'network' => $networkName,
            'service' => $request->service,
            'booking_amount' => $bookingAmount,
            'original_booking_amount' => $bookingAmount, // Store original amount
            'forwarding_service' => $request->forwarding_service,
            'v_awb' => $request->v_awb ?? '',
            'f_awb' => $request->f_awb ?? '',
            'dummy_number' => $request->dummy_number ?? '',
            'remark' => $request->remark ?? '',
        ];
        
        $bookings[] = $newBooking;
        session(['direct_entry_bookings' => $bookings]);
        session()->save();

        // If network is selected, record the initial booking amount as credit to the network
        if ($networkName) {
            try {
                $network = Network::where('name', $networkName)->first();
                if ($network) {
                    $network->credit(
                        $bookingAmount,
                        'DE-' . (string)$newId, // Prefix with DE for Direct Entry
                        $request->awb_no,
                        'booking',
                        "Direct entry booking amount for AWB: {$request->awb_no}",
                        "Direct entry booking created with amount ₹{$bookingAmount}"
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error recording network transaction on direct entry booking creation: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.direct-entry.all')->with('success', 'Direct entry booking created successfully!');
    }

    public function updateDirectEntry(Request $request, $id)
    {
        // Check for duplicate AWB No (excluding current booking)
        $bookings = $this->getDirectEntryBookings();
        $existingAwb = collect($bookings)->firstWhere(function($booking) use ($request, $id) {
            return $booking['awb_no'] == $request->awb_no && $booking['id'] != $id;
        });
        if ($existingAwb) {
            return redirect()->back()->withInput()->with('error', 'AWB No. already exists. Duplicate AWB numbers are not allowed.');
        }

        $request->validate([
            'awb_no' => 'required|string|max:255', // Special characters allowed
            'shipment_type' => 'required|string|in:Dox,Non-Dox,Other',
            'origin' => 'required|string|max:255',
            'origin_pin' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'destination_pin' => 'required|string|max:20',
            'chr_weight' => 'required|numeric|min:0.01',
            'pieces' => 'required|integer|min:1',
            'network' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'booking_amount' => 'required|numeric|min:0',
            'forwarding_service' => 'required|string|max:255',
            'v_awb' => 'nullable|string|max:255',
            'f_awb' => 'nullable|string|max:255',
            'dummy_number' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
        ]);

        // Find the existing booking
        $existingBooking = collect($bookings)->firstWhere('id', $id);
        if (!$existingBooking) {
            return redirect()->route('admin.direct-entry.all')->with('error', 'Direct entry booking not found');
        }
        
        $oldPrice = $existingBooking['booking_amount'] ?? $existingBooking['original_booking_amount'] ?? 0;
        $newPrice = $request->booking_amount;
        $oldNetworkName = $existingBooking['network'] ?? null;
        $newNetworkName = $request->network ?? null;
        $awbNo = $request->awb_no;
        
        $bookings = $this->getDirectEntryBookings();
        $bookings = array_map(function($booking) use ($id, $request, $oldPrice) {
            if ($booking['id'] == $id) {
                $originalAmount = $booking['original_booking_amount'] ?? $oldPrice;
                return [
                    'id' => $booking['id'],
                    'current_booking_date' => $booking['current_booking_date'],
                    'awb_no' => $request->awb_no,
                    'shipment_type' => $request->shipment_type,
                    'origin' => $request->origin,
                    'origin_pin' => $request->origin_pin,
                    'destination' => $request->destination,
                    'destination_pin' => $request->destination_pin,
                    'chr_weight' => $request->chr_weight,
                    'pieces' => $request->pieces,
                    'network' => $request->network ?? null,
                    'service' => $request->service,
                    'booking_amount' => $request->booking_amount,
                    'original_booking_amount' => $originalAmount, // Keep original amount
                    'forwarding_service' => $request->forwarding_service,
                    'v_awb' => $request->v_awb ?? '',
                    'f_awb' => $request->f_awb ?? '',
                    'dummy_number' => $request->dummy_number ?? '',
                    'remark' => $request->remark ?? '',
                ];
            }
            return $booking;
        }, $bookings);
        
        session(['direct_entry_bookings' => array_values($bookings)]);
        session()->save();

        try {
            // Handle network change
            if ($oldNetworkName != $newNetworkName) {
                // If old network exists, debit the old price from it
                if ($oldNetworkName) {
                    $oldNetwork = Network::where('name', $oldNetworkName)->first();
                    if ($oldNetwork) {
                        $oldNetwork->debit(
                            $oldPrice,
                            'DE-' . (string)$id,
                            $awbNo,
                            'network_change',
                            "Direct entry - Network changed from {$oldNetworkName} - Debit original booking amount",
                            "Direct entry network changed. Original booking amount ₹{$oldPrice} debited."
                        );
                    }
                }
                
                // If new network exists, credit the new price to it
                if ($newNetworkName) {
                    $newNetwork = Network::where('name', $newNetworkName)->first();
                    if ($newNetwork) {
                        $newNetwork->credit(
                            $newPrice,
                            'DE-' . (string)$id,
                            $awbNo,
                            'network_change',
                            "Direct entry - Network changed to {$newNetworkName} - Credit new booking amount",
                            "Direct entry network changed. New booking amount ₹{$newPrice} credited."
                        );
                    }
                }
            } else {
                // Same network - handle price change
                if ($oldNetworkName && $oldPrice != $newPrice) {
                    $network = Network::where('name', $oldNetworkName)->first();
                    if ($network) {
                        $priceDifference = $newPrice - $oldPrice;
                        
                        if ($priceDifference > 0) {
                            // Price increased - credit the difference
                            $network->credit(
                                $priceDifference,
                                'DE-' . (string)$id,
                                $awbNo,
                                'price_change',
                                "Direct entry - Price increased from ₹{$oldPrice} to ₹{$newPrice} - Credit difference",
                                "Direct entry booking price increased. Difference of ₹{$priceDifference} credited."
                            );
                        } else {
                            // Price decreased - debit the difference
                            $network->debit(
                                abs($priceDifference),
                                'DE-' . (string)$id,
                                $awbNo,
                                'price_change',
                                "Direct entry - Price decreased from ₹{$oldPrice} to ₹{$newPrice} - Debit difference",
                                "Direct entry booking price decreased. Difference of ₹" . abs($priceDifference) . " debited."
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error recording network transaction on direct entry booking update: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return redirect()->route('admin.direct-entry.all')->with('success', 'Direct entry booking updated successfully!');
    }

    public function deleteDirectEntry($id)
    {
        $bookings = $this->getDirectEntryBookings();
        if (!is_array($bookings)) {
            $bookings = [];
        }
        
        // Find the booking before deleting to reverse transaction if needed
        $bookingToDelete = collect($bookings)->firstWhere('id', $id);
        
        $bookings = array_filter($bookings, function($booking) use ($id) {
            return $booking['id'] != $id;
        });
        
        session(['direct_entry_bookings' => array_values($bookings)]);
        session()->save();

        // If booking had a network, debit the booking amount to reverse the transaction
        if ($bookingToDelete && isset($bookingToDelete['network']) && $bookingToDelete['network']) {
            try {
                $network = Network::where('name', $bookingToDelete['network'])->first();
                if ($network && isset($bookingToDelete['booking_amount'])) {
                    $bookingAmount = $bookingToDelete['booking_amount'];
                    $network->debit(
                        $bookingAmount,
                        'DE-' . (string)$id,
                        $bookingToDelete['awb_no'] ?? null,
                        'booking',
                        "Direct entry booking deleted - Reverse booking amount",
                        "Direct entry booking deleted. Booking amount ₹{$bookingAmount} debited to reverse transaction."
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error recording network transaction on direct entry booking deletion: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.direct-entry.all')->with('success', 'Direct entry booking deleted successfully!');
    }

    /**
     * Bulk delete direct entry bookings
     */
    public function bulkDeleteDirectEntry(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'required|integer',
        ]);

        try {
            $ids = $request->selected_ids;
            $bookings = $this->getDirectEntryBookings();
            if (!is_array($bookings)) {
                $bookings = [];
            }
            
            // Find bookings to delete before filtering to reverse transactions
            $bookingsToDelete = array_filter($bookings, function($booking) use ($ids) {
                return in_array($booking['id'], $ids);
            });
            
            // Reverse transactions for deleted bookings
            foreach ($bookingsToDelete as $booking) {
                if (isset($booking['network']) && $booking['network']) {
                    try {
                        $network = Network::where('name', $booking['network'])->first();
                        if ($network && isset($booking['booking_amount'])) {
                            $bookingAmount = $booking['booking_amount'];
                            $network->debit(
                                $bookingAmount,
                                'DE-' . (string)$booking['id'],
                                $booking['awb_no'] ?? null,
                                'booking',
                                "Bulk delete - Reverse direct entry booking amount",
                                "Direct entry booking deleted in bulk. Booking amount ₹{$bookingAmount} debited to reverse transaction."
                            );
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error recording network transaction on bulk direct entry booking deletion: ' . $e->getMessage());
                    }
                }
            }
            
            // Filter out selected bookings
            $bookings = array_filter($bookings, function($booking) use ($ids) {
                return !in_array($booking['id'], $ids);
            });
            
            session(['direct_entry_bookings' => array_values($bookings)]);
            session()->save();
            
            $deletedCount = count($ids);
            return redirect()->route('admin.direct-entry.all')
                ->with('success', "Successfully deleted {$deletedCount} direct entry booking(s).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting direct entry bookings: ' . $e->getMessage());
        }
    }

    // Reports Management
    public function zoneReport()
    {
        $zones = $this->getZones();
        return view('admin.reports.zone', compact('zones'));
    }
    
    /**
     * Calculate financial data for networks
     */
    private function calculateNetworkFinancials($networkFilter = '', $dateFrom = '', $dateTo = '')
    {
        $financials = [];
        
        // Get all networks
        $networks = $this->getNetworks();
        
        foreach ($networks as $network) {
            $networkName = $network['name'] ?? '';
            
            // Skip if network filter is set and doesn't match
            if (!empty($networkFilter) && $networkName !== $networkFilter) {
                continue;
            }
            
            // Get opening balance
            $openingBalance = $network['opening_balance'] ?? 0;
            
            // Query AWB uploads for spending (amour field)
            $spendingQuery = AwbUpload::where('network_name', $networkName)
                ->whereNotNull('amour');
            
            if (!empty($dateFrom)) {
                $spendingQuery->where('date_of_sale', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $spendingQuery->where('date_of_sale', '<=', $dateTo);
            }
            
            // Calculate spending (sum of amour)
            $spending = (float) ($spendingQuery->sum('amour') ?? 0);
            
            // Calculate credits from wallet transactions (deposits)
            // Assuming we have a way to link network to wallet transactions
            // For now, we'll use a placeholder - you may need to adjust based on your business logic
            $credit = 0;
            
            // Calculate debits (withdrawals/payments)
            // This could be from payment_deduct field in awb_uploads or wallet transactions
            $debitQuery = AwbUpload::where('network_name', $networkName)
                ->whereNotNull('payment_deduct')
                ->where('payment_deduct', '!=', '');
            
            if (!empty($dateFrom)) {
                $debitQuery->where('date_of_sale', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $debitQuery->where('date_of_sale', '<=', $dateTo);
            }
            
            // Sum payment_deduct as debit (assuming it's numeric)
            $debit = 0;
            $debitRecords = $debitQuery->get();
            foreach ($debitRecords as $record) {
                if (is_numeric($record->payment_deduct)) {
                    $debit += (float) $record->payment_deduct;
                }
            }
            
            // Calculate total balance (opening balance + credits - spending - debits)
            $totalBalance = $openingBalance + $credit - $spending - $debit;
            
            $financials[$networkName] = [
                'spending' => round($spending, 2),
                'credit' => round($credit, 2),
                'debit' => round($debit, 2),
                'total_balance' => round($totalBalance, 2),
                'opening_balance' => round($openingBalance, 2),
            ];
        }
        
        return $financials;
    }

    public function formulaReport()
    {
        $formulas = $this->getFormulas();
        return view('admin.reports.formula', compact('formulas'));
    }

    public function shippingChargesReport()
    {
        $shippingCharges = $this->getShippingCharges();
        return view('admin.reports.shipping-charges', compact('shippingCharges'));
    }

    public function bookingReport(Request $request)
    {
        // Get filter parameters
        $category = $request->get('category', '');
        $hub = $request->get('hub', '');
        $branch = $request->get('branch', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $search = $request->get('search', '');
        
        // Get all bookings
        $bookings = $this->getBookings();
        $directEntryBookings = $this->getDirectEntryBookings();
        
        // Combine bookings
        $allBookings = array_merge($bookings, $directEntryBookings);
        
        // Apply filters
        if ($hub) {
            $allBookings = array_filter($allBookings, function($booking) use ($hub) {
                return isset($booking['hub']) && $booking['hub'] === $hub;
            });
        }
        
        if ($branch) {
            $allBookings = array_filter($allBookings, function($booking) use ($branch) {
                return isset($booking['branch']) && $booking['branch'] === $branch;
            });
        }
        
        if ($dateFrom) {
            $allBookings = array_filter($allBookings, function($booking) use ($dateFrom) {
                $bookingDate = $booking['current_booking_date'] ?? $booking['date_of_sale'] ?? '';
                return $bookingDate >= $dateFrom;
            });
        }
        
        if ($dateTo) {
            $allBookings = array_filter($allBookings, function($booking) use ($dateTo) {
                $bookingDate = $booking['current_booking_date'] ?? $booking['date_of_sale'] ?? '';
                return $bookingDate <= $dateTo;
            });
        }
        
        if ($search) {
            $allBookings = array_filter($allBookings, function($booking) use ($search) {
                return stripos($booking['awb_no'] ?? '', $search) !== false 
                    || stripos($booking['origin'] ?? '', $search) !== false
                    || stripos($booking['destination'] ?? '', $search) !== false
                    || stripos($booking['admin_name'] ?? 'System', $search) !== false;
            });
        }
        
        // Separate bookings back
        $filteredBookings = [];
        $filteredDirectEntry = [];
        
        foreach ($allBookings as $booking) {
            if (isset($booking['is_direct_entry']) && $booking['is_direct_entry']) {
                $filteredDirectEntry[] = $booking;
            } else {
                $filteredBookings[] = $booking;
            }
        }
        
        // Add admin_name if not present
        foreach ($filteredBookings as &$booking) {
            if (!isset($booking['admin_name'])) {
                $booking['admin_name'] = 'SuperAdmin';
            }
        }
        foreach ($filteredDirectEntry as &$booking) {
            if (!isset($booking['admin_name'])) {
                $booking['admin_name'] = 'SuperAdmin';
            }
        }
        
        // Get filter options
        $categories = $this->getTransactionCategories();
        $hubs = $this->getUniqueHubs();
        $branches = $this->getUniqueBranches($hub);
        
        return view('admin.reports.booking', [
            'bookings' => array_values($filteredBookings),
            'directEntryBookings' => array_values($filteredDirectEntry),
            'categories' => $categories,
            'hubs' => $hubs,
            'branches' => $branches,
            'category' => $category,
            'hub' => $hub,
            'branch' => $branch,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }

    public function paymentReport()
    {
        // Payment data would come from a payment system
        $payments = session('payments', []);
        return view('admin.reports.payment', compact('payments'));
    }

    public function transactionReport(Request $request)
    {
        // Get filter parameters
        $category = $request->get('category', '');
        $hub = $request->get('hub', '');
        $branch = $request->get('branch', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $search = $request->get('search', '');
        
        // Get all transactions - combine wallet transactions and AWB-related transactions
        $transactions = $this->getTransactionReportData($category, $hub, $branch, $dateFrom, $dateTo, $search);
        
        // Get unique values for filters
        $categories = $this->getTransactionCategories();
        $hubs = $this->getUniqueHubs();
        $branches = $this->getUniqueBranches($hub);
        
        return view('admin.reports.transaction', [
            'transactions' => $transactions,
            'categories' => $categories,
            'hubs' => $hubs,
            'branches' => $branches,
            'category' => $category,
            'hub' => $hub,
            'branch' => $branch,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }
    
    /**
     * Get transaction report data with filters
     */
    private function getTransactionReportData($category = '', $hub = '', $branch = '', $dateFrom = '', $dateTo = '', $search = '')
    {
        $transactions = [];
        
        // Get wallet transactions
        $walletTransactions = \App\Models\WalletTransaction::with(['user', 'wallet'])
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($walletTransactions as $wt) {
            $transactions[] = [
                'id' => $wt->id,
                'branch' => 'N/A',
                'awb' => $wt->reference_id ?? '',
                'categoryName' => ucfirst($wt->type),
                'mode' => 'wallet',
                'opening_balance' => $wt->balance_before ?? 0,
                'debit' => $wt->type === 'withdrawal' ? $wt->amount : 0,
                'credit' => $wt->type === 'deposit' ? $wt->amount : 0,
                'balance' => $wt->balance_after ?? 0,
                'comment' => $wt->description ?? $wt->notes ?? '',
                'date' => $wt->created_at->format('Y-m-d H:i:s'),
                'admin_name' => $wt->user->name ?? 'System',
            ];
        }
        
        // Get AWB upload transactions (shipment bookings, payments, etc.)
        $awbQuery = \App\Models\AwbUpload::query();
        
        if ($hub) {
            $awbQuery->where('hub', $hub);
        }
        if ($branch) {
            $awbQuery->where('branch', $branch);
        }
        if ($dateFrom) {
            $awbQuery->whereDate('date_of_sale', '>=', $dateFrom);
        }
        if ($dateTo) {
            $awbQuery->whereDate('date_of_sale', '<=', $dateTo);
        }
        if ($search) {
            $awbQuery->where(function($q) use ($search) {
                $q->where('awb_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }
        
        $awbUploads = $awbQuery->orderBy('date_of_sale', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($awbUploads as $awb) {
            // Shipment booked transaction
            $transactions[] = [
                'id' => 'AWB-' . $awb->id,
                'branch' => $awb->branch ?? 'N/A',
                'awb' => $awb->awb_no ?? '',
                'categoryName' => 'Shipment Booked',
                'mode' => 'wallet',
                'opening_balance' => 0, // Will be calculated
                'debit' => $awb->amour ?? 0,
                'credit' => 0,
                'balance' => 0, // Will be calculated
                'comment' => ($awb->awb_no ?? '') . ': Shipment Booked',
                'date' => $awb->date_of_sale ? $awb->date_of_sale->format('Y-m-d H:i:s') : $awb->created_at->format('Y-m-d H:i:s'),
                'admin_name' => 'SuperAdmin', // Default, can be linked to user
            ];
            
            // Payment deducted transaction
            if ($awb->payment_deduct === 'Yes' && $awb->amour) {
                $transactions[] = [
                    'id' => 'PAY-' . $awb->id,
                    'branch' => $awb->branch ?? 'N/A',
                    'awb' => $awb->awb_no ?? '',
                    'categoryName' => 'Payment Deducted',
                    'mode' => 'wallet',
                    'opening_balance' => 0,
                    'debit' => 0,
                    'credit' => $awb->amour ?? 0,
                    'balance' => 0,
                    'comment' => ($awb->awb_no ?? '') . ': Payment Deducted',
                    'date' => $awb->created_at->format('Y-m-d H:i:s'),
                    'admin_name' => 'SuperAdmin',
                ];
            }
        }
        
        // Apply category filter
        if ($category) {
            $transactions = array_filter($transactions, function($t) use ($category) {
                return stripos($t['categoryName'], $category) !== false;
            });
        }
        
        // Apply search filter
        if ($search) {
            $transactions = array_filter($transactions, function($t) use ($search) {
                return stripos($t['awb'], $search) !== false 
                    || stripos($t['comment'], $search) !== false
                    || stripos($t['admin_name'], $search) !== false;
            });
        }
        
        // Calculate opening balance and running balance
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        $runningBalance = 0;
        foreach ($transactions as &$transaction) {
            $transaction['opening_balance'] = $runningBalance;
            $runningBalance = $runningBalance - $transaction['debit'] + $transaction['credit'];
            $transaction['balance'] = $runningBalance;
        }
        
        // Sort by date descending for display
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_values($transactions);
    }
    
    /**
     * Get unique transaction categories
     */
    private function getTransactionCategories()
    {
        return [
            'TRANSACTION' => 'All Transactions',
            'Shipment Booked' => 'Shipment Booked',
            'deposit' => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'Payment Deducted' => 'Payment Deducted',
            'UPI' => 'UPI',
            'Difference Amount' => 'Difference Amount',
        ];
    }
    
    /**
     * Get unique hubs from AWB uploads
     */
    private function getUniqueHubs()
    {
        return \App\Models\AwbUpload::whereNotNull('hub')
            ->distinct()
            ->pluck('hub')
            ->filter()
            ->values()
            ->toArray();
    }
    
    /**
     * Get unique branches from AWB uploads
     */
    private function getUniqueBranches($hub = '')
    {
        $query = \App\Models\AwbUpload::whereNotNull('branch');
        if ($hub) {
            $query->where('hub', $hub);
        }
        return $query->distinct()
            ->pluck('branch')
            ->filter()
            ->values()
            ->toArray();
    }
    
    /**
     * Export transaction report to Excel
     */
    public function exportTransactionReport(Request $request)
    {
        $category = $request->get('category', '');
        $hub = $request->get('hub', '');
        $branch = $request->get('branch', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $search = $request->get('search', '');
        
        $transactions = $this->getTransactionReportData($category, $hub, $branch, $dateFrom, $dateTo, $search);
        
        $export = new class($transactions) implements FromArray, WithHeadings {
            protected $transactions;
            
            public function __construct($transactions)
            {
                $this->transactions = $transactions;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->transactions as $transaction) {
                    $data[] = [
                        $transaction['branch'],
                        $transaction['awb'],
                        $transaction['categoryName'],
                        $transaction['mode'],
                        number_format($transaction['opening_balance'], 2),
                        number_format($transaction['debit'], 2),
                        number_format($transaction['credit'], 2),
                        number_format($transaction['balance'], 2),
                        $transaction['comment'],
                        $transaction['date'],
                        $transaction['admin_name'],
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return [
                    'Branch',
                    'AWB',
                    'Category',
                    'Mode',
                    'Opening Balance',
                    'Debit',
                    'Credit',
                    'Balance',
                    'Comment',
                    'Date',
                    'Admin Name',
                ];
            }
        };
        
        $filename = 'transaction_report_' . date('Y-m-d');
        if ($dateFrom || $dateTo) {
            $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
        }
        
        return Excel::download($export, $filename . '.xlsx');
    }

    public function networkReport(Request $request)
    {
        // Get filter parameters
        $category = $request->get('category', '');
        $hub = $request->get('hub', '');
        $branch = $request->get('branch', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $search = $request->get('search', '');
        
        // Get all networks
        $networks = $this->getNetworks();
        
        // Apply search filter
        if (!empty($search)) {
            $networks = array_filter($networks, function($network) use ($search) {
                return stripos($network['name'] ?? '', $search) !== false 
                    || stripos($network['remark'] ?? '', $search) !== false
                    || stripos($network['admin_name'] ?? 'System', $search) !== false;
            });
        }
        
        // Calculate financial data for each network
        $networkFinancials = $this->calculateNetworkFinancials('', $dateFrom, $dateTo);
        
        // Add financial data and admin_name to networks
        $networksWithFinancials = [];
        foreach ($networks as $network) {
            $networkName = $network['name'] ?? 'Unknown';
            $network['financial'] = $networkFinancials[$networkName] ?? [
                'spending' => 0,
                'credit' => 0,
                'debit' => 0,
                'total_balance' => 0,
                'opening_balance' => $network['opening_balance'] ?? 0,
            ];
            // Add admin_name if not present
            if (!isset($network['admin_name'])) {
                $network['admin_name'] = 'SuperAdmin'; // Default admin name
            }
            $networksWithFinancials[] = $network;
        }
        
        // Get filter options
        $categories = $this->getTransactionCategories();
        $hubs = $this->getUniqueHubs();
        $branches = $this->getUniqueBranches($hub);
        
        return view('admin.reports.network', [
            'networks' => array_values($networksWithFinancials),
            'categories' => $categories,
            'hubs' => $hubs,
            'branches' => $branches,
            'category' => $category,
            'hub' => $hub,
            'branch' => $branch,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
            'networkFinancials' => $networkFinancials,
        ]);
    }

    public function serviceReport()
    {
        $services = $this->getServices();
        return view('admin.reports.service', compact('services'));
    }

    public function countryReport()
    {
        $countries = $this->getCountries();
        return view('admin.reports.country', compact('countries'));
    }

    // Excel Export Methods
    public function exportZoneReport()
    {
        $zones = $this->getZones();
        
        $export = new class($zones) implements FromArray, WithHeadings {
            protected $zones;
            
            public function __construct($zones)
            {
                $this->zones = $zones;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->zones as $zone) {
                    $data[] = [
                        $zone['id'],
                        $zone['pincode'],
                        $zone['country'],
                        $zone['zone'],
                        $zone['network'],
                        $zone['service'],
                        $zone['status'],
                        $zone['remark'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Pincode', 'Country', 'Zone', 'Network', 'Service', 'Status', 'Remark'];
            }
        };
        
        return Excel::download($export, 'zone_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportFormulaReport()
    {
        $formulas = $this->getFormulas();
        
        $export = new class($formulas) implements FromArray, WithHeadings {
            protected $formulas;
            
            public function __construct($formulas)
            {
                $this->formulas = $formulas;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->formulas as $formula) {
                    $data[] = [
                        $formula['id'],
                        $formula['formula_name'],
                        $formula['network'],
                        $formula['service'],
                        $formula['type'],
                        $formula['scope'],
                        $formula['priority'],
                        $formula['value'],
                        $formula['status'],
                        $formula['remark'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Formula Name', 'Network', 'Service', 'Type', 'Scope', 'Priority', 'Value', 'Status', 'Remark'];
            }
        };
        
        return Excel::download($export, 'formula_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportShippingChargesReport()
    {
        $shippingCharges = $this->getShippingCharges();
        
        $export = new class($shippingCharges) implements FromArray, WithHeadings {
            protected $shippingCharges;
            
            public function __construct($shippingCharges)
            {
                $this->shippingCharges = $shippingCharges;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->shippingCharges as $charge) {
                    $data[] = [
                        $charge['id'],
                        $charge['origin'],
                        $charge['origin_zone'],
                        $charge['destination'],
                        $charge['destination_zone'],
                        $charge['shipment_type'],
                        $charge['min_weight'],
                        $charge['max_weight'],
                        $charge['network'],
                        $charge['service'],
                        $charge['rate'],
                        $charge['remark'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Origin', 'Origin Zone', 'Destination', 'Destination Zone', 'Shipment Type', 'Min Weight', 'Max Weight', 'Network', 'Service', 'Rate', 'Remark'];
            }
        };
        
        return Excel::download($export, 'shipping_charges_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportBookingReport()
    {
        $bookings = $this->getBookings();
        $directEntryBookings = $this->getDirectEntryBookings();
        
        $export = new class($bookings, $directEntryBookings) implements FromArray, WithHeadings {
            protected $bookings;
            protected $directEntryBookings;
            
            public function __construct($bookings, $directEntryBookings)
            {
                $this->bookings = $bookings;
                $this->directEntryBookings = $directEntryBookings;
            }
            
            public function array(): array
            {
                $data = [];
                
                // Add regular bookings
                foreach ($this->bookings as $booking) {
                    $data[] = [
                        $booking['id'],
                        $booking['current_booking_date'] ?? date('Y-m-d'),
                        $booking['awb_no'],
                        $booking['shipment_type'],
                        $booking['origin'],
                        $booking['destination'],
                        $booking['chr_weight'],
                        $booking['pieces'],
                        $booking['network'] ?? 'N/A',
                        $booking['service'] ?? 'N/A',
                        $booking['booking_amount'],
                        'Regular',
                    ];
                }
                
                // Add direct entry bookings
                foreach ($this->directEntryBookings as $booking) {
                    $data[] = [
                        $booking['id'],
                        $booking['current_booking_date'] ?? date('Y-m-d'),
                        $booking['awb_no'],
                        $booking['shipment_type'],
                        $booking['origin'],
                        $booking['destination'],
                        $booking['chr_weight'],
                        $booking['pieces'],
                        $booking['network'] ?? 'N/A',
                        $booking['service'] ?? 'N/A',
                        $booking['booking_amount'],
                        'Direct Entry',
                    ];
                }
                
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Booking Date', 'AWB No.', 'Shipment Type', 'Origin', 'Destination', 'Chr Weight', 'Pieces', 'Network', 'Service', 'Booking Amount', 'Type'];
            }
        };
        
        return Excel::download($export, 'booking_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPaymentReport()
    {
        $payments = session('payments', []);
        
        $export = new class($payments) implements FromArray, WithHeadings {
            protected $payments;
            
            public function __construct($payments)
            {
                $this->payments = $payments;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->payments as $payment) {
                    $data[] = [
                        $payment['id'] ?? '-',
                        $payment['payment_date'] ?? '-',
                        $payment['payment_method'] ?? '-',
                        $payment['amount'] ?? '0',
                        $payment['status'] ?? '-',
                        $payment['reference'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Payment Date', 'Payment Method', 'Amount', 'Status', 'Reference'];
            }
        };
        
        return Excel::download($export, 'payment_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportNetworkReport(Request $request)
    {
        // Get filter parameters
        $networkFilter = $request->get('network', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        // Get networks with financial data (same logic as networkReport)
        $networks = $this->getNetworks();
        
        // Apply network filter if provided
        if (!empty($networkFilter)) {
            $networks = array_filter($networks, function($network) use ($networkFilter) {
                return isset($network['name']) && $network['name'] === $networkFilter;
            });
        }
        
        // Calculate financial data
        $networkFinancials = $this->calculateNetworkFinancials($networkFilter, $dateFrom, $dateTo);
        
        // Add financial data to networks
        $networksWithFinancials = [];
        foreach ($networks as $network) {
            $networkName = $network['name'] ?? 'Unknown';
            $network['financial'] = $networkFinancials[$networkName] ?? [
                'spending' => 0,
                'credit' => 0,
                'debit' => 0,
                'total_balance' => 0,
                'opening_balance' => $network['opening_balance'] ?? 0,
            ];
            $networksWithFinancials[] = $network;
        }
        
        $export = new class($networksWithFinancials) implements FromArray, WithHeadings {
            protected $networks;
            
            public function __construct($networks)
            {
                $this->networks = $networks;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->networks as $network) {
                    $financial = $network['financial'] ?? [];
                    $data[] = [
                        $network['id'],
                        $network['name'],
                        $network['type'],
                        $network['status'],
                        number_format($financial['opening_balance'] ?? 0, 2),
                        number_format($financial['spending'] ?? 0, 2),
                        number_format($financial['credit'] ?? 0, 2),
                        number_format($financial['debit'] ?? 0, 2),
                        number_format($financial['total_balance'] ?? 0, 2),
                        $network['bank_details'] ?? '-',
                        $network['upi_scanner'] ?? '-',
                        $network['remark'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return [
                    'ID', 
                    'Name', 
                    'Type', 
                    'Status',
                    'Opening Balance',
                    'Spending',
                    'Credit',
                    'Debit',
                    'Total Balance',
                    'Bank Details', 
                    'UPI Scanner', 
                    'Remark'
                ];
            }
        };
        
        $filename = 'network_report_' . date('Y-m-d');
        if (!empty($networkFilter)) {
            $filename .= '_' . str_replace(' ', '_', $networkFilter);
        }
        if (!empty($dateFrom) || !empty($dateTo)) {
            $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
        }
        
        return Excel::download($export, $filename . '.xlsx');
    }

    public function exportServiceReport()
    {
        $services = $this->getServices();
        
        $export = new class($services) implements FromArray, WithHeadings {
            protected $services;
            
            public function __construct($services)
            {
                $this->services = $services;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->services as $service) {
                    $data[] = [
                        $service['id'],
                        $service['name'],
                        $service['network'],
                        $service['transit_time'],
                        $service['items_allowed'],
                        $service['status'],
                        $service['remark'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Name', 'Network', 'Transit Time', 'Items Allowed', 'Status', 'Remark'];
            }
        };
        
        return Excel::download($export, 'service_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportCountryReport()
    {
        $countries = $this->getCountries();
        
        $export = new class($countries) implements FromArray, WithHeadings {
            protected $countries;
            
            public function __construct($countries)
            {
                $this->countries = $countries;
            }
            
            public function array(): array
            {
                $data = [];
                foreach ($this->countries as $country) {
                    $data[] = [
                        $country['id'],
                        $country['name'],
                        $country['code'],
                        $country['isd_no'],
                        $country['dialing_code'],
                        $country['status'],
                        $country['remark'] ?? '-',
                    ];
                }
                return $data;
            }
            
            public function headings(): array
            {
                return ['ID', 'Name', 'Code', 'ISD No.', 'Dialing Code', 'Status', 'Remark'];
            }
        };
        
        return Excel::download($export, 'country_report_' . date('Y-m-d') . '.xlsx');
    }

    public function bankReport()
    {
        $banks = $this->getBanks();
        return view('admin.reports.bank', compact('banks'));
    }

    public function walletReport()
    {
        // Wallet data would come from a wallet system
        $wallets = session('wallets', []);
        return view('admin.reports.wallet', compact('wallets'));
    }

    // Banks Management
    private function getBanks()
    {
        if (session()->has('banks')) {
            return session('banks');
        }
        
        $defaultBanks = [
            [
                'id' => 1,
                'bank_name' => 'HDFC Bank',
                'account_holder_name' => 'Haxo Shipping Pvt Ltd',
                'account_number' => '123456789012',
                'ifsc_code' => 'HDFC0001234',
                'opening_balance' => 50000.00,
            ],
            [
                'id' => 2,
                'bank_name' => 'ICICI Bank',
                'account_holder_name' => 'Haxo Shipping Pvt Ltd',
                'account_number' => '987654321098',
                'ifsc_code' => 'ICIC0005678',
                'opening_balance' => 75000.00,
            ],
        ];
        
        session(['banks' => $defaultBanks]);
        return $defaultBanks;
    }

    public function banks()
    {
        return redirect()->route('admin.banks.create');
    }

    public function createBank()
    {
        $banks = $this->getBanks();
        return view('admin.banks.create', [
            'banks' => $banks,
        ]);
    }

    public function allBanks()
    {
        $banks = $this->getBanks();
        return view('admin.banks.all', [
            'banks' => $banks,
        ]);
    }

    public function editBank($id)
    {
        $banks = $this->getBanks();
        $bank = collect($banks)->firstWhere('id', $id);
        
        if (!$bank) {
            return redirect()->route('admin.banks.all')->with('error', 'Bank not found');
        }

        return view('admin.banks.edit', [
            'bank' => $bank,
            'banks' => $banks,
        ]);
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $banks = $this->getBanks();
        if (!is_array($banks)) {
            $banks = [];
        }
        
        $newId = count($banks) > 0 ? max(array_column($banks, 'id')) + 1 : 1;
        
        $newBank = [
            'id' => $newId,
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'opening_balance' => (float) $request->opening_balance,
        ];
        
        $banks[] = $newBank;
        session(['banks' => $banks]);
        session()->save();

        return redirect()->route('admin.banks.all')->with('success', 'Bank created successfully!');
    }

    public function updateBank(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $banks = $this->getBanks();
        if (!is_array($banks)) {
            $banks = [];
        }
        
        $banks = array_map(function($bank) use ($id, $request) {
            if ($bank['id'] == $id) {
                return [
                    'id' => $id,
                    'bank_name' => $request->bank_name,
                    'account_holder_name' => $request->account_holder_name,
                    'account_number' => $request->account_number,
                    'ifsc_code' => $request->ifsc_code,
                    'opening_balance' => (float) $request->opening_balance,
                ];
            }
            return $bank;
        }, $banks);
        
        session(['banks' => array_values($banks)]);
        session()->save();

        return redirect()->route('admin.banks.all')->with('success', 'Bank updated successfully!');
    }

    public function deleteBank($id)
    {
        $banks = $this->getBanks();
        if (!is_array($banks)) {
            $banks = [];
        }
        
        $banks = array_filter($banks, function($bank) use ($id) {
            return $bank['id'] != $id;
        });
        
        session(['banks' => array_values($banks)]);
        session()->save();

        return redirect()->route('admin.banks.all')->with('success', 'Bank deleted successfully!');
    }

    // Payments Into Bank Management
    private function getPaymentsIntoBank()
    {
        if (session()->has('payments_into_bank')) {
            return session('payments_into_bank');
        }
        
        return [];
    }

    public function paymentsIntoBank()
    {
        return redirect()->route('admin.payments-into-bank.create');
    }

    public function createPaymentIntoBank()
    {
        $banks = $this->getBanks();
        $payments = $this->getPaymentsIntoBank();
        return view('admin.payments-into-bank.create', [
            'banks' => $banks,
            'payments' => $payments,
        ]);
    }

    public function allPaymentsIntoBank()
    {
        $payments = $this->getPaymentsIntoBank();
        $banks = $this->getBanks();
        return view('admin.payments-into-bank.all', [
            'payments' => $payments,
            'banks' => $banks,
        ]);
    }

    public function editPaymentIntoBank($id)
    {
        $payments = $this->getPaymentsIntoBank();
        $payment = collect($payments)->firstWhere('id', $id);
        $banks = $this->getBanks();
        
        if (!$payment) {
            return redirect()->route('admin.payments-into-bank.all')->with('error', 'Payment not found');
        }

        return view('admin.payments-into-bank.edit', [
            'payment' => $payment,
            'banks' => $banks,
            'payments' => $payments,
        ]);
    }

    public function storePaymentIntoBank(Request $request)
    {
        $request->validate([
            'bank_account' => 'required|string|max:255',
            'mode_of_payment' => 'required|string|in:UPI,Cash,Netf',
            'type' => 'required|string|in:Credit,Debit',
            'category_bank' => 'required|string|max:255',
            'transaction_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'remark' => 'required|string',
        ]);

        $payments = $this->getPaymentsIntoBank();
        if (!is_array($payments)) {
            $payments = [];
        }
        
        $newId = count($payments) > 0 ? max(array_column($payments, 'id')) + 1 : 1;
        
        $newPayment = [
            'id' => $newId,
            'bank_account' => $request->bank_account,
            'mode_of_payment' => $request->mode_of_payment,
            'type' => $request->type,
            'category_bank' => $request->category_bank,
            'transaction_no' => $request->transaction_no,
            'amount' => $request->amount,
            'remark' => $request->remark,
            'created_at' => now()->toDateTimeString(),
        ];
        
        $payments[] = $newPayment;
        session(['payments_into_bank' => $payments]);
        session()->save();

        return redirect()->route('admin.payments-into-bank.all')->with('success', 'Payment added successfully!');
    }

    public function updatePaymentIntoBank(Request $request, $id)
    {
        $request->validate([
            'bank_account' => 'required|string|max:255',
            'mode_of_payment' => 'required|string|in:UPI,Cash,Netf',
            'type' => 'required|string|in:Credit,Debit',
            'category_bank' => 'required|string|max:255',
            'transaction_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'remark' => 'required|string',
        ]);

        $payments = $this->getPaymentsIntoBank();
        if (!is_array($payments)) {
            $payments = [];
        }
        
        $payments = array_map(function($payment) use ($id, $request) {
            if ($payment['id'] == $id) {
                return [
                    'id' => $id,
                    'bank_account' => $request->bank_account,
                    'mode_of_payment' => $request->mode_of_payment,
                    'type' => $request->type,
                    'category_bank' => $request->category_bank,
                    'transaction_no' => $request->transaction_no,
                    'amount' => $request->amount,
                    'remark' => $request->remark,
                    'created_at' => $payment['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
            return $payment;
        }, $payments);
        
        session(['payments_into_bank' => array_values($payments)]);
        session()->save();

        return redirect()->route('admin.payments-into-bank.all')->with('success', 'Payment updated successfully!');
    }

    public function deletePaymentIntoBank($id)
    {
        $payments = $this->getPaymentsIntoBank();
        if (!is_array($payments)) {
            $payments = [];
        }
        
        $payments = array_filter($payments, function($payment) use ($id) {
            return $payment['id'] != $id;
        });
        
        session(['payments_into_bank' => array_values($payments)]);
        session()->save();

        return redirect()->route('admin.payments-into-bank.all')->with('success', 'Payment deleted successfully!');
    }

    // Payments Management (Single & Bulk)
    private function getPayments()
    {
        if (session()->has('payments')) {
            return session('payments');
        }
        
        return [];
    }

    private function getPaymentGateways()
    {
        if (session()->has('payment_gateways')) {
            return session('payment_gateways');
        }
        
        // Default payment gateways structure for future API keys
        $defaultGateways = [
            [
                'id' => 1,
                'name' => 'Razorpay',
                'api_key' => '',
                'api_secret' => '',
                'status' => 'Inactive',
                'mode' => 'sandbox', // sandbox or live
            ],
            [
                'id' => 2,
                'name' => 'Stripe',
                'api_key' => '',
                'api_secret' => '',
                'status' => 'Inactive',
                'mode' => 'sandbox',
            ],
            [
                'id' => 3,
                'name' => 'PayPal',
                'api_key' => '',
                'api_secret' => '',
                'status' => 'Inactive',
                'mode' => 'sandbox',
            ],
        ];
        
        session(['payment_gateways' => $defaultGateways]);
        return $defaultGateways;
    }

    private function getCategories()
    {
        // Categories based on payment mode
        return [
            'UPI' => ['Online Payment', 'UPI Transfer', 'QR Payment', 'Mobile Payment'],
            'Cash' => ['Cash Deposit', 'Cash Withdrawal', 'Cash Payment', 'Cash Collection'],
            'Netf' => ['NEFT Transfer', 'RTGS Transfer', 'IMPS Transfer', 'Bank Transfer'],
        ];
    }

    public function payments()
    {
        return redirect()->route('admin.payments.create');
    }

    public function createPayment()
    {
        $networks = $this->getNetworks();
        $banks = $this->getBanks();
        // Get only wallet type categories from database
        $walletCategories = BookingCategory::where('type', 'wallet')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->toArray();
        
        // Format categories for the view (maintaining structure for payment modes)
        $categories = [
            'UPI' => $walletCategories,
            'Cash' => $walletCategories,
            'Netf' => $walletCategories,
        ];
        
        $payments = $this->getPayments();
        $gateways = $this->getPaymentGateways();
        
        return view('admin.payments.create', [
            'networks' => $networks,
            'banks' => $banks,
            'categories' => $categories,
            'payments' => $payments,
            'gateways' => $gateways,
        ]);
    }

    public function allPayments()
    {
        $payments = $this->getPayments();
        $networks = $this->getNetworks();
        $banks = $this->getBanks();
        
        return view('admin.payments.all', [
            'payments' => $payments,
            'networks' => $networks,
            'banks' => $banks,
        ]);
    }

    public function editPayment($id)
    {
        $payments = $this->getPayments();
        $payment = collect($payments)->firstWhere('id', $id);
        $networks = $this->getNetworks();
        $banks = $this->getBanks();
        
        // Get only wallet type categories from database
        $walletCategories = BookingCategory::where('type', 'wallet')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->toArray();
        
        // Format categories for the view (maintaining structure for payment modes)
        $categories = [
            'UPI' => $walletCategories,
            'Cash' => $walletCategories,
            'Netf' => $walletCategories,
        ];
        
        $gateways = $this->getPaymentGateways();
        
        if (!$payment) {
            return redirect()->route('admin.payments.all')->with('error', 'Payment not found');
        }

        return view('admin.payments.edit', [
            'payment' => $payment,
            'networks' => $networks,
            'banks' => $banks,
            'categories' => $categories,
            'payments' => $payments,
            'gateways' => $gateways,
        ]);
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'network' => 'required|string|max:255',
            'transaction_no' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'mode_of_payment' => 'required|string|in:UPI,Cash,Netf',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
        ]);

        $payments = $this->getPayments();
        if (!is_array($payments)) {
            $payments = [];
        }
        
        $newId = count($payments) > 0 ? max(array_column($payments, 'id')) + 1 : 1;
        
        $newPayment = [
            'id' => $newId,
            'network' => $request->network,
            'transaction_no' => $request->transaction_no,
            'bank_account' => $request->bank_account,
            'mode_of_payment' => $request->mode_of_payment,
            'category' => $request->category,
            'amount' => $request->amount,
            'remark' => $request->remark ?? '',
            'created_at' => now()->toDateTimeString(),
        ];
        
        $payments[] = $newPayment;
        session(['payments' => $payments]);
        session()->save();

        return redirect()->route('admin.payments.all')->with('success', 'Payment added successfully!');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'network' => 'required|string|max:255',
            'transaction_no' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'mode_of_payment' => 'required|string|in:UPI,Cash,Netf',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
        ]);

        $payments = $this->getPayments();
        if (!is_array($payments)) {
            $payments = [];
        }
        
        $payments = array_map(function($payment) use ($id, $request) {
            if ($payment['id'] == $id) {
                return [
                    'id' => $id,
                    'network' => $request->network,
                    'transaction_no' => $request->transaction_no,
                    'bank_account' => $request->bank_account,
                    'mode_of_payment' => $request->mode_of_payment,
                    'category' => $request->category,
                    'amount' => $request->amount,
                    'remark' => $request->remark ?? '',
                    'created_at' => $payment['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
            return $payment;
        }, $payments);
        
        session(['payments' => array_values($payments)]);
        session()->save();

        return redirect()->route('admin.payments.all')->with('success', 'Payment updated successfully!');
    }

    public function deletePayment($id)
    {
        $payments = $this->getPayments();
        if (!is_array($payments)) {
            $payments = [];
        }
        
        $payments = array_filter($payments, function($payment) use ($id) {
            return $payment['id'] != $id;
        });
        
        session(['payments' => array_values($payments)]);
        session()->save();

        return redirect()->route('admin.payments.all')->with('success', 'Payment deleted successfully!');
    }

    // Manage Wallet (Bulk Update)
    public function manageWallet()
    {
        $awbUploads = $this->getAwbUploads();
        $networks = $this->getNetworks();
        $categories = $this->getCategories();
        $walletTransactions = session('wallet_transactions', []);
        
        return view('admin.payments.wallet', [
            'awbUploads' => $awbUploads,
            'networks' => $networks,
            'categories' => $categories,
            'walletTransactions' => $walletTransactions,
        ]);
    }

    public function bulkUpdateWallet(Request $request)
    {
        $request->validate([
            'awb_number' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'transaction_type' => 'required|string|max:255',
            'mode' => 'required|string|in:UPI,Cash,Netf',
            'type' => 'required|string|in:Credit,Debit',
            'amount' => 'required|numeric|min:0',
            'remark' => 'required|string',
        ]);

        $walletTransactions = session('wallet_transactions', []);
        if (!is_array($walletTransactions)) {
            $walletTransactions = [];
        }
        
        $newId = count($walletTransactions) > 0 ? max(array_column($walletTransactions, 'id')) + 1 : 1;
        
        $newTransaction = [
            'id' => $newId,
            'awb_number' => $request->awb_number,
            'network' => $request->network,
            'transaction_type' => $request->transaction_type,
            'mode' => $request->mode,
            'type' => $request->type,
            'amount' => $request->amount,
            'remark' => $request->remark,
            'created_at' => now()->toDateTimeString(),
        ];
        
        $walletTransactions[] = $newTransaction;
        session(['wallet_transactions' => $walletTransactions]);
        session()->save();

        return redirect()->route('admin.payments.wallet')->with('success', 'Wallet transaction added successfully!');
    }

    // Payment Gateways Management
    public function paymentGateways()
    {
        $gateways = $this->getPaymentGateways();
        return view('admin.payments.gateways.all', [
            'gateways' => $gateways,
        ]);
    }

    public function createPaymentGateway()
    {
        $gateways = $this->getPaymentGateways();
        return view('admin.payments.gateways.create', [
            'gateways' => $gateways,
        ]);
    }

    public function storePaymentGateway(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'mode' => 'required|string|in:sandbox,live',
            'status' => 'nullable|boolean',
        ]);

        $status = $request->has('status') && $request->status ? 'Active' : 'Inactive';

        $gateways = $this->getPaymentGateways();
        if (!is_array($gateways)) {
            $gateways = [];
        }
        
        $newId = count($gateways) > 0 ? max(array_column($gateways, 'id')) + 1 : 1;
        
        $newGateway = [
            'id' => $newId,
            'name' => $request->name,
            'api_key' => $request->api_key ?? '',
            'api_secret' => $request->api_secret ?? '',
            'mode' => $request->mode,
            'status' => $status,
            'created_at' => now()->toDateTimeString(),
        ];
        
        $gateways[] = $newGateway;
        session(['payment_gateways' => $gateways]);
        session()->save();

        return redirect()->route('admin.payments.gateways')->with('success', 'Payment gateway added successfully!');
    }

    public function editPaymentGateway($id)
    {
        $gateways = $this->getPaymentGateways();
        $gateway = collect($gateways)->firstWhere('id', $id);
        
        if (!$gateway) {
            return redirect()->route('admin.payments.gateways')->with('error', 'Payment gateway not found');
        }

        return view('admin.payments.gateways.edit', [
            'gateway' => $gateway,
            'gateways' => $gateways,
        ]);
    }

    public function updatePaymentGateway(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'mode' => 'required|string|in:sandbox,live',
            'status' => 'nullable|boolean',
        ]);

        $status = $request->has('status') && $request->status ? 'Active' : 'Inactive';

        $gateways = $this->getPaymentGateways();
        if (!is_array($gateways)) {
            $gateways = [];
        }
        
        $gateways = array_map(function($gateway) use ($id, $request, $status) {
            if ($gateway['id'] == $id) {
                return [
                    'id' => $id,
                    'name' => $request->name,
                    'api_key' => $request->api_key ?? $gateway['api_key'] ?? '',
                    'api_secret' => $request->api_secret ?? $gateway['api_secret'] ?? '',
                    'mode' => $request->mode,
                    'status' => $status,
                    'created_at' => $gateway['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
            return $gateway;
        }, $gateways);
        
        session(['payment_gateways' => array_values($gateways)]);
        session()->save();

        return redirect()->route('admin.payments.gateways')->with('success', 'Payment gateway updated successfully!');
    }

    public function deletePaymentGateway($id)
    {
        $gateways = $this->getPaymentGateways();
        if (!is_array($gateways)) {
            $gateways = [];
        }
        
        $gateways = array_filter($gateways, function($gateway) use ($id) {
            return $gateway['id'] != $id;
        });
        
        session(['payment_gateways' => array_values($gateways)]);
        session()->save();

        return redirect()->route('admin.payments.gateways')->with('success', 'Payment gateway deleted successfully!');
    }

    // Landing Page Sections Management
    public function editServicesSection()
    {
        $settings = FrontendSetting::getSettings();
        return view('admin.landing-sections.services.edit', compact('settings'));
    }

    public function updateServicesSection(Request $request)
    {
        $settings = FrontendSetting::getSettings();
        
        $request->validate([
            'services_section_title' => 'nullable|string|max:255',
            'services_section_content' => 'nullable|string',
        ]);

        $settings->services_section_title = $request->services_section_title;
        $settings->services_section_content = $request->services_section_content;
        $settings->save();

        return redirect()->route('admin.frontend-settings')->with('success', 'Services section updated successfully!');
    }

    public function editWhyHaxoSection()
    {
        $settings = FrontendSetting::getSettings();
        return view('admin.landing-sections.why-haxo.edit', compact('settings'));
    }

    public function updateWhyHaxoSection(Request $request)
    {
        $settings = FrontendSetting::getSettings();
        
        $request->validate([
            'why_haxo_section_title' => 'nullable|string|max:255',
            'why_haxo_section_content' => 'nullable|string',
        ]);

        $settings->why_haxo_section_title = $request->why_haxo_section_title;
        $settings->why_haxo_section_content = $request->why_haxo_section_content;
        $settings->save();

        return redirect()->route('admin.frontend-settings')->with('success', 'Why Haxo section updated successfully!');
    }

    public function editPricingSection()
    {
        $settings = FrontendSetting::getSettings();
        return view('admin.landing-sections.pricing.edit', compact('settings'));
    }

    public function updatePricingSection(Request $request)
    {
        $settings = FrontendSetting::getSettings();
        
        $request->validate([
            'pricing_section_title' => 'nullable|string|max:255',
            'pricing_section_content' => 'nullable|string',
        ]);

        $settings->pricing_section_title = $request->pricing_section_title;
        $settings->pricing_section_content = $request->pricing_section_content;
        $settings->save();

        return redirect()->route('admin.frontend-settings')->with('success', 'Pricing section updated successfully!');
    }

    public function editStatsSection()
    {
        $settings = FrontendSetting::getSettings();
        return view('admin.landing-sections.stats.edit', compact('settings'));
    }

    public function updateStatsSection(Request $request)
    {
        $settings = FrontendSetting::getSettings();
        
        $request->validate([
            'stats_section_content' => 'nullable|string',
        ]);

        $settings->stats_section_content = $request->stats_section_content;
        $settings->save();

        return redirect()->route('admin.frontend-settings')->with('success', 'Stats section updated successfully!');
    }

    // Support Tickets Management
    public function supportTickets(Request $request)
    {
        $query = SupportTicket::with(['user', 'attachments']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        $tickets = $query->latest()->get();
        
        return view('admin.support-tickets.all', [
            'tickets' => $tickets,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? '',
            'priorityFilter' => $request->priority ?? '',
        ]);
    }

    public function viewSupportTicket($id)
    {
        $ticket = SupportTicket::with(['user', 'attachments'])->findOrFail($id);
        
        return view('admin.support-tickets.view', [
            'ticket' => $ticket,
        ]);
    }

    public function updateSupportTicket(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'admin_response' => 'nullable|string|max:5000',
        ]);

        $ticket = SupportTicket::with('user')->findOrFail($id);
        $oldStatus = $ticket->status;
        $oldAdminResponse = $ticket->admin_response;
        
        $ticket->update([
            'status' => $request->status,
            'priority' => $request->priority,
            'admin_response' => $request->admin_response,
            'resolved_at' => $request->status === 'resolved' || $request->status === 'closed' ? now() : null,
        ]);

        // Create notification for user when status changes
        if ($oldStatus !== $request->status) {
            $statusMessages = [
                'open' => 'Your support ticket has been opened.',
                'in_progress' => 'Your support ticket is now in progress. We are working on it!',
                'resolved' => 'Your support ticket has been resolved.',
                'closed' => 'Your support ticket has been closed.',
            ];

            $statusTitles = [
                'open' => 'Support Ticket Opened',
                'in_progress' => 'Support Ticket In Progress',
                'resolved' => 'Support Ticket Resolved',
                'closed' => 'Support Ticket Closed',
            ];

            \App\Models\Notification::create([
                'user_id' => $ticket->user_id,
                'type' => 'support_ticket_status_update',
                'title' => $statusTitles[$request->status] ?? 'Support Ticket Updated',
                'message' => $statusMessages[$request->status] ?? 'Your support ticket status has been updated.',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_subject' => $ticket->subject,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                ],
                'read' => false,
            ]);
        }

        // Create notification if admin response is added or updated
        if ($request->admin_response && $request->admin_response !== $oldAdminResponse) {
            \App\Models\Notification::create([
                'user_id' => $ticket->user_id,
                'type' => 'support_ticket_response',
                'title' => 'Admin Response to Your Support Ticket',
                'message' => 'Admin has responded to your support ticket: "' . \Illuminate\Support\Str::limit($ticket->subject, 50) . '"',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_subject' => $ticket->subject,
                    'admin_response' => $request->admin_response,
                ],
                'read' => false,
            ]);
        }

        return redirect()->route('admin.support-tickets.view', $id)
            ->with('success', 'Support ticket updated successfully!');
    }

    public function deleteSupportTicket($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        // Delete attachments
        foreach ($ticket->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }
        
        $ticket->delete();

        return redirect()->route('admin.support-tickets.all')
            ->with('success', 'Support ticket deleted successfully!');
    }

    // ==================== Settings Submenu Methods ====================
    
    // Delivery Category Methods
    public function deliveryCategoryIndex()
    {
        $categories = DeliveryCategory::latest()->get();
        return view('admin.settings.delivery-category.index', compact('categories'));
    }

    public function deliveryCategoryCreate()
    {
        return view('admin.settings.delivery-category.create');
    }

    public function deliveryCategoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        DeliveryCategory::create($request->all());

        return redirect()->route('admin.delivery-category.index')
            ->with('success', 'Delivery category created successfully!');
    }

    public function deliveryCategoryEdit($id)
    {
        $category = DeliveryCategory::findOrFail($id);
        return view('admin.settings.delivery-category.edit', compact('category'));
    }

    public function deliveryCategoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category = DeliveryCategory::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('admin.delivery-category.index')
            ->with('success', 'Delivery category updated successfully!');
    }

    public function deliveryCategoryDelete($id)
    {
        DeliveryCategory::findOrFail($id)->delete();
        return redirect()->route('admin.delivery-category.index')
            ->with('success', 'Delivery category deleted successfully!');
    }

    // Delivery Charge Methods
    public function deliveryChargeIndex()
    {
        $charges = DeliveryCharge::latest()->get();
        return view('admin.settings.delivery-charge.index', compact('charges'));
    }

    public function deliveryChargeCreate()
    {
        return view('admin.settings.delivery-charge.create');
    }

    public function deliveryChargeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_charge' => 'required|numeric|min:0',
            'per_kg_charge' => 'nullable|numeric|min:0',
            'zone' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'remark' => 'nullable|string',
        ]);

        DeliveryCharge::create($request->all());

        return redirect()->route('admin.delivery-charge.index')
            ->with('success', 'Delivery charge created successfully!');
    }

    public function deliveryChargeEdit($id)
    {
        $charge = DeliveryCharge::findOrFail($id);
        return view('admin.settings.delivery-charge.edit', compact('charge'));
    }

    public function deliveryChargeUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_charge' => 'required|numeric|min:0',
            'per_kg_charge' => 'nullable|numeric|min:0',
            'zone' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'remark' => 'nullable|string',
        ]);

        $charge = DeliveryCharge::findOrFail($id);
        $charge->update($request->all());

        return redirect()->route('admin.delivery-charge.index')
            ->with('success', 'Delivery charge updated successfully!');
    }

    public function deliveryChargeDelete($id)
    {
        DeliveryCharge::findOrFail($id)->delete();
        return redirect()->route('admin.delivery-charge.index')
            ->with('success', 'Delivery charge deleted successfully!');
    }

    // Delivery Type Methods
    public function deliveryTypeIndex()
    {
        $types = DeliveryType::latest()->get();
        return view('admin.settings.delivery-type.index', compact('types'));
    }

    public function deliveryTypeCreate()
    {
        return view('admin.settings.delivery-type.create');
    }

    public function deliveryTypeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        DeliveryType::create($request->all());

        return redirect()->route('admin.delivery-type.index')
            ->with('success', 'Delivery type created successfully!');
    }

    public function deliveryTypeEdit($id)
    {
        $type = DeliveryType::findOrFail($id);
        return view('admin.settings.delivery-type.edit', compact('type'));
    }

    public function deliveryTypeUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        $type = DeliveryType::findOrFail($id);
        $type->update($request->all());

        return redirect()->route('admin.delivery-type.index')
            ->with('success', 'Delivery type updated successfully!');
    }

    public function deliveryTypeDelete($id)
    {
        DeliveryType::findOrFail($id)->delete();
        return redirect()->route('admin.delivery-type.index')
            ->with('success', 'Delivery type deleted successfully!');
    }

    // Liquid/Fragile Methods
    public function liquidFragileIndex()
    {
        $items = LiquidFragile::latest()->get();
        return view('admin.settings.liquid-fragile.index', compact('items'));
    }

    public function liquidFragileCreate()
    {
        return view('admin.settings.liquid-fragile.create');
    }

    public function liquidFragileStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Liquid,Fragile,Both',
            'additional_charge' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        LiquidFragile::create($request->all());

        return redirect()->route('admin.liquid-fragile.index')
            ->with('success', 'Liquid/Fragile item created successfully!');
    }

    public function liquidFragileEdit($id)
    {
        $item = LiquidFragile::findOrFail($id);
        return view('admin.settings.liquid-fragile.edit', compact('item'));
    }

    public function liquidFragileUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Liquid,Fragile,Both',
            'additional_charge' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        $item = LiquidFragile::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('admin.liquid-fragile.index')
            ->with('success', 'Liquid/Fragile item updated successfully!');
    }

    public function liquidFragileDelete($id)
    {
        LiquidFragile::findOrFail($id)->delete();
        return redirect()->route('admin.liquid-fragile.index')
            ->with('success', 'Liquid/Fragile item deleted successfully!');
    }

    // SMS Settings Methods
    public function smsSettingsIndex()
    {
        $settings = SmsSetting::getSettings();
        return view('admin.settings.sms-settings.index', compact('settings'));
    }

    public function smsSettingsUpdate(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'sender_id' => 'nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
        ]);

        $settings = SmsSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.sms-settings.index')
            ->with('success', 'SMS settings updated successfully!');
    }

    // SMS Send Settings Methods
    public function smsSendSettingsIndex()
    {
        $settings = SmsSendSetting::getSettings();
        return view('admin.settings.sms-send-settings.index', compact('settings'));
    }

    public function smsSendSettingsUpdate(Request $request)
    {
        $request->validate([
            'send_on_booking' => 'sometimes|boolean',
            'send_on_delivery' => 'sometimes|boolean',
            'send_on_pickup' => 'sometimes|boolean',
            'send_on_status_update' => 'sometimes|boolean',
            'booking_template' => 'nullable|string',
            'delivery_template' => 'nullable|string',
            'pickup_template' => 'nullable|string',
            'status_update_template' => 'nullable|string',
        ]);

        $settings = SmsSendSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.sms-send-settings.index')
            ->with('success', 'SMS send settings updated successfully!');
    }

    // GoogleMap Settings Methods
    public function googlemapSettingsIndex()
    {
        $settings = GooglemapSetting::getSettings();
        return view('admin.settings.googlemap-settings.index', compact('settings'));
    }

    public function googlemapSettingsUpdate(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
            'map_type' => 'required|in:roadmap,satellite,hybrid,terrain',
            'zoom_level' => 'required|integer|min:1|max:20',
        ]);

        $settings = GooglemapSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.googlemap-settings.index')
            ->with('success', 'GoogleMap settings updated successfully!');
    }

    // Mail Settings Methods
    public function mailSettingsIndex()
    {
        $settings = MailSetting::getSettings();
        return view('admin.settings.mail-settings.index', compact('settings'));
    }

    public function mailSettingsUpdate(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string|max:255',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|max:255',
            'from_address' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
        ]);

        $settings = MailSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.mail-settings.index')
            ->with('success', 'Mail settings updated successfully!');
    }

    // Social Login Settings Methods
    public function socialLoginIndex()
    {
        $settings = SocialLoginSetting::getSettings();
        return view('admin.settings.social-login.index', compact('settings'));
    }

    public function socialLoginUpdate(Request $request)
    {
        $request->validate([
            'google_enabled' => 'sometimes|boolean',
            'google_client_id' => 'nullable|string|max:255',
            'google_client_secret' => 'nullable|string|max:255',
            'facebook_enabled' => 'sometimes|boolean',
            'facebook_app_id' => 'nullable|string|max:255',
            'facebook_app_secret' => 'nullable|string|max:255',
            'twitter_enabled' => 'sometimes|boolean',
            'twitter_client_id' => 'nullable|string|max:255',
            'twitter_client_secret' => 'nullable|string|max:255',
        ]);

        $settings = SocialLoginSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.social-login.index')
            ->with('success', 'Social login settings updated successfully!');
    }

    // Payment Setup Methods
    public function paymentSetupIndex()
    {
        $gateways = PaymentSetup::latest()->get();
        return view('admin.settings.payment-setup.index', compact('gateways'));
    }

    public function paymentSetupUpdate(Request $request)
    {
        $request->validate([
            'gateway_name' => 'required|string|max:255',
            'gateway_type' => 'required|in:online,offline',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'merchant_id' => 'nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
            'test_mode' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        if ($request->has('id')) {
            $gateway = PaymentSetup::findOrFail($request->id);
            $gateway->update($request->except('id'));
        } else {
            PaymentSetup::create($request->except('id'));
        }

        return redirect()->route('admin.payment-setup.index')
            ->with('success', 'Payment gateway updated successfully!');
    }

    // Packaging Methods
    public function packagingIndex()
    {
        $packages = Packaging::latest()->get();
        return view('admin.settings.packaging.index', compact('packages'));
    }

    public function packagingCreate()
    {
        return view('admin.settings.packaging.create');
    }

    public function packagingStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        Packaging::create($request->all());

        return redirect()->route('admin.packaging.index')
            ->with('success', 'Packaging created successfully!');
    }

    public function packagingEdit($id)
    {
        $package = Packaging::findOrFail($id);
        return view('admin.settings.packaging.edit', compact('package'));
    }

    public function packagingUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        $package = Packaging::findOrFail($id);
        $package->update($request->all());

        return redirect()->route('admin.packaging.index')
            ->with('success', 'Packaging updated successfully!');
    }

    public function packagingDelete($id)
    {
        Packaging::findOrFail($id)->delete();
        return redirect()->route('admin.packaging.index')
            ->with('success', 'Packaging deleted successfully!');
    }

    // Currency Methods
    public function currencyIndex()
    {
        $currencies = Currency::latest()->get();
        return view('admin.settings.currency.index', compact('currencies'));
    }

    public function currencyCreate()
    {
        return view('admin.settings.currency.create');
    }

    public function currencyStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($request->has('is_default') && $request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create($request->all());

        return redirect()->route('admin.currency.index')
            ->with('success', 'Currency created successfully!');
    }

    public function currencyEdit($id)
    {
        $currency = Currency::findOrFail($id);
        return view('admin.settings.currency.edit', compact('currency'));
    }

    public function currencyUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code,' . $id,
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        $currency = Currency::findOrFail($id);
        
        if ($request->has('is_default') && $request->is_default) {
            Currency::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $currency->update($request->all());

        return redirect()->route('admin.currency.index')
            ->with('success', 'Currency updated successfully!');
    }

    public function currencyDelete($id)
    {
        $currency = Currency::findOrFail($id);
        if ($currency->is_default) {
            return redirect()->route('admin.currency.index')
                ->with('error', 'Cannot delete default currency!');
        }
        $currency->delete();
        return redirect()->route('admin.currency.index')
            ->with('success', 'Currency deleted successfully!');
    }

    public function currencySetDefault(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|exists:currencies,id',
        ]);

        Currency::where('is_default', true)->update(['is_default' => false]);
        Currency::findOrFail($request->currency_id)->update(['is_default' => true]);

        return redirect()->route('admin.currency.index')
            ->with('success', 'Default currency updated successfully!');
    }

    /**
     * Show salary generate index page
     */
    public function salaryGenerateIndex()
    {
        // Fetch only users who have logged into the website (have last_login_at set)
        $users = User::whereIn('role', ['merchant', 'deliveryman', 'user'])
            ->whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->get();
        return view('admin.payroll.salary-generate.index', compact('users'));
    }

    /**
     * Store manual salary generation
     */
    public function salaryGenerateStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_type' => 'required|in:merchant,deliveryman,user',
            'salary_amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'generation_type' => 'required|in:manual,calendar',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Store payroll data in session (you can later move this to database)
        $payrolls = session('payrolls', []);
        $payrolls[] = [
            'id' => count($payrolls) + 1,
            'user_id' => $request->user_id,
            'user_type' => $request->user_type,
            'salary_amount' => $request->salary_amount,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'generation_type' => $request->generation_type,
            'remarks' => $request->remarks,
            'created_at' => now()->toDateTimeString(),
        ];
        session(['payrolls' => $payrolls]);
        session()->save();

        return redirect()->route('admin.payroll.salary-generate.index')
            ->with('success', 'Salary generated successfully!');
    }

    /**
     * Auto generate salary for all users of a type
     */
    public function salaryGenerateAuto(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:merchant,deliveryman,user',
            'salary_amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'generation_type' => 'required|in:manual,calendar',
        ]);

        // Fetch only users who have logged into the website (have last_login_at set)
        $users = User::where('role', $request->user_type)
            ->whereNotNull('last_login_at')
            ->get();

        $payrolls = session('payrolls', []);
        $baseId = 0;
        if (count($payrolls) > 0 && !empty($payrolls)) {
            $ids = array_column($payrolls, 'id');
            if (!empty($ids)) {
                $baseId = max($ids);
            }
        }

        foreach ($users as $user) {
            $baseId++;
            $payrolls[] = [
                'id' => $baseId,
                'user_id' => $user->id,
                'user_type' => $request->user_type,
                'salary_amount' => $request->salary_amount,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'generation_type' => $request->generation_type,
                'remarks' => 'Auto generated for all ' . $request->user_type . 's',
                'created_at' => now()->toDateTimeString(),
            ];
        }

        session(['payrolls' => $payrolls]);
        session()->save();

        return redirect()->route('admin.payroll.salary-generate.index')
            ->with('success', 'Salary generated automatically for ' . $users->count() . ' user(s)!');
    }

    /**
     * Show payroll list
     */
    public function payrollList()
    {
        $payrolls = session('payrolls', []);
        
        // Get user details for each payroll
        foreach ($payrolls as &$payroll) {
            $user = User::find($payroll['user_id']);
            $payroll['user'] = $user;
        }

        return view('admin.payroll.list.index', compact('payrolls'));
    }

    /**
     * Show sand bullary generate index page
     */
    public function sandBullaryGenerateIndex()
    {
        return view('admin.payroll.sand-bullary-generate.index');
    }
}

