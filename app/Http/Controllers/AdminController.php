<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FrontendSetting;
use App\Models\Bank;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        
        // Calculate Bank Account Balances
        $banks = $this->getBanks();
        $allPayments = $this->getPaymentsIntoBank();
        
        $bankBalances = [];
        $totalBankBalance = 0;
        
        foreach ($banks as $index => $bank) {
            $bankIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            $openingBalance = $bank['opening_balance'] ?? 0;
            
            // Calculate credits and debits for this bank
            $credits = 0;
            $debits = 0;
            
            foreach ($allPayments as $payment) {
                if (($payment['bank_account'] ?? '') === $bankIdentifier) {
                    if (($payment['type'] ?? '') === 'Credit') {
                        $credits += floatval($payment['amount'] ?? 0);
                    } elseif (($payment['type'] ?? '') === 'Debit') {
                        $debits += floatval($payment['amount'] ?? 0);
                    }
                }
            }
            
            $currentBalance = $openingBalance + $credits - $debits;
            $bankBalances[] = [
                'name' => $bank['bank_name'] ?? 'Account ' . ($index + 1),
                'balance' => $currentBalance,
            ];
            $totalBankBalance += $currentBalance;
        }
        
        // Calculate Network Wallet Balances
        $networks = $this->getNetworks();
        $networkBalances = [];
        
        foreach ($networks as $index => $network) {
            $networkId = $network['id'] ?? null;
            $openingBalance = $network['opening_balance'] ?? 0;
            
            if ($networkId) {
                // Get transactions from database
                $transactions = \App\Models\NetworkTransaction::where('network_id', $networkId)->get();
                $credits = $transactions->where('type', 'credit')->sum('amount');
                $debits = $transactions->where('type', 'debit')->sum('amount');
                $currentBalance = $openingBalance + $credits - $debits;
            } else {
                $currentBalance = $openingBalance;
            }
            
            $networkBalances[] = [
                'name' => $network['name'] ?? 'Network ' . ($index + 1),
                'balance' => $currentBalance,
            ];
        }
        
        // Calculate Expense Category Wise
        $expenseCategories = [
            'Fuel' => 0,
            'Maintenance' => 0,
            'Salaries' => 0,
            'Other' => 0,
        ];
        
        foreach ($allPayments as $payment) {
            if (($payment['type'] ?? '') === 'Debit') {
                $category = $payment['category_bank'] ?? 'Other';
                $amount = floatval($payment['amount'] ?? 0);
                
                // Map category_bank to expense categories
                if ($category === 'Salary') {
                    $expenseCategories['Salaries'] += $amount;
                } elseif ($category === 'Expense') {
                    // Check remark for more specific category
                    $remark = strtolower($payment['remark'] ?? '');
                    if (strpos($remark, 'fuel') !== false || strpos($remark, 'petrol') !== false || strpos($remark, 'diesel') !== false) {
                        $expenseCategories['Fuel'] += $amount;
                    } elseif (strpos($remark, 'maintenance') !== false || strpos($remark, 'repair') !== false) {
                        $expenseCategories['Maintenance'] += $amount;
                    } else {
                        $expenseCategories['Other'] += $amount;
                    }
                } else {
                    $expenseCategories['Other'] += $amount;
                }
            }
        }
        
        $totalExpense = array_sum($expenseCategories);
        
        // Generate last 7 days dates for graphs
        $dates = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('d-m-Y');
            
            // Calculate actual income and expense for each day
            $dayIncome = 0;
            $dayExpense = 0;
            
            foreach ($allPayments as $payment) {
                $paymentDate = isset($payment['created_at']) ? date('Y-m-d', strtotime($payment['created_at'])) : null;
                if ($paymentDate === $date->format('Y-m-d')) {
                    if (($payment['type'] ?? '') === 'Credit') {
                        $dayIncome += floatval($payment['amount'] ?? 0);
                    } elseif (($payment['type'] ?? '') === 'Debit') {
                        $dayExpense += floatval($payment['amount'] ?? 0);
                    }
                }
            }
            
            $incomeData[] = $dayIncome;
            $expenseData[] = $dayExpense;
        }
        
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'dates' => $dates,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'bankBalances' => $bankBalances,
            'totalBankBalance' => $totalBankBalance,
            'networkBalances' => $networkBalances,
            'expenseCategories' => $expenseCategories,
            'totalExpense' => $totalExpense,
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
            if ($settings->logo) {
                if (Storage::disk('public')->exists($settings->logo)) {
                    Storage::disk('public')->delete($settings->logo);
                }
                $this->deleteMirroredPublicFile($settings->logo);
            }
            $logoPath = $request->file('logo')->store('frontend', 'public');
            $settings->logo = $logoPath;
            $this->mirrorFileToPublicStorage($logoPath);
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($settings->banner) {
                if (Storage::disk('public')->exists($settings->banner)) {
                    Storage::disk('public')->delete($settings->banner);
                }
                $this->deleteMirroredPublicFile($settings->banner);
            }
            $bannerPath = $request->file('banner')->store('frontend', 'public');
            $settings->banner = $bannerPath;
            $this->mirrorFileToPublicStorage($bannerPath);
        }

        // Handle footer logo upload
        if ($request->hasFile('footer_logo')) {
            // Delete old footer logo if exists
            if ($settings->footer_logo) {
                if (Storage::disk('public')->exists($settings->footer_logo)) {
                    Storage::disk('public')->delete($settings->footer_logo);
                }
                $this->deleteMirroredPublicFile($settings->footer_logo);
            }
            $footerLogoPath = $request->file('footer_logo')->store('frontend', 'public');
            $settings->footer_logo = $footerLogoPath;
            $this->mirrorFileToPublicStorage($footerLogoPath);
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
        $countries = $this->getCountries(true); // Get only active countries
        $shipmentTypes = ['Dox', 'Non-Dox', 'Medicine', 'Special'];

        return view('admin.rate-calculator', [
            'countries' => $countries,
            'shipmentTypes' => $shipmentTypes,
        ]);
    }

    // API endpoint to get pincodes and zones for a country
    public function getPincodesByCountry(Request $request)
    {
        $countryName = $request->input('country');
        
        if (empty($countryName)) {
            return response()->json([
                'success' => false,
                'message' => 'Country name is required'
            ], 400);
        }

        $zones = $this->getZones(true); // Get only active zones
        
        // Filter zones by country (already filtered by active status)
        $countryZones = collect($zones)->filter(function($zone) use ($countryName) {
            return ($zone['country'] ?? '') == $countryName;
        });

        // Group by pincode and collect unique zones
        $pincodeZoneMap = [];
        foreach ($countryZones as $zone) {
            $pincode = $zone['pincode'] ?? '';
            if (!empty($pincode)) {
                if (!isset($pincodeZoneMap[$pincode])) {
                    $pincodeZoneMap[$pincode] = [
                        'pincode' => $pincode,
                        'zones' => []
                    ];
                }
                $zoneName = $zone['zone'] ?? '';
                if (!empty($zoneName) && !in_array($zoneName, $pincodeZoneMap[$pincode]['zones'])) {
                    $pincodeZoneMap[$pincode]['zones'][] = $zoneName;
                }
            }
        }

        // Format response: array of {pincode, zones[]}
        $result = array_values($pincodeZoneMap);

        return response()->json([
            'success' => true,
            'data' => $result
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

        // Get zones based on pincodes (only active zones)
        $zones = $this->getZones(true);
        $destinationZone = null;
        
        // Find destination zone from destination pincode (for base price matching)
        $destinationZoneData = collect($zones)->first(function($zone) use ($request) {
            return ($zone['pincode'] ?? '') == $request->destination_pincode 
                && ($zone['country'] ?? '') == $request->destination_country;
        });
        if ($destinationZoneData) {
            $destinationZone = $destinationZoneData['zone'] ?? null;
        }
        
        // Get all formulas, shipping charges, and services (only active ones)
        $allFormulas = $this->getFormulas(true);
        $allShippingCharges = $this->getShippingCharges();
        $allServices = $this->getServices(true);
        $weight = (float)$request->weight;
        
        // ============================================
        // BASE PRICE: Find from shipping charges
        // Matching criteria:
        // 1. Destination zone matches (from destination pincode)
        // 2. Find best matching network/service combination
        // ============================================
        $baseRate = 0;
        $matchingBaseCharge = null;
        $selectedNetwork = null;
        $selectedService = null;
        
        // Find all shipping charges matching destination zone
        $matchingBaseCharges = collect($allShippingCharges)->filter(function($charge) use ($destinationZone) {
            // Must match destination zone (from destination pincode)
            if ($destinationZone) {
                $chargeDestinationZone = trim($charge['destination_zone'] ?? '');
                if ($chargeDestinationZone != $destinationZone) {
                    return false;
                }
            }
            
            return true;
        })->sortBy(function($charge) use ($destinationZone) {
            // Prioritize exact zone matches, then by rate (lowest first)
            $score = 0;
            if ($destinationZone && ($charge['destination_zone'] ?? '') == $destinationZone) {
                $score += 10;
            }
            // Add rate as secondary sort (lower rate = higher priority)
            $rate = (float)($charge['rate'] ?? 999999);
            $score += (1000 - $rate) / 1000; // Normalize rate for sorting
            return -$score; // Negative for descending sort
        })->values()->toArray();
        
        // Group matching charges by network/service combination
        $groupedCharges = [];
        foreach ($matchingBaseCharges as $charge) {
            $network = trim($charge['network'] ?? '');
            $service = trim($charge['service'] ?? '');
            $key = $network . '|' . $service;
            
            if (!isset($groupedCharges[$key])) {
                $groupedCharges[$key] = [];
            }
            $groupedCharges[$key][] = $charge;
        }
        
        // Get base rate from best matching shipping charge
        // Priority: 1) Lowest rate, 2) First match
        $matchingBaseCharge = null;
        $allMatchingCharges = [];
        $selectedServiceDetails = null;
        
        if (!empty($matchingBaseCharges)) {
            // Sort by rate (lowest first) to get best price
            $sortedCharges = collect($matchingBaseCharges)->sortBy(function($charge) {
                return (float)($charge['rate'] ?? 999999);
            })->values()->toArray();
            
            $matchingBaseCharge = $sortedCharges[0];
            $baseRate = (float)($matchingBaseCharge['rate'] ?? 0);
            $selectedNetwork = trim($matchingBaseCharge['network'] ?? '');
            $selectedService = trim($matchingBaseCharge['service'] ?? '');
            
            // Find service details for selected network/service
            $selectedServiceDetails = collect($allServices)->first(function($service) use ($selectedNetwork, $selectedService) {
                $serviceNetwork = trim($service['network'] ?? '');
                $serviceName = trim($service['name'] ?? '');
                return $serviceNetwork == $selectedNetwork && $serviceName == $selectedService;
            });
            
            // Collect all matching charges with same network/service for display
            $allMatchingCharges = collect($matchingBaseCharges)->filter(function($charge) use ($selectedNetwork, $selectedService) {
                return trim($charge['network'] ?? '') == $selectedNetwork && 
                       trim($charge['service'] ?? '') == $selectedService;
            })->values()->toArray();
        }
        
        // Default base rate if no match found
        if ($baseRate <= 0) {
            $baseRate = 100; // Default fallback
            }
        
        // ============================================
        // WEIGHT PRICE: Find from formulas
        // Matching criteria:
        // 1. Network matches (from base charge)
        // 2. Service matches (from base charge)
        // Priority: Exact match > General formulas
        // ============================================
        $appliedFormulas = [];
        $formulaChargeTotal = 0;
        
        // All formulas are already filtered to active only
        $allActiveFormulas = collect($allFormulas);
        
        // Find formulas that match the exact network and service from base charge
        $applicableFormulas = collect([]);
        
        if (!empty($selectedNetwork) && !empty($selectedService)) {
            // Filter formulas that match the specific network and service EXACTLY
            // Priority: Exact match (network AND service) > General formulas (no network/service)
            $specificFormulas = $allActiveFormulas->filter(function($formula) use ($selectedNetwork, $selectedService) {
                $formulaNetwork = trim($formula['network'] ?? '');
                $formulaService = trim($formula['service'] ?? '');
                
                // Must match both network and service exactly
                // OR formula has no network/service specified (general formula applies to all)
                if (empty($formulaNetwork) && empty($formulaService)) {
                    // General formula - applies to all
                    return true;
                }
                
                // Exact match required
                return $formulaNetwork == $selectedNetwork && $formulaService == $selectedService;
            });
            
            // Use the matching formulas
            $applicableFormulas = $specificFormulas;
        } else {
            // If no network/service from base charge, use general formulas only (no network/service)
            $applicableFormulas = $allActiveFormulas->filter(function($formula) {
                $formulaNetwork = trim($formula['network'] ?? '');
                $formulaService = trim($formula['service'] ?? '');
                return empty($formulaNetwork) && empty($formulaService);
            });
        }
        
        // Sort by priority (1st, 2nd, 3rd, 4th)
        $applicableFormulas = $applicableFormulas->sortBy(function($formula) {
            $priority = $formula['priority'] ?? '4th';
            $priorityMap = ['1st' => 1, '2nd' => 2, '3rd' => 3, '4th' => 4];
            return $priorityMap[$priority] ?? 4;
        })->values();
        
        // Calculate charges for each applicable formula
        foreach ($applicableFormulas as $formula) {
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
                'name' => $formula['formula_name'] ?? 'Formula',
                'calculated_charge' => round($calculatedCharge, 2),
                'network' => $selectedNetwork ?: ($formula['network'] ?? 'N/A'),
                'service' => $selectedService ?: ($formula['service'] ?? 'N/A'),
            ]);
        }
        
        // Calculate weight charge (use formula total if available, otherwise default)
        $weightCharge = $formulaChargeTotal > 0 ? $formulaChargeTotal : ($weight * 10);
        
        // Calculate distance charge (default calculation)
        $distanceCharge = 20.00;
        
        // Calculate total rate
        $totalRate = $baseRate + $weightCharge + $distanceCharge;
        
        // Helper function to calculate weight charge for a given network/service
        $calculateWeightChargeForNetworkService = function($network, $service, $baseRateForOption) use ($allActiveFormulas, $weight) {
            $optionFormulas = [];
            $optionFormulaTotal = 0;
            
            // Filter formulas for this network/service
            $optionApplicableFormulas = $allActiveFormulas->filter(function($formula) use ($network, $service) {
                $formulaNetwork = trim($formula['network'] ?? '');
                $formulaService = trim($formula['service'] ?? '');
                
                // General formula applies to all
                if (empty($formulaNetwork) && empty($formulaService)) {
                    return true;
                }
                
                // Exact match required
                return $formulaNetwork == $network && $formulaService == $service;
            })->sortBy(function($formula) {
                $priority = $formula['priority'] ?? '4th';
                $priorityMap = ['1st' => 1, '2nd' => 2, '3rd' => 3, '4th' => 4];
                return $priorityMap[$priority] ?? 4;
            })->values();
            
            // Calculate charges for each formula
            foreach ($optionApplicableFormulas as $formula) {
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
                        $percentageAmount = ($baseRateForOption * $formulaValue / 100);
                        $calculatedCharge = $percentageAmount * $weight;
                    } else {
                        $calculatedCharge = $baseRateForOption * $formulaValue / 100;
                    }
                }
                
                $optionFormulaTotal += $calculatedCharge;
                
                $optionFormulas[] = array_merge($formula, [
                    'name' => $formula['formula_name'] ?? 'Formula',
                    'calculated_charge' => round($calculatedCharge, 2),
                ]);
            }
            
            $weightCharge = $optionFormulaTotal > 0 ? $optionFormulaTotal : ($weight * 10);
            
            return [
                'weight_charge' => round($weightCharge, 2),
                'formulas' => $optionFormulas,
            ];
        };
        
        // Prepare all matching network/service combinations with weight charges
        $allNetworkServiceOptions = [];
        if (!empty($groupedCharges)) {
            foreach ($groupedCharges as $key => $charges) {
                // Get the best rate for this network/service combination
                $bestCharge = collect($charges)->sortBy(function($charge) {
                    return (float)($charge['rate'] ?? 999999);
                })->first();
                
                if ($bestCharge) {
                    $optionNetwork = trim($bestCharge['network'] ?? '');
                    $optionService = trim($bestCharge['service'] ?? '');
                    $optionBaseRate = (float)($bestCharge['rate'] ?? 0);
                    
                    // Find service details (transit_time and items_allowed)
                    $serviceDetails = collect($allServices)->first(function($service) use ($optionNetwork, $optionService) {
                        $serviceNetwork = trim($service['network'] ?? '');
                        $serviceName = trim($service['name'] ?? '');
                        return $serviceNetwork == $optionNetwork && $serviceName == $optionService;
                    });
                    
                    // Calculate weight charge for this network/service
                    $weightChargeData = $calculateWeightChargeForNetworkService($optionNetwork, $optionService, $optionBaseRate);
                    
                    // Calculate total rate for this option
                    $optionTotalRate = $optionBaseRate + $weightChargeData['weight_charge'] + $distanceCharge;
                    
                    $allNetworkServiceOptions[] = [
                        'network' => $optionNetwork,
                        'service' => $optionService,
                        'base_rate' => $optionBaseRate,
                        'weight_charge' => $weightChargeData['weight_charge'],
                        'distance_charge' => $distanceCharge,
                        'total_rate' => round($optionTotalRate, 2),
                        'count' => count($charges),
                        'formulas' => $weightChargeData['formulas'],
                        'transit_time' => $serviceDetails['transit_time'] ?? 'N/A',
                        'items_allowed' => $serviceDetails['items_allowed'] ?? 'N/A',
                        'is_selected' => $optionNetwork == $selectedNetwork && $optionService == $selectedService,
                    ];
                }
            }
        }
        
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
            'base_price_info' => [
                'network' => $selectedNetwork ?: 'N/A',
                'service' => $selectedService ?: 'N/A',
                'destination_zone' => $destinationZone ?: 'N/A',
                'transit_time' => $selectedServiceDetails['transit_time'] ?? 'N/A',
                'items_allowed' => $selectedServiceDetails['items_allowed'] ?? 'N/A',
            ],
            'applied_formulas' => $appliedFormulas,
            'matching_base_charge' => $matchingBaseCharge,
            'all_matching_charges' => $allMatchingCharges,
            'all_network_service_options' => $allNetworkServiceOptions,
            'destination_zone' => $destinationZone,
        ]);
    }

    // Get networks from database with session fallback
    private function getNetworks($activeOnly = false)
    {
        // Try to get from database first
        $dbNetworks = Network::all();
        
        $networks = [];
        if ($dbNetworks->isNotEmpty()) {
            // Convert to array format for backward compatibility
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
        } else {
            // Fallback to session
            if (session()->has('networks')) {
                $networks = session('networks');
            } else {
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
                $networks = $defaultNetworks;
            }
        }
        
        // Filter to return only Active networks if requested
        if ($activeOnly) {
            return array_filter($networks, function($network) {
                return ($network['status'] ?? '') == 'Active';
            });
        }
        
        return $networks;
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

    public function viewNetwork($id)
    {
        try {
            // Try to get from database first
            $networkModel = Network::find($id);
            $network = null;
            
            if ($networkModel) {
                $network = $networkModel;
            } else {
                // Fallback to session
                $networks = $this->getNetworks();
                $networkArray = collect($networks)->firstWhere('id', $id);
                
                if (!$networkArray) {
                    return redirect()->route('admin.networks.all')->with('error', 'Network not found');
                }
                
                // Convert session network to object-like structure for consistency
                $network = (object) $networkArray;
            }
            
            // Get network transactions (credit/debit) - use database ID
            $transactions = \App\Models\NetworkTransaction::where('network_id', $id)->orderBy('created_at', 'desc')->get();
            
            $creditTransactions = $transactions->where('type', 'credit')->values();
            $debitTransactions = $transactions->where('type', 'debit')->values();
            
            $totalCredits = $transactions->where('type', 'credit')->sum('amount');
            $totalDebits = $transactions->where('type', 'debit')->sum('amount');
            
            // Calculate current balance
            $openingBalance = is_object($network) && isset($network->opening_balance) 
                ? $network->opening_balance 
                : (isset($network['opening_balance']) ? $network['opening_balance'] : 0);
            $currentBalance = $openingBalance + $totalCredits - $totalDebits;
            
            // Get AWB uploads for this network
            $networkName = is_object($network) && isset($network->name) 
                ? $network->name 
                : (isset($network['name']) ? $network['name'] : '');
            
            $awbUploads = \App\Models\AwbUpload::where('network_name', $networkName)->orderBy('created_at', 'desc')->get();
            
            // Calculate AWB statistics
            $totalAwbs = $awbUploads->count();
            $totalAwbAmount = $awbUploads->sum('amour');
            $totalAwbWeight = $awbUploads->sum('chargeable_weight');
            
            // Get unique AWB numbers with transactions
            $awbNumbers = $transactions->whereNotNull('awb_no')->pluck('awb_no')->unique()->values();
            
            // Prepare transaction data
            $creditTransactionsData = $creditTransactions->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'awb_no' => $transaction->awb_no,
                    'booking_id' => $transaction->booking_id,
                    'amount' => $transaction->amount,
                    'balance_before' => $transaction->balance_before,
                    'balance_after' => $transaction->balance_after,
                    'transaction_type' => $transaction->transaction_type,
                    'description' => $transaction->description,
                    'notes' => $transaction->notes,
                    'created_at' => $transaction->created_at,
                ];
            })->toArray();
            
            $debitTransactionsData = $debitTransactions->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'awb_no' => $transaction->awb_no,
                    'booking_id' => $transaction->booking_id,
                    'amount' => $transaction->amount,
                    'balance_before' => $transaction->balance_before,
                    'balance_after' => $transaction->balance_after,
                    'transaction_type' => $transaction->transaction_type,
                    'description' => $transaction->description,
                    'notes' => $transaction->notes,
                    'created_at' => $transaction->created_at,
                ];
            })->toArray();
            
            // Prepare AWB uploads data
            $awbUploadsData = $awbUploads->map(function($awb) {
                return [
                    'id' => $awb->id,
                    'awb_no' => $awb->awb_no,
                    'origin' => $awb->origin,
                    'destination' => $awb->destination,
                    'service_name' => $awb->service_name,
                    'chargeable_weight' => $awb->chargeable_weight,
                    'amour' => $awb->amour,
                    'date_of_sale' => $awb->date_of_sale,
                    'created_at' => $awb->created_at,
                ];
            })->toArray();
            
            return view('admin.networks.view', [
                'network' => $network,
                'creditTransactions' => $creditTransactionsData,
                'debitTransactions' => $debitTransactionsData,
                'totalCredits' => $totalCredits,
                'totalDebits' => $totalDebits,
                'openingBalance' => $openingBalance,
                'currentBalance' => $currentBalance,
                'totalTransactions' => $transactions->count(),
                'awbUploads' => $awbUploadsData,
                'totalAwbs' => $totalAwbs,
                'totalAwbAmount' => $totalAwbAmount,
                'totalAwbWeight' => $totalAwbWeight,
                'uniqueAwbCount' => $awbNumbers->count(),
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('admin.networks.all')->with('error', 'Error loading network: ' . $e->getMessage());
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
            
            // Store old network name for cascading updates
            $oldNetworkName = $network->name;
            $newNetworkName = $request->network_name;
            
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
                'name' => $newNetworkName,
                'type' => $request->network_type,
                'opening_balance' => $openingBalance, // Keep original opening balance
                'status' => $status,
                'bank_details' => $request->bank_details ?? '',
                'upi_scanner' => $upiScanner,
                'remark' => $request->remark ?? '',
            ]);
            
            // Cascade update: Update all related records if network name changed
            if ($oldNetworkName !== $newNetworkName) {
                // Update AwbUploads in database
                \DB::table('awb_uploads')
                    ->where('network_name', $oldNetworkName)
                    ->update(['network_name' => $newNetworkName]);
                
                // Update Services in session
                $services = $this->getServices();
                if (is_array($services)) {
                    $services = array_map(function($service) use ($oldNetworkName, $newNetworkName) {
                        if (isset($service['network']) && $service['network'] === $oldNetworkName) {
                            $service['network'] = $newNetworkName;
                        }
                        return $service;
                    }, $services);
                    session(['services' => array_values($services)]);
                }
                
                // Update Bookings in session
                $bookings = $this->getBookings();
                if (is_array($bookings)) {
                    $bookings = array_map(function($booking) use ($oldNetworkName, $newNetworkName) {
                        if (isset($booking['network']) && $booking['network'] === $oldNetworkName) {
                            $booking['network'] = $newNetworkName;
                        }
                        return $booking;
                    }, $bookings);
                    session(['bookings' => array_values($bookings)]);
                }
                
                // Update Direct Entry Bookings in session
                if (session()->has('direct_entry_bookings')) {
                    $directEntryBookings = session('direct_entry_bookings');
                    if (is_array($directEntryBookings)) {
                        $directEntryBookings = array_map(function($booking) use ($oldNetworkName, $newNetworkName) {
                            if (isset($booking['network']) && $booking['network'] === $oldNetworkName) {
                                $booking['network'] = $newNetworkName;
                            }
                            return $booking;
                        }, $directEntryBookings);
                        session(['direct_entry_bookings' => array_values($directEntryBookings)]);
                    }
                }
                
                // Update Shipping Charges in session
                if (session()->has('shipping_charges')) {
                    $shippingCharges = session('shipping_charges');
                    if (is_array($shippingCharges)) {
                        $shippingCharges = array_map(function($charge) use ($oldNetworkName, $newNetworkName) {
                            if (isset($charge['network']) && $charge['network'] === $oldNetworkName) {
                                $charge['network'] = $newNetworkName;
                            }
                            return $charge;
                        }, $shippingCharges);
                        session(['shipping_charges' => array_values($shippingCharges)]);
                    }
                }
                
                // Update Formulas in session
                if (session()->has('formulas')) {
                    $formulas = session('formulas');
                    if (is_array($formulas)) {
                        $formulas = array_map(function($formula) use ($oldNetworkName, $newNetworkName) {
                            if (isset($formula['network']) && $formula['network'] === $oldNetworkName) {
                                $formula['network'] = $newNetworkName;
                            }
                            return $formula;
                        }, $formulas);
                        session(['formulas' => array_values($formulas)]);
                    }
                }
            }
            
            // Also update session for backward compatibility
            $networks = $this->getNetworks();
            if (!is_array($networks)) {
                $networks = [];
            }
            
            $networks = array_map(function($n) use ($id, $request, $status, $openingBalance, $upiScanner, $newNetworkName) {
                if ($n['id'] == $id) {
                    return [
                        'id' => $id,
                        'name' => $newNetworkName,
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
    private function getServices($activeOnly = false)
    {
        // Try to get from database first
        $dbServices = \App\Models\Service::orderBy('name', 'asc')->get();
        
        if ($dbServices->isNotEmpty()) {
            // Convert to array format for backward compatibility
            $services = $dbServices->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'network' => $service->network,
                    'transit_time' => $service->transit_time,
                    'items_allowed' => $service->items_allowed,
                    'status' => $service->status,
                    'remark' => $service->remark,
                    'display_title' => $service->display_title,
                    'description' => $service->description,
                    'icon_type' => $service->icon_type,
                    'is_highlighted' => $service->is_highlighted,
                    'created_at' => $service->created_at ? $service->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            })->toArray();
        } else {
            // Fallback to session if database is empty
            if (session()->has('services') && !empty(session('services'))) {
                $services = session('services');
            } else {
                // Initialize default services if none exist
                $this->initializeDefaultServices();
                $services = session('services');
            }
        }
        
        // Filter to return only Active services if requested
        if ($activeOnly) {
            return array_filter($services, function($service) {
                return ($service['status'] ?? '') == 'Active';
            });
        }
        
        return $services;
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
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        return view('admin.services.create', [
            'services' => $services,
            'networks' => $networks,
        ]);
    }

    public function allServices(Request $request)
    {
        $services = $this->getServices();
        $networks = $this->getNetworks(true); // Get only active networks for filtering
        
        // Filter services to only show those whose network exists and is active
        $networkNames = collect($networks)->pluck('name')->toArray();
        $services = array_filter($services, function($service) use ($networkNames) {
            return in_array($service['network'] ?? '', $networkNames);
        });
        
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
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        
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

        // Check if network exists (only check active networks)
        $networks = $this->getNetworks(true);
        $networkNames = collect($networks)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Network does not exist or is inactive. Please create an active network first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Network does not exist or is inactive. Please create an active network first.');
        }

        $services = $this->getServices();
        if (!is_array($services)) {
            $services = [];
        }

        // Check if service with same name and network already exists
        $exists = collect($services)->first(function($service) use ($request) {
            return strcasecmp($service['name'] ?? '', $request->service_name) === 0 &&
                   strcasecmp($service['network'] ?? '', $request->network) === 0;
        });

        if ($exists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service with this name and network already exists.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Service with this name and network already exists.');
        }

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';
        
        // Handle is_highlighted checkbox
        $isHighlightedValue = $request->input('is_highlighted');
        $isHighlighted = ($isHighlightedValue === '1' || $isHighlightedValue === 'on' || $isHighlightedValue === true || $isHighlightedValue === 1);
        
        // Save to database
        try {
            \App\Models\Service::create([
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
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
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
                'created_at' => now()->toDateTimeString(),
            ];
            
            $services[] = $newService;
            session(['services' => $services]);
            session()->save();
        }

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

        // Check if network exists (only check active networks)
        $networks = $this->getNetworks(true);
        $networkNames = collect($networks)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Network does not exist or is inactive. Please create an active network first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Network does not exist or is inactive. Please create an active network first.');
        }

        $services = $this->getServices();
        if (!is_array($services)) {
            $services = [];
        }
        
        // Store old service name for cascading updates
        $oldService = collect($services)->firstWhere('id', $id);
        if (!$oldService) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found.',
                ], 404);
            }
            return redirect()->back()->withInput()->with('error', 'Service not found.');
        }
        
        $oldServiceName = $oldService['name'] ?? null;
        $oldNetwork = $oldService['network'] ?? null;
        $newServiceName = $request->service_name;
        $newNetwork = $request->network;

        // Check if another service with same name and network already exists (excluding current service)
        $exists = collect($services)->first(function($service) use ($id, $newServiceName, $newNetwork) {
            return $service['id'] != $id &&
                   strcasecmp($service['name'] ?? '', $newServiceName) === 0 &&
                   strcasecmp($service['network'] ?? '', $newNetwork) === 0;
        });

        if ($exists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Another service with this name and network already exists.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Another service with this name and network already exists.');
        }

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';
        
        // Handle is_highlighted checkbox
        $isHighlightedValue = $request->input('is_highlighted');
        $isHighlighted = ($isHighlightedValue === '1' || $isHighlightedValue === 'on' || $isHighlightedValue === true || $isHighlightedValue === 1);
        
        // Try to update in database first
        try {
            $service = \App\Models\Service::findOrFail($id);
            
            // Cascade update: Update all related records if service name changed
            if ($oldServiceName && $oldServiceName !== $newServiceName) {
                // Update AwbUploads in database
                \DB::table('awb_uploads')
                    ->where('service_name', $oldServiceName)
                    ->update(['service_name' => $newServiceName]);
                
                // Update Zones in database
                \DB::table('zones')
                    ->where('service', $oldServiceName)
                    ->update(['service' => $newServiceName]);
            }
            
            $service->update([
                'name' => $newServiceName,
                'network' => $request->network,
                'transit_time' => $request->transit_time,
                'items_allowed' => $request->items_allowed,
                'status' => $status,
                'remark' => $request->remark ?? '',
                'display_title' => $request->display_title ?? $newServiceName,
                'description' => $request->description ?? $service->description,
                'icon_type' => $request->icon_type ?? $service->icon_type ?? 'truck',
                'is_highlighted' => $isHighlighted,
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $services = array_map(function($service) use ($id, $request, $status, $isHighlighted, $newServiceName) {
                if ($service['id'] == $id) {
                    return [
                        'id' => $id,
                        'name' => $newServiceName,
                        'network' => $request->network,
                        'transit_time' => $request->transit_time,
                        'items_allowed' => $request->items_allowed,
                        'status' => $status,
                        'remark' => $request->remark ?? '',
                        'display_title' => $request->display_title ?? $newServiceName,
                        'description' => $request->description ?? ($service['description'] ?? ''),
                        'icon_type' => $request->icon_type ?? ($service['icon_type'] ?? 'truck'),
                        'is_highlighted' => $isHighlighted,
                    ];
                }
                return $service;
            }, $services);
            
            // Cascade update: Update all related records if service name changed
            if ($oldServiceName && $oldServiceName !== $newServiceName) {
                // Update Bookings in session
                $bookings = $this->getBookings();
                if (is_array($bookings)) {
                    $bookings = array_map(function($booking) use ($oldServiceName, $newServiceName) {
                        if (isset($booking['service']) && $booking['service'] === $oldServiceName) {
                            $booking['service'] = $newServiceName;
                        }
                        return $booking;
                    }, $bookings);
                    session(['bookings' => array_values($bookings)]);
                }
                
                // Update Direct Entry Bookings in session
                if (session()->has('direct_entry_bookings')) {
                    $directEntryBookings = session('direct_entry_bookings');
                    if (is_array($directEntryBookings)) {
                        $directEntryBookings = array_map(function($booking) use ($oldServiceName, $newServiceName) {
                            if (isset($booking['service']) && $booking['service'] === $oldServiceName) {
                                $booking['service'] = $newServiceName;
                            }
                            return $booking;
                        }, $directEntryBookings);
                        session(['direct_entry_bookings' => array_values($directEntryBookings)]);
                    }
                }
                
                // Update Shipping Charges in session
                if (session()->has('shipping_charges')) {
                    $shippingCharges = session('shipping_charges');
                    if (is_array($shippingCharges)) {
                        $shippingCharges = array_map(function($charge) use ($oldServiceName, $newServiceName) {
                            if (isset($charge['service']) && $charge['service'] === $oldServiceName) {
                                $charge['service'] = $newServiceName;
                            }
                            return $charge;
                        }, $shippingCharges);
                        session(['shipping_charges' => array_values($shippingCharges)]);
                    }
                }
                
                // Update Formulas in session
                if (session()->has('formulas')) {
                    $formulas = session('formulas');
                    if (is_array($formulas)) {
                        $formulas = array_map(function($formula) use ($oldServiceName, $newServiceName) {
                            if (isset($formula['service']) && $formula['service'] === $oldServiceName) {
                                $formula['service'] = $newServiceName;
                            }
                            return $formula;
                        }, $formulas);
                        session(['formulas' => array_values($formulas)]);
                    }
                }
            }
            
            session(['services' => array_values($services)]);
            session()->save();
        }

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
            // Try to update in database first
            $service = \App\Models\Service::findOrFail($id);
            $service->status = $service->status === 'Active' ? 'Inactive' : 'Active';
            $service->save();
            
            return response()->json([
                'success' => true,
                'status' => $service->status,
                'message' => 'Service status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            // Fallback to session if database fails
            try {
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
            } catch (\Exception $sessionError) {
                \Log::error('Service status toggle failed: ' . $sessionError->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating service status: ' . $sessionError->getMessage()
                ], 422);
            }
        }
    }

    public function deleteService($id)
    {
        try {
            // Try to delete from database first
            \App\Models\Service::findOrFail($id)->delete();
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $services = $this->getServices();
            if (!is_array($services)) {
                $services = [];
            }
            
            // Convert ID to integer for proper comparison
            $id = (int)$id;
            
            $services = array_filter($services, function($service) use ($id) {
                return (int)($service['id'] ?? 0) != $id;
            });
            
            session(['services' => array_values($services)]);
            session()->save();
        }
        
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
    private function getCountries($activeOnly = false)
    {
        // Try to get from database first
        $dbCountries = \App\Models\Country::orderBy('name', 'asc')->get();
        
        if ($dbCountries->isNotEmpty()) {
            // Convert to array format for backward compatibility
            $countries = $dbCountries->map(function($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code,
                    'isd_no' => $country->isd_no,
                    'dialing_code' => $country->dialing_code,
                    'status' => $country->status,
                    'remark' => $country->remark,
                    'created_at' => $country->created_at ? $country->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            })->toArray();
        } else {
            // Fallback to session if database is empty
            if (session()->has('countries')) {
                $countries = session('countries');
            } else {
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
                $countries = $defaultCountries;
            }
        }
        
        // Filter to return only Active countries if requested
        if ($activeOnly) {
            return array_filter($countries, function($country) {
                return ($country['status'] ?? '') == 'Active';
            });
        }
        
        return $countries;
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

        // Save to database
        try {
            \App\Models\Country::create([
                'name' => $request->country_name,
                'code' => $request->country_code,
                'isd_no' => $request->country_isd_no,
                'dialing_code' => $request->country_dialing_code ?? '',
                'status' => $status,
                'remark' => $request->remark ?? '',
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
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
                'created_at' => now()->toDateTimeString(),
            ];
            
            $countries[] = $newCountry;
            session(['countries' => $countries]);
            session()->save();
        }

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

        // Try to update in database first
        try {
            $country = \App\Models\Country::findOrFail($id);
            $country->update([
                'name' => $request->country_name,
                'code' => $request->country_code,
                'isd_no' => $request->country_isd_no,
                'dialing_code' => $request->country_dialing_code ?? '',
                'status' => $status,
                'remark' => $request->remark ?? '',
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
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
                        'created_at' => $country['created_at'] ?? now()->toDateTimeString(),
                    ];
                }
                return $country;
            }, $countries);
            
            session(['countries' => array_values($countries)]);
            session()->save();
        }

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
            // Try to update in database first
            $country = \App\Models\Country::findOrFail($id);
            $country->status = $country->status === 'Active' ? 'Inactive' : 'Active';
            $country->save();
            
            return response()->json([
                'success' => true,
                'status' => $country->status,
                'message' => 'Country status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            // Fallback to session if database fails
            try {
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
            } catch (\Exception $sessionError) {
                \Log::error('Country status toggle failed: ' . $sessionError->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating country status: ' . $sessionError->getMessage()
                ], 422);
            }
        }
    }

    public function deleteCountry($id)
    {
        // Try to delete from database first
        try {
            \App\Models\Country::findOrFail($id)->delete();
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $countries = $this->getCountries();
            if (!is_array($countries)) {
                $countries = [];
            }
            
            $countries = array_filter($countries, function($country) use ($id) {
                return $country['id'] != $id;
            });
            
            session(['countries' => array_values($countries)]);
            session()->save();
        }
        
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
    private function getZones($activeOnly = false)
    {
        // Try to get from database first
        $dbZones = \App\Models\Zone::orderBy('pincode', 'asc')->get();
        
        if ($dbZones->isNotEmpty()) {
            // Convert to array format for backward compatibility
            $zones = $dbZones->map(function($zone) {
                return [
                    'id' => $zone->id,
                    'pincode' => $zone->pincode,
                    'country' => $zone->country,
                    'zone' => $zone->zone,
                    'network' => $zone->network,
                    'service' => $zone->service,
                    'status' => $zone->status,
                    'remark' => $zone->remark,
                    'created_at' => $zone->created_at ? $zone->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            })->toArray();
        } else {
            // Fallback to session if database is empty
            if (session()->has('zones')) {
                $zones = session('zones');
            } else {
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
                $zones = $defaultZones;
            }
        }
        
        // Filter to return only Active zones if requested
        if ($activeOnly) {
            return array_filter($zones, function($zone) {
                return ($zone['status'] ?? '') == 'Active';
            });
        }
        
        return $zones;
    }

    private function getZoneOptions()
    {
        return array_merge(
            [
                'No Zone',
                'Remote',
            ],
            array_map(function ($i) {
                return "Zone {$i}";
            }, range(1, 60))
        );
    }

    public function zones()
    {
        return redirect()->route('admin.zones.create');
    }

    public function createZone()
    {
        $zones = $this->getZones();
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
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
        $networks = $this->getNetworks(true); // Get only active networks for filtering
        $services = $this->getServices(true); // Get only active services for filtering
        
        // Filter zones to only show those with existing active networks and services
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        $zones = array_filter($zones, function($zone) use ($networkNames, $serviceNames) {
            return in_array($zone['network'] ?? '', $networkNames) &&
                   in_array($zone['service'] ?? '', $serviceNames);
        });
        
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
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

        // Check if network and service exist (only check active ones)
        $networks = $this->getNetworks(true);
        $services = $this->getServices(true);
        $countries = $this->getCountries(true);
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        $countryNames = collect($countries)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Network does not exist or is inactive. Please create an active network first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Network does not exist or is inactive. Please create an active network first.');
        }

        if (!in_array($request->service, $serviceNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service does not exist or is inactive. Please create an active service first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Service does not exist or is inactive. Please create an active service first.');
        }
        
        if (!in_array($request->country, $countryNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country does not exist or is inactive. Please create an active country first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Country does not exist or is inactive. Please create an active country first.');
        }

        $zones = $this->getZones();
        if (!is_array($zones)) {
            $zones = [];
        }
        
        // Check if zone with same pincode already exists - update it instead of creating new
        $existingZoneIndex = null;
        foreach ($zones as $index => $zone) {
            if (strcasecmp($zone['pincode'] ?? '', $request->pincode) === 0) {
                $existingZoneIndex = $index;
                break;
            }
        }

        // Convert checkbox to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === '1' || $statusValue === 'on' || $statusValue === true || $statusValue === 1) ? 'Active' : 'Inactive';

        // Save to database
        try {
            // Check if zone with same pincode exists
            $existingZone = \App\Models\Zone::where('pincode', $request->pincode)->first();
            
            if ($existingZone) {
                // Update existing zone
                $existingZone->update([
                    'country' => $request->country,
                    'zone' => $request->zone,
                    'network' => $request->network,
                    'service' => $request->service,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                ]);
                $message = 'Zone updated successfully! (Pincode already existed)';
            } else {
                // Create new zone
                \App\Models\Zone::create([
                    'pincode' => $request->pincode,
                    'country' => $request->country,
                    'zone' => $request->zone,
                    'network' => $request->network,
                    'service' => $request->service,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                ]);
                $message = 'Zone created successfully!';
            }
        } catch (\Exception $e) {
            // Fallback to session if database fails
            if ($existingZoneIndex !== null) {
                // Update existing zone
                $existingZone = $zones[$existingZoneIndex];
                $zones[$existingZoneIndex] = [
                    'id' => $existingZone['id'],
                    'pincode' => $request->pincode,
                    'country' => $request->country,
                    'zone' => $request->zone,
                    'network' => $request->network,
                    'service' => $request->service,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                    'created_at' => $existingZone['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
                $message = 'Zone updated successfully! (Pincode already existed)';
            } else {
                // Create new zone
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
                    'created_at' => now()->toDateTimeString(),
                ];
                $zones[] = $newZone;
                $message = 'Zone created successfully!';
            }
            
            session(['zones' => $zones]);
            session()->save();
        }

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.zones.all')
            ]);
        }

        return redirect()->route('admin.zones.all')->with('success', $message);
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

        // Check if network and service exist (only check active ones)
        $networks = $this->getNetworks(true);
        $services = $this->getServices(true);
        $countries = $this->getCountries(true);
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        $countryNames = collect($countries)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Network does not exist or is inactive. Please create an active network first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Network does not exist or is inactive. Please create an active network first.');
        }

        if (!in_array($request->service, $serviceNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service does not exist or is inactive. Please create an active service first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Service does not exist or is inactive. Please create an active service first.');
        }
        
        if (!in_array($request->country, $countryNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country does not exist or is inactive. Please create an active country first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Country does not exist or is inactive. Please create an active country first.');
        }

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
                    'created_at' => $zone['created_at'] ?? now()->toDateTimeString(),
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
        // Try to delete from database first
        try {
            \App\Models\Zone::findOrFail($id)->delete();
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $zones = $this->getZones();
            if (!is_array($zones)) {
                $zones = [];
            }
            
            $zones = array_filter($zones, function($zone) use ($id) {
                return $zone['id'] != $id;
            });
            
            session(['zones' => array_values($zones)]);
            session()->save();
        }
        
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $zones = $this->getZones(true); // Get only active zones for dropdown
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
        $shipmentTypes = $this->getShipmentTypes();
        
        // Get zone options (includes No Zone, Remote, and Zone 1-60)
        $zoneOptions = array_values($this->getZoneOptions());
        
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
        $networks = $this->getNetworks(true); // Get only active networks for filtering
        $services = $this->getServices(true); // Get only active services for filtering
        $countries = $this->getCountries(true); // Get only active countries for filtering
        
        // Filter shipping charges to only show those with existing active networks, services, and countries
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        $countryNames = collect($countries)->pluck('name')->toArray();
        
        $shippingCharges = array_filter($shippingCharges, function($charge) use ($networkNames, $serviceNames, $countryNames) {
            // Check if network exists
            if (!in_array($charge['network'] ?? '', $networkNames)) {
                return false;
            }
            
            // Check if service exists
            if (!in_array($charge['service'] ?? '', $serviceNames)) {
                return false;
            }
            
            // Check if origin exists (country)
            if (!in_array($charge['origin'] ?? '', $countryNames)) {
                return false;
            }
            
            // Check if destination exists (country)
            if (!in_array($charge['destination'] ?? '', $countryNames)) {
                return false;
            }
            
            return true;
        });
        
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $zones = $this->getZones(true); // Get only active zones for dropdown
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
        $shipmentTypes = $this->getShipmentTypes();
        
        // Get zone options (includes No Zone, Remote, and Zone 1-60)
        $zoneOptions = array_values($this->getZoneOptions());
        
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

        // Check if network and service exist
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $countries = $this->getCountries();
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        $countryNames = collect($countries)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Network does not exist. Please create the network first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Network does not exist. Please create the network first.');
        }

        if (!in_array($request->service, $serviceNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service does not exist. Please create the service first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Service does not exist. Please create the service first.');
        }

        if (!in_array($request->origin, $countryNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Origin country does not exist. Please create the country first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Origin country does not exist. Please create the country first.');
        }

        if (!in_array($request->destination, $countryNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination country does not exist. Please create the country first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Destination country does not exist. Please create the country first.');
        }

        $shippingCharges = $this->getShippingCharges();
        if (!is_array($shippingCharges)) {
            $shippingCharges = [];
        }
        
        // Check if shipping charge with same origin, destination, zones, network, service already exists - update it
        $existingChargeIndex = null;
        foreach ($shippingCharges as $index => $charge) {
            if (strcasecmp($charge['origin'] ?? '', $request->origin) === 0 &&
                strcasecmp($charge['destination'] ?? '', $request->destination) === 0 &&
                strcasecmp($charge['origin_zone'] ?? '', $request->origin_zone) === 0 &&
                strcasecmp($charge['destination_zone'] ?? '', $request->destination_zone) === 0 &&
                strcasecmp($charge['network'] ?? '', $request->network) === 0 &&
                strcasecmp($charge['service'] ?? '', $request->service) === 0) {
                $existingChargeIndex = $index;
                break;
            }
        }

        if ($existingChargeIndex !== null) {
            // Update existing charge
            $existingCharge = $shippingCharges[$existingChargeIndex];
            $shippingCharges[$existingChargeIndex] = [
                'id' => $existingCharge['id'],
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
                'created_at' => $existingCharge['created_at'] ?? now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
            $message = 'Shipping charge updated successfully! (Existing record found)';
        } else {
            // Create new charge
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
            'created_at' => now()->toDateTimeString(),
        ];
        $shippingCharges[] = $newCharge;
            $message = 'Shipping charge created successfully!';
        }
        
        session(['shipping_charges' => $shippingCharges]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.shipping-charges.all')
            ]);
        }

        return redirect()->route('admin.shipping-charges.all')->with('success', $message);
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

        // Check if network and service exist
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $countries = $this->getCountries();
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        $countryNames = collect($countries)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Network does not exist. Please create the network first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Network does not exist. Please create the network first.');
        }

        if (!in_array($request->service, $serviceNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service does not exist. Please create the service first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Service does not exist. Please create the service first.');
        }

        if (!in_array($request->origin, $countryNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Origin country does not exist. Please create the country first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Origin country does not exist. Please create the country first.');
        }

        if (!in_array($request->destination, $countryNames)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination country does not exist. Please create the country first.',
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Destination country does not exist. Please create the country first.');
        }

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
                    'created_at' => $charge['created_at'] ?? now()->toDateTimeString(),
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
    private function getFormulas($activeOnly = false)
    {
        if (session()->has('formulas')) {
            $formulas = session('formulas');
        } else {
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
            $formulas = $defaultFormulas;
        }
        
        // Filter to return only Active formulas if requested
        if ($activeOnly) {
            return array_filter($formulas, function($formula) {
                return ($formula['status'] ?? '') == 'Active';
            });
        }
        
        return $formulas;
    }

    private function getFormulaTypes()
    {
        return ['Fixed', 'Percentage'];
    }

    private function getFormulaScopes()
    {
        return ['per kg', 'Flat'];
    }

    private function normalizePriority(?string $priority): string
    {
        return strtolower(trim($priority ?? ''));
    }

    private function priorityExists(string $priority, string $network, string $service, ?int $ignoreId = null): bool
    {
        $normalized = $this->normalizePriority($priority);
        if ($normalized === '') {
            return false;
        }

        $formulas = $this->getFormulas();
        foreach ($formulas as $formula) {
            if ($ignoreId !== null && isset($formula['id']) && (int)$formula['id'] === (int)$ignoreId) {
                continue;
            }

            // Check if priority exists for the same network and service combination
            $formulaNetwork = $formula['network'] ?? '';
            $formulaService = $formula['service'] ?? '';
            
            if (strcasecmp($formulaNetwork, $network) === 0 && 
                strcasecmp($formulaService, $service) === 0 &&
                $this->normalizePriority($formula['priority'] ?? '') === $normalized) {
                return true;
            }
        }

        return false;
    }

    public function formulas()
    {
        return redirect()->route('admin.formulas.create');
    }

    public function createFormula()
    {
        $formulas = $this->getFormulas();
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
        $types = $this->getFormulaTypes();
        $scopes = $this->getFormulaScopes();
        return view('admin.formulas.create', [
            'formulas' => $formulas,
            'networks' => $networks,
            'services' => $services,
            'types' => $types,
            'scopes' => $scopes,
        ]);
    }

    public function allFormulas(Request $request)
    {
        $formulas = $this->getFormulas();
        
        // Get networks - prioritize database, fallback to session (only active)
        $dbNetworks = Network::where('status', 'Active')->get();
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
            // Fallback to session networks (only active)
            $networks = $this->getNetworks(true);
        }
        
        // Get services to validate against (only active)
        $services = $this->getServices(true);
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        
        // Filter formulas to only show those where service exists and has its network
        $formulas = array_filter($formulas, function($formula) use ($networkNames, $serviceNames, $services) {
            $formulaNetwork = $formula['network'] ?? '';
            $formulaService = $formula['service'] ?? '';
            
            // Check if network exists
            if (!in_array($formulaNetwork, $networkNames)) {
                return false;
            }
            
            // Check if service exists
            if (!in_array($formulaService, $serviceNames)) {
                return false;
            }
            
            // Check if service has this network
            $service = collect($services)->first(function($svc) use ($formulaService) {
                return strcasecmp($svc['name'] ?? '', $formulaService) === 0;
            });
            
            return $service && strcasecmp($service['network'] ?? '', $formulaNetwork) === 0;
        });
        
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
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
        $types = $this->getFormulaTypes();
        $scopes = $this->getFormulaScopes();
        
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
            'priority' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Check if network and service exist and service has this network
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            $message = 'Network does not exist. Please create the network first.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        if (!in_array($request->service, $serviceNames)) {
            $message = 'Service does not exist. Please create the service first.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        // Check if service has this network
        $service = collect($services)->first(function($svc) use ($request) {
            return strcasecmp($svc['name'] ?? '', $request->service) === 0;
        });

        if (!$service || strcasecmp($service['network'] ?? '', $request->network) !== 0) {
            $message = 'Selected service does not belong to the selected network.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        $priorityValue = trim($request->priority);
        if ($this->priorityExists($priorityValue, $request->network, $request->service)) {
            $message = 'Priority already exists for this network and service combination. Please choose another value.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['priority' => $message])
                ->withInput();
        }

        // Convert checkbox value to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === 'on' || $statusValue === '1' || $statusValue === 1 || $statusValue === true || $statusValue === 'true') ? 'Active' : 'Inactive';

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
            'priority' => $priorityValue,
            'value' => $request->value,
            'status' => $status,
            'remark' => $request->remark ?? '',
            'created_at' => now()->toDateTimeString(),
        ];
        
        $formulas[] = $newFormula;
        session(['formulas' => $formulas]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formula created successfully!',
                'redirect' => route('admin.formulas.all')
            ]);
        }

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
            'priority' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Check if network and service exist and service has this network
        $networks = $this->getNetworks();
        $services = $this->getServices();
        $networkNames = collect($networks)->pluck('name')->toArray();
        $serviceNames = collect($services)->pluck('name')->toArray();
        
        if (!in_array($request->network, $networkNames)) {
            $message = 'Network does not exist. Please create the network first.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        if (!in_array($request->service, $serviceNames)) {
            $message = 'Service does not exist. Please create the service first.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        // Check if service has this network
        $service = collect($services)->first(function($svc) use ($request) {
            return strcasecmp($svc['name'] ?? '', $request->service) === 0;
        });

        if (!$service || strcasecmp($service['network'] ?? '', $request->network) !== 0) {
            $message = 'Selected service does not belong to the selected network.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        $priorityValue = trim($request->priority);
        if ($this->priorityExists($priorityValue, $request->network, $request->service, (int)$id)) {
            $message = 'Priority already exists for this network and service combination. Please choose another value.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['priority' => $message])
                ->withInput();
        }

        // Convert checkbox value to status string
        $statusValue = $request->input('status');
        $status = ($statusValue === 'on' || $statusValue === '1' || $statusValue === 1 || $statusValue === true || $statusValue === 'true') ? 'Active' : 'Inactive';

        $formulas = $this->getFormulas();
        if (!is_array($formulas)) {
            $formulas = [];
        }
        
        $formulas = array_map(function($formula) use ($id, $request, $status, $priorityValue) {
            if ($formula['id'] == $id) {
                return [
                    'id' => $id,
                    'formula_name' => $request->formula_name,
                    'network' => $request->network,
                    'service' => $request->service,
                    'type' => $request->type,
                    'scope' => $request->scope,
                    'priority' => $priorityValue,
                    'value' => $request->value,
                    'status' => $status,
                    'remark' => $request->remark ?? '',
                    'created_at' => $formula['created_at'] ?? now()->toDateTimeString(),
                ];
            }
            return $formula;
        }, $formulas);
        
        session(['formulas' => array_values($formulas)]);
        session()->save();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formula updated successfully!',
                'redirect' => route('admin.formulas.all')
            ]);
        }

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
        $zoneInfo = null;
        $networkTransactions = [];
        $totalAmount = 0;
        $supportTicketCount = 0;
        
        if ($awbNumber) {
            // Search from database first (AwbUpload model)
            $awbUpload = \App\Models\AwbUpload::where('awb_no', $awbNumber)->first();
            
            if ($awbUpload) {
                // Convert to array for compatibility
                $awb = $awbUpload->toArray();
                
                // Get zone information based on origin and destination pincodes
                $zones = $this->getZones();
                $originPincode = $awbUpload->origin_zone_pincode ?? null;
                $destinationPincode = $awbUpload->destination_zone_pincode ?? null;
                
                $originZone = null;
                $destinationZone = null;
                
                if ($originPincode) {
                    $originZone = collect($zones)->first(function($zone) use ($originPincode) {
                        return strcasecmp($zone['pincode'] ?? '', $originPincode) === 0;
                    });
                }
                
                if ($destinationPincode) {
                    $destinationZone = collect($zones)->first(function($zone) use ($destinationPincode) {
                        return strcasecmp($zone['pincode'] ?? '', $destinationPincode) === 0;
                    });
                }
                
                $zoneInfo = [
                    'origin' => $originZone,
                    'destination' => $destinationZone,
                ];
                
                // Get network transactions (amounts) related to this AWB
                $transactions = \App\Models\NetworkTransaction::where('awb_no', $awbNumber)
                    ->with('network')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $networkTransactions = $transactions->map(function($transaction) {
                    return [
                        'id' => $transaction->id,
                        'network_name' => $transaction->network->name ?? 'N/A',
                        'type' => $transaction->type,
                        'amount' => $transaction->amount,
                        'transaction_type' => $transaction->transaction_type,
                        'description' => $transaction->description,
                        'created_at' => $transaction->created_at,
                        'balance_after' => $transaction->balance_after,
                    ];
                })->toArray();
                
                // Calculate total amount (sum of all transaction amounts)
                $totalAmount = $transactions->sum('amount');
                
                // Get support ticket count - check if AWB number is mentioned in subject or message
                // Use distinct to avoid counting duplicates
                $ticketIds = \App\Models\SupportTicket::where(function($query) use ($awbNumber) {
                    $query->where('subject', 'like', '%' . $awbNumber . '%')
                          ->orWhere('message', 'like', '%' . $awbNumber . '%');
                })->pluck('id')->toArray();
                
                // Also check via bookings - if any booking has this AWB number and has tickets
                $bookings = $this->getBookings();
                $directEntryBookings = session('direct_entry_bookings', []);
                $allBookings = array_merge(
                    is_array($bookings) ? $bookings : [],
                    is_array($directEntryBookings) ? $directEntryBookings : []
                );
                
                // Check if any booking with this AWB has tickets linked (via user_id)
                // Only count tickets that mention the AWB number in subject/message
                foreach ($allBookings as $booking) {
                    if (isset($booking['awb_no']) && strcasecmp($booking['awb_no'], $awbNumber) === 0) {
                        if (isset($booking['user_id'])) {
                            $userTicketIds = \App\Models\SupportTicket::where('user_id', $booking['user_id'])
                                ->where(function($query) use ($awbNumber) {
                                    $query->where('subject', 'like', '%' . $awbNumber . '%')
                                          ->orWhere('message', 'like', '%' . $awbNumber . '%');
                                })
                                ->pluck('id')
                                ->toArray();
                            $ticketIds = array_merge($ticketIds, $userTicketIds);
                        }
                    }
                }
                
                // Get unique ticket count
                $supportTicketCount = count(array_unique($ticketIds));
                
                // Add to history
                $this->addToHistory($awb);
            } else {
                // Fallback to session if not found in database
            $awbUploads = $this->getAwbUploads();
            $awb = collect($awbUploads)->first(function ($item) use ($awbNumber) {
                return strtoupper($item['awb_no']) === strtoupper($awbNumber);
            });
            
            if ($awb) {
                $this->addToHistory($awb);
                    
                    // Try to get zone info and transactions even for session data
                    $zones = $this->getZones();
                    $originPincode = $awb['origin_pin'] ?? $awb['origin_zone_pincode'] ?? null;
                    $destinationPincode = $awb['destination_pin'] ?? $awb['destination_zone_pincode'] ?? null;
                    
                    $originZone = null;
                    $destinationZone = null;
                    
                    if ($originPincode) {
                        $originZone = collect($zones)->first(function($zone) use ($originPincode) {
                            return strcasecmp($zone['pincode'] ?? '', $originPincode) === 0;
                        });
                    }
                    
                    if ($destinationPincode) {
                        $destinationZone = collect($zones)->first(function($zone) use ($destinationPincode) {
                            return strcasecmp($zone['pincode'] ?? '', $destinationPincode) === 0;
                        });
                    }
                    
                    $zoneInfo = [
                        'origin' => $originZone,
                        'destination' => $destinationZone,
                    ];
                    
                    // Get network transactions
                    $transactions = \App\Models\NetworkTransaction::where('awb_no', $awbNumber)->get();
                    $networkTransactions = $transactions->map(function($transaction) {
                        return [
                            'id' => $transaction->id,
                            'network_name' => $transaction->network->name ?? 'N/A',
                            'type' => $transaction->type,
                            'amount' => $transaction->amount,
                            'transaction_type' => $transaction->transaction_type,
                            'description' => $transaction->description,
                            'created_at' => $transaction->created_at,
                            'balance_after' => $transaction->balance_after,
                        ];
                    })->toArray();
                    
                    $totalAmount = $transactions->sum('amount');
                    
                    // Get support ticket count
                    $ticketIds = \App\Models\SupportTicket::where(function($query) use ($awbNumber) {
                        $query->where('subject', 'like', '%' . $awbNumber . '%')
                              ->orWhere('message', 'like', '%' . $awbNumber . '%');
                    })->pluck('id')->toArray();
                    
                    $supportTicketCount = count($ticketIds);
                }
            }
        }
        
        return view('admin.search-with-awb.search', [
            'awb' => $awb,
            'awbNumber' => $awbNumber ?? '',
            'zoneInfo' => $zoneInfo,
            'networkTransactions' => $networkTransactions,
            'totalAmount' => $totalAmount,
            'supportTicketCount' => $supportTicketCount,
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
        // Try to get from database first
        $dbAwbUploads = AwbUpload::orderBy('awb_no', 'asc')->get();
        
        if ($dbAwbUploads->isNotEmpty()) {
            // Convert to array format for backward compatibility
            return $dbAwbUploads->map(function($awb) {
                return [
                    'id' => $awb->id,
                    'awb_no' => $awb->awb_no,
                    'date_of_sale' => $awb->date_of_sale,
                    'branch' => $awb->branch,
                    'hub' => $awb->hub,
                    'status' => $awb->status,
                    'booking_type' => $awb->booking_type,
                    'shipment_type' => $awb->shipment_type,
                    'destination' => $awb->destination,
                    'consignee' => $awb->consignee,
                    'origin_zone_pincode' => $awb->origin_zone_pincode,
                    'destination_zone_pincode' => $awb->destination_zone_pincode,
                    'pk' => $awb->pk,
                    'actual_weight' => $awb->actual_weight,
                    'volumetric_weight' => $awb->volumetric_weight,
                    'chargeable_weight' => $awb->chargeable_weight,
                    'network' => $awb->network_name,
                    'service' => $awb->service_name,
                    'network_name' => $awb->network_name,
                    'service_name' => $awb->service_name,
                    'amour' => $awb->amour,
                    'consignor' => $awb->consignor,
                    'consignee_name' => $awb->consignee,
                    'origin_pin' => $awb->origin_zone_pincode,
                    'destination_pin' => $awb->destination_zone_pincode,
                    'display_service_name' => $awb->display_service_name,
                    'operation_remark' => $awb->operation_remark,
                ];
            })->toArray();
        }
        
        // Fallback to session
        if (session()->has('awb_uploads')) {
            return session('awb_uploads');
        }
        
        // Initialize with default if both are empty
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
        $bookingTypes = $this->getBookingTypes();
        $shipmentTypes = $this->getShipmentTypesForUpload();
        return view('admin.awb-upload.create', [
            'awbUploads' => $awbUploads,
            'countries' => $countries,
            'networks' => $networks,
            'services' => $services,
            'bookingTypes' => $bookingTypes,
            'shipmentTypes' => $shipmentTypes,
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        $services = $this->getServices(true); // Get only active services for dropdown
        $bookingTypes = $this->getBookingTypes();
        $shipmentTypes = $this->getShipmentTypesForUpload();
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
        ]);
    }

    public function storeAwbUpload(Request $request)
    {
        $request->validate([
            'awb_no' => 'required|string|max:255',
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
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string',
            'forwarding_number' => 'nullable|string|max:255',
            'branch' => 'required|string|max:255',
            'hub' => 'required|string|max:255',
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
            'branch' => $request->branch,
            'hub' => $request->hub,
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
            'awb_no' => 'required|string|max:255',
            'date_of_sale' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'hub' => 'nullable|string|max:255',
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
                    'hub' => $request->hub ?? '',
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $shipmentTypes = $this->getShipmentTypesForBooking();
        $bookingTypes = $this->getBookingTypes();
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        
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
        $countries = $this->getCountries(true); // Get only active countries for dropdown
        $shipmentTypes = $this->getShipmentTypesForBooking();
        $bookingTypes = $this->getBookingTypes();
        $networks = $this->getNetworks(true); // Get only active networks for dropdown
        
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
    public function reportsIndex()
    {
        return view('admin.reports.index');
    }

    public function getReportContent(Request $request, $reportType)
    {
        // Map report types to their methods and views
        $reportConfig = [
            'zone' => ['method' => 'zoneReport', 'view' => 'admin.reports.zone'],
            'formula' => ['method' => 'formulaReport', 'view' => 'admin.reports.formula'],
            'shipping-charges' => ['method' => 'shippingChargesReport', 'view' => 'admin.reports.shipping-charges'],
            'booking' => ['method' => 'bookingReport', 'view' => 'admin.reports.booking'],
            'payment' => ['method' => 'paymentReport', 'view' => 'admin.reports.payment'],
            'transaction' => ['method' => 'transactionReport', 'view' => 'admin.reports.transaction'],
            'network' => ['method' => 'networkReport', 'view' => 'admin.reports.network'],
            'service' => ['method' => 'serviceReport', 'view' => 'admin.reports.service'],
            'country' => ['method' => 'countryReport', 'view' => 'admin.reports.country'],
            'bank' => ['method' => 'bankReport', 'view' => 'admin.reports.bank'],
            'wallet' => ['method' => 'walletReport', 'view' => 'admin.reports.wallet'],
        ];

        if (!isset($reportConfig[$reportType])) {
            abort(404, 'Report not found');
        }

        $config = $reportConfig[$reportType];
        $method = $config['method'];
        $viewName = $config['view'];
        
        // Call the appropriate report method to get the view with data
        $viewResponse = $this->$method($request);
        
        // Extract data from the view response
        $viewData = $viewResponse->getData();
        
        // Render the view and extract just the content section
        // Since views extend layouts, we need to render the full view and extract content
        $fullHtml = $viewResponse->render();
        
        // Extract content between @section('content') and @endsection
        // For now, we'll return the full rendered view and let JavaScript handle it
        // Or we can create a wrapper that strips the layout
        
        // Better approach: Return the view response but mark it as AJAX request
        // The view will be rendered fully, but we'll handle it in JavaScript
        return response($fullHtml)->header('Content-Type', 'text/html');
    }

    public function zoneReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $zones = $this->normalizeReportTimestamps($this->getZones(), 'zones');
        $zones = $this->filterCollectionByDate($zones, $dateFrom, $dateTo);

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $zones = array_filter($zones, function($zone) use ($searchLower) {
                return strpos(strtolower($zone['pincode'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($zone['country'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($zone['zone'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($zone['network'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($zone['service'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($zone['status'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($zone['remark'] ?? ''), $searchLower) !== false;
            });
            $zones = array_values($zones);
        }

        return view('admin.reports.zone', [
            'zones' => $zones,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
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

    public function formulaReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $formulas = $this->normalizeReportTimestamps($this->getFormulas(), 'formulas');
        $formulas = $this->filterCollectionByDate($formulas, $dateFrom, $dateTo);

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $formulas = array_filter($formulas, function($formula) use ($searchLower) {
                return strpos(strtolower($formula['formula_name'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($formula['network'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($formula['service'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($formula['type'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($formula['scope'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($formula['status'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($formula['remark'] ?? ''), $searchLower) !== false;
            });
            $formulas = array_values($formulas);
        }

        return view('admin.reports.formula', [
            'formulas' => $formulas,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }

    public function shippingChargesReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $shippingCharges = $this->normalizeReportTimestamps($this->getShippingCharges(), 'shipping_charges');
        $shippingCharges = $this->filterCollectionByDate($shippingCharges, $dateFrom, $dateTo);

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $shippingCharges = array_filter($shippingCharges, function($charge) use ($searchLower) {
                return strpos(strtolower($charge['origin'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['origin_zone'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['destination'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['destination_zone'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['shipment_type'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['network'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['service'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($charge['remark'] ?? ''), $searchLower) !== false;
            });
            $shippingCharges = array_values($shippingCharges);
        }

        return view('admin.reports.shipping-charges', [
            'shippingCharges' => $shippingCharges,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
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

    public function paymentReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $payments = $this->normalizeReportTimestamps(session('payments', []), null, 'payment_date');
        $payments = $this->filterCollectionByDate($payments, $dateFrom, $dateTo, 'payment_date');

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $payments = array_filter($payments, function($payment) use ($searchLower) {
                return strpos(strtolower($payment['payment_method'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($payment['status'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($payment['reference'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($payment['payment_date'] ?? ''), $searchLower) !== false;
            });
            $payments = array_values($payments);
        }

        return view('admin.reports.payment', [
            'payments' => $payments,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
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
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
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
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting transaction report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
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

    public function serviceReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $services = $this->normalizeReportTimestamps($this->getServices(), 'services');
        $services = $this->filterCollectionByDate($services, $dateFrom, $dateTo);

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $services = array_filter($services, function($service) use ($searchLower) {
                return strpos(strtolower($service['name'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($service['network'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($service['status'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($service['remark'] ?? ''), $searchLower) !== false;
            });
            $services = array_values($services);
        }

        return view('admin.reports.service', [
            'services' => $services,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }

    public function countryReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $countries = $this->normalizeReportTimestamps($this->getCountries(), 'countries');
        $countries = $this->filterCollectionByDate($countries, $dateFrom, $dateTo);

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $countries = array_filter($countries, function($country) use ($searchLower) {
                return strpos(strtolower($country['name'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($country['code'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($country['isd_no'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($country['dialing_code'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($country['status'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($country['remark'] ?? ''), $searchLower) !== false;
            });
            $countries = array_values($countries);
        }

        return view('admin.reports.country', [
            'countries' => $countries,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }

    /**
     * Check if XMLWriter extension is available
     */
    private function checkXmlWriterExtension()
    {
        if (!extension_loaded('xmlwriter')) {
            return redirect()->back()->with('error', 'XMLWriter PHP extension is not installed on the server. Please contact your server administrator to enable the xmlwriter extension.');
        }
        return null;
    }

    /**
     * Export data as CSV when XMLWriter is not available
     */
    private function exportAsCsv(array $data, array $headings, string $filename)
    {
        $filename = str_ends_with($filename, '.csv') ? $filename : "{$filename}.csv";
        
        return response()->streamDownload(function () use ($headings, $data) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Write headings
            fputcsv($handle, $headings);
            
            // Write data rows
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Safe Excel download with CSV fallback
     */
    private function safeExcelDownload($export, string $filename, array $csvData = null, array $csvHeadings = null)
    {
        // Check if XMLWriter is available
        if (!extension_loaded('xmlwriter')) {
            // Use CSV fallback if data is provided
            if ($csvData !== null && $csvHeadings !== null) {
                return $this->exportAsCsv($csvData, $csvHeadings, $filename);
            }
            // Otherwise try to extract data from export object
            try {
                if (method_exists($export, 'array') && method_exists($export, 'headings')) {
                    $data = $export->array();
                    $headings = $export->headings();
                    return $this->exportAsCsv($data, $headings, $filename);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to extract data for CSV fallback: ' . $e->getMessage());
            }
            return redirect()->back()->with('error', 'XMLWriter PHP extension is not installed. CSV export data not available. Please contact your server administrator to enable the xmlwriter extension.');
        }
        
        // Try Excel export
        try {
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Excel export failed: ' . $e->getMessage());
            // Fallback to CSV if available
            if ($csvData !== null && $csvHeadings !== null) {
                return $this->exportAsCsv($csvData, $csvHeadings, $filename);
            }
            // Try to extract from export object
            try {
                if (method_exists($export, 'array') && method_exists($export, 'headings')) {
                    $data = $export->array();
                    $headings = $export->headings();
                    return $this->exportAsCsv($data, $headings, $filename);
                }
            } catch (\Exception $csvError) {
                \Log::error('CSV fallback failed: ' . $csvError->getMessage());
            }
            throw $e; // Re-throw original exception
        }
    }

    // Excel Export Methods
    public function exportZoneReport(Request $request)
    {
        try {
            // Check for XMLWriter extension - if not available, use CSV fallback
            $useCsv = !extension_loaded('xmlwriter');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

            $zones = $this->normalizeReportTimestamps($this->getZones(), 'zones');
            $zones = $this->filterCollectionByDate($zones, $dateFrom, $dateTo);

            // Apply search filter if provided
            if ($search) {
                $searchLower = strtolower($search);
                $zones = array_filter($zones, function($zone) use ($searchLower) {
                    return strpos(strtolower($zone['pincode'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($zone['country'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($zone['zone'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($zone['network'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($zone['service'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($zone['status'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($zone['remark'] ?? ''), $searchLower) !== false;
                });
                $zones = array_values($zones);
            }
            
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
            
            $filename = 'zone_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            
            // Prepare CSV data for fallback
            $csvHeadings = ['ID', 'Pincode', 'Country', 'Zone', 'Network', 'Service', 'Status', 'Remark'];
            $csvData = [];
            foreach ($zones as $zone) {
                $csvData[] = [
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
            
            // Use safe download with CSV fallback
            return $this->safeExcelDownload($export, $filename, $csvData, $csvHeadings);
        } catch (\Exception $e) {
            \Log::error('Error exporting zone report: ' . $e->getMessage());
            // Try CSV fallback on error if we have zones data
            if (isset($zones) && is_array($zones) && !empty($zones)) {
                try {
                    $headings = ['ID', 'Pincode', 'Country', 'Zone', 'Network', 'Service', 'Status', 'Remark'];
                    $csvData = [];
                    foreach ($zones as $zone) {
                        $csvData[] = [
                            $zone['id'] ?? '',
                            $zone['pincode'] ?? '',
                            $zone['country'] ?? '',
                            $zone['zone'] ?? '',
                            $zone['network'] ?? '',
                            $zone['service'] ?? '',
                            $zone['status'] ?? '',
                            $zone['remark'] ?? '-',
                        ];
                    }
                    return $this->exportAsCsv($csvData, $headings, 'zone_report_' . date('Y-m-d') . '.csv');
                } catch (\Exception $csvError) {
                    \Log::error('CSV fallback also failed: ' . $csvError->getMessage());
                }
            }
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportFormulaReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

            $formulas = $this->normalizeReportTimestamps($this->getFormulas(), 'formulas');
            $formulas = $this->filterCollectionByDate($formulas, $dateFrom, $dateTo);

            // Apply search filter if provided
            if ($search) {
                $searchLower = strtolower($search);
                $formulas = array_filter($formulas, function($formula) use ($searchLower) {
                    return strpos(strtolower($formula['formula_name'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($formula['network'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($formula['service'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($formula['type'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($formula['scope'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($formula['status'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($formula['remark'] ?? ''), $searchLower) !== false;
                });
                $formulas = array_values($formulas);
            }
            
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
        
            $filename = 'formula_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting formula report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportShippingChargesReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

        $shippingCharges = $this->normalizeReportTimestamps($this->getShippingCharges(), 'shipping_charges');
        $shippingCharges = $this->filterCollectionByDate($shippingCharges, $dateFrom, $dateTo);
        
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
        
            $filename = 'shipping_charges_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting shipping charges report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportBookingReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            // Get filter parameters (same as bookingReport method)
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
            
            // Apply filters (same logic as bookingReport method)
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
            
            $export = new class($filteredBookings, $filteredDirectEntry) implements FromArray, WithHeadings {
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
            
            $filename = 'booking_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($hub) {
                $filename .= '_hub_' . str_replace(' ', '_', $hub);
            }
            if ($branch) {
                $filename .= '_branch_' . str_replace(' ', '_', $branch);
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting booking report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportPaymentReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

            $payments = $this->normalizeReportTimestamps(session('payments', []), null, 'payment_date');
            $payments = $this->filterCollectionByDate($payments, $dateFrom, $dateTo, 'payment_date');

            // Apply search filter if provided
            if ($search) {
                $searchLower = strtolower($search);
                $payments = array_filter($payments, function($payment) use ($searchLower) {
                    return strpos(strtolower($payment['payment_method'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($payment['status'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($payment['reference'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($payment['payment_date'] ?? ''), $searchLower) !== false;
                });
                $payments = array_values($payments);
            }

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
                            $payment['payment_date'] ?? ($payment['created_at'] ?? '-'),
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
            
            $filename = 'payment_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting payment report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportNetworkReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            // Get filter parameters
            $networkFilter = $request->get('network', '');
            $dateFrom = $request->get('date_from', '');
            $dateTo = $request->get('date_to', '');
            $search = $request->get('search', '');
            
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
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting network report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportServiceReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

            $services = $this->normalizeReportTimestamps($this->getServices(), 'services');
            $services = $this->filterCollectionByDate($services, $dateFrom, $dateTo);

            // Apply search filter if provided
            if ($search) {
                $searchLower = strtolower($search);
                $services = array_filter($services, function($service) use ($searchLower) {
                    return strpos(strtolower($service['name'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($service['network'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($service['status'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($service['remark'] ?? ''), $searchLower) !== false;
                });
                $services = array_values($services);
            }
            
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
            
            $filename = 'service_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting service report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportCountryReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

            $countries = $this->normalizeReportTimestamps($this->getCountries(), 'countries');
            $countries = $this->filterCollectionByDate($countries, $dateFrom, $dateTo);

            // Apply search filter if provided
            if ($search) {
                $searchLower = strtolower($search);
                $countries = array_filter($countries, function($country) use ($searchLower) {
                    return strpos(strtolower($country['name'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($country['code'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($country['isd_no'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($country['dialing_code'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($country['status'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($country['remark'] ?? ''), $searchLower) !== false;
                });
                $countries = array_values($countries);
            }
            
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
            
            $filename = 'country_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting country report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportBankReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $category = $request->query('category');
            $networkName = $request->query('network');
            $search = $request->query('search');
            $bankId = $request->query('bank');

            // Get bank reports (same logic as bankReport method)
            $allBanks = $this->normalizeReportTimestamps($this->getBanks(), 'banks');
            $allPayments = $this->getPaymentsIntoBank();
            
            $bankReports = [];
            foreach ($allBanks as $bank) {
            $bankAccountIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            
            $bankPayments = collect($allPayments)->filter(function($payment) use ($bankAccountIdentifier, $dateFrom, $dateTo, $category) {
                $match = ($payment['bank_account'] ?? '') == $bankAccountIdentifier;
                
                if ($match && ($dateFrom || $dateTo)) {
                    $paymentDate = $payment['created_at'] ?? '';
                    if ($paymentDate) {
                        try {
                            $paymentDateObj = \Carbon\Carbon::parse($paymentDate);
                            if ($dateFrom && $paymentDateObj->lt(\Carbon\Carbon::parse($dateFrom)->startOfDay())) {
                                $match = false;
                            }
                            if ($dateTo && $paymentDateObj->gt(\Carbon\Carbon::parse($dateTo)->endOfDay())) {
                                $match = false;
                            }
                        } catch (\Exception $e) {}
                    }
                }
                
                if ($match && $category) {
                    $match = ($payment['category_bank'] ?? '') == $category;
                }
                
                return $match;
            })->values()->all();
            
            if ($bankId && $bank['id'] != $bankId) {
                continue;
            }
            
            $totalCredits = collect($bankPayments)->where('type', 'Credit')->sum('amount');
            $totalDebits = collect($bankPayments)->where('type', 'Debit')->sum('amount');
            
            $salaryCredits = collect($bankPayments)->where('type', 'Credit')->where('category_bank', 'Salary')->sum('amount');
            $salaryDebits = collect($bankPayments)->where('type', 'Debit')->where('category_bank', 'Salary')->sum('amount');
            
            $expenseCredits = collect($bankPayments)->where('type', 'Credit')->where('category_bank', 'Expense')->sum('amount');
            $expenseDebits = collect($bankPayments)->where('type', 'Debit')->where('category_bank', 'Expense')->sum('amount');
            
            $revenueCredits = collect($bankPayments)->where('type', 'Credit')->where('category_bank', 'Revenue')->sum('amount');
            $revenueDebits = collect($bankPayments)->where('type', 'Debit')->where('category_bank', 'Revenue')->sum('amount');
            
            $currentBalance = ($bank['opening_balance'] ?? 0) + $totalCredits - $totalDebits;
            
            $bankReports[] = [
                'bank' => $bank,
                'total_credits' => $totalCredits,
                'total_debits' => $totalDebits,
                'current_balance' => $currentBalance,
                'salary_credits' => $salaryCredits,
                'salary_debits' => $salaryDebits,
                'salary_total' => $salaryCredits - $salaryDebits,
                'expense_credits' => $expenseCredits,
                'expense_debits' => $expenseDebits,
                'expense_total' => $expenseCredits - $expenseDebits,
                'revenue_credits' => $revenueCredits,
                'revenue_debits' => $revenueDebits,
                'revenue_total' => $revenueCredits - $revenueDebits,
                'transactions' => $bankPayments,
            ];
        }

        $export = new class($bankReports) implements FromArray, WithHeadings {
            protected $bankReports;

            public function __construct($bankReports)
            {
                $this->bankReports = $bankReports;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->bankReports as $report) {
                    $bank = $report['bank'];
                    $data[] = [
                        $bank['id'] ?? '',
                        $bank['bank_name'] ?? '',
                        $bank['account_holder_name'] ?? '',
                        $bank['account_number'] ?? '',
                        $bank['ifsc_code'] ?? '',
                        $bank['opening_balance'] ?? 0,
                        $report['total_credits'] ?? 0,
                        $report['total_debits'] ?? 0,
                        $report['current_balance'] ?? 0,
                        $report['salary_credits'] ?? 0,
                        $report['salary_debits'] ?? 0,
                        $report['salary_total'] ?? 0,
                        $report['expense_credits'] ?? 0,
                        $report['expense_debits'] ?? 0,
                        $report['expense_total'] ?? 0,
                        $report['revenue_credits'] ?? 0,
                        $report['revenue_debits'] ?? 0,
                        $report['revenue_total'] ?? 0,
                        count($report['transactions'] ?? []),
                    ];
                }
                return $data;
            }

            public function headings(): array
            {
                return [
                    'ID', 'Bank Name', 'Account Holder', 'Account Number', 'IFSC Code', 
                    'Opening Balance', 'Total Credits', 'Total Debits', 'Current Balance',
                    'Salary Credits', 'Salary Debits', 'Salary Total',
                    'Expense Credits', 'Expense Debits', 'Expense Total',
                    'Revenue Credits', 'Revenue Debits', 'Revenue Total',
                    'Total Transactions'
                ];
            }
            };

            $filename = 'bank_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($category) {
                $filename .= '_' . strtolower($category);
            }
            if ($networkName) {
                $filename .= '_' . str_replace(' ', '_', strtolower($networkName));
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting bank report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function exportWalletReport(Request $request)
    {
        // Check for XMLWriter extension
        $check = $this->checkXmlWriterExtension();
        if ($check) {
            return $check;
        }

        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $search = $request->query('search');

            $wallets = $this->normalizeReportTimestamps(session('wallets', []), 'wallets');
            $wallets = $this->filterCollectionByDate($wallets, $dateFrom, $dateTo);

            // Apply search filter if provided
            if ($search) {
                $searchLower = strtolower($search);
                $wallets = array_filter($wallets, function($wallet) use ($searchLower) {
                    return strpos(strtolower($wallet['wallet_name'] ?? ''), $searchLower) !== false
                        || strpos(strtolower($wallet['status'] ?? ''), $searchLower) !== false;
                });
                $wallets = array_values($wallets);
            }

            $export = new class($wallets) implements FromArray, WithHeadings {
            protected $wallets;

            public function __construct($wallets)
            {
                $this->wallets = $wallets;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->wallets as $wallet) {
                    $data[] = [
                        $wallet['id'] ?? '',
                        $wallet['wallet_name'] ?? '',
                        $wallet['balance'] ?? 0,
                        $wallet['status'] ?? '',
                        $wallet['last_transaction'] ?? '',
                        $wallet['created_at'] ?? '',
                    ];
                }
                return $data;
            }

            public function headings(): array
            {
                return ['ID', 'Wallet Name', 'Balance', 'Status', 'Last Transaction', 'Created At'];
            }
            };

            $filename = 'wallet_report_' . date('Y-m-d');
            if ($dateFrom || $dateTo) {
                $filename .= '_' . ($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all');
            }
            if ($search) {
                $filename .= '_search_' . substr($search, 0, 10);
            }
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Error exporting wallet report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    public function bankReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $category = $request->query('category'); // Salary, Expense, Revenue
        $networkName = $request->query('network'); // Network name filter
        $bankId = $request->query('bank'); // Specific bank filter
        $search = $request->query('search'); // Search filter

        // Get all banks
        $allBanks = $this->normalizeReportTimestamps($this->getBanks(), 'banks');
        if (!is_array($allBanks)) {
            $allBanks = [];
        }
        
        // Get all payments into bank
        $allPayments = $this->getPaymentsIntoBank();
        if (!is_array($allPayments)) {
            $allPayments = [];
        }
        
        // Get all networks
        $allNetworks = $this->getNetworks();
        if (!is_array($allNetworks)) {
            $allNetworks = [];
        }
        
        // Build bank reports with transactions
        $bankReports = [];
        
        foreach ($allBanks as $bank) {
            $bankAccountIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            
            // Filter payments for this bank
            $bankPayments = collect($allPayments)->filter(function($payment) use ($bankAccountIdentifier, $dateFrom, $dateTo, $category) {
                $match = ($payment['bank_account'] ?? '') == $bankAccountIdentifier;
                
                // Date filter
                if ($match && ($dateFrom || $dateTo)) {
                    $paymentDate = $payment['created_at'] ?? '';
                    if ($paymentDate) {
                        try {
                            $paymentDateObj = \Carbon\Carbon::parse($paymentDate);
                            if ($dateFrom && $paymentDateObj->lt(\Carbon\Carbon::parse($dateFrom)->startOfDay())) {
                                $match = false;
                            }
                            if ($dateTo && $paymentDateObj->gt(\Carbon\Carbon::parse($dateTo)->endOfDay())) {
                                $match = false;
                            }
                        } catch (\Exception $e) {
                            // Skip date comparison if parsing fails
                        }
                    }
                }
                
                // Category filter
                if ($match && $category) {
                    $match = ($payment['category_bank'] ?? '') == $category;
                }
                
                return $match;
            })->values()->all();
            
            // Calculate totals by category
            $totalCredits = collect($bankPayments)->where('type', 'Credit')->sum('amount');
            $totalDebits = collect($bankPayments)->where('type', 'Debit')->sum('amount');
            
            $salaryCredits = collect($bankPayments)->where('type', 'Credit')->where('category_bank', 'Salary')->sum('amount');
            $salaryDebits = collect($bankPayments)->where('type', 'Debit')->where('category_bank', 'Salary')->sum('amount');
            
            $expenseCredits = collect($bankPayments)->where('type', 'Credit')->where('category_bank', 'Expense')->sum('amount');
            $expenseDebits = collect($bankPayments)->where('type', 'Debit')->where('category_bank', 'Expense')->sum('amount');
            
            $revenueCredits = collect($bankPayments)->where('type', 'Credit')->where('category_bank', 'Revenue')->sum('amount');
            $revenueDebits = collect($bankPayments)->where('type', 'Debit')->where('category_bank', 'Revenue')->sum('amount');
            
            // Get network-related transactions if network filter is applied
            $networkTransactions = [];
            if ($networkName) {
                $networkPayments = collect($bankPayments)->filter(function($payment) use ($networkName) {
                    // Check if payment remark or description mentions the network
                    $remark = strtolower($payment['remark'] ?? '');
                    return strpos($remark, strtolower($networkName)) !== false;
                })->values()->all();
                
                $networkTransactions = $networkPayments;
            }
            
            $currentBalance = ($bank['opening_balance'] ?? 0) + $totalCredits - $totalDebits;
            
            // Apply bank filter if specified
            if ($bankId && $bank['id'] != $bankId) {
                continue;
            }
            
            // Always add bank to reports, even if no transactions (for report completeness)
            $bankReports[] = [
                'bank' => $bank,
                'total_transactions' => count($bankPayments),
                'total_credits' => $totalCredits,
                'total_debits' => $totalDebits,
                'current_balance' => $currentBalance,
                'salary_credits' => $salaryCredits,
                'salary_debits' => $salaryDebits,
                'salary_total' => $salaryCredits - $salaryDebits,
                'expense_credits' => $expenseCredits,
                'expense_debits' => $expenseDebits,
                'expense_total' => $expenseCredits - $expenseDebits,
                'revenue_credits' => $revenueCredits,
                'revenue_debits' => $revenueDebits,
                'revenue_total' => $revenueCredits - $revenueDebits,
                'transactions' => $networkName ? $networkTransactions : $bankPayments,
            ];
        }
        
        // Filter by network if specified (affects which banks to show)
        if ($networkName) {
            $bankReports = array_filter($bankReports, function($report) {
                return count($report['transactions'] ?? []) > 0;
            });
            $bankReports = array_values($bankReports); // Re-index array
        }

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $bankReports = array_filter($bankReports, function($report) use ($searchLower) {
                $bank = $report['bank'] ?? [];
                return strpos(strtolower($bank['bank_name'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($bank['account_holder_name'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($bank['account_number'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($bank['ifsc_code'] ?? ''), $searchLower) !== false;
            });
            $bankReports = array_values($bankReports);
        }

        // Ensure allBanks is an array
        if (!is_array($allBanks)) {
            $allBanks = [];
        }

        // Ensure allNetworks is an array
        if (!is_array($allNetworks)) {
            $allNetworks = [];
        }

        // Ensure bankReports is an array
        if (!is_array($bankReports)) {
            $bankReports = [];
        }

        return view('admin.reports.bank', [
            'bankReports' => $bankReports,
            'allBanks' => $allBanks,
            'allNetworks' => $allNetworks,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'category' => $category,
            'networkName' => $networkName,
            'bankId' => $bankId,
            'search' => $search,
        ]);
    }

    public function walletReport(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $wallets = $this->normalizeReportTimestamps(session('wallets', []), 'wallets');
        $wallets = $this->filterCollectionByDate($wallets, $dateFrom, $dateTo);

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $wallets = array_filter($wallets, function($wallet) use ($searchLower) {
                return strpos(strtolower($wallet['wallet_name'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($wallet['status'] ?? ''), $searchLower) !== false
                    || strpos(strtolower($wallet['last_transaction'] ?? ''), $searchLower) !== false;
            });
            $wallets = array_values($wallets);
        }

        return view('admin.reports.wallet', [
            'wallets' => $wallets,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }

    /**
     * Ensure each report item has a timestamp for filtering.
     */
    private function normalizeReportTimestamps(array $items, ?string $sessionKey = null, string $dateKey = 'created_at'): array
    {
        $normalized = array_map(function ($item) use ($dateKey) {
            if (empty($item[$dateKey])) {
                $item[$dateKey] = now()->toDateTimeString();
            }
            return $item;
        }, $items);

        if ($sessionKey) {
            session([$sessionKey => $normalized]);
            session()->save();
        }

        return $normalized;
    }

    /**
     * Filter report items by the provided date range.
     */
    private function filterCollectionByDate(array $items, ?string $dateFrom, ?string $dateTo, string $dateKey = 'created_at'): array
    {
        if (empty($dateFrom) && empty($dateTo)) {
            return $items;
        }

        $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : null;

        return array_values(array_filter($items, function ($item) use ($from, $to, $dateKey) {
            if (empty($item[$dateKey])) {
                return true;
            }

            try {
                $itemDate = Carbon::parse($item[$dateKey]);
            } catch (\Exception $exception) {
                return true;
            }

            if ($from && $itemDate->lt($from)) {
                return false;
            }

            if ($to && $itemDate->gt($to)) {
                return false;
            }

            return true;
        }));
    }

    // Banks Management
    private function getBanks()
    {
        // Try to get from database first
        $dbBanks = \App\Models\Bank::orderBy('bank_name', 'asc')->get();
        
        if ($dbBanks->isNotEmpty()) {
            // Convert to array format for backward compatibility
            return $dbBanks->map(function($bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->bank_name,
                    'account_holder_name' => $bank->account_holder_name,
                    'account_number' => $bank->account_number,
                    'ifsc_code' => $bank->ifsc_code,
                    'opening_balance' => $bank->opening_balance,
                    'created_at' => $bank->created_at ? $bank->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            })->toArray();
        }
        
        // Fallback to session if database is empty
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

    public function allBanks(Request $request)
    {
        $banks = $this->getBanks();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $banks = array_filter($banks, function($bank) use ($searchTerm) {
                return strpos(strtolower($bank['bank_name'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($bank['account_holder_name'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($bank['account_number'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($bank['ifsc_code'] ?? ''), $searchTerm) !== false;
            });
            $banks = array_values($banks);
        }
        
        return view('admin.banks.all', [
            'banks' => $banks,
            'searchParams' => [
                'search' => $request->search ?? '',
            ],
        ]);
    }

    public function viewBank($id)
    {
        $banks = $this->getBanks();
        $bank = collect($banks)->firstWhere('id', $id);
        
        if (!$bank) {
            return redirect()->route('admin.banks.all')->with('error', 'Bank not found');
        }

        // Get all payments into bank
        $allPayments = $this->getPaymentsIntoBank();
        
        // Build bank account identifier: "bank_name - account_number"
        $bankAccountIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
        
        // Filter payments for this bank
        $bankPayments = collect($allPayments)->filter(function($payment) use ($bankAccountIdentifier) {
            return ($payment['bank_account'] ?? '') == $bankAccountIdentifier;
        })->values()->all();
        
        // Calculate totals
        $totalCredits = collect($bankPayments)->where('type', 'Credit')->sum('amount');
        $totalDebits = collect($bankPayments)->where('type', 'Debit')->sum('amount');
        $currentBalance = ($bank['opening_balance'] ?? 0) + $totalCredits - $totalDebits;
        
        // Separate credit and debit transactions
        $creditTransactions = collect($bankPayments)->where('type', 'Credit')->sortByDesc('created_at')->values()->all();
        $debitTransactions = collect($bankPayments)->where('type', 'Debit')->sortByDesc('created_at')->values()->all();

        return view('admin.banks.view', [
            'bank' => $bank,
            'creditTransactions' => $creditTransactions,
            'debitTransactions' => $debitTransactions,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
            'openingBalance' => $bank['opening_balance'] ?? 0,
            'currentBalance' => $currentBalance,
            'totalTransactions' => count($bankPayments),
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

        // Save to database
        try {
            \App\Models\Bank::create([
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'opening_balance' => (float) $request->opening_balance,
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
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
                'created_at' => now()->toDateTimeString(),
            ];
            
            $banks[] = $newBank;
            session(['banks' => $banks]);
            session()->save();
        }

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

        // Try to update in database first
        try {
            $bank = \App\Models\Bank::findOrFail($id);
            $bank->update([
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'opening_balance' => (float) $request->opening_balance,
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
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
                        'created_at' => $bank['created_at'] ?? now()->toDateTimeString(),
                    ];
                }
                return $bank;
            }, $banks);
            
            session(['banks' => array_values($banks)]);
            session()->save();
        }

        return redirect()->route('admin.banks.all')->with('success', 'Bank updated successfully!');
    }

    public function deleteBank($id)
    {
        // Try to delete from database first
        try {
            \App\Models\Bank::findOrFail($id)->delete();
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $banks = $this->getBanks();
            if (!is_array($banks)) {
                $banks = [];
            }
            
            $banks = array_filter($banks, function($bank) use ($id) {
                return $bank['id'] != $id;
            });
            
            session(['banks' => array_values($banks)]);
            session()->save();
        }

        return redirect()->route('admin.banks.all')->with('success', 'Bank deleted successfully!');
    }

    public function bulkDeleteBanks(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'integer',
        ]);

        $ids = array_map('intval', $validated['selected_ids']);

        try {
            Bank::whereIn('id', $ids)->delete();
        } catch (\Throwable $exception) {
            Log::warning('Failed to delete banks from database', [
                'ids' => $ids,
                'error' => $exception->getMessage(),
            ]);
        }

        $banks = $this->getBanks();
        if (!is_array($banks)) {
            $banks = [];
        }

        $beforeCount = count($banks);
        $banks = array_filter($banks, function ($bank) use ($ids) {
            return !in_array($bank['id'], $ids);
        });
        $deletedCount = $beforeCount - count($banks);

        session(['banks' => array_values($banks)]);
        session()->save();

        if ($deletedCount === 0) {
            return redirect()->route('admin.banks.all')
                ->with('info', 'No banks were removed.');
        }

        return redirect()->route('admin.banks.all')
            ->with('success', "Successfully deleted {$deletedCount} bank(s).");
    }

    // Bank Transfer Management
    public function bankTransfer()
    {
        $banks = $this->getBanks();
        $payments = $this->getPaymentsIntoBank();
        return view('admin.banks.transfer', [
            'banks' => $banks,
            'payments' => $payments,
        ]);
    }

    public function storeBankTransfer(Request $request)
    {
        $request->validate([
            'from_bank' => 'required|string|max:255',
            'to_bank' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_no' => 'required|string|max:255',
            'remark' => 'nullable|string',
        ]);

        // Check if from and to banks are different
        if ($request->from_bank === $request->to_bank) {
            return redirect()->back()->withInput()->with('error', 'Source and destination banks must be different.');
        }

        // Get all banks to check if they exist
        $banks = $this->getBanks();
        $fromBank = collect($banks)->firstWhere(function($bank) use ($request) {
            $bankIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            return $bankIdentifier === $request->from_bank;
        });

        $toBank = collect($banks)->firstWhere(function($bank) use ($request) {
            $bankIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            return $bankIdentifier === $request->to_bank;
        });

        if (!$fromBank) {
            return redirect()->back()->withInput()->with('error', 'Source bank not found.');
        }

        if (!$toBank) {
            return redirect()->back()->withInput()->with('error', 'Destination bank not found.');
        }

        // Get all payments to calculate current balance
        $allPayments = $this->getPaymentsIntoBank();
        if (!is_array($allPayments)) {
            $allPayments = [];
        }

        // Calculate current balance for source bank
        $fromBankPayments = collect($allPayments)->filter(function($payment) use ($request) {
            return ($payment['bank_account'] ?? '') === $request->from_bank;
        });

        $fromBankCredits = $fromBankPayments->where('type', 'Credit')->sum('amount');
        $fromBankDebits = $fromBankPayments->where('type', 'Debit')->sum('amount');
        $fromBankBalance = ($fromBank['opening_balance'] ?? 0) + $fromBankCredits - $fromBankDebits;

        // Check if source bank has sufficient balance
        if ($fromBankBalance < $request->amount) {
            return redirect()->back()->withInput()->with('error', 'Insufficient balance in source bank. Available balance: ₹' . number_format($fromBankBalance, 2));
        }

        // Get next ID for payments
        $nextId = count($allPayments) > 0 ? max(array_column($allPayments, 'id')) + 1 : 1;

        // Create debit transaction for source bank
        $debitPayment = [
            'id' => $nextId,
            'bank_account' => $request->from_bank,
            'mode_of_payment' => 'Netf', // NEFT/RTGS/IMPS
            'type' => 'Debit',
            'category_bank' => 'Expense',
            'transaction_no' => $request->transaction_no,
            'amount' => $request->amount,
            'remark' => 'Transfer to ' . $request->to_bank . ($request->remark ? ' - ' . $request->remark : ''),
            'created_at' => now()->toDateTimeString(),
        ];

        // Create credit transaction for destination bank
        $creditPayment = [
            'id' => $nextId + 1,
            'bank_account' => $request->to_bank,
            'mode_of_payment' => 'Netf', // NEFT/RTGS/IMPS
            'type' => 'Credit',
            'category_bank' => 'Revenue',
            'transaction_no' => $request->transaction_no,
            'amount' => $request->amount,
            'remark' => 'Transfer from ' . $request->from_bank . ($request->remark ? ' - ' . $request->remark : ''),
            'created_at' => now()->toDateTimeString(),
        ];

        // Save to database
        try {
            \App\Models\PaymentIntoBank::create([
                'bank_account' => $request->from_bank,
                'mode_of_payment' => 'Netf',
                'type' => 'Debit',
                'category_bank' => 'Expense',
                'transaction_no' => $request->transaction_no,
                'amount' => $request->amount,
                'remark' => 'Transfer to ' . $request->to_bank . ($request->remark ? ' - ' . $request->remark : ''),
            ]);

            \App\Models\PaymentIntoBank::create([
                'bank_account' => $request->to_bank,
                'mode_of_payment' => 'Netf',
                'type' => 'Credit',
                'category_bank' => 'Revenue',
                'transaction_no' => $request->transaction_no,
                'amount' => $request->amount,
                'remark' => 'Transfer from ' . $request->from_bank . ($request->remark ? ' - ' . $request->remark : ''),
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $allPayments[] = $debitPayment;
            $allPayments[] = $creditPayment;
            session(['payments_into_bank' => $allPayments]);
            session()->save();
        }

        return redirect()->route('admin.banks.transfer')->with('success', 'Bank transfer completed successfully! Amount ₹' . number_format($request->amount, 2) . ' transferred from ' . $request->from_bank . ' to ' . $request->to_bank);
    }

    public function allBankTransfers(Request $request)
    {
        $allPayments = $this->getPaymentsIntoBank();
        if (!is_array($allPayments)) {
            $allPayments = [];
        }

        // Filter only transfer transactions (those with "Transfer" in remark)
        $transfers = collect($allPayments)->filter(function($payment) {
            $remark = strtolower($payment['remark'] ?? '');
            return strpos($remark, 'transfer') !== false;
        })->values()->all();

        // Group transfers by transaction number (debit and credit pairs)
        $groupedTransfers = [];
        $processedIds = [];

        foreach ($transfers as $transfer) {
            if (in_array($transfer['id'], $processedIds)) {
                continue;
            }

            $transactionNo = $transfer['transaction_no'] ?? '';
            $relatedTransfer = collect($transfers)->first(function($t) use ($transactionNo, $transfer) {
                return ($t['transaction_no'] ?? '') === $transactionNo 
                    && $t['id'] != $transfer['id']
                    && $t['type'] != $transfer['type'];
            });

            if ($relatedTransfer) {
                $groupedTransfers[] = [
                    'id' => $transfer['id'],
                    'transaction_no' => $transactionNo,
                    'from_bank' => $transfer['type'] === 'Debit' ? $transfer['bank_account'] : $relatedTransfer['bank_account'],
                    'to_bank' => $transfer['type'] === 'Credit' ? $transfer['bank_account'] : $relatedTransfer['bank_account'],
                    'amount' => $transfer['amount'],
                    'remark' => $transfer['remark'] ?? '',
                    'created_at' => $transfer['created_at'] ?? now()->toDateTimeString(),
                    'debit_id' => $transfer['type'] === 'Debit' ? $transfer['id'] : $relatedTransfer['id'],
                    'credit_id' => $transfer['type'] === 'Credit' ? $transfer['id'] : $relatedTransfer['id'],
                ];
                $processedIds[] = $transfer['id'];
                $processedIds[] = $relatedTransfer['id'];
            }
        }

        // Sort by date descending
        usort($groupedTransfers, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $groupedTransfers = array_filter($groupedTransfers, function($transfer) use ($searchTerm) {
                return strpos(strtolower($transfer['from_bank'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($transfer['to_bank'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($transfer['transaction_no'] ?? ''), $searchTerm) !== false;
            });
            $groupedTransfers = array_values($groupedTransfers);
        }

        return view('admin.banks.transfer-all', [
            'transfers' => $groupedTransfers,
            'searchParams' => [
                'search' => $request->search ?? '',
            ],
        ]);
    }

    public function viewBankTransfer($id)
    {
        $allPayments = $this->getPaymentsIntoBank();
        if (!is_array($allPayments)) {
            $allPayments = [];
        }

        // Find the transfer transaction
        $transfer = collect($allPayments)->firstWhere('id', $id);
        
        if (!$transfer) {
            return redirect()->route('admin.banks.transfer.all')->with('error', 'Transfer not found.');
        }

        // Check if it's a transfer transaction
        $remark = strtolower($transfer['remark'] ?? '');
        if (strpos($remark, 'transfer') === false) {
            return redirect()->route('admin.banks.transfer.all')->with('error', 'This is not a transfer transaction.');
        }

        // Find the related transaction (debit or credit pair)
        $transactionNo = $transfer['transaction_no'] ?? '';
        $relatedTransfer = collect($allPayments)->first(function($t) use ($transactionNo, $transfer) {
            return ($t['transaction_no'] ?? '') === $transactionNo 
                && $t['id'] != $transfer['id']
                && $t['type'] != $transfer['type'];
        });

        if (!$relatedTransfer) {
            return redirect()->route('admin.banks.transfer.all')->with('error', 'Related transfer transaction not found.');
        }

        // Determine which is debit and which is credit
        $debitTransaction = $transfer['type'] === 'Debit' ? $transfer : $relatedTransfer;
        $creditTransaction = $transfer['type'] === 'Credit' ? $transfer : $relatedTransfer;

        // Get bank details
        $banks = $this->getBanks();
        $fromBank = collect($banks)->first(function($bank) use ($debitTransaction) {
            $bankIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            return $bankIdentifier === ($debitTransaction['bank_account'] ?? '');
        });

        $toBank = collect($banks)->first(function($bank) use ($creditTransaction) {
            $bankIdentifier = $bank['bank_name'] . ' - ' . $bank['account_number'];
            return $bankIdentifier === ($creditTransaction['bank_account'] ?? '');
        });

        // Calculate balances at the time of transfer
        $allPaymentsBefore = collect($allPayments)->filter(function($p) use ($transfer) {
            $transferDate = $transfer['created_at'] ?? '';
            $paymentDate = $p['created_at'] ?? '';
            if (!$transferDate || !$paymentDate) {
                return false;
            }
            return strtotime($paymentDate) < strtotime($transferDate);
        });

        // Calculate from bank balance before transfer
        $fromBankPaymentsBefore = $allPaymentsBefore->filter(function($p) use ($debitTransaction) {
            return ($p['bank_account'] ?? '') === ($debitTransaction['bank_account'] ?? '');
        });
        $fromBankCreditsBefore = $fromBankPaymentsBefore->where('type', 'Credit')->sum('amount');
        $fromBankDebitsBefore = $fromBankPaymentsBefore->where('type', 'Debit')->sum('amount');
        $fromBankBalanceBefore = ($fromBank['opening_balance'] ?? 0) + $fromBankCreditsBefore - $fromBankDebitsBefore;
        $fromBankBalanceAfter = $fromBankBalanceBefore - ($debitTransaction['amount'] ?? 0);

        // Calculate to bank balance before transfer
        $toBankPaymentsBefore = $allPaymentsBefore->filter(function($p) use ($creditTransaction) {
            return ($p['bank_account'] ?? '') === ($creditTransaction['bank_account'] ?? '');
        });
        $toBankCreditsBefore = $toBankPaymentsBefore->where('type', 'Credit')->sum('amount');
        $toBankDebitsBefore = $toBankPaymentsBefore->where('type', 'Debit')->sum('amount');
        $toBankBalanceBefore = ($toBank['opening_balance'] ?? 0) + $toBankCreditsBefore - $toBankDebitsBefore;
        $toBankBalanceAfter = $toBankBalanceBefore + ($creditTransaction['amount'] ?? 0);

        return view('admin.banks.transfer-view', [
            'transfer' => [
                'transaction_no' => $transactionNo,
                'amount' => $transfer['amount'],
                'remark' => $transfer['remark'] ?? '',
                'created_at' => $transfer['created_at'] ?? now()->toDateTimeString(),
            ],
            'debitTransaction' => $debitTransaction,
            'creditTransaction' => $creditTransaction,
            'fromBank' => $fromBank,
            'toBank' => $toBank,
            'fromBankBalanceBefore' => $fromBankBalanceBefore,
            'fromBankBalanceAfter' => $fromBankBalanceAfter,
            'toBankBalanceBefore' => $toBankBalanceBefore,
            'toBankBalanceAfter' => $toBankBalanceAfter,
        ]);
    }

    public function deleteBankTransfer($id)
    {
        try {
            // Get all payments
            $allPayments = $this->getPaymentsIntoBank();
            if (!is_array($allPayments)) {
                $allPayments = [];
            }

            // Find the transfer transaction
            $transfer = collect($allPayments)->firstWhere('id', $id);
            
            if (!$transfer) {
                return redirect()->route('admin.banks.transfer.all')->with('error', 'Transfer not found.');
            }

            // Check if it's a transfer transaction
            $remark = strtolower($transfer['remark'] ?? '');
            if (strpos($remark, 'transfer') === false) {
                return redirect()->route('admin.banks.transfer.all')->with('error', 'This is not a transfer transaction.');
            }

            // Find the related transaction (debit or credit pair)
            $transactionNo = $transfer['transaction_no'] ?? '';
            $relatedTransfer = collect($allPayments)->first(function($t) use ($transactionNo, $transfer) {
                return ($t['transaction_no'] ?? '') === $transactionNo 
                    && $t['id'] != $transfer['id']
                    && $t['type'] != $transfer['type'];
            });

            if (!$relatedTransfer) {
                return redirect()->route('admin.banks.transfer.all')->with('error', 'Related transfer transaction not found.');
            }

            $debitId = $transfer['type'] === 'Debit' ? $transfer['id'] : $relatedTransfer['id'];
            $creditId = $transfer['type'] === 'Credit' ? $transfer['id'] : $relatedTransfer['id'];
            $amount = $transfer['amount'] ?? 0;
            $fromBank = $transfer['type'] === 'Debit' ? $transfer['bank_account'] : $relatedTransfer['bank_account'];
            $toBank = $transfer['type'] === 'Credit' ? $transfer['bank_account'] : $relatedTransfer['bank_account'];

            // Try to delete from database first
            $deletedFromDb = false;
            try {
                $debitDeleted = \App\Models\PaymentIntoBank::where('id', $debitId)->delete();
                $creditDeleted = \App\Models\PaymentIntoBank::where('id', $creditId)->delete();
                $deletedFromDb = ($debitDeleted > 0 || $creditDeleted > 0);
            } catch (\Exception $dbError) {
                \Log::warning('Database deletion failed, trying session fallback: ' . $dbError->getMessage());
            }

            // If database deletion didn't work, try session fallback
            if (!$deletedFromDb) {
                $allPayments = array_filter($allPayments, function($payment) use ($debitId, $creditId) {
                    return ($payment['id'] ?? null) != $debitId && ($payment['id'] ?? null) != $creditId;
                });
                $allPayments = array_values($allPayments);
                session(['payments_into_bank' => $allPayments]);
                session()->save();
            }

            return redirect()->route('admin.banks.transfer.all')->with('success', 'Bank transfer deleted successfully! Amount ₹' . number_format($amount, 2) . ' transfer from ' . $fromBank . ' to ' . $toBank . ' has been cancelled.');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting bank transfer: ' . $e->getMessage());
            return redirect()->route('admin.banks.transfer.all')->with('error', 'Error deleting transfer: ' . $e->getMessage());
        }
    }

    // Payments Into Bank Management
    private function getPaymentsIntoBank()
    {
        // Try to get from database first
        $dbPayments = \App\Models\PaymentIntoBank::orderBy('created_at', 'desc')->get();
        
        if ($dbPayments->isNotEmpty()) {
            // Convert to array format for backward compatibility
            return $dbPayments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'bank_account' => $payment->bank_account,
                    'mode_of_payment' => $payment->mode_of_payment,
                    'type' => $payment->type,
                    'category_bank' => $payment->category_bank,
                    'transaction_no' => $payment->transaction_no,
                    'amount' => $payment->amount,
                    'remark' => $payment->remark,
                    'created_at' => $payment->created_at ? $payment->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            })->toArray();
        }
        
        // Fallback to session if database is empty
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

    public function allPaymentsIntoBank(Request $request)
    {
        $payments = $this->getPaymentsIntoBank();
        $banks = $this->getBanks();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $payments = array_filter($payments, function($payment) use ($searchTerm) {
                return strpos(strtolower($payment['bank_account'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($payment['transaction_no'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($payment['category_bank'] ?? ''), $searchTerm) !== false
                    || strpos(strtolower($payment['remark'] ?? ''), $searchTerm) !== false;
            });
        }
        
        // Apply bank filter
        if ($request->filled('bank')) {
            $bankFilter = $request->bank;
            $payments = array_filter($payments, function($payment) use ($bankFilter) {
                return ($payment['bank_account'] ?? '') == $bankFilter;
            });
        }
        
        // Apply mode filter
        if ($request->filled('mode')) {
            $modeFilter = $request->mode;
            $payments = array_filter($payments, function($payment) use ($modeFilter) {
                return ($payment['mode_of_payment'] ?? '') == $modeFilter;
            });
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $typeFilter = $request->type;
            $payments = array_filter($payments, function($payment) use ($typeFilter) {
                return ($payment['type'] ?? '') == $typeFilter;
            });
        }
        
        // Re-index array after filtering
        $payments = array_values($payments);
        
        return view('admin.payments-into-bank.all', [
            'payments' => $payments,
            'banks' => $banks,
            'searchParams' => [
                'search' => $request->search ?? '',
                'bank' => $request->bank ?? '',
                'mode' => $request->mode ?? '',
                'type' => $request->type ?? '',
            ],
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
            'category_bank' => 'required|string|in:Salary,Expense,Revenue',
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
        
        // Save to database
        try {
            \App\Models\PaymentIntoBank::create([
                'bank_account' => $request->bank_account,
                'mode_of_payment' => $request->mode_of_payment,
                'type' => $request->type,
                'category_bank' => $request->category_bank,
                'transaction_no' => $request->transaction_no,
                'amount' => $request->amount,
                'remark' => $request->remark,
            ]);
        } catch (\Exception $e) {
            // Fallback to session if database fails
            $payments[] = $newPayment;
            session(['payments_into_bank' => $payments]);
            session()->save();
        }

        return redirect()->route('admin.payments-into-bank.all')->with('success', 'Payment added successfully!');
    }

    public function updatePaymentIntoBank(Request $request, $id)
    {
        $request->validate([
            'bank_account' => 'required|string|max:255',
            'mode_of_payment' => 'required|string|in:UPI,Cash,Netf',
            'type' => 'required|string|in:Credit,Debit',
            'category_bank' => 'required|string|in:Salary,Expense,Revenue',
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

    public function downloadPaymentsIntoBankTemplate()
    {
        $headers = [
            'Bank Account',
            'Mode of Payment',
            'Type',
            'Category Bank',
            'Transaction No',
            'Amount',
            'Remark',
        ];
        
        $exampleRow = [
            'HDFC Bank - 1234567890',
            'UPI',
            'Credit',
            'Salary',
            'TXN123456789',
            '10000.00',
            'Monthly salary payment',
        ];
        
        $data = [$headers, $exampleRow];
        
        $export = new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function array(): array
            {
                return $this->data;
            }
            
            public function headings(): array
            {
                return $this->data[0] ?? [];
            }
        };
        
        $filename = 'payments_into_bank_template_' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function importPaymentsIntoBank(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PaymentsIntoBankImport(), $request->file('file'));
            
            return redirect()->route('admin.payments-into-bank.all')
                ->with('success', 'Payments imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing payments: ' . $e->getMessage());
        }
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

    public function downloadWalletTransactionsTemplate()
    {
        $export = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function array(): array
            {
                return [
                    [
                        'AWB Number' => 'AWB123456789',
                        'Network' => 'DTDC',
                        'Transaction Type' => 'Payment',
                        'Mode' => 'UPI',
                        'Type' => 'Credit',
                        'Amount' => '1000.00',
                        'Remark' => 'Payment for shipment',
                    ],
                ];
            }
            
            public function headings(): array
            {
                return [
                    'AWB Number',
                    'Network',
                    'Transaction Type',
                    'Mode',
                    'Type',
                    'Amount',
                    'Remark',
                ];
            }
        };
        
        $filename = 'wallet_transactions_template_' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function importWalletTransactions(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\WalletTransactionsImport(), $request->file('file'));
            
            return redirect()->route('admin.payments.wallet')
                ->with('success', 'Wallet transactions imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing wallet transactions: ' . $e->getMessage());
        }
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
            'user_type' => 'required|string|max:255',
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
            'user_type' => 'required|string|max:255',
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
        if (!empty($payrolls)) {
            foreach ($payrolls as &$payroll) {
                if (isset($payroll['user_id'])) {
                    $user = User::find($payroll['user_id']);
                    $payroll['user'] = $user;
                }
            }
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

    /**
     * Mirror a stored file into the publicly accessible storage directory.
     *
     * This provides compatibility for hosting environments where symbolic links
     * (used by the default Laravel storage:link approach) are not available.
     */
    private function mirrorFileToPublicStorage(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        try {
            $sourcePath = storage_path('app/public/' . $relativePath);
            if (!File::exists($sourcePath)) {
                return;
            }

            $targetPath = public_path('storage/' . $relativePath);

            $targetDirectory = dirname($targetPath);
            if (!File::exists($targetDirectory)) {
                File::makeDirectory($targetDirectory, 0755, true);
            }

            File::copy($sourcePath, $targetPath);
        } catch (\Throwable $exception) {
            Log::warning('Failed to mirror file to public storage', [
                'path' => $relativePath,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Remove the mirrored file from the publicly accessible storage directory.
     */
    private function deleteMirroredPublicFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        $targetPath = public_path('storage/' . $relativePath);
        if (File::exists($targetPath)) {
            try {
                File::delete($targetPath);
            } catch (\Throwable $exception) {
                Log::warning('Failed to delete mirrored public file', [
                    'path' => $relativePath,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}

