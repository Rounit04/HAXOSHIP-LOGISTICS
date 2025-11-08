<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FormulasTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Sample Formula 1',
                'DTDC',
                'Express',
                'Fixed',
                'per kg',
                '1st',
                '10.50',
                'Active',
                'Sample remark',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Formula Name',
            'Network',
            'Service',
            'Type',
            'Scope',
            'Priority',
            'Value',
            'Status',
            'Remark',
        ];
    }
}


