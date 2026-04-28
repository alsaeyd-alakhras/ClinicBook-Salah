<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تقرير الحجوزات</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h2 { margin: 0 0 8px; }
        .meta { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>تقرير الحجوزات</h2>
    <div class="meta">
        <div>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
        <div>
            الفلاتر:
            من {{ $filters['from_date'] ?: '-' }}
            إلى {{ $filters['to_date'] ?: '-' }}
            | الاسم: {{ $filters['patient_name'] ?: '-' }}
            | نوع الكشفية: {{ ($filters['visit_type'] ?? '') === 'strabismus' ? 'حول' : (($filters['visit_type'] ?? '') === 'other' ? 'أخرى' : 'الكل') }}
            | الحالة: {{ $filters['status'] ?: 'الكل' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>تاريخ العيادة</th>
                <th>تسلسل</th>
                <th>الاسم</th>
                <th>الهوية</th>
                <th>الجوال</th>
                <th>العمر</th>
                <th>نوع الكشفية</th>
                <th>الحالة</th>
                <th>وقت التسجيل</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($row->booking_date)->format('Y-m-d') }}</td>
                    <td>{{ $row->serial_number }}</td>
                    <td>{{ $row->patient_name }}</td>
                    <td>{{ $row->national_id }}</td>
                    <td>{{ $row->phone }}</td>
                    <td>{{ $row->age }}</td>
                    <td>{{ $row->visit_type === 'strabismus' ? 'حول' : 'أخرى' }}</td>
                    <td>{{ $row->status === 'ticket_received' ? 'تم الاستلام' : 'قيد الانتظار' }}</td>
                    <td>{{ optional($row->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">لا توجد بيانات مطابقة للفلاتر.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
