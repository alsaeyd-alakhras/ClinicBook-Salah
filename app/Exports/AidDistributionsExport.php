<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AidDistributionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rows;

    private $rowNumber = 1;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function map($row): array
    {
        $aidMode = $row['aid_mode'] ?? '';
        $aidModeLabel = $aidMode === 'cash' ? 'نقدية' : ($aidMode === 'in_kind' ? 'عينية' : $aidMode);

        return [
            $this->rowNumber++,
            $row['distributed_at'] ?? '-',
            $row['primary_name'] ?? '-',
            $row['national_id'] ?? '-',
            $row['housing_location'] ?? '-',
            $row['family_members_count'] ?? '-',
            $row['marital_status'] ?? '-',
            $row['office_name'] ?? '-',
            $row['institution_name'] ?? '-',
            $row['project_name'] ?? '-',
            $aidModeLabel,
            $row['aid_value'] ?? '-',
            $row['quantity'] ?? '-',
            $row['mobile'] ?? '-',
            $row['creator_name'] ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'تاريخ المساعدة',
            'الاسم',
            'رقم الهوية',
            'مكان السكن',
            'عدد الأفراد',
            'الحالة الزوجية',
            'المكتب',
            'المؤسسة',
            'المشروع',
            'نوع المساعدة',
            'القيمة/الصنف',
            'الكمية',
            'الجوال',
            'مدخل العملية',
        ];
    }
}
