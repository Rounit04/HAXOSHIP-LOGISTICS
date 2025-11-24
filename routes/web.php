<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminTodoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

/**
 * Helper function to get all tables from database (works with both MySQL and SQLite)
 */
if (!function_exists('getDatabaseTables')) {
    function getDatabaseTables() {
        try {
            $driver = \DB::connection()->getDriverName();
            $tables = [];
            
            if ($driver === 'sqlite') {
                // For SQLite
                $results = \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($results as $row) {
                    $tables[] = $row->name;
                }
            } else {
                // For MySQL/MariaDB
                $databaseName = \DB::connection()->getDatabaseName();
                $results = \DB::select('SHOW TABLES');
                $tableKey = 'Tables_in_' . $databaseName;
                foreach ($results as $row) {
                    $tables[] = $row->$tableKey;
                }
            }
            
            return $tables;
        } catch (\Exception $e) {
            return [];
        }
    }
}

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/tracking', 'tracking')->name('tracking');
Route::get('/blogs', [FrontendController::class, 'blogs'])->name('blogs');
Route::view('/about', 'about')->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::view('/faq', 'faq')->name('faq');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-of-use', 'terms-of-use')->name('terms-of-use');

// Authentication Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// User Dashboard (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/support-tickets', [App\Http\Controllers\UserController::class, 'storeSupportTicket'])->name('support-tickets.store');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\UserController::class, 'markNotificationAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\UserController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-as-read');
});

// Google OAuth Routes
Route::get('/auth/google', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Admin Login Routes (Public)
Route::prefix('admin')->name('admin.')->group(function () {
    // Redirect /admin to login or dashboard based on authentication
    Route::get('/', [AdminAuthController::class, 'entry'])->name('entry');
    
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout.submit');
});

// Admin routes (Protected)
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/general', [AdminController::class, 'updateGeneralSettings'])->name('settings.update-general');
        Route::post('/settings/gdpr-cookie', [AdminController::class, 'updateGdprCookieSettings'])->name('settings.update-gdpr-cookie');
        Route::get('/notification-settings', [AdminController::class, 'notificationSettings'])->name('notification-settings');
        Route::post('/notification-settings', [AdminController::class, 'updateNotificationSettings'])->name('notification-settings.update');
        Route::get('/frontend-settings', [AdminController::class, 'frontendSettings'])->name('frontend-settings');
        Route::post('/frontend-settings', [AdminController::class, 'updateFrontendSettings'])->name('frontend-settings.update');
        
        // Settings Submenu Routes
        Route::prefix('delivery-category')->name('delivery-category.')->group(function () {
            Route::get('/', [AdminController::class, 'deliveryCategoryIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'deliveryCategoryCreate'])->name('create');
            Route::post('/', [AdminController::class, 'deliveryCategoryStore'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'deliveryCategoryEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'deliveryCategoryUpdate'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'deliveryCategoryDelete'])->name('delete');
        });
        
        Route::prefix('delivery-charge')->name('delivery-charge.')->group(function () {
            Route::get('/', [AdminController::class, 'deliveryChargeIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'deliveryChargeCreate'])->name('create');
            Route::post('/', [AdminController::class, 'deliveryChargeStore'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'deliveryChargeEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'deliveryChargeUpdate'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'deliveryChargeDelete'])->name('delete');
        });
        
        Route::prefix('delivery-type')->name('delivery-type.')->group(function () {
            Route::get('/', [AdminController::class, 'deliveryTypeIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'deliveryTypeCreate'])->name('create');
            Route::post('/', [AdminController::class, 'deliveryTypeStore'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'deliveryTypeEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'deliveryTypeUpdate'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'deliveryTypeDelete'])->name('delete');
        });
        
        Route::prefix('liquid-fragile')->name('liquid-fragile.')->group(function () {
            Route::get('/', [AdminController::class, 'liquidFragileIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'liquidFragileCreate'])->name('create');
            Route::post('/', [AdminController::class, 'liquidFragileStore'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'liquidFragileEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'liquidFragileUpdate'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'liquidFragileDelete'])->name('delete');
        });
        
        Route::prefix('sms-settings')->name('sms-settings.')->group(function () {
            Route::get('/', [AdminController::class, 'smsSettingsIndex'])->name('index');
            Route::post('/', [AdminController::class, 'smsSettingsUpdate'])->name('update');
        });
        
        Route::prefix('sms-send-settings')->name('sms-send-settings.')->group(function () {
            Route::get('/', [AdminController::class, 'smsSendSettingsIndex'])->name('index');
            Route::post('/', [AdminController::class, 'smsSendSettingsUpdate'])->name('update');
        });
        
        Route::prefix('googlemap-settings')->name('googlemap-settings.')->group(function () {
            Route::get('/', [AdminController::class, 'googlemapSettingsIndex'])->name('index');
            Route::post('/', [AdminController::class, 'googlemapSettingsUpdate'])->name('update');
        });
        
        Route::prefix('mail-settings')->name('mail-settings.')->group(function () {
            Route::get('/', [AdminController::class, 'mailSettingsIndex'])->name('index');
            Route::post('/', [AdminController::class, 'mailSettingsUpdate'])->name('update');
        });
        
        Route::prefix('social-login')->name('social-login.')->group(function () {
            Route::get('/', [AdminController::class, 'socialLoginIndex'])->name('index');
            Route::post('/', [AdminController::class, 'socialLoginUpdate'])->name('update');
        });
        
        Route::prefix('payment-setup')->name('payment-setup.')->group(function () {
            Route::get('/', [AdminController::class, 'paymentSetupIndex'])->name('index');
            Route::post('/', [AdminController::class, 'paymentSetupUpdate'])->name('update');
        });
        
        Route::prefix('packaging')->name('packaging.')->group(function () {
            Route::get('/', [AdminController::class, 'packagingIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'packagingCreate'])->name('create');
            Route::post('/', [AdminController::class, 'packagingStore'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'packagingEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'packagingUpdate'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'packagingDelete'])->name('delete');
        });
        
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::get('/', [AdminController::class, 'currencyIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'currencyCreate'])->name('create');
            Route::post('/', [AdminController::class, 'currencyStore'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'currencyEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'currencyUpdate'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'currencyDelete'])->name('delete');
            Route::post('/set-default', [AdminController::class, 'currencySetDefault'])->name('set-default');
        });
        Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
        Route::post('/roles/assign', [AdminController::class, 'assignRole'])->name('roles.assign');
        Route::post('/roles/create', [AdminController::class, 'createRole'])->name('roles.create');
        Route::post('/roles/create-admin-user', [AdminController::class, 'createAdminUser'])->name('roles.create-admin-user');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{id}/verify', [AdminController::class, 'verifyUser'])->name('users.verify');
        Route::post('/users/{id}/unverify', [AdminController::class, 'unverifyUser'])->name('users.unverify');
        Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])->name('users.ban');
        Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
        Route::post('/users/{id}/login-as', [AdminController::class, 'loginAsUser'])->name('users.login-as');
    Route::get('/rate-calculator', [AdminController::class, 'rateCalculator'])->name('rate-calculator');
    Route::post('/rate-calculator/calculate', [AdminController::class, 'calculateRate'])->name('rate-calculator.calculate');
    Route::get('/search-with-awb', [AdminController::class, 'searchWithAwb'])->name('search-with-awb');
    Route::get('/search-with-awb/search', [AdminController::class, 'searchAWB'])->name('search-with-awb.search');
    Route::post('/search-with-awb/search', [AdminController::class, 'searchAWB'])->name('search-with-awb.search.submit');
    Route::get('/search-with-awb/history', [AdminController::class, 'historyAWB'])->name('search-with-awb.history');
    Route::delete('/search-with-awb/history/{id}', [AdminController::class, 'deleteHistoryEntry'])->name('search-with-awb.history.delete');
    Route::get('/search-with-awb/awb-numbers', [AdminController::class, 'getAwbNumbers'])->name('search-with-awb.awb-numbers');
    Route::get('/networks', [AdminController::class, 'networks'])->name('networks');
    Route::get('/networks/create', [AdminController::class, 'createNetwork'])->name('networks.create');
    Route::get('/networks/all', [AdminController::class, 'allNetworks'])->name('networks.all');
    Route::post('/networks', [AdminController::class, 'storeNetwork'])->name('networks.store');
    Route::post('/networks/bulk-delete', [AdminController::class, 'bulkDeleteNetworks'])->name('networks.bulk-delete');
    Route::get('/networks/{id}/view', [AdminController::class, 'viewNetwork'])->name('networks.view');
    Route::get('/networks/{id}/edit', [AdminController::class, 'editNetwork'])->name('networks.edit');
    Route::put('/networks/{id}', [AdminController::class, 'updateNetwork'])->name('networks.update');
    Route::post('/networks/{id}/toggle-status', [AdminController::class, 'toggleNetworkStatus'])->name('networks.toggle-status');
    Route::delete('/networks/{id}', [AdminController::class, 'deleteNetwork'])->name('networks.delete');
    Route::post('/networks/import', [AdminController::class, 'importNetworks'])->name('networks.import');
    Route::get('/networks/template/download', [AdminController::class, 'downloadNetworkTemplate'])->name('networks.template.download');
    Route::get('/transactions/all', [AdminController::class, 'allTransactions'])->name('transactions.all');
    Route::get('/transactions/{id}', [AdminController::class, 'showTransaction'])->name('transactions.show');
    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::get('/services/create', [AdminController::class, 'createService'])->name('services.create');
    Route::get('/services/all', [AdminController::class, 'allServices'])->name('services.all');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::get('/services/{id}/edit', [AdminController::class, 'editService'])->name('services.edit');
    Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');
    Route::post('/services/{id}/toggle-status', [AdminController::class, 'toggleServiceStatus'])->name('services.toggle-status');
    Route::delete('/services/{id}', [AdminController::class, 'deleteService'])->name('services.delete');
    Route::post('/services/bulk-delete', [AdminController::class, 'bulkDeleteServices'])->name('services.bulk-delete');
    Route::post('/services/import', [AdminController::class, 'importServices'])->name('services.import');
    Route::get('/services/template/download', [AdminController::class, 'downloadServiceTemplate'])->name('services.template.download');
    Route::get('/countries', [AdminController::class, 'countries'])->name('countries');
    Route::get('/countries/create', [AdminController::class, 'createCountry'])->name('countries.create');
    Route::get('/countries/all', [AdminController::class, 'allCountries'])->name('countries.all');
    Route::post('/countries', [AdminController::class, 'storeCountry'])->name('countries.store');
    Route::get('/countries/{id}/edit', [AdminController::class, 'editCountry'])->name('countries.edit');
    Route::put('/countries/{id}', [AdminController::class, 'updateCountry'])->name('countries.update');
    Route::post('/countries/{id}/toggle-status', [AdminController::class, 'toggleCountryStatus'])->name('countries.toggle-status');
    Route::delete('/countries/{id}', [AdminController::class, 'deleteCountry'])->name('countries.delete');
    Route::post('/countries/bulk-delete', [AdminController::class, 'bulkDeleteCountries'])->name('countries.bulk-delete');
    Route::post('/countries/import', [AdminController::class, 'importCountries'])->name('countries.import');
    Route::get('/countries/template/download', [AdminController::class, 'downloadCountryTemplate'])->name('countries.template.download');
    Route::get('/zones', [AdminController::class, 'zones'])->name('zones');
    Route::get('/zones/create', [AdminController::class, 'createZone'])->name('zones.create');
    Route::get('/zones/all', [AdminController::class, 'allZones'])->name('zones.all');
    Route::post('/zones', [AdminController::class, 'storeZone'])->name('zones.store');
    Route::get('/zones/{id}/edit', [AdminController::class, 'editZone'])->name('zones.edit');
    Route::put('/zones/{id}', [AdminController::class, 'updateZone'])->name('zones.update');
    Route::delete('/zones/{id}', [AdminController::class, 'deleteZone'])->name('zones.delete');
    Route::post('/zones/bulk-delete', [AdminController::class, 'bulkDeleteZones'])->name('zones.bulk-delete');
    Route::post('/zones/import', [AdminController::class, 'importZones'])->name('zones.import');
    Route::get('/zones/template/download', [AdminController::class, 'downloadZoneTemplate'])->name('zones.template.download');
    Route::get('/api/pincodes-by-country', [AdminController::class, 'getPincodesByCountry'])->name('api.pincodes-by-country');
    Route::get('/api/zones-by-country', [AdminController::class, 'getZonesByCountry'])->name('api.zones-by-country');
    Route::get('/api/pincodes-by-zone', [AdminController::class, 'getPincodesByZone'])->name('api.pincodes-by-zone');
    Route::get('/shipping-charges', [AdminController::class, 'shippingCharges'])->name('shipping-charges');
    Route::get('/shipping-charges/create', [AdminController::class, 'createShippingCharge'])->name('shipping-charges.create');
    Route::get('/shipping-charges/all', [AdminController::class, 'allShippingCharges'])->name('shipping-charges.all');
    Route::post('/shipping-charges', [AdminController::class, 'storeShippingCharge'])->name('shipping-charges.store');
    Route::get('/shipping-charges/{id}/edit', [AdminController::class, 'editShippingCharge'])->name('shipping-charges.edit');
    Route::put('/shipping-charges/{id}', [AdminController::class, 'updateShippingCharge'])->name('shipping-charges.update');
    Route::delete('/shipping-charges/{id}', [AdminController::class, 'deleteShippingCharge'])->name('shipping-charges.delete');
    Route::post('/shipping-charges/bulk-delete', [AdminController::class, 'bulkDeleteShippingCharges'])->name('shipping-charges.bulk-delete');
    Route::post('/shipping-charges/import', [AdminController::class, 'importShippingCharges'])->name('shipping-charges.import');
    Route::get('/shipping-charges/template/download', [AdminController::class, 'downloadShippingChargeTemplate'])->name('shipping-charges.template.download');
    Route::get('/shipping-charges/php-limits', [AdminController::class, 'phpLimitsCheck'])->name('shipping-charges.php-limits');
    Route::post('/shipping-charges/update-file', [AdminController::class, 'importShippingChargeUpdates'])->name('shipping-charges.import-updates');
    Route::get('/shipping-charges/update-template/download', [AdminController::class, 'downloadShippingChargeUpdateTemplate'])->name('shipping-charges.update-template.download');
    Route::get('/formulas', [AdminController::class, 'formulas'])->name('formulas');
    Route::get('/formulas/create', [AdminController::class, 'createFormula'])->name('formulas.create');
    Route::get('/formulas/all', [AdminController::class, 'allFormulas'])->name('formulas.all');
    Route::post('/formulas', [AdminController::class, 'storeFormula'])->name('formulas.store');
    Route::get('/formulas/{id}/edit', [AdminController::class, 'editFormula'])->name('formulas.edit');
    Route::put('/formulas/{id}', [AdminController::class, 'updateFormula'])->name('formulas.update');
    Route::post('/formulas/{id}/toggle-status', [AdminController::class, 'toggleFormulaStatus'])->name('formulas.toggle-status');
    Route::delete('/formulas/{id}', [AdminController::class, 'deleteFormula'])->name('formulas.delete');
    Route::post('/formulas/bulk-delete', [AdminController::class, 'bulkDeleteFormulas'])->name('formulas.bulk-delete');
    Route::post('/formulas/import', [AdminController::class, 'importFormulas'])->name('formulas.import');
    Route::get('/formulas/template/download', [AdminController::class, 'downloadFormulaTemplate'])->name('formulas.template.download');
    Route::get('/awb-upload', [AdminController::class, 'awbUpload'])->name('awb-upload');
    Route::get('/awb-upload/create', [AdminController::class, 'createAwbUpload'])->name('awb-upload.create');
    Route::get('/awb-upload/all', [AdminController::class, 'allAwbUpload'])->name('awb-upload.all');
    Route::post('/awb-upload', [AdminController::class, 'storeAwbUpload'])->name('awb-upload.store');
    Route::post('/awb-upload/bulk', [AdminController::class, 'bulkUploadAwbUpload'])->name('awb-upload.bulk');
    Route::post('/awb-upload/bulk-delete', [AdminController::class, 'bulkDeleteAwbUpload'])->name('awb-upload.bulk-delete');
    Route::get('/awb-upload/template/download', [AdminController::class, 'downloadAwbUploadTemplate'])->name('awb-upload.template.download');
    Route::get('/awb-upload/{id}/edit', [AdminController::class, 'editAwbUpload'])->name('awb-upload.edit');
    Route::put('/awb-upload/{id}', [AdminController::class, 'updateAwbUpload'])->name('awb-upload.update');
    Route::delete('/awb-upload/{id}', [AdminController::class, 'deleteAwbUpload'])->name('awb-upload.delete');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('bookings.create');
    Route::get('/bookings/all', [AdminController::class, 'allBookings'])->name('bookings.all');
    Route::post('/bookings', [AdminController::class, 'storeBooking'])->name('bookings.store');
    Route::get('/bookings/{id}/edit', [AdminController::class, 'editBooking'])->name('bookings.edit');
    Route::put('/bookings/{id}', [AdminController::class, 'updateBooking'])->name('bookings.update');
    Route::delete('/bookings/{id}', [AdminController::class, 'deleteBooking'])->name('bookings.delete');
    Route::post('/bookings/bulk-delete', [AdminController::class, 'bulkDeleteBookings'])->name('bookings.bulk-delete');
    Route::get('/booking-categories', [AdminController::class, 'bookingCategories'])->name('booking-categories');
    Route::get('/booking-categories/create', [AdminController::class, 'createBookingCategory'])->name('booking-categories.create');
    Route::get('/booking-categories/all', [AdminController::class, 'allBookingCategories'])->name('booking-categories.all');
    Route::post('/booking-categories', [AdminController::class, 'storeBookingCategory'])->name('booking-categories.store');
    Route::get('/booking-categories/{id}/edit', [AdminController::class, 'editBookingCategory'])->name('booking-categories.edit');
    Route::put('/booking-categories/{id}', [AdminController::class, 'updateBookingCategory'])->name('booking-categories.update');
    Route::delete('/booking-categories/{id}', [AdminController::class, 'deleteBookingCategory'])->name('booking-categories.delete');
    Route::post('/booking-categories/bulk-delete', [AdminController::class, 'bulkDeleteBookingCategories'])->name('booking-categories.bulk-delete');
    Route::post('/booking-categories/import', [AdminController::class, 'importBookingCategories'])->name('booking-categories.import');
    Route::get('/booking-categories/template/download', [AdminController::class, 'downloadBookingCategoryTemplate'])->name('booking-categories.template.download');
    Route::get('/blogs', [AdminController::class, 'blogs'])->name('blogs');
    Route::get('/blogs/create', [AdminController::class, 'createBlog'])->name('blogs.create');
    Route::get('/blogs/all', [AdminController::class, 'allBlogs'])->name('blogs.all');
    Route::post('/blogs', [AdminController::class, 'storeBlog'])->name('blogs.store');
    Route::get('/blogs/{id}/edit', [AdminController::class, 'editBlog'])->name('blogs.edit');
    Route::put('/blogs/{id}', [AdminController::class, 'updateBlog'])->name('blogs.update');
    Route::delete('/blogs/{id}', [AdminController::class, 'deleteBlog'])->name('blogs.delete');
    Route::get('/about-us/edit', [AdminController::class, 'editAboutUs'])->name('about-us.edit');
    Route::put('/about-us', [AdminController::class, 'updateAboutUs'])->name('about-us.update');
    Route::get('/services-section/edit', [AdminController::class, 'editServicesSection'])->name('services-section.edit');
    Route::put('/services-section', [AdminController::class, 'updateServicesSection'])->name('services-section.update');
    Route::get('/why-haxo-section/edit', [AdminController::class, 'editWhyHaxoSection'])->name('why-haxo-section.edit');
    Route::put('/why-haxo-section', [AdminController::class, 'updateWhyHaxoSection'])->name('why-haxo-section.update');
    Route::get('/pricing-section/edit', [AdminController::class, 'editPricingSection'])->name('pricing-section.edit');
    Route::put('/pricing-section', [AdminController::class, 'updatePricingSection'])->name('pricing-section.update');
    Route::get('/stats-section/edit', [AdminController::class, 'editStatsSection'])->name('stats-section.edit');
    Route::put('/stats-section', [AdminController::class, 'updateStatsSection'])->name('stats-section.update');
    Route::get('/direct-entry/create', [AdminController::class, 'createDirectEntry'])->name('direct-entry.create');
    Route::get('/direct-entry/all', [AdminController::class, 'allDirectEntry'])->name('direct-entry.all');
    Route::post('/direct-entry', [AdminController::class, 'storeDirectEntry'])->name('direct-entry.store');
    Route::get('/direct-entry/{id}/edit', [AdminController::class, 'editDirectEntry'])->name('direct-entry.edit');
    Route::put('/direct-entry/{id}', [AdminController::class, 'updateDirectEntry'])->name('direct-entry.update');
    Route::delete('/direct-entry/{id}', [AdminController::class, 'deleteDirectEntry'])->name('direct-entry.delete');
    Route::post('/direct-entry/bulk-delete', [AdminController::class, 'bulkDeleteDirectEntry'])->name('direct-entry.bulk-delete');
    Route::get('/reports', [AdminController::class, 'reportsIndex'])->name('reports.index');
    Route::get('/reports/{reportType}/content', [AdminController::class, 'getReportContent'])->name('reports.content');
    Route::get('/reports/zone', [AdminController::class, 'zoneReport'])->name('reports.zone');
    Route::get('/reports/formula', [AdminController::class, 'formulaReport'])->name('reports.formula');
    Route::get('/reports/shipping-charges', [AdminController::class, 'shippingChargesReport'])->name('reports.shipping-charges');
    Route::get('/reports/booking', [AdminController::class, 'bookingReport'])->name('reports.booking');
    Route::get('/reports/payment', [AdminController::class, 'paymentReport'])->name('reports.payment');
    Route::get('/reports/zone/export', [AdminController::class, 'exportZoneReport'])->name('reports.zone.export');
    Route::get('/reports/formula/export', [AdminController::class, 'exportFormulaReport'])->name('reports.formula.export');
    Route::get('/reports/shipping-charges/export', [AdminController::class, 'exportShippingChargesReport'])->name('reports.shipping-charges.export');
    Route::get('/reports/booking/export', [AdminController::class, 'exportBookingReport'])->name('reports.booking.export');
    Route::get('/reports/payment/export', [AdminController::class, 'exportPaymentReport'])->name('reports.payment.export');
    Route::get('/reports/network/export', [AdminController::class, 'exportNetworkReport'])->name('reports.network.export');
    Route::get('/reports/service/export', [AdminController::class, 'exportServiceReport'])->name('reports.service.export');
    Route::get('/reports/country/export', [AdminController::class, 'exportCountryReport'])->name('reports.country.export');
    Route::get('/reports/bank/export', [AdminController::class, 'exportBankReport'])->name('reports.bank.export');
    Route::get('/reports/wallet/export', [AdminController::class, 'exportWalletReport'])->name('reports.wallet.export');
    Route::get('/reports/transaction', [AdminController::class, 'transactionReport'])->name('reports.transaction');
    Route::get('/reports/transaction/export', [AdminController::class, 'exportTransactionReport'])->name('reports.transaction.export');
    Route::get('/reports/network', [AdminController::class, 'networkReport'])->name('reports.network');
    Route::get('/reports/service', [AdminController::class, 'serviceReport'])->name('reports.service');
    Route::get('/reports/country', [AdminController::class, 'countryReport'])->name('reports.country');
    Route::get('/reports/bank', [AdminController::class, 'bankReport'])->name('reports.bank');
    Route::get('/reports/wallet', [AdminController::class, 'walletReport'])->name('reports.wallet');
    Route::get('/banks', [AdminController::class, 'banks'])->name('banks');
    Route::get('/banks/create', [AdminController::class, 'createBank'])->name('banks.create');
    Route::get('/banks/all', [AdminController::class, 'allBanks'])->name('banks.all');
    Route::get('/banks/transfer', [AdminController::class, 'bankTransfer'])->name('banks.transfer');
    Route::post('/banks/transfer', [AdminController::class, 'storeBankTransfer'])->name('banks.transfer.store');
    Route::get('/banks/transfer/all', [AdminController::class, 'allBankTransfers'])->name('banks.transfer.all');
    Route::get('/banks/transfer/export', [AdminController::class, 'exportBankTransfers'])->name('banks.transfer.export');
    Route::get('/banks/transfer/{id}/view', [AdminController::class, 'viewBankTransfer'])->name('banks.transfer.view');
    Route::delete('/banks/transfer/{id}', [AdminController::class, 'deleteBankTransfer'])->name('banks.transfer.delete');
    Route::post('/banks/bulk-delete', [AdminController::class, 'bulkDeleteBanks'])->name('banks.bulk-delete');
    Route::post('/banks', [AdminController::class, 'storeBank'])->name('banks.store');
    Route::get('/banks/{id}/view', [AdminController::class, 'viewBank'])->name('banks.view');
    Route::get('/banks/{id}/edit', [AdminController::class, 'editBank'])->name('banks.edit');
    Route::put('/banks/{id}', [AdminController::class, 'updateBank'])->name('banks.update');
    Route::delete('/banks/{id}', [AdminController::class, 'deleteBank'])->name('banks.delete');
    Route::post('/banks/import', [AdminController::class, 'importBanks'])->name('banks.import');
    Route::get('/banks/template/download', [AdminController::class, 'downloadBankTemplate'])->name('banks.template.download');
    Route::get('/payments-into-bank', [AdminController::class, 'paymentsIntoBank'])->name('payments-into-bank');
    Route::get('/payments-into-bank/create', [AdminController::class, 'createPaymentIntoBank'])->name('payments-into-bank.create');
    Route::get('/payments-into-bank/all', [AdminController::class, 'allPaymentsIntoBank'])->name('payments-into-bank.all');
    Route::post('/payments-into-bank', [AdminController::class, 'storePaymentIntoBank'])->name('payments-into-bank.store');
    Route::post('/payments-into-bank/import', [AdminController::class, 'importPaymentsIntoBank'])->name('payments-into-bank.import');
    Route::get('/payments-into-bank/template/download', [AdminController::class, 'downloadPaymentsIntoBankTemplate'])->name('payments-into-bank.template.download');
    Route::get('/payments-into-bank/{id}/edit', [AdminController::class, 'editPaymentIntoBank'])->name('payments-into-bank.edit');
    Route::put('/payments-into-bank/{id}', [AdminController::class, 'updatePaymentIntoBank'])->name('payments-into-bank.update');
    Route::delete('/payments-into-bank/{id}', [AdminController::class, 'deletePaymentIntoBank'])->name('payments-into-bank.delete');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/payments/create', [AdminController::class, 'createPayment'])->name('payments.create');
    Route::get('/payments/all', [AdminController::class, 'allPayments'])->name('payments.all');
    Route::post('/payments', [AdminController::class, 'storePayment'])->name('payments.store');
    Route::get('/payments/{id}/edit', [AdminController::class, 'editPayment'])->name('payments.edit');
    Route::put('/payments/{id}', [AdminController::class, 'updatePayment'])->name('payments.update');
    Route::delete('/payments/{id}', [AdminController::class, 'deletePayment'])->name('payments.delete');
    Route::get('/payments/wallet', [AdminController::class, 'manageWallet'])->name('payments.wallet');
    Route::post('/payments/wallet/bulk', [AdminController::class, 'bulkUpdateWallet'])->name('payments.wallet.bulk');
    Route::post('/payments/wallet/import', [AdminController::class, 'importWalletTransactions'])->name('payments.wallet.import');
    Route::get('/payments/wallet/template/download', [AdminController::class, 'downloadWalletTransactionsTemplate'])->name('payments.wallet.template.download');
    Route::get('/payments/gateways', [AdminController::class, 'paymentGateways'])->name('payments.gateways');
    Route::get('/payments/gateways/create', [AdminController::class, 'createPaymentGateway'])->name('payments.gateways.create');
    Route::post('/payments/gateways', [AdminController::class, 'storePaymentGateway'])->name('payments.gateways.store');
    Route::get('/payments/gateways/{id}/edit', [AdminController::class, 'editPaymentGateway'])->name('payments.gateways.edit');
    Route::put('/payments/gateways/{id}', [AdminController::class, 'updatePaymentGateway'])->name('payments.gateways.update');
    Route::delete('/payments/gateways/{id}', [AdminController::class, 'deletePaymentGateway'])->name('payments.gateways.delete');
    
    // Support Tickets routes
    Route::get('/support-tickets', [AdminController::class, 'supportTickets'])->name('support-tickets.all');
    Route::get('/support-tickets/{id}', [AdminController::class, 'viewSupportTicket'])->name('support-tickets.view');
    Route::put('/support-tickets/{id}', [AdminController::class, 'updateSupportTicket'])->name('support-tickets.update');
    Route::delete('/support-tickets/{id}', [AdminController::class, 'deleteSupportTicket'])->name('support-tickets.delete');
    
    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    // Payroll routes
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/salary-generate', [AdminController::class, 'salaryGenerateIndex'])->name('salary-generate.index');
        Route::post('/salary-generate', [AdminController::class, 'salaryGenerateStore'])->name('salary-generate.store');
        Route::post('/salary-generate/auto', [AdminController::class, 'salaryGenerateAuto'])->name('salary-generate.auto');
        Route::get('/list', [AdminController::class, 'payrollList'])->name('list');
        Route::get('/sand-bullary-generate', [AdminController::class, 'sandBullaryGenerateIndex'])->name('sand-bullary-generate.index');
    });

    Route::prefix('todos')->name('todos.')->group(function () {
        Route::get('/', [AdminTodoController::class, 'index'])->name('index');
        Route::post('/', [AdminTodoController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminTodoController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-complete', [AdminTodoController::class, 'toggleComplete'])->name('toggle-complete');
        Route::delete('/{id}', [AdminTodoController::class, 'destroy'])->name('destroy');
        Route::get('/poll/reminders', [AdminTodoController::class, 'pollReminders'])->name('poll-reminders');
    });
});

/**
 * Serve files stored in storage/app/public when symbolic links are not available.
 */
Route::get('/storage/{path}', function (string $path) {
    $relativePath = rawurldecode($path);
    $relativePath = ltrim($relativePath, '/');

    $storageBasePath = storage_path('app/public');
    $targetPath = realpath($storageBasePath . DIRECTORY_SEPARATOR . $relativePath);

    if ($targetPath === false || !str_starts_with($targetPath, $storageBasePath)) {
        abort(404);
    }

    if (!File::exists($targetPath)) {
        abort(404);
    }

    return response()->file($targetPath);
})->where('path', '.*');

/**
 * Run migrations route (for deployment without SSH access)
 * Access: /run-migrations?token=YOUR_SECRET_TOKEN
 * Set MIGRATION_TOKEN in .env file for security
 */
Route::get('/run-migrations', function () {
    // TOKEN CHECK DISABLED BY DEFAULT - Allow access without token
    // This route works without authentication for easy deployment
    // To enable token requirement, uncomment the code below and set MIGRATION_TOKEN in .env
    
    // Optional: Enable token check (currently disabled)
    /*
    $expectedToken = trim((string)(env('MIGRATION_TOKEN') ?: ''));
    if (!empty($expectedToken)) {
        $providedToken = trim((string)(request()->query('token') ?? ''));
        if (empty($providedToken) || $providedToken !== $expectedToken) {
            return response()->json([
                'error' => 'Unauthorized. Invalid or missing token.',
                'message' => 'Token required. Add ?token=YOUR_TOKEN to URL.'
            ], 401);
        }
    }
    */
    // Proceeding without token requirement
    
    try {
        // Get current database connection
        $connection = \DB::connection();
        $databaseName = \DB::connection()->getDatabaseName();
        
        // Get all existing tables (works with both MySQL and SQLite)
        $existingTables = getDatabaseTables();
        
        // Get migration status before running
        Artisan::call('migrate:status');
        $statusBefore = Artisan::output();
        
        // Count migrations before
        $migrationsBefore = 0;
        try {
            $migrationsBefore = \DB::table('migrations')->count();
        } catch (\Exception $e) {
            // migrations table might not exist
        }
        
        // Run migrations with force flag - this will run any pending migrations
        Artisan::call('migrate', ['--force' => true]);
        
        $output = Artisan::output();
        
        // Get migration status after running
        Artisan::call('migrate:status');
        $statusAfter = Artisan::output();
        
        // Count migrations after
        $migrationsAfter = 0;
        try {
            $migrationsAfter = \DB::table('migrations')->count();
        } catch (\Exception $e) {
            // migrations table might not exist
        }
        
        // Get all tables after migration (works with both MySQL and SQLite)
        $tablesAfter = getDatabaseTables();
        
        // Get list of all migration files to count total
        $migrationFiles = glob(database_path('migrations/*.php'));
        $totalMigrations = count($migrationFiles);
        
        $newMigrationsRun = $migrationsAfter - $migrationsBefore;
        $newTablesCreated = count($tablesAfter) - count($existingTables);
        
        return response()->json([
            'success' => true,
            'message' => 'Migrations ran successfully!',
            'total_migration_files' => $totalMigrations,
            'migrations_before' => $migrationsBefore,
            'migrations_after' => $migrationsAfter,
            'new_migrations_run' => $newMigrationsRun,
            'tables_before' => count($existingTables),
            'tables_after' => count($tablesAfter),
            'new_tables_created' => $newTablesCreated,
            'existing_tables' => $existingTables,
            'all_tables_now' => $tablesAfter,
            'output' => $output,
            'status_before' => $statusBefore,
            'status_after' => $statusAfter
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error('Migration route error: ' . $e->getMessage());
        \Log::error('Migration route stack trace: ' . $e->getTraceAsString());
        
        // Get detailed error information for debugging
        $errorDetails = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ];
        
        // Check database connection
        $dbStatus = 'unknown';
        try {
            \DB::connection()->getPdo();
            $dbStatus = 'connected';
            $dbName = \DB::connection()->getDatabaseName();
            $errorDetails['database'] = [
                'status' => $dbStatus,
                'name' => $dbName,
                'connection' => 'ok'
            ];
        } catch (\Exception $dbEx) {
            $dbStatus = 'error';
            $errorDetails['database'] = [
                'status' => $dbStatus,
                'error' => $dbEx->getMessage(),
                'connection' => 'failed'
            ];
        }
        
        // Check migrations directory
        $migrationsPath = database_path('migrations');
        $errorDetails['migrations_directory'] = [
            'path' => $migrationsPath,
            'exists' => file_exists($migrationsPath),
            'readable' => is_readable($migrationsPath),
            'file_count' => file_exists($migrationsPath) ? count(glob($migrationsPath . '/*.php')) : 0
        ];
        
        // Check permissions
        $errorDetails['permissions'] = [
            'database_writable' => is_writable(database_path()),
            'storage_writable' => is_writable(storage_path()),
            'bootstrap_cache_writable' => is_writable(bootstrap_path('cache'))
        ];
        
        return response()->json([
            'success' => false,
            'error' => 'Migration failed',
            'error_details' => $errorDetails,
            'suggestions' => [
                'Check database credentials in .env file',
                'Verify database exists and user has proper permissions',
                'Check file permissions on database/ and storage/ directories',
                'Ensure migrations directory exists: ' . $migrationsPath,
                'Check PHP error logs for more details'
            ]
        ], 500);
    }
})->name('run-migrations');

/**
 * Clear config cache route (for deployment without SSH access)
 * Access: /clear-config-cache
 */
Route::get('/clear-config-cache', function () {
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully!',
            'output' => Artisan::output()
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to clear cache',
            'message' => $e->getMessage()
        ], 500);
    }
})->name('clear-config-cache');

/**
 * Force re-run all migrations route (resets migrations table and re-runs all)
 * This is useful when migrations are marked as run but tables weren't created
 * Access: /force-migrate
 * WARNING: This will reset the migrations table and re-run ALL migrations
 */
Route::get('/force-migrate', function () {
    try {
        // Get current tables (works with both MySQL and SQLite)
        $tablesBefore = getDatabaseTables();
        
        // Count migrations before
        $migrationsBefore = 0;
        try {
            $migrationsBefore = \DB::table('migrations')->count();
        } catch (\Exception $e) {
            // migrations table might not exist
        }
        
        // Reset migrations table - remove all entries to force re-run
        try {
            \DB::table('migrations')->truncate();
        } catch (\Exception $e) {
            // If truncate fails, try delete
            try {
                \DB::table('migrations')->delete();
            } catch (\Exception $e2) {
                // migrations table might not exist, continue
            }
        }
        
        // Now run all migrations fresh
        Artisan::call('migrate', ['--force' => true]);
        
        $output = Artisan::output();
        
        // Get status after
        Artisan::call('migrate:status');
        $statusAfter = Artisan::output();
        
        // Count migrations after
        $migrationsAfter = 0;
        try {
            $migrationsAfter = \DB::table('migrations')->count();
        } catch (\Exception $e) {
            // migrations table might not exist
        }
        
        // Get all tables after migration (works with both MySQL and SQLite)
        $tablesAfter = getDatabaseTables();
        
        $migrationFiles = glob(database_path('migrations/*.php'));
        $totalMigrations = count($migrationFiles);
        
        $newTablesCreated = count($tablesAfter) - count($tablesBefore);
        $missingTables = array_diff($tablesAfter, $tablesBefore);
        
        return response()->json([
            'success' => true,
            'message' => 'All migrations re-run successfully!',
            'total_migration_files' => $totalMigrations,
            'migrations_before' => $migrationsBefore,
            'migrations_after' => $migrationsAfter,
            'tables_before' => count($tablesBefore),
            'tables_after' => count($tablesAfter),
            'new_tables_created' => $newTablesCreated,
            'missing_tables_now_created' => array_values($missingTables),
            'all_tables' => $tablesAfter,
            'output' => $output,
            'status' => $statusAfter
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error('Force migrate route error: ' . $e->getMessage());
        \Log::error('Force migrate route stack trace: ' . $e->getTraceAsString());
        
        // Get detailed error information for debugging on live servers
        $errorDetails = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ];
        
        // Check database connection
        try {
            \DB::connection()->getPdo();
            $dbName = \DB::connection()->getDatabaseName();
            $errorDetails['database'] = [
                'status' => 'connected',
                'name' => $dbName
            ];
        } catch (\Exception $dbEx) {
            $errorDetails['database'] = [
                'status' => 'error',
                'error' => $dbEx->getMessage()
            ];
        }
        
        // Check migrations directory
        $migrationsPath = database_path('migrations');
        $errorDetails['migrations_directory'] = [
            'path' => $migrationsPath,
            'exists' => file_exists($migrationsPath),
            'file_count' => file_exists($migrationsPath) ? count(glob($migrationsPath . '/*.php')) : 0
        ];
        
        return response()->json([
            'success' => false,
            'error' => 'Force migration failed',
            'error_details' => $errorDetails,
            'suggestions' => [
                'Verify database connection settings in .env file',
                'Check database user has CREATE, ALTER, DROP permissions',
                'Ensure migrations directory exists and is readable',
                'Check PHP memory_limit and max_execution_time settings',
                'Review server error logs for more details'
            ]
        ], 500);
    }
})->name('force-migrate');

/**
 * Diagnostic route to check server and database configuration
 * Access: /check-migration-status
 */
Route::get('/check-migration-status', function () {
    $diagnostics = [];
    
    // Check database connection
    try {
        $pdo = \DB::connection()->getPdo();
        $dbName = \DB::connection()->getDatabaseName();
        $diagnostics['database'] = [
            'status' => 'connected',
            'name' => $dbName,
            'driver' => \DB::connection()->getDriverName(),
        ];
        
        // Check if migrations table exists
        try {
            $migrationsCount = \DB::table('migrations')->count();
            $diagnostics['migrations_table'] = [
                'exists' => true,
                'recorded_migrations' => $migrationsCount
            ];
        } catch (\Exception $e) {
            $diagnostics['migrations_table'] = [
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
        
        // Count actual tables (works with both MySQL and SQLite)
        try {
            $tableNames = getDatabaseTables();
            $diagnostics['tables'] = [
                'count' => count($tableNames),
                'names' => $tableNames
            ];
        } catch (\Exception $e) {
            $diagnostics['tables'] = [
                'error' => $e->getMessage()
            ];
        }
        
    } catch (\Exception $e) {
        $diagnostics['database'] = [
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
    
    // Check migrations directory
    $migrationsPath = database_path('migrations');
    $migrationFiles = glob($migrationsPath . '/*.php');
    $diagnostics['migrations_directory'] = [
        'path' => $migrationsPath,
        'exists' => file_exists($migrationsPath),
        'readable' => is_readable($migrationsPath),
        'file_count' => count($migrationFiles),
        'files' => array_map('basename', $migrationFiles)
    ];
    
    // Check file permissions
    $diagnostics['permissions'] = [
        'database_dir_writable' => is_writable(database_path()),
        'storage_dir_writable' => is_writable(storage_path()),
        'bootstrap_cache_writable' => is_writable(base_path('bootstrap/cache'))
    ];
    
    // Check PHP configuration
    $diagnostics['php'] = [
        'version' => PHP_VERSION,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'pdo_mysql_loaded' => extension_loaded('pdo_mysql'),
        'mysqli_loaded' => extension_loaded('mysqli')
    ];
    
    // Check environment
    $diagnostics['environment'] = [
        'app_env' => env('APP_ENV', 'not set'),
        'app_debug' => env('APP_DEBUG', 'not set'),
        'db_connection' => env('DB_CONNECTION', 'not set'),
        'db_host' => env('DB_HOST', 'not set'),
        'db_database' => env('DB_DATABASE', 'not set'),
        'db_port' => env('DB_PORT', 'not set'),
    ];
    
    return response()->json([
        'success' => true,
        'diagnostics' => $diagnostics,
        'recommendations' => []
    ], 200);
})->name('check-migration-status');

// Exclude asset paths from fallback to prevent MIME type errors
Route::fallback(function () {
    $path = request()->path();
    
    // If requesting static assets (CSS, JS, images, etc.), return proper 404 without HTML
    if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot|json|map|wasm)$/i', $path)) {
        abort(404);
    }
    
    return app(ErrorController::class)->notFound();
});
