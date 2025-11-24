<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountriesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $errors = [];
    public $importedCount = 0;
    public $rowNumber = 1;
    public $validRows = [];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Get all existing countries from database for duplicate checking
        $existingCountries = Country::all();
        $existingCountryNames = $existingCountries->pluck('name')->map(function($name) {
            return strtolower(trim($name));
        })->toArray();

        // STEP 1: Validate ALL rows first (don't import anything yet)
        foreach ($collection as $row) {
            $this->rowNumber++;
            $rowErrors = [];
            
            $countryName = $this->getValue($row, ['country_name', 'name', 'country']);
            $countryName = $countryName ? trim($countryName) : '';
            
            if (empty($countryName)) {
                $rowErrors[] = "Country name is required";
            } else {
                // Check if country already exists in database (case-insensitive)
                $countryNameLower = strtolower($countryName);
                if (in_array($countryNameLower, $existingCountryNames)) {
                    // Skip duplicate countries silently (don't add to errors, just skip)
                    continue;
                }
            }

            $code = $this->getValue($row, ['country_code', 'code']);
            $code = $code ? trim($code) : '';
            
            if (empty($code)) {
                $rowErrors[] = "Country code is required";
            }

            $isdNo = $this->getValue($row, ['country_isd_no', 'isd_no', 'isd no']);
            $isdNo = $isdNo ? trim($isdNo) : '';
            
            if (empty($isdNo)) {
                $rowErrors[] = "ISD number is required";
            }

            $dialingCode = $this->getValue($row, ['country_dialing_code', 'dialing_code', 'dialing code']);
            $dialingCode = $dialingCode ? trim($dialingCode) : '';
            
            $status = $this->getStatus($this->getValue($row, ['status']));
            $remark = $this->getValue($row, ['remark', 'remarks']);
            $remark = $remark ? trim($remark) : '';

            // If there are errors in this row, add them to the errors list
            if (!empty($rowErrors)) {
                $countryNameDisplay = !empty($countryName) ? "'{$countryName}'" : 'Unknown';
                $this->errors[] = "Row {$this->rowNumber} (Country: {$countryNameDisplay}): " . implode(', ', $rowErrors);
            } else {
                // Row is valid, store it for import
                $this->validRows[] = [
                    'row_number' => $this->rowNumber,
                    'name' => $countryName,
                    'code' => $code,
                    'isd_no' => $isdNo,
                    'dialing_code' => $dialingCode,
                    'status' => $status,
                    'remark' => $remark,
                ];
                // Add to existing names to prevent duplicates within the same import
                $existingCountryNames[] = strtolower($countryName);
            }
        }

        // STEP 2: Only import if there are NO errors (all-or-nothing approach)
        // If any errors were found in STEP 1, skip import entirely
        // This ensures errors are shown first, and import only happens when all data is valid
        if (empty($this->errors)) {
            // Use database transaction to ensure all-or-nothing import
            DB::beginTransaction();
            try {
                foreach ($this->validRows as $validRow) {
                    // Check again in database to avoid race conditions
                    $exists = Country::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validRow['name']))])->exists();
                    
                    if (!$exists) {
                        Country::create([
                            'name' => $validRow['name'],
                            'code' => $validRow['code'],
                            'isd_no' => $validRow['isd_no'],
                            'dialing_code' => $validRow['dialing_code'],
                            'status' => $validRow['status'],
                            'remark' => $validRow['remark'],
                        ]);
                        $this->importedCount++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Database error during import: " . $e->getMessage();
            }
        }
        // If errors exist, import is skipped and errors will be displayed to user
    }

    private function getValue($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
            foreach ($row as $rowKey => $value) {
                if (strcasecmp(trim($rowKey), trim($key)) === 0) {
                    return $value;
                }
            }
        }
        return null;
    }

    private function getStatus($value)
    {
        if (empty($value)) return 'Active';
        $status = strtolower(trim($value));
        if (in_array($status, ['active', '1', 'yes', 'true'])) {
            return 'Active';
        }
        return 'Inactive';
    }
}

