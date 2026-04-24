<x-front-layout>
    @php
        $days = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];
    @endphp

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إعدادات العيادة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم العيادة</label>
                                <input type="text" class="form-control" name="clinic_name" value="{{ old('clinic_name', $settings['clinic_name']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الطاقة الافتراضية</label>
                                <input type="number" class="form-control" name="default_capacity" min="1" value="{{ old('default_capacity', $settings['default_capacity']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">شعار العيادة</label>
                                <input type="file" class="form-control" name="clinic_logo" accept="image/*">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">ساعة فتح الحجز</label>
                                <input type="number" class="form-control" name="booking_open_hour" min="0" max="23" value="{{ old('booking_open_hour', $settings['booking_open_hour']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ساعة إغلاق الحجز</label>
                                <input type="number" class="form-control" name="booking_close_hour" min="0" max="23" value="{{ old('booking_close_hour', $settings['booking_close_hour']) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block">أيام العيادة</label>
                                <div class="row g-2">
                                    @foreach($days as $value => $label)
                                        @php
                                            $selected = in_array($value, old('clinic_days', $settings['clinic_days']));
                                            $capacity = old('day_capacities.' . $value, optional($weeklyConfigs->get($value))->capacity ?? $settings['default_capacity']);
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="border rounded p-2">
                                                <label class="d-flex align-items-center gap-2 mb-2">
                                                    <input type="checkbox" name="clinic_days[]" value="{{ $value }}" {{ $selected ? 'checked' : '' }}>
                                                    <span>{{ $label }}</span>
                                                </label>
                                                <input type="number" class="form-control form-control-sm" name="day_capacities[{{ $value }}]" min="1" value="{{ $capacity }}" placeholder="سعة هذا اليوم">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">استثناء يوم محدد</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.settings.day-config') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">التاريخ</label>
                            <input type="date" class="form-control" name="specific_date" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">السعة</label>
                            <input type="number" class="form-control" name="capacity" min="1" placeholder="اختياري">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">رسالة الإغلاق</label>
                            <input type="text" class="form-control" name="close_message" maxlength="255" placeholder="تظهر عند إغلاق اليوم">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <label class="d-flex align-items-center gap-2">
                                <input type="checkbox" name="is_closed" value="1">
                                <span>إغلاق اليوم</span>
                            </label>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-success w-100" type="submit">حفظ</button>
                        </div>
                    </form>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>اليوم</th>
                                    <th>السعة</th>
                                    <th>مغلق؟</th>
                                    <th>رسالة الإغلاق</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exceptions as $item)
                                    <tr>
                                        <td>{{ optional($item->specific_date)->format('Y-m-d') }}</td>
                                        <td>{{ $days[$item->day_of_week] ?? '-' }}</td>
                                        <td>{{ $item->capacity }}</td>
                                        <td>{{ $item->is_closed ? 'نعم' : 'لا' }}</td>
                                        <td>{{ $item->close_message ?: '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('dashboard.settings.day-config') }}">
                                                @csrf
                                                <input type="hidden" name="delete_id" value="{{ $item->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا يوجد استثناءات حالياً.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-front-layout>
