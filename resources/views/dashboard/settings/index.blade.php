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
                                <input type="number" class="form-control js-total-capacity" name="default_capacity" min="1" value="{{ old('default_capacity', $settings['default_capacity']) }}" required data-group="default">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">حالات الحول الافتراضية</label>
                                <input type="number" class="form-control js-strabismus-capacity" name="default_strabismus_capacity" min="0" value="{{ old('default_strabismus_capacity', $settings['default_strabismus_capacity']) }}" required data-group="default">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">حالات الأخرى الافتراضية</label>
                                <input type="number" class="form-control js-other-capacity" name="default_other_capacity" min="0" value="{{ old('default_other_capacity', $settings['default_other_capacity']) }}" required data-group="default">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">شعار العيادة</label>
                                <input type="file" class="form-control" name="clinic_logo" accept="image/*">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">ساعة إغلاق الحجز</label>
                                <input type="number" class="form-control" name="booking_close_hour" min="0" max="23" value="{{ old('booking_close_hour', $settings['booking_close_hour']) }}" required>
                                <small class="text-muted">يفتح الحجز تلقائياً قبل هذه الساعة بـ 24 ساعة.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block">أيام العيادة</label>
                                <div class="row g-2">
                                    @foreach($days as $value => $label)
                                        @php
                                            $selected = in_array($value, old('clinic_days', $settings['clinic_days']));
                                            $capacity = old('day_capacities.' . $value, optional($weeklyConfigs->get($value))->capacity ?? $settings['default_capacity']);
                                            $strabismusCapacity = old('day_strabismus_capacities.' . $value, optional($weeklyConfigs->get($value))->strabismus_capacity ?? $settings['default_strabismus_capacity']);
                                            $otherCapacity = old('day_other_capacities.' . $value, optional($weeklyConfigs->get($value))->other_capacity ?? max(0, $capacity - $strabismusCapacity));
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="border rounded p-2">
                                                <label class="d-flex align-items-center gap-2 mb-2">
                                                    <input type="checkbox" name="clinic_days[]" value="{{ $value }}" {{ $selected ? 'checked' : '' }}>
                                                    <span>{{ $label }}</span>
                                                </label>
                                                <label class="form-label small text-muted mb-1">إجمالي اليوم</label>
                                                <input type="number" class="form-control form-control-sm js-total-capacity mb-1" name="day_capacities[{{ $value }}]" min="1" value="{{ $capacity }}" placeholder="إجمالي اليوم" data-group="day-{{ $value }}">
                                                <div class="row g-1">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-1">حالات الحول</label>
                                                        <input type="number" class="form-control form-control-sm js-strabismus-capacity" name="day_strabismus_capacities[{{ $value }}]" min="0" value="{{ $strabismusCapacity }}" placeholder="حول" data-group="day-{{ $value }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-1">حالات أخرى</label>
                                                        <input type="number" class="form-control form-control-sm js-other-capacity" name="day_other_capacities[{{ $value }}]" min="0" value="{{ $otherCapacity }}" placeholder="أخرى" data-group="day-{{ $value }}">
                                                    </div>
                                                </div>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">استثناء يوم محدد</h5>
                        <button type="button" class="btn btn-sm btn-success js-add-day-config" data-bs-toggle="modal" data-bs-target="#dayConfigModal">إضافة يوم جديد</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                     <th>اليوم</th>
                                    <th>السعات</th>
                                    <th>الإغلاق</th>
                                    <th>رسالة الإغلاق</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exceptions as $item)
                                    <tr>
                                        <td>{{ optional($item->specific_date)->format('Y-m-d') }}</td>
                                        <td>{{ $days[$item->day_of_week] ?? '-' }}</td>
                                        <td>الإجمالي: {{ $item->capacity }} | حول: {{ $item->strabismus_capacity ?? 0 }} | أخرى: {{ $item->other_capacity ?? max(0, $item->capacity - ($item->strabismus_capacity ?? 0)) }}</td>
                                        <td>
                                            {{ $item->is_closed ? 'اليوم كامل' : 'لا' }}
                                            @if($item->is_strabismus_closed) | حول @endif
                                            @if($item->is_other_closed) | أخرى @endif
                                        </td>
                                        <td>
                                            {{ $item->close_message ?: '-' }}
                                            @if($item->strabismus_close_message) <div>حول: {{ $item->strabismus_close_message }}</div> @endif
                                            @if($item->other_close_message) <div>أخرى: {{ $item->other_close_message }}</div> @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary js-edit-day-config"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#dayConfigModal"
                                                    data-specific-date="{{ optional($item->specific_date)->format('Y-m-d') }}"
                                                    data-capacity="{{ $item->capacity }}"
                                                    data-strabismus-capacity="{{ $item->strabismus_capacity ?? 0 }}"
                                                    data-other-capacity="{{ $item->other_capacity ?? max(0, $item->capacity - ($item->strabismus_capacity ?? 0)) }}"
                                                    data-is-closed="{{ $item->is_closed ? 1 : 0 }}"
                                                    data-is-strabismus-closed="{{ $item->is_strabismus_closed ? 1 : 0 }}"
                                                    data-is-other-closed="{{ $item->is_other_closed ? 1 : 0 }}"
                                                    data-close-message="{{ e($item->close_message) }}"
                                                    data-strabismus-close-message="{{ e($item->strabismus_close_message) }}"
                                                    data-other-close-message="{{ e($item->other_close_message) }}"
                                                >تعديل</button>
                                                <form method="POST" action="{{ route('dashboard.settings.day-config') }}">
                                                @csrf
                                                <input type="hidden" name="delete_id" value="{{ $item->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                </form>
                                            </div>
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

    <div class="modal fade" id="dayConfigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('dashboard.settings.day-config') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="dayConfigModalTitle">إضافة تخصيص يوم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">التاريخ</label>
                                <input type="date" class="form-control" name="specific_date" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">إجمالي اليوم</label>
                                <input type="number" class="form-control js-total-capacity" name="capacity" min="1" value="{{ $settings['default_capacity'] }}" data-group="exception" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">رسالة إغلاق اليوم الكامل</label>
                                <input type="text" class="form-control" name="close_message" maxlength="255" placeholder="اختياري">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">عدد حالات الحول</label>
                                <input type="number" class="form-control js-strabismus-capacity" name="strabismus_capacity" min="0" value="{{ $settings['default_strabismus_capacity'] }}" data-group="exception" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عدد حالات الأخرى</label>
                                <input type="number" class="form-control js-other-capacity" name="other_capacity" min="0" value="{{ $settings['default_other_capacity'] }}" data-group="exception" required>
                            </div>

                            <div class="col-md-4">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="checkbox" name="is_closed" value="1">
                                    <span>إغلاق اليوم بالكامل</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="checkbox" name="is_strabismus_closed" value="1">
                                    <span>إغلاق حالات الحول</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="checkbox" name="is_other_closed" value="1">
                                    <span>إغلاق حالات الأخرى</span>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">رسالة إغلاق الحول</label>
                                <input type="text" class="form-control" name="strabismus_close_message" maxlength="255" placeholder="اعتذر الطبيب عن استقبال حالات الحول لهذا اليوم">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رسالة إغلاق الأخرى</label>
                                <input type="text" class="form-control" name="other_close_message" maxlength="255" placeholder="اعتذر الطبيب عن استقبال حالات الأخرى لهذا اليوم">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">حفظ التخصيص</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                function syncCapacities(group, changedType) {
                    const $total = $(`.js-total-capacity[data-group="${group}"]`);
                    const $strabismus = $(`.js-strabismus-capacity[data-group="${group}"]`);
                    const $other = $(`.js-other-capacity[data-group="${group}"]`);
                    const total = Math.max(0, parseInt($total.val(), 10) || 0);
                    const strabismus = Math.max(0, parseInt($strabismus.val(), 10) || 0);
                    const other = Math.max(0, parseInt($other.val(), 10) || 0);

                    if (changedType === 'other') {
                        $strabismus.val(Math.max(0, total - other));
                        return;
                    }

                    $other.val(Math.max(0, total - strabismus));
                }

                $(document).on('input', '.js-total-capacity, .js-strabismus-capacity', function () {
                    syncCapacities($(this).data('group'), 'strabismus');
                });

                $(document).on('input', '.js-other-capacity', function () {
                    syncCapacities($(this).data('group'), 'other');
                });

                function setDayConfigModal(data) {
                    const $modal = $('#dayConfigModal');

                    $('#dayConfigModalTitle').text(data.title);
                    $modal.find('[name="specific_date"]').val(data.specific_date || '');
                    $modal.find('[name="capacity"]').val(data.capacity || '{{ $settings['default_capacity'] }}');
                    $modal.find('[name="strabismus_capacity"]').val(data.strabismus_capacity || '{{ $settings['default_strabismus_capacity'] }}');
                    $modal.find('[name="other_capacity"]').val(data.other_capacity || '{{ $settings['default_other_capacity'] }}');
                    $modal.find('[name="close_message"]').val(data.close_message || '');
                    $modal.find('[name="strabismus_close_message"]').val(data.strabismus_close_message || '');
                    $modal.find('[name="other_close_message"]').val(data.other_close_message || '');
                    $modal.find('[name="is_closed"]').prop('checked', data.is_closed === '1');
                    $modal.find('[name="is_strabismus_closed"]').prop('checked', data.is_strabismus_closed === '1');
                    $modal.find('[name="is_other_closed"]').prop('checked', data.is_other_closed === '1');
                }

                $(document).on('click', '.js-add-day-config', function () {
                    setDayConfigModal({ title: 'إضافة تخصيص يوم' });
                });

                $(document).on('click', '.js-edit-day-config', function () {
                    const $button = $(this);
                    setDayConfigModal({
                        title: 'تعديل تخصيص يوم',
                        specific_date: $button.data('specific-date'),
                        capacity: $button.data('capacity'),
                        strabismus_capacity: $button.data('strabismus-capacity'),
                        other_capacity: $button.data('other-capacity'),
                        is_closed: String($button.data('is-closed')),
                        is_strabismus_closed: String($button.data('is-strabismus-closed')),
                        is_other_closed: String($button.data('is-other-closed')),
                        close_message: $button.data('close-message'),
                        strabismus_close_message: $button.data('strabismus-close-message'),
                        other_close_message: $button.data('other-close-message')
                    });
                });
            })();
        </script>
    @endpush
</x-front-layout>
