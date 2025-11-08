<?php

namespace App\Http\Controllers;

use App\Imports\NetworksImport;
use App\Imports\ServicesImport;
use App\Imports\CountriesImport;
use App\Imports\ZonesImport;
use App\Imports\BookingCategoriesImport;
use App\Imports\ShippingChargesImport;
use App\Imports\BanksImport;
use App\Models\Network;
use App\Models\BookingCategory;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

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
            
            return redirect()->route('admin.services.all')
                ->with('success', "Bulk import completed! Service(s) imported successfully.");
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
            
            return redirect()->route('admin.countries.all')
                ->with('success', "Bulk import completed! Country(ies) imported successfully.");
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
            
            return redirect()->route('admin.zones.all')
                ->with('success', "Bulk import completed! Zone(s) imported successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function downloadNetworkTemplate()
    {
        $headers = ['Network Name', 'Network Type', 'Opening Balance', 'Status', 'Bank Details', 'Remark'];
        $data = [
            ['DTDC', 'Domestic', '10000', 'Active', 'Bank details here', 'Sample network'],
            ['FedEx', 'International', '5000', 'Active', '', 'International network'],
        ];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'networks_template.xlsx');
    }

    public function downloadServiceTemplate()
    {
        $headers = ['Service Name', 'Network', 'Transit Time', 'Items Allowed', 'Status', 'Remark', 'Display Title', 'Description', 'Icon Type', 'Is Highlighted'];
        $data = [['Express', 'DTDC', '24-48 Hours', 'Documents, Small Packages', 'Active', 'Fast delivery', 'Express Delivery', 'Fast and reliable', 'truck', 'No']];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'services_template.xlsx');
    }

    public function downloadCountryTemplate()
    {
        $headers = ['Country Name', 'Country Code', 'Country ISD No', 'Country Dialing Code', 'Status', 'Remark'];
        $data = [
            ['India', 'IN', '+91', '91', 'Active', ''],
            ['USA', 'US', '+1', '1', 'Active', ''],
        ];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'countries_template.xlsx');
    }

    public function downloadZoneTemplate()
    {
        $headers = ['Pincode', 'Country', 'Zone', 'Network', 'Service', 'Status', 'Remark'];
        $data = [
            ['110001', 'India', 'Zone A', 'DTDC', 'Express', 'Active', ''],
            ['400001', 'India', 'Zone B', 'FedEx', 'Economy', 'Active', ''],
        ];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'zones_template.xlsx');
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
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'booking_categories_template.xlsx');
    }

    public function importShippingCharges(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new ShippingChargesImport();
            Excel::import($import, $file);
            
            return redirect()->route('admin.shipping-charges.all')
                ->with('success', "Bulk import completed! Shipping charge(s) imported successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }

    public function downloadShippingChargeTemplate()
    {
        $headers = ['Origin', 'Origin Zone', 'Destination', 'Destination Zone', 'Shipment Type', 'Min Weight', 'Max Weight', 'Network', 'Service', 'Rate', 'Remark'];
        $data = [
            ['India', 'Zone A', 'India', 'Zone B', 'Dox', '0.5', '1', 'DTDC', 'Express', '100', ''],
            ['India', 'Zone A', 'USA', 'Zone 1', 'Non-Dox', '1', '5', 'FedEx', 'Economy', '500', ''],
        ];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'shipping_charges_template.xlsx');
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

    public function downloadBankTemplate()
    {
        $headers = ['Bank Name', 'Account Holder Name', 'Account Number', 'IFSC Code', 'Opening Balance'];
        $data = [
            ['HDFC Bank', 'Haxo Shipping Pvt Ltd', '123456789012', 'HDFC0001234', '50000'],
            ['ICICI Bank', 'Haxo Shipping Pvt Ltd', '987654321098', 'ICIC0005678', '75000'],
        ];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'banks_template.xlsx');
    }

    public function downloadFormulaTemplate()
    {
        $headers = ['Formula Name', 'Network', 'Service', 'Type', 'Scope', 'Priority', 'Value', 'Status', 'Remark'];
        $data = [
            ['Express Delivery Fee', 'DTDC', 'Express', 'Fixed', 'Flat', '1st', '50.00', 'Active', 'Fixed fee for express delivery'],
            ['Weight Based Charge', 'Blue Dart', 'Economy', 'Percentage', 'per kg', '2nd', '10.5', 'Active', '10.5% per kg'],
        ];
        
        return Excel::download(new class($data, $headers) implements FromArray, WithHeadings {
            protected $data; protected $headers;
            public function __construct($data, $headers) { $this->data = $data; $this->headers = $headers; }
            public function array(): array { return $this->data; }
            public function headings(): array { return $this->headers; }
        }, 'formulas_template.xlsx');
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

