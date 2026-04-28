<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    private int $rowNumber = 1;

    public function __construct(private readonly Collection $rows) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function map($row): array
    {
        return [
            $this->rowNumber++,
            optional($row->booking_date)->format('Y-m-d') ?? '-',
            $row->serial_number,
            $row->patient_name,
            $row->national_id,
            $row->phone,
            $row->age,
            $this->visitTypeLabel($row->visit_type),
            $row->status === 'ticket_received' ? 'تم الاستلام' : 'قيد الانتظار',
            optional($row->created_at)->format('Y-m-d H:i') ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'تاريخ العيادة',
            'الرقم التسلسلي',
            'الاسم',
            'رقم الهوية',
            'رقم الجوال',
            'العمر',
            'نوع الكشفية',
            'الحالة',
            'وقت التسجيل',
        ];
    }

    private function visitTypeLabel(?string $visitType): string
    {
        return $visitType === 'strabismus' ? 'حول' : 'أخرى';
    }
}
