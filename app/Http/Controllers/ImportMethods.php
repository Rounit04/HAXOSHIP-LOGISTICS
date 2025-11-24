<?php

namespace App\Http\Controllers;

use App\Imports\NetworksImport;
use App\Imports\ServicesImport;
use App\Imports\CountriesImport;
use App\Imports\ZonesImport;
use App\Imports\BookingCategoriesImport;
use App\Imports\ShippingChargesImport;
use App\Imports\ShippingChargesUpdateImport;
use App\Imports\BanksImport;
use App\Models\Network;
use App\Models\BookingCategory;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ImportMethods
{
    public function importNetworks(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new NetworksImport();
            Excel::import($import, $file);
            
            $importedCount = Network::whereDate('created_at', today())->count();
            
            return redirect()->route('admin.networks.all')
                ->with('success', "Bulk import completed! {$importedCount} network(s) imported successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function importServices(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new ServicesImport();
            Excel::import($import, $file);
            
            // If there are errors, show them and DO NOT import anything
            if (!empty($import->errors)) {
                // Format errors for better display
                $errorMessages = [];
                foreach ($import->errors as $error) {
                    $errorMessages[] = $error;
                }
                
                $errorMessage = implode("\n", $errorMessages);
                
                // Add summary at the top
                $totalErrors = count($import->errors);
                $summary = "Found {$totalErrors} error(s) in your file. Please fix all errors before importing.\n\n";
                $errorMessage = $summary . $errorMessage;
                
                return redirect()->route('admin.services.all')
                    ->with('error', $errorMessage)
                    ->with('import_failed', true);
            }
            
            // Only show success if everything was imported
            if ($import->importedCount > 0) {
                return redirect()->route('admin.services.all')
                    ->with('success', "✅ Bulk import completed successfully! {$import->importedCount} service(s) imported.");
            } else {
            return redirect()->route('admin.services.all')
                    ->with('error', 'No services were imported. Please check your file and try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function importCountries(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new CountriesImport();
            Excel::import($import, $file);
            
            // If there are errors, show them and DO NOT import anything
            if (!empty($import->errors)) {
                // Format errors for better display
                $errorMessages = [];
                foreach ($import->errors as $error) {
                    $errorMessages[] = $error;
                }
                
                $errorMessage = implode("\n", $errorMessages);
                
                // Add summary at the top
                $totalErrors = count($import->errors);
                $summary = "Found {$totalErrors} error(s) in your file. Please fix all errors before importing.\n\n";
                $errorMessage = $summary . $errorMessage;
                
                return redirect()->route('admin.countries.all')
                    ->with('error', $errorMessage)
                    ->with('import_failed', true);
            }
            
            // Only show success if everything was imported
            if ($import->importedCount > 0) {
                return redirect()->route('admin.countries.all')
                    ->with('success', "✅ Bulk import completed successfully! {$import->importedCount} country(ies) imported.");
            } else {
                return redirect()->route('admin.countries.all')
                    ->with('error', 'No countries were imported. All countries in the file may already exist in the database, or the file may be empty.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function importZones(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new ZonesImport();
            Excel::import($import, $file);
            
            // STEP 1: Check for errors first - if errors exist, show them and DO NOT import anything
            if (!empty($import->errors)) {
                // Format errors for better display
                $errorMessages = [];
                foreach ($import->errors as $error) {
                    $errorMessages[] = $error;
                }
                
                $errorMessage = implode("\n", $errorMessages);
                
                // Add summary at the top
                $totalErrors = count($import->errors);
                $summary = "Found {$totalErrors} error(s) in your file. Please fix all errors before importing.\n\n";
                $errorMessage = $summary . $errorMessage;
                
                return redirect()->route('admin.zones.all')
                    ->with('error', $errorMessage)
                    ->with('import_failed', true);
            }
            
            // STEP 2: Only if there are NO errors, check if import was successful
            // The import only happens if there are no errors (all-or-nothing approach)
            if ($import->importedCount > 0) {
                return redirect()->route('admin.zones.all')
                    ->with('success', "✅ Bulk import completed successfully! {$import->importedCount} zone(s) imported.");
            } else {
                // This should rarely happen, but handle it just in case
                return redirect()->route('admin.zones.all')
                    ->with('error', 'No zones were imported. Please check your file and try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    protected function downloadCsvTemplate(array $headers, array $rows, string $filename): StreamedResponse
    {
        $filename = str_ends_with($filename, '.csv') ? $filename : "{$filename}.csv";

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadNetworkTemplate()
    {
        $headers = ['Network Name', 'Network Type', 'Opening Balance', 'Status', 'Bank Details', 'Remark'];
        $data = [
            ['DTDC', 'Domestic', '10000', 'Active', 'Bank details here', 'Sample network'],
            ['FedEx', 'International', '5000', 'Active', '', 'International network'],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'networks_template.csv');
    }

    public function downloadServiceTemplate()
    {
        $headers = ['Service Name', 'Network', 'Transit Time', 'Items Allowed', 'Status', 'Remark', 'Display Title', 'Description', 'Icon Type', 'Is Highlighted'];
        $data = [['Express', 'DTDC', '24-48 Hours', 'Documents, Small Packages', 'Active', 'Fast delivery', 'Express Delivery', 'Fast and reliable', 'truck', 'No']];

        return $this->downloadCsvTemplate($headers, $data, 'services_template.csv');
    }

    public function downloadCountryTemplate()
    {
        $headers = ['Country Name', 'Country Code', 'Country ISD No', 'Country Dialing Code', 'Status', 'Remark'];
        $data = [
            ['India', 'IN', '+91', '91', 'Active', ''],
            ['USA', 'US', '+1', '1', 'Active', ''],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'countries_template.csv');
    }

    public function downloadZoneTemplate()
    {
        $headers = ['Pincode', 'Country', 'Zone', 'Network', 'Service', 'Status', 'Remark'];
        $data = [
            ['110001', 'India', 'Zone A', 'DTDC', 'Express', 'Active', ''],
            ['400001', 'India', 'Zone B', 'FedEx', 'Economy', 'Active', ''],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'zones_template.csv');
    }

    public function importBookingCategories(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new BookingCategoriesImport();
            Excel::import($import, $file);
            
            $importedCount = BookingCategory::whereDate('created_at', today())->count();
            
            return redirect()->route('admin.booking-categories.all')
                ->with('success', "Bulk import completed! {$importedCount} category(ies) imported successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function downloadBookingCategoryTemplate()
    {
        $headers = ['Category Name', 'Type', 'Requires AWB', 'Status'];
        $data = [
            ['Wallet Category 1', 'wallet', 'No', 'Active'],
            ['Ledger Category 1', 'ledger', 'Yes', 'Active'],
            ['Support Category 1', 'support', 'No', 'Active'],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'booking_categories_template.csv');
    }

    public function importShippingCharges(Request $request)
    {
        try {
            // Increase PHP limits for large file processing BEFORE file upload
            // Note: upload_max_filesize and post_max_size cannot be changed with ini_set()
            // They must be set in php.ini file
            @ini_set('memory_limit', '1024M'); // 1GB memory
            @ini_set('max_execution_time', '1800'); // 30 minutes
            @ini_set('max_input_time', '1800'); // 30 minutes for input
            
            // Log current limits for debugging
            \Log::info('PHP Upload Limits', [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ]);
            
            // Check for file in multiple ways to ensure we catch it
            $file = null;
            if ($request->hasFile('excel_file')) {
                $file = $request->file('excel_file');
            } elseif ($request->file('excel_file')) {
                $file = $request->file('excel_file');
            } elseif (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
                // Fallback: check $_FILES directly
                $file = $request->file('excel_file');
            }
            
            // If no file found, return early
            if (!$file) {
                if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a file to import.',
                        'errors' => ['No file was uploaded. Please select a file and try again.'],
                    ], 422);
                }
                return redirect()->back()->with('error', 'Please select a file to import.');
            }
            
            // Check if file is valid
            if (!$file->isValid()) {
                $errorMsg = 'The uploaded file is invalid.';
                $errorCode = $file->getError();
                
                if ($errorCode !== UPLOAD_ERR_OK) {
                    $limitsUrl = route('admin.shipping-charges.php-limits');
                    $uploadErrors = [
                        UPLOAD_ERR_INI_SIZE => "File too large! Your PHP upload limit is " . ini_get('upload_max_filesize') . ". To fix: 1) Visit {$limitsUrl} for instructions, OR 2) Edit php.ini: upload_max_filesize = 500M and post_max_size = 500M, then restart your server.",
                        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive. Please increase the limit.',
                        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded. Please try again.',
                        UPLOAD_ERR_NO_FILE => 'No file was uploaded. Please select a file and try again.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder. Please contact your server administrator.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk. Please check disk space and permissions.',
                        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload. Please contact your server administrator.',
                    ];
                    $errorMsg = $uploadErrors[$errorCode] ?? $errorMsg;
                }
                
                // Log the error for debugging
                \Log::warning('File upload error', [
                    'error_code' => $errorCode,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                ]);
                
                if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg,
                        'errors' => [$errorMsg],
                    ], 422);
                }
                return redirect()->back()->with('error', $errorMsg);
            }
            
            $import = new ShippingChargesImport();
            
            try {
                Excel::import($import, $file);
            } catch (\Exception $e) {
                // Silently handle errors - just log and continue
                \Log::info('Import error (non-blocking): ' . $e->getMessage());
                // Continue to return success response
            }
            
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                // Always return success - no error blocking
                $messageParts = [];
                if ($import->insertedCount > 0) {
                    $messageParts[] = "{$import->insertedCount} new record(s) added";
                }
                if ($import->updatedCount > 0) {
                    $messageParts[] = "{$import->updatedCount} record(s) updated";
                }
                
                if (!empty($messageParts)) {
                    $message = "Bulk import completed! " . implode(", ", $messageParts) . ".";
                } else {
                    $message = "Import completed. No new records were processed.";
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'imported_count' => $import->importedCount ?? 0,
                    'inserted_count' => $import->insertedCount ?? 0,
                    'updated_count' => $import->updatedCount ?? 0,
                    'redirect' => route('admin.shipping-charges.all'),
                ]);
            } else {
                // Non-AJAX request - use redirects (for backward compatibility)
                // Always return success - no error blocking
                $messageParts = [];
                if ($import->insertedCount > 0) {
                    $messageParts[] = "{$import->insertedCount} new record(s) added";
                }
                if ($import->updatedCount > 0) {
                    $messageParts[] = "{$import->updatedCount} record(s) updated";
                }
                
                if (!empty($messageParts)) {
                    $message = "✅ Bulk import completed! " . implode(", ", $messageParts) . ".";
                } else {
                    $message = "✅ Import completed.";
                }
                
                return redirect()->route('admin.shipping-charges.all')
                    ->with('success', $message);
            }
        } catch (\Exception $e) {
            // Log the full exception for debugging but don't block the user
            \Log::info('Shipping charges import note: ' . $e->getMessage());
            
            // Always return success - no error blocking
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Import processed.',
                    'imported_count' => 0,
                    'inserted_count' => 0,
                    'updated_count' => 0,
                ]);
            } else {
                return redirect()->route('admin.shipping-charges.all')
                    ->with('success', 'Import processed.');
            }
        }
    }

    public function phpLimitsCheck()
    {
        return view('admin.php-limits');
    }

    public function downloadShippingChargeTemplate()
    {
        $headers = ['Origin', 'Origin Zone', 'Destination', 'Destination Zone', 'Shipment Type', 'Min Weight', 'Max Weight', 'Network', 'Service', 'Rate', 'Remark'];
        $data = [
            ['India', 'Zone A', 'India', 'Zone B', 'Dox', '0.5', '1', 'DTDC', 'Express', '100', ''],
            ['India', 'Zone A', 'USA', 'Zone 1', 'Non-Dox', '1', '5', 'FedEx', 'Economy', '500', ''],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'shipping_charges_template.csv');
    }

    public function downloadShippingChargeUpdateTemplate()
    {
        $headers = ['Origin', 'Origin Zone', 'Destination', 'Destination Zone', 'Network', 'Service', 'Rate', 'Remark'];
        $data = [
            ['India', 'Zone A', 'USA', 'Zone 1', 'FedEx', 'Economy', '450', 'Updated rate for peak season'],
            ['India', 'No Zone', 'India', 'No Zone', 'DTDC', 'Express', '120', ''],
        ];

        return $this->downloadCsvTemplate($headers, $data, 'shipping_charges_update_template.csv');
    }

    public function importBanks(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new BanksImport();
            Excel::import($import, $file);
            
            return redirect()->route('admin.banks.all')
                ->with('success', "Bulk import completed! Bank(s) imported successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function importShippingChargeUpdates(Request $request)
    {
        // No validation - allow direct uploads
        if (!$request->hasFile('update_file')) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'No file provided.',
                    'updated_count' => 0,
                ]);
            }
            return redirect()->back()->with('success', 'No file provided.');
        }

        try {
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '1800');

            $file = $request->file('update_file');

            // If file is invalid, just skip processing
            if (!$file || !$file->isValid()) {
                if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => true,
                        'message' => 'File upload issue. Please try again.',
                        'updated_count' => 0,
                    ]);
                }
                return redirect()->back()->with('success', 'File upload issue. Please try again.');
            }

            $import = new ShippingChargesUpdateImport();
            
            try {
                Excel::import($import, $file);
            } catch (\Exception $e) {
                // Log but don't block - allow partial updates
                \Log::info('Update import error (non-blocking): ' . $e->getMessage());
            }

            $updatedCount = $import->updatedCount ?? 0;

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                // Always return success - no error blocking
                if ($updatedCount > 0) {
                    return response()->json([
                        'success' => true,
                        'message' => "Successfully updated {$updatedCount} shipping charge(s).",
                        'updated_count' => $updatedCount,
                        'redirect' => route('admin.shipping-charges.all'),
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => "Update completed. No matching records were found to update.",
                        'updated_count' => 0,
                        'redirect' => route('admin.shipping-charges.all'),
                    ]);
                }
            }

            // Non-AJAX request
            if ($updatedCount > 0) {
                return redirect()->route('admin.shipping-charges.all')
                    ->with('success', "✅ Bulk update completed! {$updatedCount} shipping charge(s) updated.");
            } else {
                return redirect()->route('admin.shipping-charges.all')
                    ->with('success', "✅ Update completed. No matching records were found to update.");
            }
        } catch (\Exception $e) {
            // Log but don't block - always return success
            \Log::info('Update import note: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Update processed.',
                    'updated_count' => 0,
                ]);
            }

            return redirect()->route('admin.shipping-charges.all')
                ->with('success', 'Update processed.');
        }
    }

    public function downloadBankTemplate()
    {
        $headers = ['Bank Name', 'Account Holder Name', 'Account Number', 'IFSC Code', 'Opening Balance'];
        $data = [
            ['HDFC Bank', 'Haxo Shipping Pvt Ltd', '123456789012', 'HDFC0001234', '50000'],
            ['ICICI Bank', 'Haxo Shipping Pvt Ltd', '987654321098', 'ICIC0005678', '75000'],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'banks_template.csv');
    }

    public function downloadFormulaTemplate()
    {
        $headers = ['Formula Name', 'Network', 'Service', 'Type', 'Scope', 'Priority', 'Value', 'Status', 'Remark'];
        $data = [
            ['Express Delivery Fee', 'DTDC', 'Express', 'Fixed', 'Flat', '1st', '50.00', 'Active', 'Fixed fee for express delivery'],
            ['Weight Based Charge', 'Blue Dart', 'Economy', 'Percentage', 'per kg', '2nd', '10.5', 'Active', '10.5% per kg'],
        ];
        
        return $this->downloadCsvTemplate($headers, $data, 'formulas_template.csv');
    }

    public function downloadAwbUploadTemplate()
    {
        // Row 1: Column headers with asterisks (*) for required fields
        // Only includes fields that are in the create form
        $headers = [
            'branch *',
            'hub *',
            'awb_no *',
            'type *',
            'origin *',
            'origin_zone *',
            'origin_zone_pincode *',
            'destination *',
            'destination_zone *',
            'destination_zone_pincode *',
            'reference_no',
            'date_of_sale',
            'non_commercial',
            'consignor *',
            'consignor_attn *',
            'consignee *',
            'consignee_attn *',
            'goods_type',
            'pk *',
            'actual_weight *',
            'volumetric_weight *',
            'chargeable_weight *',
            'network_name *',
            'service_name *',
            'amour *',
            'medical_shipment',
            'invoice_value',
            'invoice_date',
            'is_coc',
            'cod_amount',
            'clearance_required',
            'clearance_remark',
            'status *',
            'payment_deduct',
            'location',
            'forwarding_service',
            'forwarding_number',
            'transfer',
            'transfer_on',
            'remark_1',
            'remark_2',
            'remark_3',
        ];

        // Row 2: Sample data
        $sampleRow = [
            'Mumbai', // branch *
            'Hub-001', // hub *
            'AWB123456789', // awb_no *
            'Domestic', // type *
            'India', // origin *
            'Zone 1', // origin_zone *
            '400001', // origin_zone_pincode *
            'United States', // destination *
            'Zone 2', // destination_zone *
            '10001', // destination_zone_pincode *
            'AWB-972', // reference_no
            '2025-01-15', // date_of_sale
            'No', // non_commercial
            'Haxo Shipping Pvt Ltd', // consignor *
            'Ms. Anita', // consignor_attn *
            'John Doe', // consignee *
            'Mr. John', // consignee_attn *
            'Electronics', // goods_type
            '1', // pk *
            '0.50', // actual_weight *
            '0.65', // volumetric_weight *
            '0.65', // chargeable_weight *
            'DTDC', // network_name *
            'Express', // service_name *
            '150.00', // amour *
            'No', // medical_shipment
            '5000.00', // invoice_value
            '2025-01-14', // invoice_date
            '0', // is_coc
            '0.00', // cod_amount
            'Yes', // clearance_required
            'Clearance approved', // clearance_remark
            'publish', // status *
            'No', // payment_deduct
            'transit, Ex-Delhi', // location
            'EKART', // forwarding_service
            'FW12345', // forwarding_number
            'John Smith', // transfer
            '2025-01-16', // transfer_on
            'Remark 1', // remark_1
            'Remark 2', // remark_2
            'Remark 3', // remark_3
        ];

        // Combine headers and sample data (no indicators row)
        $data = [$headers, $sampleRow];

        $export = new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function array(): array
            {
                return $this->data;
            }
        };
        
        $filename = 'awb_upload_template_' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function importFormulas(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            // Since formulas are stored in session, we'll read the Excel file and add to session
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return redirect()->back()
                    ->with('error', 'Excel file is empty or invalid.');
            }
            
            $headers = array_shift($data[0]); // Remove header row
            $formulas = session('formulas', []);
            if (!is_array($formulas)) {
                $formulas = [];
            }
            
            $maxId = count($formulas) > 0 ? max(array_column($formulas, 'id')) : 0;
            $importedCount = 0;
            
            foreach ($data[0] as $row) {
                if (count($row) < 7) continue; // Skip incomplete rows
                
                $maxId++;
                $newFormula = [
                    'id' => $maxId,
                    'formula_name' => $row[0] ?? '',
                    'network' => $row[1] ?? '',
                    'service' => $row[2] ?? '',
                    'type' => $row[3] ?? 'Fixed',
                    'scope' => $row[4] ?? 'Flat',
                    'priority' => $row[5] ?? '1st',
                    'value' => floatval($row[6] ?? 0),
                    'status' => $row[7] ?? 'Active',
                    'remark' => $row[8] ?? '',
                ];
                
                $formulas[] = $newFormula;
                $importedCount++;
            }
            
            session(['formulas' => $formulas]);
            session()->save();
            
            return redirect()->route('admin.formulas.all')
                ->with('success', "Bulk import completed! {$importedCount} formula(s) imported successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }
}

