<x-front-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/datatable/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/dataTables.bootstrap4.css') }}">
        <style>
            .bookings-head { margin-bottom: 14px; }
            .bookings-head h4 { margin: 0; font-weight: 700; }
            .bookings-head p { margin: 4px 0 0; color: #6b7280; }
            .name-col { text-align: right !important; }
            .filter-card {
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 14px;
                margin-bottom: 14px;
                background: linear-gradient(180deg, #ffffff 0%, #fafbfd 100%);
            }
            .bookings-table-card {
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
            }
            .status-pill { padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
            .status-pill.pending { background: #fef3c7; color: #8a5a00; }
            .status-pill.ticket_received { background: #dcfce7; color: #15603a; }

            #bookingsTable {
                border-collapse: separate;
                border-spacing: 0;
                margin-top: 0 !important;
            }
            #bookingsTable thead th {
                background: #f8fafc;
                color: #334155;
                font-weight: 700;
                border-bottom: 1px solid #e2e8f0;
                white-space: nowrap;
            }
            #bookingsTable tbody td {
                border-bottom: 1px solid #eef2f7;
                vertical-align: middle;
            }
            #bookingsTable tbody tr:hover {
                background: #f8fbff;
            }

            .bookings-ltr {
                direction: ltr;
                unicode-bidi: plaintext;
                display: inline-block;
                min-width: 78px;
                text-align: center;
            }

            .confirm-btn {
                border-radius: 10px;
                padding: 6px 10px;
                font-size: 12px;
                font-weight: 600;
            }

            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter,
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                margin-top: 10px;
            }
            div.dataTables_wrapper div.dataTables_length label,
            div.dataTables_wrapper div.dataTables_filter label {
                color: #475569;
                font-weight: 600;
                font-size: 13px;
            }
            div.dataTables_wrapper div.dataTables_length select {
                border: 1px solid #cfd8e3;
                border-radius: 8px;
                min-width: 66px;
                padding: 3px 8px;
            }
            .dataTables_info {
                color: #64748b !important;
                font-size: 13px;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                border-radius: 8px !important;
                border: 1px solid #d7e0ea !important;
                background: #fff !important;
                color: #334155 !important;
                padding: 5px 10px !important;
                margin-inline: 2px;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                border-color: #2563eb !important;
                background: #eff6ff !important;
                color: #1d4ed8 !important;
                font-weight: 700;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                border-color: #93c5fd !important;
                background: #f0f7ff !important;
                color: #1e3a8a !important;
            }
        </style>
    @endpush

    <x-slot:extra_nav>
        <div class="mx-1 nav-item">
            <button id="exportExcelBtn" class="btn btn-sm btn-success">تصدير Excel</button>
        </div>
        <div class="mx-1 nav-item">
            <button id="exportPdfBtn" class="btn btn-sm btn-danger">تصدير PDF</button>
        </div>
    </x-slot:extra_nav>

    <div class="bookings-head">
        <h4>سجلات الحجوزات</h4>
        <p>عرض الحجوزات اليومية مع الفلترة والتصدير حسب النتائج الحالية.</p>
    </div>

    <div class="filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">بحث بالاسم</label>
                <input type="text" id="patient_name" class="form-control" placeholder="اكتب الاسم...">
            </div>
            <div class="col-md-2">
                <label class="form-label">من تاريخ</label>
                <input type="date" id="from_date" class="form-control" value="{{ $defaultDate }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" id="to_date" class="form-control" value="{{ $defaultDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">نوع الكشفية</label>
                <select id="visit_type" class="form-control">
                    <option value="">الكل</option>
                    <option value="strabismus">حول</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select id="status" class="form-control">
                    <option value="">الكل</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="ticket_received">تم الاستلام</option>
                </select>
            </div>
        </div>
        <div class="row justify-content-between g-2 mt-1">
            <div class="col-md-2 d-grid">
                <button id="resetBtn" class="btn btn-outline-secondary">إعادة تعيين</button>
            </div>
            <div class="col-md-1 d-grid">
                <button id="filterBtn" class="btn btn-primary">بحث</button>
            </div>
        </div>
    </div>

    <div class="card bookings-table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="bookingsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>تاريخ العيادة</th>
                            <th>تسلسل اليوم</th>
                            <th>الهوية</th>
                            <th>الجوال</th>
                            <th>العمر</th>
                            <th>نوع الكشفية</th>
                            <th>وقت التسجيل</th>
                            <th>الحالة</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/plugins/jquery.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/jquery.dataTables.min.js') }}"></script>
        <script>
            const table = $('#bookingsTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                order: [],
                pageLength: 70,
                lengthMenu: [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'الكل']],
                language: {
                    url: "{{ asset('files/Arabic.json') }}"
                },
                ajax: {
                    url: "{{ route('dashboard.bookings.index') }}",
                    data: function (d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.patient_name = $('#patient_name').val();
                        d.status = $('#status').val();
                        d.visit_type = $('#visit_type').val();
                    },
                    dataSrc: function (json) {
                        if (json.resolved_date) {
                            $('#from_date').val(json.resolved_date);
                            $('#to_date').val(json.resolved_date);
                        }

                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'patient_name' },
                    { data: 'booking_date' },
                    { data: 'serial_number' },
                    { data: 'national_id' },
                    { data: 'phone' },
                    { data: 'age' },
                    { data: 'visit_type_label' },
                    { data: 'created_at' },
                    {
                        data: 'status',
                        render: function (data, type, row) {
                            const text = row.status_label;
                            return `<span class="status-pill ${data}">${text}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (id, type, row) {
                            if (row.status === 'ticket_received') {
                                return '<span class="text-success">تم</span>';
                            }

                            return `<button class="btn btn-sm btn-outline-primary confirm-btn" data-id="${id}">تم استلام التذكرة</button>`;
                        }
                    }
                ],
                columnDefs: [
                    { targets: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10], className: 'text-center' },
                    { targets: [1], className: 'name-col' },
                    { targets: [2, 4, 5, 8], render: function (data, type) { return type === 'display' ? `<span class="bookings-ltr">${data ?? '-'}</span>` : data; } }
                ]
            });

            $('#from_date').on('change', function () {
                const fromVal = $(this).val();
                const toVal = $('#to_date').val();

                if (fromVal && toVal && toVal < fromVal) {
                    $('#to_date').val(fromVal);
                }
            });

            $('#to_date').on('change', function () {
                const fromVal = $('#from_date').val();
                const toVal = $(this).val();

                if (fromVal && toVal && toVal < fromVal) {
                    $(this).val(fromVal);
                }
            });

            $('#filterBtn').on('click', function () {
                table.ajax.reload();
            });

            $('#patient_name, #from_date, #to_date, #visit_type, #status').on('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    table.ajax.reload();
                }
            });

            $('#resetBtn').on('click', function () {
                $('#from_date').val('');
                $('#to_date').val('');
                $('#patient_name').val('');
                $('#status').val('');
                $('#visit_type').val('');
                table.ajax.reload();
            });

            $(document).on('click', '.confirm-btn', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ route('dashboard.bookings.confirm', ':id') }}`.replace(':id', id),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' }
                }).done(function () {
                    table.ajax.reload(null, false);
                });
            });

            function buildExportUrl(type) {
                const params = new URLSearchParams({
                    type,
                    from_date: $('#from_date').val() || '',
                    to_date: $('#to_date').val() || '',
                    patient_name: $('#patient_name').val() || '',
                    status: $('#status').val() || '',
                    visit_type: $('#visit_type').val() || ''
                });

                return `{{ route('dashboard.bookings.export') }}?${params.toString()}`;
            }

            $('#exportExcelBtn').on('click', function () {
                window.open(buildExportUrl('excel'), '_blank');
            });

            $('#exportPdfBtn').on('click', function () {
                window.open(buildExportUrl('pdf'), '_blank');
            });
        </script>
    @endpush
</x-front-layout>
