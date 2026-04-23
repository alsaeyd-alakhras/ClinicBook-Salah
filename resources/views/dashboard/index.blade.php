<x-front-layout>
    @push('styles')
        <style>
            .dashboard-soft-bg {
                background: #f8fafc;
                border-radius: 12px;
            }

            .kpi-card {
                border: 1px solid #edf2f7;
                border-radius: 14px;
                box-shadow: 0 1px 8px rgba(15, 23, 42, 0.05);
            }

            .kpi-value {
                font-size: 1.7rem;
                font-weight: 700;
                line-height: 1.2;
            }

            .kpi-sub {
                font-size: 0.85rem;
                color: #64748b;
            }

            .cash-text {
                color: #198754;
                font-weight: 700;
            }

            .section-card {
                border: 1px solid #edf2f7;
                border-radius: 14px;
                box-shadow: 0 1px 8px rgba(15, 23, 42, 0.04);
            }

            .table > :not(caption) > * > * {
                color: #000;
            }

            .project-notes-popover .popover-body {
                max-width: 320px;
                max-height: 200px;
                overflow-y: auto;
            }
        </style>
    @endpush

    <x-slot:extra_nav>
        <div class="mx-2 nav-item">
            <form method="POST" action="{{ route('dashboard.home.refresh-cache') }}" class="d-inline">
                @csrf
                <button class="p-2 border-0 btn btn-outline-primary rounded-pill me-n1 waves-effect waves-light"
                    type="submit" title="تحديث الإحصائيات من المصدر مباشرة">
                    <i class="fa-solid fa-rotate-right fe-16"></i>
                </button>
            </form>
        </div>
    </x-slot:extra_nav>

    <x-slot:breadcrumb>
        <li><a href="#">الرئيسية</a></li>
    </x-slot:breadcrumb>

    @php
        $formatMoney = fn($value) => number_format((float) $value, 2);
        $formatCount = fn($value) => number_format((int) $value);
    @endphp

    <div class="p-3 mb-4 dashboard-soft-bg">
        <h5 class="mb-1">لوحة الإحصائيات</h5>
        <p class="mb-0 text-muted">متابعة فورية لإجمالي الأسر والمساعدات وأداء المكاتب لسنة {{ $year }}</p>
    </div>

    <div class="mb-4 row g-3">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">إجمالي الأسر</div>
                <div class="kpi-value">{{ $formatCount($globalStats['total_families']) }}</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['total_families'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">إجمالي عمليات الصرف</div>
                <div class="kpi-value">{{ $formatCount($globalStats['total_distributions']) }}</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['total_distributions'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">إجمالي النقد المصروف</div>
                <div class="kpi-value cash-text">{{ $formatMoney($globalStats['total_cash_all_time']) }} ₪</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['total_cash_all_time'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">صرف الشهر الحالي</div>
                <div class="kpi-value">{{ $formatCount($globalStats['current_month_distributions']) }}</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['current_month_distributions'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">نقد الشهر الحالي</div>
                <div class="kpi-value cash-text">{{ $formatMoney($globalStats['current_month_cash']) }} ₪</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['current_month_cash'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">المكاتب المفعلة</div>
                <div class="kpi-value">{{ $formatCount($globalStats['active_offices']) }}</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['active_offices'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="p-3 card kpi-card h-100">
                <div class="mb-1 text-muted">إجمالي المؤسسات</div>
                <div class="kpi-value">{{ $formatCount($globalStats['total_institutions']) }}</div>
                <div class="kpi-sub">{{ $globalStats['comparison']['total_institutions'] }}</div>
            </div>
        </div>
    </div>
    <div class="mb-4 card section-card">
        <div class="py-3 card-header">
            <h6 class="mb-0">المشاريع</h6>
        </div>
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table mb-0 table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>رقم المشروع</th>
                            <th>اسم المشروع</th>
                            <th>المؤسسة</th>
                            <th>النوع</th>
                            <th>الإجمالي</th>
                            <th>المصروف</th>
                            <th>المتبقي</th>
                            @if($showStorageOfficesBalance ?? false)
                                <th>رصيد المخزن</th>
                                <th>رصيد المكاتب</th>
                                <th>رفع</th>
                            @endif
                            <th>المستفيدين</th>
                            <th>المكررين</th>
                            <th>تفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projectStats as $project)
                            <tr>
                                <td>
                                    @if($project['can_edit'] ?? false)
                                        <a href="{{ route('dashboard.projects.edit', $project['id']) }}" class="text-decoration-none">{{ $project['project_number'] }}</a>
                                    @else
                                        {{ $project['project_number'] }}
                                    @endif
                                </td>
                                <td>
                                    <span class="view-project-breakdown-btn text-primary text-decoration-underline" data-project-id="{{ $project['id'] }}" role="button" title="تفاصيل المشروع" style="cursor: pointer;">{{ $project['name'] }}</span>
                                </td>
                                <td>{{ $project['institution_name'] }}</td>
                                <td>
                                    @if($project['project_type'] === 'cash')
                                        <span class="badge bg-success">نقدي</span>
                                    @else
                                        <span class="badge bg-info">عيني</span>
                                        <div class="small text-muted">{{ $project['aid_item_name'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($project['project_type'] === 'cash')
                                        {{ $formatMoney($project['total_amount']) }} ₪
                                    @else
                                        {{ number_format($project['total_quantity'], 2) }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $aidDistUrl = route('dashboard.aid-distributions.index', ['project_id' => $project['id']]);
                                    @endphp
                                    @if($project['project_type'] === 'cash')
                                        <a href="{{ $aidDistUrl }}" class="text-danger text-decoration-none">{{ $formatMoney($project['consumed_amount']) }} ₪</a>
                                    @else
                                        <a href="{{ $aidDistUrl }}" class="text-danger text-decoration-none">{{ number_format($project['consumed_quantity'], 2) }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if($project['project_type'] === 'cash')
                                        <span class="text-success">{{ $formatMoney($project['remaining_amount']) }} ₪</span>
                                    @else
                                        <span class="text-success">{{ number_format($project['remaining_quantity'], 2) }}</span>
                                    @endif
                                </td>
                                @if($showStorageOfficesBalance ?? false)
                                    <td>
                                        @if($project['project_type'] === 'cash')
                                            {{ $formatMoney($project['storage_balance_amount'] ?? 0) }} ₪
                                        @else
                                            {{ number_format($project['storage_balance_quantity'] ?? 0, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($project['project_type'] === 'cash')
                                            {{ $formatMoney($project['offices_balance_amount'] ?? 0) }} ₪
                                        @else
                                            {{ number_format($project['offices_balance_quantity'] ?? 0, 2) }}
                                        @endif
                                    </td>
                                    <td>{{ $project['receipts_display'] ?? '-' }}</td>
                                @endif
                                <td>
                                    <span class="badge bg-primary">{{ $project['beneficiaries_total'] }}</span>
                                    /
                                    <span class="badge bg-warning">{{ $project['beneficiaries_consumed'] }}</span>
                                    /
                                    <span class="badge bg-success">{{ $project['remaining_beneficiaries'] }}</span>
                                </td>
                                <td>
                                    @php $repeatersCount = (int) ($project['repeaters_count'] ?? 0); @endphp
                                    @if($repeatersCount > 0)
                                        <span class="view-repeaters-btn text-primary text-decoration-underline" role="button" style="cursor: pointer;"
                                            data-project-id="{{ $project['id'] }}" data-project-name="{{ e($project['name']) }}" data-office-id=""
                                            title="عرض المكررين">{{ $repeatersCount }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-project-breakdown-btn"
                                            data-project-id="{{ $project['id'] }}" title="تفاصيل الصرف">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        @if(!empty(trim($project['notes'] ?? '')))
                                            <button type="button" class="btn btn-sm btn-outline-warning project-notes-btn"
                                                data-notes="{{ e(str_replace(["\r\n","\n","\r"], '||NL||', $project['notes'] ?? '')) }}"
                                                title="ملاحظات المشروع">
                                                <i class="fa-solid fa-sticky-note"></i>
                                            </button>
                                        @endif
                                        @if(isset($project['allocation_id']) && Auth::user()?->user_type === 'employee')
                                            <button type="button" class="btn btn-sm upload-receipt-btn {{ ($project['has_receipt'] ?? false) ? 'btn-success text-white' : 'btn-outline-secondary' }}"
                                                data-project-id="{{ $project['id'] }}"
                                                data-allocation-id="{{ $project['allocation_id'] }}"
                                                data-has-receipt="{{ $project['has_receipt'] ? '1' : '0' }}"
                                                title="{{ ($project['has_receipt'] ?? false) ? 'تم رفع كشف الإستلام' : 'رفع كشف الإستلام' }}">
                                                <i class="fa-solid fa-file-upload"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($showStorageOfficesBalance ?? false) ? 14 : 11 }}" class="py-4 text-center text-muted">لا توجد مشاريع لعرضها</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-3 bg-white card-footer">
            {{ $projectStats->appends(request()->except('project_page'))->links() }}
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card section-card h-100">
                <div class="py-3 card-header">
                    <h6 class="mb-0">أداء المكاتب</h6>
                </div>
                <div class="p-0 card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>المكتب</th>
                                    <th>عدد العمليات</th>
                                    <th>إجمالي النقد</th>
                                    <th>العيني</th>
                                    <th>آخر عملية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($officeStats as $office)
                                    <tr>
                                        <td>{{ $office->name }}</td>
                                        <td>{{ $formatCount($office->total_distributions) }}</td>
                                        <td class="cash-text">{{ $formatMoney($office->cash_total) }} ₪</td>
                                        <td>{{ $formatCount($office->in_kind_count) }}</td>
                                        <td>{{ $office->last_distribution_date ? \Carbon\Carbon::parse($office->last_distribution_date)->format('Y-m-d') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-muted">لا توجد بيانات مكاتب لعرضها</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pt-3 bg-white card-footer">
                    {{ $officeStats->appends(request()->except('office_page'))->links() }}
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card section-card h-100">
                <div class="py-3 card-header">
                    <h6 class="mb-0">أكثر المساعدات العينية استخداماً</h6>
                </div>
                <div class="p-0 card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الصنف</th>
                                    <th>مرات الصرف</th>
                                    <th>آخر صرف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topAidItems as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $formatCount($item->total_distributed) }}</td>
                                        <td>{{ $item->last_distribution_date ? \Carbon\Carbon::parse($item->last_distribution_date)->format('Y-m-d') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-muted">لا توجد بيانات مساعدة عينية</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pt-3 bg-white card-footer">
                    {{ $topAidItems->appends(request()->except('aid_item_page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 card section-card">
        <div class="py-3 card-header">
            <h6 class="mb-0">المؤسسات والمصروف عليها</h6>
        </div>
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table mb-0 table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>المؤسسة</th>
                            <th>عدد عمليات الصرف</th>
                            <th>المنصرف النقدي</th>
                            <th>الكمية المصروفة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($institutionStats as $institution)
                            <tr>
                                <td>{{ $institution->name }}</td>
                                <td>{{ $formatCount($institution->total_distributions ?? 0) }}</td>
                                <td class="cash-text">{{ $formatMoney($institution->total_spent_cash ?? 0) }} ₪</td>
                                <td>{{ number_format((float) ($institution->total_spent_quantity ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">لا توجد بيانات مؤسسات لعرضها</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-3 bg-white card-footer">
            {{ $institutionStats->appends(request()->except('institution_page'))->links() }}
        </div>
    </div>

    <div class="mt-4 card section-card">
        <div class="py-3 card-header">
            <h6 class="mb-0">آخر عمليات الصرف</h6>
        </div>
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table mb-0 table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>الأسرة</th>
                            <th>المكتب</th>
                            <th>نوع المساعدة</th>
                            <th>النقد / الصنف</th>
                            <th>مدخل العملية</th>
                            <th>الحالة</th>
                            <th>تفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentDistributions as $distribution)
                            @php
                                $isCancelled = $distribution->status === 'cancelled';
                            @endphp
                            <tr class="{{ $isCancelled ? 'table-danger' : '' }}">
                                <td>{{ optional($distribution->distributed_at)->format('Y-m-d') }}</td>
                                <td>{{ $distribution->family?->full_name ?? '-' }}</td>
                                <td>{{ $distribution->office?->name ?? '-' }}</td>
                                <td>
                                    @if ($distribution->aid_mode === 'cash')
                                        <span class="badge bg-label-success">نقدية</span>
                                    @else
                                        <span class="badge bg-label-info">عينية</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($distribution->aid_mode === 'cash')
                                        <span class="cash-text">{{ $formatMoney($distribution->cash_amount) }} ₪</span>
                                    @else
                                        {{ $distribution->aidItem?->name ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $distribution->creator?->name ?? '-' }}</td>
                                <td>
                                    @if ($isCancelled)
                                        <span class="badge bg-danger">ملغي</span>
                                    @else
                                        <span class="badge bg-success">نشط</span>
                                    @endif
                                </td>
                                <td>
                                    @can('update', App\Models\AidDistribution::class)
                                        <a href="{{ route('dashboard.aid-distributions.show', $distribution->id) }}"
                                            class="btn btn-sm btn-outline-primary">عرض التفاصيل</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-center text-muted">لا توجد عمليات حديثة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-3 bg-white card-footer">
            {{ $recentDistributions->appends(request()->except('recent_page'))->links() }}
        </div>
    </div>

    <div class="mt-4 card section-card">
        <div class="py-3 card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Monthly Distribution Overview</h6>
            <span class="text-muted small">{{ $year }}</span>
        </div>
        <div class="card-body">
            <canvas id="monthlyOverviewChart" height="90"></canvas>
        </div>
    </div>

    <div class="modal fade" id="projectBreakdownModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل المشروع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>رقم المشروع:</strong> <span id="breakdown-project-number"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>اسم المشروع:</strong> <span id="breakdown-project-name"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>المؤسسة:</strong> <span id="breakdown-institution"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>النوع:</strong> <span id="breakdown-type"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>المعتمد ككل:</strong> <span id="breakdown-offices-balance"></span>
                        </div>
                    </div>
                    <hr>
                    <h6>توزيع الصرف حسب المكتب:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>المكتب</th>
                                    <th>المعتمد للمكتب</th>
                                    <th>عدد المستفيدين</th>
                                    <th>عدد المساعدات</th>
                                    <th>المكررين</th>
                                    <th>المبلغ/الكمية</th>
                                    <th>كشف الإستلام</th>
                                </tr>
                            </thead>
                            <tbody id="breakdown-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="repeatersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex flex-wrap align-items-center gap-2">
                        <span>المكررين</span>
                        <span class="text-muted">—</span>
                        <span id="repeaters-modal-project-name"></span>
                        <span id="repeaters-modal-office-badge" class="badge bg-label-info align-middle" style="display: none;"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="repeaters-modal-project-id" value="">
                    <input type="hidden" id="repeaters-modal-office-id" value="">
                    <div class="mb-3 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                            <label class="form-check form-check-inline mb-0">
                                <input type="radio" name="repeaters-period" value="all" class="form-check-input" checked>
                                <span class="form-check-label"><i class="fa-solid fa-infinity text-muted me-1"></i>عرض الكل</span>
                            </label>
                            <label class="form-check form-check-inline mb-0">
                                <input type="radio" name="repeaters-period" value="range" class="form-check-input">
                                <span class="form-check-label"><i class="fa-solid fa-calendar-days text-muted me-1"></i>تحديد مدة</span>
                            </label>
                        </div>
                        <div id="repeaters-date-range" class="d-none mt-2 pt-2" style="border-top: 1px dashed #cbd5e1;">
                            <div class="d-flex flex-wrap align-items-end gap-2">
                                <div class="flex-grow-1" style="min-width: 140px;">
                                    <label class="form-label small text-muted mb-1">من تاريخ</label>
                                    <input type="date" id="repeaters-from-date" class="form-control form-control-sm">
                                </div>
                                <div class="align-self-center text-muted small px-1">—</div>
                                <div class="flex-grow-1" style="min-width: 140px;">
                                    <label class="form-label small text-muted mb-1">إلى تاريخ</label>
                                    <input type="date" id="repeaters-to-date" class="form-control form-control-sm">
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="repeaters-apply-dates">
                                    <i class="fa-solid fa-filter me-1"></i>تطبيق
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>عدد المرات</th>
                                    <th>المبلغ (₪)</th>
                                    <th>الكمية</th>
                                    <th>آخر عملية</th>
                                </tr>
                            </thead>
                            <tbody id="repeaters-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">رفع كشف الإستلام</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadReceiptForm">
                        @csrf
                        <input type="hidden" id="upload-receipt-project-id" name="project_id">
                        <input type="hidden" id="upload-receipt-allocation-id" name="allocation_id">
                        <div class="mb-3">
                            <label class="form-label">الملف (PDF, Excel, صور)</label>
                            <input type="file" class="form-control" id="receipt_file" name="receipt_file"
                                accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png,.gif,.webp">
                            <small class="text-muted d-block mt-1">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                يتم رفع ملف واحد فقط — عند رفع ملف آخر يتم استبدال الملف القديم
                            </small>
                        </div>
                        <div id="receipt-view-area" class="mb-2" style="display: none;">
                            <a href="#" id="receipt-view-link" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> عرض الملف المرفوع
                            </a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="uploadReceiptBtn">
                        <i class="fa-solid fa-upload me-1"></i> رفع
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>
        <script>
            const monthlyLabels = @json($monthlyStats['labels']);
            const monthlyDistributionSeries = @json($monthlyStats['distribution_series']);
            const monthlyCashSeries = @json($monthlyStats['cash_series']);

            const chartCanvas = document.getElementById('monthlyOverviewChart');
            if (chartCanvas) {
                new Chart(chartCanvas, {
                    data: {
                        labels: monthlyLabels,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'عدد العمليات',
                                data: monthlyDistributionSeries,
                                backgroundColor: 'rgba(54, 162, 235, 0.35)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                yAxisID: 'y'
                            },
                            {
                                type: 'line',
                                label: 'إجمالي النقد (₪)',
                                data: monthlyCashSeries,
                                borderColor: 'rgba(25, 135, 84, 1)',
                                backgroundColor: 'rgba(25, 135, 84, 0.12)',
                                tension: 0.35,
                                fill: false,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'عدد العمليات'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'إجمالي النقد (₪)'
                                }
                            }
                        }
                    }
                });
            }

            $('.project-notes-btn').each(function () {
                const $btn = $(this);
                const notes = ($btn.data('notes') || '').replace(/\|\|NL\|\|/g, '<br>');
                if (notes) {
                    new bootstrap.Popover($btn[0], {
                        title: '<i class="fa-solid fa-sticky-note me-1"></i> ملاحظات المشروع',
                        content: notes,
                        html: true,
                        placement: 'left',
                        trigger: 'click',
                        customClass: 'project-notes-popover'
                    });
                }
            });

            const aidDistributionsUrl = @json(route('dashboard.aid-distributions.index'));

            const uploadReceiptUrlTemplate = @json(route('dashboard.projects.allocations.upload-receipt', ['projectId' => '__PID__', 'allocationId' => '__AID__']));

            $('.upload-receipt-btn').on('click', function () {
                const projectId = $(this).data('project-id');
                const allocationId = $(this).data('allocation-id');
                const hasReceipt = $(this).data('has-receipt') == 1;
                $('#upload-receipt-project-id').val(projectId);
                $('#upload-receipt-allocation-id').val(allocationId);
                $('#receipt_file').val('');
                const receiptUrl = '{{ url("/") }}/projects/' + projectId + '/allocations/' + allocationId + '/receipt';
                if (hasReceipt) {
                    $('#receipt-view-link').attr('href', receiptUrl);
                    $('#receipt-view-area').show();
                } else {
                    $('#receipt-view-area').hide();
                }
                new bootstrap.Modal(document.getElementById('uploadReceiptModal')).show();
            });

            $('#uploadReceiptBtn').on('click', function () {
                const projectId = $('#upload-receipt-project-id').val();
                const allocationId = $('#upload-receipt-allocation-id').val();
                const fileInput = document.getElementById('receipt_file');
                if (!fileInput.files.length) {
                    toastr.warning('يرجى اختيار ملف للرفع');
                    return;
                }
                const formData = new FormData();
                formData.append('receipt_file', fileInput.files[0]);
                formData.append('_token', '{{ csrf_token() }}');
                const url = uploadReceiptUrlTemplate.replace('__PID__', projectId).replace('__AID__', allocationId);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        bootstrap.Modal.getInstance(document.getElementById('uploadReceiptModal')).hide();
                        toastr.success('تم رفع الملف بنجاح');
                        location.reload();
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || xhr.responseJSON?.errors?.receipt_file?.[0] || 'فشل رفع الملف';
                        toastr.error(msg);
                    }
                });
            });

            $('.view-project-breakdown-btn').on('click', function () {
                const projectId = $(this).data('project-id');

                $.ajax({
                    url: `/api/projects/${projectId}/breakdown`,
                    method: 'GET',
                    success: function (response) {
                        $('#breakdown-project-number').text(response.project.project_number);
                        $('#breakdown-project-name').text(response.project.name);
                        $('#breakdown-institution').text(response.project.institution_name);

                        const typeText = response.project.project_type === 'cash'
                            ? 'نقدي'
                            : `عيني (${response.project.aid_item_name})`;
                        $('#breakdown-type').text(typeText);

                        const officesBalanceText = response.project.project_type === 'cash'
                            ? parseFloat(response.project.offices_balance_amount || 0).toFixed(2) + ' ₪'
                            : parseFloat(response.project.offices_balance_quantity || 0).toFixed(2);
                        $('#breakdown-offices-balance').text(officesBalanceText);

                        const $tbody = $('#breakdown-table-body');
                        $tbody.empty();

                        if (response.breakdown.length === 0) {
                            $tbody.append('<tr><td colspan="7" class="text-center text-muted">لا توجد عمليات صرف لهذا المشروع</td></tr>');
                        } else {
                            response.breakdown.forEach(function (item) {
                                const allocatedDisplay = response.project.project_type === 'cash'
                                    ? parseFloat(item.allocated_amount || 0).toFixed(2) + ' ₪'
                                    : parseFloat(item.allocated_quantity || 0).toFixed(2);
                                const valueDisplay = response.project.project_type === 'cash'
                                    ? parseFloat(item.total_cash || 0).toFixed(2) + ' ₪'
                                    : parseFloat(item.total_quantity || 0).toFixed(2);

                                const aidCountUrl = aidDistributionsUrl + '?project_id=' + projectId + '&office_id=' + (item.office_id || '');
                                const aidCountCell = item.aid_count > 0
                                    ? `<a href="${aidCountUrl}" class="text-primary text-decoration-none">${item.aid_count}</a>`
                                    : item.aid_count;

                                const repeatersCount = item.repeaters_count || 0;
                                const officeNameEsc = (item.office_name || '').replace(/"/g, '&quot;');
                                const repeatersCell = repeatersCount > 0
                                    ? `<span class="view-repeaters-btn text-primary text-decoration-underline" role="button" style="cursor: pointer;" data-project-id="${projectId}" data-project-name="${response.project.name}" data-office-id="${item.office_id || ''}" data-office-name="${officeNameEsc}">${repeatersCount}</span>`
                                    : repeatersCount;

                                const receiptCell = item.receipt_url
                                    ? `<a href="${item.receipt_url}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-file-pdf me-1"></i>عرض</a>`
                                    : '<span class="text-muted small">المكتب مش مسلم نهائي الملف</span>';

                                $tbody.append(`
                                    <tr>
                                        <td>${item.office_name}</td>
                                        <td>${allocatedDisplay}</td>
                                        <td>${item.beneficiaries}</td>
                                        <td>${aidCountCell}</td>
                                        <td>${repeatersCell}</td>
                                        <td>${valueDisplay}</td>
                                        <td>${receiptCell}</td>
                                    </tr>
                                `);
                            });
                        }

                        $('#projectBreakdownModal .view-repeaters-btn').off('click').on('click', function () {
                            const projectId = $(this).data('project-id');
                            const projectName = $(this).data('project-name');
                            const officeId = $(this).data('office-id') || null;
                            const officeName = $(this).data('office-name') || '';
                            const breakdownModal = document.getElementById('projectBreakdownModal');
                            const bsModal = bootstrap.Modal.getInstance(breakdownModal);
                            $(breakdownModal).one('hidden.bs.modal', function () {
                                setTimeout(function () {
                                    openRepeatersModal(projectId, projectName, officeId, officeName);
                                }, 50);
                            });
                            bsModal.hide();
                        });

                        new bootstrap.Modal(document.getElementById('projectBreakdownModal')).show();
                    },
                    error: function () {
                        toastr.error('فشل تحميل تفاصيل المشروع');
                    }
                });
            });

            const aidDistributionsIndexUrl = @json(route('dashboard.aid-distributions.index'));

            function openRepeatersModal(projectId, projectName, officeId, officeName) {
                $('#repeaters-modal-project-name').text(projectName || '');
                const $officeBadge = $('#repeaters-modal-office-badge');
                if (officeName) {
                    $officeBadge.text('مكتب: ' + officeName).show();
                } else {
                    $officeBadge.hide();
                }
                $('#repeaters-modal-project-id').val(projectId);
                $('#repeaters-modal-office-id').val(officeId || '');
                $('input[name="repeaters-period"][value="all"]').prop('checked', true);
                $('#repeaters-date-range').addClass('d-none');
                $('#repeaters-from-date, #repeaters-to-date').val('');
                loadRepeatersData(projectId, officeId, null, null);
                const repeatersModalEl = document.getElementById('repeatersModal');
                $(repeatersModalEl).off('hidden.bs.modal.repeatersCleanup').on('hidden.bs.modal.repeatersCleanup', function () {
                    if (!$('.modal.show').length) {
                        $('body').removeClass('modal-open').css('padding-right', '');
                        $('.modal-backdrop').remove();
                    }
                });
                new bootstrap.Modal(repeatersModalEl).show();
            }

            function loadRepeatersData(projectId, officeId, fromDate, toDate) {
                let url = `/api/projects/${projectId}/repeaters`;
                const params = [];
                if (officeId) params.push('office_id=' + officeId);
                if (fromDate) params.push('from_date=' + fromDate);
                if (toDate) params.push('to_date=' + toDate);
                if (params.length) url += '?' + params.join('&');

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        const $tbody = $('#repeaters-table-body');
                        $tbody.empty();
                        if (response.repeaters.length === 0) {
                            $tbody.append('<tr><td colspan="5" class="text-center text-muted">لا يوجد مكررين</td></tr>');
                        } else {
                            response.repeaters.forEach(function (r) {
                                const cashDisplay = parseFloat(r.total_cash || 0).toFixed(2) + ' ₪';
                                const qtyDisplay = parseFloat(r.total_quantity || 0).toFixed(2);
                                const recordUrl = aidDistributionsIndexUrl + '?project_id=' + projectId + '&family_id=' + r.family_id
                                    + (fromDate ? '&from_date=' + fromDate : '')
                                    + (toDate ? '&to_date=' + toDate : '');
                                $tbody.append(`
                                    <tr>
                                        <td>${r.full_name || '-'}</td>
                                        <td><a href="${recordUrl}" class="text-primary text-decoration-none">${r.repeat_count}</a></td>
                                        <td>${cashDisplay}</td>
                                        <td>${qtyDisplay}</td>
                                        <td>${r.last_distributed_at || '-'}</td>
                                    </tr>
                                `);
                            });
                        }
                    },
                    error: function () {
                        toastr.error('فشل تحميل المكررين');
                        $('#repeaters-table-body').html('<tr><td colspan="5" class="text-center text-danger">فشل التحميل</td></tr>');
                    }
                });
            }

            $(document).on('click', '.view-repeaters-btn', function () {
                openRepeatersModal(
                    $(this).data('project-id'),
                    $(this).data('project-name'),
                    $(this).data('office-id') || null,
                    $(this).data('office-name') || ''
                );
            });

            $('input[name="repeaters-period"]').on('change', function () {
                const val = $(this).val();
                if (val === 'range') {
                    $('#repeaters-date-range').removeClass('d-none');
                } else {
                    $('#repeaters-date-range').addClass('d-none');
                    const projectId = $('#repeaters-modal-project-id').val();
                    const officeId = $('#repeaters-modal-office-id').val() || null;
                    if (projectId) loadRepeatersData(projectId, officeId, null, null);
                }
            });

            function applyRepeatersDateFilter() {
                const projectId = $('#repeaters-modal-project-id').val();
                const officeId = $('#repeaters-modal-office-id').val() || null;
                const fromDate = $('#repeaters-from-date').val();
                const toDate = $('#repeaters-to-date').val();
                if (projectId && fromDate && toDate) {
                    loadRepeatersData(projectId, officeId, fromDate, toDate);
                } else if (projectId && (fromDate || toDate)) {
                    toastr.warning('يرجى اختيار تاريخ البداية والنهاية');
                }
            }

            $('#repeaters-apply-dates').on('click', applyRepeatersDateFilter);

            $('#repeaters-from-date, #repeaters-to-date').on('change', function () {
                if ($('#repeaters-from-date').val() && $('#repeaters-to-date').val()) {
                    applyRepeatersDateFilter();
                }
            });
        </script>
    @endpush
</x-front-layout>