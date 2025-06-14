<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class UsersExport implements
    WithHeadings,
    FromCollection,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Phone',
            'Address',
            'Photo',
            'Status',
        ];
    }

    public function collection()
    {
        return User::select('name', 'email', 'phone', 'address', 'photo', 'status')
            ->orderBy('name')
            ->get();
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $this->formatPhoneNumber($user->phone) ?? '-',
            $user->address ?? '-',
            '=HYPERLINK("' . $user->photo . '","View Photo")' ?? '-',
            $user->status,
        ];
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strpos($phone, '62') === 0) {
            return '+' . substr($phone, 0, 2) . ' ' .
                substr($phone, 2, 3) . ' ' .
                substr($phone, 5, 4) . ' ' .
                substr($phone, 9);
        }

        return $phone;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:F1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4287f5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ],
            'E' => [
                'font' => [
                    'color' => ['rgb' => '0000FF'],
                    'underline' => true
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $event->sheet->setAutoFilter($event->sheet->calculateWorksheetDimension());
            },
        ];
    }
}
