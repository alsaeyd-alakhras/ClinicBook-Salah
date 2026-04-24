<x-front-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/datatable/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/dataTables.dataTables.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.css') }}">

        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom2/stickyTable.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom2/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom2/datatableIndex.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom2/datatableIndex2.css') }}">
        <style>
            :root {
                --sticky-col1-width: 60px;
                --sticky-col2-width: 90px;
                --sticky-col3-width: 110px;
                --sticky-col4-width: 130px;
                --sticky-col2-right: var(--sticky-col1-width);
                --sticky-col3-right: calc(var(--sticky-col1-width) + var(--sticky-col2-width));
                --sticky-col4-right: calc(var(--sticky-col1-width) + var(--sticky-col2-width) + var(--sticky-col3-width));
            }

            th.enhanced-sticky:nth-child(1), td.enhanced-sticky:nth-child(1) {
                right: 0;
                width: var(--sticky-col1-width);
                min-width: var(--sticky-col1-width);
            }

            th.enhanced-sticky:nth-child(2), td.enhanced-sticky:nth-child(2) {
                right: var(--sticky-col2-right);
                width: var(--sticky-col2-width);
                min-width: var(--sticky-col2-width);
            }

            th.enhanced-sticky:nth-child(3), td.enhanced-sticky:nth-child(3) {
                right: var(--sticky-col3-right);
                width: var(--sticky-col3-width);
                min-width: var(--sticky-col3-width);
            }

            th.enhanced-sticky:nth-child(4), td.enhanced-sticky:nth-child(4) {
                right: var(--sticky-col4-right);
                width: var(--sticky-col4-width);
                min-width: var(--sticky-col4-width);
            }
        </style>
    @endpush

    <x-slot:extra_nav>
        <div class="mx-2 my-0 nav-item form-group">
            <select name="year" id="year" class="form-control">
                @for ($year = date('Y'); $year >= 2025; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>
        <div class="nav-item">
            <select class="form-control" name="advanced-pagination" id="advanced-pagination">
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="500">500</option>
                <option value="-1">all</option>
            </select>
        </div>
        <div class="mx-2 nav-item">
            <button type="button" class="m-0 text-white btn btn-success" id="aid-excel-export" title="تصدير Excel">
                <i class="fa-solid fa-file-excel fe-16 me-1"></i> تصدير Excel
            </button>
        </div>
        @can('import', 'App\\Models\AidDistribution')
        <div class="mx-2 nav-item">
            <a href="{{ route('dashboard.import') }}" class="m-0 text-white btn btn-success">
                <i class="fa-solid fa-plus fe-16"></i> استيراد excel
            </a>
        </div>
        @endcan
        @can('create', 'App\\Models\AidDistribution')
        <div class="mx-2 nav-item">
            <a href="{{ route('dashboard.aid-distributions.create') }}" class="m-0 text-white btn btn-primary">
                <i class="fa-solid fa-plus fe-16"></i> اضافة
            </a>
        </div>
        @endcan
        <div class="mx-2 nav-item">
            <button class="p-2 border-0 btn btn-outline-danger rounded-pill me-n1 waves-effect waves-light d-none"
                type="button" id="filterBtnClear" title="إزالة التصفية">
                <i class="fa-solid fa-eraser fe-16"></i>
            </button>
        </div>
        <div class="mx-2 nav-item d-flex align-items-center justify-content-center">
            <button type="button" class="btn" id="refreshData">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </x-slot:extra_nav>

    @php
        $fields = [
            'edit' => 'تعديل',
            'distributed_at' => 'تاريخ المساعدة',
            'primary_name' => 'الاسم',
            'national_id' => 'رقم الهوية',
            'housing_location' => 'مكان السكن',
            'family_members_count' => 'عدد الأفراد',
            'marital_status' => 'الحالة الزوجية',
            'office_name' => 'المكتب',
            'institution_name' => 'المؤسسة',
            'project_name' => 'المشروع',
            'aid_mode' => 'نوع المساعدة',
            'aid_value' => 'القيمة/الصنف',
            'quantity' => 'الكمية',
            'mobile' => 'الجوال',
            'creator_name' => 'مدخل العملية',
        ];
    @endphp

    <div class="mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1">سجل المساعدات</h4>
            <p class="mb-0 text-muted">فلترة سريعة وتصدير مباشر حسب النتائج الظاهرة في الجدول.</p>
        </div>
    </div>

    <div class="shadow-lg enhanced-card">
        <div class="enhanced-card-body">
            <div class="col-12" style="padding: 0;">
                <div class="table-container">
                    <table id="aid-distributions-table"
                        class="table enhanced-sticky table-striped table-hover" style="display: table; width:100%; height: auto;">
                        <thead>
                            <tr>
                                <th class="text-center enhanced-sticky">#</th>
                                @foreach ($fields as $index => $label)
                                    <th class="{{ $loop->index < 3 ? 'enhanced-sticky' : '' }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>{{ $label }}</span>
                                            <div class="enhanced-filter-dropdown d-flex align-items-center gap-1">
                                                @if ($index != 'edit')
                                                <button class="btn-sort btn btn-sm border-0 p-1" type="button"
                                                    data-sort-field="{{ $index }}" title="فرز">
                                                    <i class="fas fa-sort text-muted"></i>
                                                </button>
                                                @endif
                                                <div class="dropdown">
                                                    <button class="enhanced-btn-filter btn-filter" type="button"
                                                        data-bs-toggle="dropdown"
                                                        id="btn-filter-{{ $loop->index + 1 }}">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <div class="dropdown-menu enhanced-filter-menu filterDropdownMenu"
                                                        aria-labelledby="{{ $index }}_filter">
                                                        @if ($index == 'distributed_at')
                                                            <div class="mb-3">
                                                                <label class="form-label text-muted small">من تاريخ:</label>
                                                                <input type="date"
                                                                    class="form-control form-control-sm" id="from_date"
                                                                    data-column="{{ $loop->index + 1 }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label text-muted small">إلى تاريخ:</label>
                                                                <input type="date"
                                                                    class="form-control form-control-sm" id="to_date"
                                                                    data-column="{{ $loop->index + 1 }}">
                                                            </div>
                                                            <div class="gap-2 d-flex">
                                                                <button class="enhanced-apply-btn flex-fill" id="filter-date-btn">
                                                                    <i class="fas fa-check me-1"></i> تطبيق
                                                                </button>
                                                                <button class="btn btn-outline-secondary btn-sm flex-fill" id="filter-date-clear-btn" type="button">
                                                                    <i class="fas fa-times me-1"></i> مسح
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                                <input type="search" class="form-control search-checkbox"
                                                                    placeholder="ابحث..." data-index="{{ $loop->index + 1 }}">
                                                                <button class="enhanced-apply-btn ms-2 filter-apply-btn-checkbox"
                                                                    data-target="{{ $loop->index + 1 }}"
                                                                    data-field="{{ $index }}">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </div>
                                                            <div class="enhanced-checkbox-list checkbox-list-box">
                                                                <label style="display: block;">
                                                                    <input type="checkbox" value="all"
                                                                        class="all-checkbox"
                                                                        data-index="{{ $loop->index + 1 }}"> الكل
                                                                </label>
                                                                <div class="checkbox-list checkbox-list-{{ $loop->index + 1 }}"></div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="enhanced-sticky">العمليات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.aid-distributions.export-excel') }}" target="_blank" id="aid-export-form" class="d-none">
        @csrf
        <input type="hidden" name="year" id="export-year">
        <input type="hidden" name="from_date" id="export-from_date">
        <input type="hidden" name="to_date" id="export-to_date">
        <input type="hidden" name="column_filters" id="export-column_filters">
        <input type="hidden" name="project_id" id="export-project_id">
        <input type="hidden" name="office_id" id="export-office_id">
        <input type="hidden" name="family_id" id="export-family_id">
    </form>

    <div class="modal fade delete-modal" id="deleteConfirmModal" tabindex="-1"
        aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        تأكيد الحذف
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="delete-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="delete-warning-text">هل أنت متأكد؟</div>
                    <p class="delete-sub-text">
                        لن تتمكن من التراجع عن هذا الإجراء بعد الحذف!
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> إلغاء
                    </button>
                    <button type="button" class="text-white btn btn-confirm-delete" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i> حذف نهائي
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/plugins/jquery.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/dataTables.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/dataTables.buttons.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/buttons.dataTables.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/jszip.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/pdfmake.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/vfs_fonts.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/buttons.print.min.js') }}"></script>
        <script src="{{ asset('js/plugins/jquery.validate.min.js') }}"></script>


        <script>
            $(document).ready(function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('from_date')) $('#from_date').val(urlParams.get('from_date'));
                if (urlParams.has('to_date')) $('#to_date').val(urlParams.get('to_date'));
            });
            const tableId = 'aid-distributions-table';
            const arabicFileJson = "{{ asset('files/Arabic.json') }}";
            const _token = "{{ csrf_token() }}";

            const urlIndex = `{{ route('dashboard.aid-distributions.index') }}`;
            const urlFilters = `{{ route('dashboard.aid-distributions.filters', ':column') }}`;
            const urlCreate = `{{ route('dashboard.aid-distributions.create') }}`;
            const urlStore = `{{ route('dashboard.aid-distributions.store') }}`;
            const urlEdit = `{{ route('dashboard.aid-distributions.edit', ':id') }}`;
            const urlUpdate = `{{ route('dashboard.aid-distributions.update', ':id') }}`;
            const urlDelete = `{{ route('dashboard.aid-distributions.destroy', ':id') }}`;

            const abilityCreate = "{{ Auth::user()->can('create', 'App\\Models\\AidDistribution') }}";
            const abilityEdit = "{{ Auth::user()->can('update', 'App\\Models\\AidDistribution') }}";
            const abilityDelete = "{{ Auth::user()->can('delete', 'App\\Models\\AidDistribution') }}";

            const fields = [
                '#',
                'edit',
                'distributed_at',
                'primary_name',
                'national_id',
                'housing_location',
                'family_members_count',
                'marital_status',
                'office_name',
                'institution_name',
                'project_name',
                'aid_mode',
                'aid_value',
                'quantity',
                'mobile',
                'creator_name',
                'delete'
            ];

            const columnsTable = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, class: 'enhanced-sticky text-center' },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    class: 'enhanced-sticky',
                    searchable: false,
                    render: function(data) {
                        if (!abilityEdit) {
                            return `<div class="d-flex align-items-center justify-content-evenly"></div>`;
                        }
                        return `
                            <div class="d-flex align-items-center justify-content-evenly">
                                <a href="${urlEdit.replace(':id', data)}" class="action-btn btn-edit" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        `;
                    }
                },
                { data: 'distributed_at', name: 'distributed_at', orderable: false, class: 'enhanced-sticky text-center' },
                {
                    data: 'primary_name',
                    name: 'primary_name',
                    orderable: false,
                    class: 'enhanced-sticky'
                },
                { data: 'national_id', name: 'national_id', orderable: false, class: 'text-center' },
                { data: 'housing_location', name: 'housing_location', orderable: false },
                { data: 'family_members_count', name: 'family_members_count', orderable: false, class: 'text-center' },
                { data: 'marital_status', name: 'marital_status', orderable: false, class: 'text-center' },
                { data: 'office_name', name: 'office_name', orderable: false },
                { data: 'institution_name', name: 'institution_name', orderable: false },
                { data: 'project_name', name: 'project_name', orderable: false },
                {
                    data: 'aid_mode',
                    name: 'aid_mode',
                    orderable: false,
                    render: function(data) {
                        return data === 'cash' ? 'نقدية' : 'عينية';
                    }
                },
                { data: 'aid_value', name: 'aid_value', orderable: false, class: 'text-center' },
                { data: 'quantity', name: 'quantity', orderable: false, class: 'text-center' },
                { data: 'mobile', name: 'mobile', orderable: false, class: 'text-center' },
                { data: 'creator_name', name: 'creator_name', orderable: false },
                {
                    data: 'delete',
                    name: 'delete',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (!abilityDelete) {
                            return `<div class="d-flex align-items-center justify-content-evenly"></div>`;
                        }
                        return `
                            <div class="d-flex align-items-center justify-content-evenly">
                                <button class="action-btn btn-delete delete_row" data-id="${data}" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ];

            const SUMMABLE_COLUMNS = { enabled: false, columns: {} };
            const dataForm = {};
            const sortConfig = { enabled: true };
            let currentSortColumn = '';
            let currentSortDirection = '';
            const columnsCopy = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
            const columnNamesCopy = ['distributed_at', 'primary_name', 'national_id', 'housing_location', 'family_members_count', 'marital_status', 'office_name', 'institution_name', 'project_name', 'aid_mode', 'aid_value', 'quantity', 'mobile', 'creator_name'];
            const urlExportExcel = `{{ route('dashboard.aid-distributions.export-excel') }}`;
        </script>
        <script type="text/javascript" src="{{ asset('js/datatable.js') }}"></script>
        <script>
            $(document).on('click', '#aid-excel-export', function() {
                function getExportFilters() {
                    let filters = {};
                    $('input[type="checkbox"]:checked').each(function() {
                        let className = $(this).attr('class');
                        let value = $(this).val();
                        if (value === "الكل" || value === "all" || value === "All") return;
                        let fieldMatch = className.match(/(\w+)-checkbox/);
                        if (fieldMatch) {
                            let fieldName = fieldMatch[1];
                            if (!filters[fieldName]) filters[fieldName] = [];
                            filters[fieldName].push(value);
                        }
                    });
                    let fromDate = $("#from_date").val();
                    let toDate = $("#to_date").val();
                    if (fromDate || toDate) {
                        filters['distributed_at'] = {};
                        if (fromDate) filters['distributed_at']['from'] = fromDate;
                        if (toDate) filters['distributed_at']['to'] = toDate;
                    }
                    return filters;
                }
                let form = $('#aid-export-form');
                form.attr('action', urlExportExcel);
                form.find('#export-year').val($('#year').val() || '');
                form.find('#export-from_date').val($('#from_date').val() || '');
                form.find('#export-to_date').val($('#to_date').val() || '');
                form.find('#export-column_filters').val(JSON.stringify(getExportFilters()));
                let urlParams = new URLSearchParams(window.location.search);
                form.find('#export-project_id').val(urlParams.get('project_id') || '');
                form.find('#export-office_id').val(urlParams.get('office_id') || '');
                form.find('#export-family_id').val(urlParams.get('family_id') || '');
                form.submit();
            });
        </script>
    @endpush
</x-front-layout>
