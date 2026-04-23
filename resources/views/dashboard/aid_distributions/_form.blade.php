@php
    $currentUser = auth()->user();
    $isEmployee = $currentUser?->user_type == 'employee';
    $lockedOfficeId = $isEdit ? $distribution->office_id : ($currentUser?->office_id ?? $distribution->office_id);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/select2.min.css') }}">
@endpush

<input type="hidden" name="family_id" id="family_id" value="">
<input type="hidden" name="resolution_mode" id="resolution_mode" value="">

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">بيانات الأسرة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <label for="national_id" class="form-label">رقم الهوية <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="national_id"
                                name="national_id"
                                maxlength="9"
                                value="{{ $familyForm['national_id'] ?? '' }}"
                                required
                            />
                            <button type="button" class="btn btn-primary" id="search-family-btn">
                                <i class="ti ti-search me-1"></i>
                                <span class="btn-text">بحث</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input
                            name="primary_name"
                            label="الاسم الرباعي"
                            :value="$familyForm['primary_name'] ?? ''"
                            required
                        />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input
                            name="mobile"
                            label="رقم الجوال"
                            maxlength="10"
                            :value="$familyForm['mobile'] ?? ''"
                        />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input
                            type="number"
                            min="1"
                            name="family_members_count"
                            label="عدد أفراد الأسرة"
                            :value="$familyForm['family_members_count'] ?? ''"
                        />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input
                            name="housing_location"
                            label="مكان السكن"
                            :value="$familyForm['housing_location'] ?? ''"
                        />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.select
                            name="marital_status"
                            id="marital_status"
                            label="الحالة الزوجية"
                            :options="[
                                'single' => 'أعزب/عزباء',
                                'married' => 'متزوج/ة',
                                'polygamous' => 'متعدد الزوجات',
                                'widowed' => 'أرمل/ة',
                                'divorced' => 'مطلق/ة',
                            ]"
                            :value="$familyForm['marital_status'] ?? 'single'"
                            required
                        />
                    </div>
                    <div id="spouse-fields" class="row">
                        @for ($wifeIndex = 0; $wifeIndex < 4; $wifeIndex++)
                            <div class="row spouse-row" data-spouse-index="{{ $wifeIndex }}">
                                <div class="mb-4 col-md-5">
                                    <x-form.input
                                        id="spouses_{{ $wifeIndex }}_full_name"
                                        name="spouses[{{ $wifeIndex }}][full_name]"
                                        label="اسم الزوجة {{ $wifeIndex + 1 }}"
                                        :value="old('spouses.' . $wifeIndex . '.full_name', $familyForm['spouses'][$wifeIndex]['full_name'] ?? '')"
                                    />
                                </div>
                                <div class="mb-4 col-md-5">
                                    <x-form.input
                                        id="spouses_{{ $wifeIndex }}_national_id"
                                        name="spouses[{{ $wifeIndex }}][national_id]"
                                        label="رقم هوية الزوجة {{ $wifeIndex + 1 }}"
                                        maxlength="9"
                                        :value="old('spouses.' . $wifeIndex . '.national_id', $familyForm['spouses'][$wifeIndex]['national_id'] ?? '')"
                                    />
                                </div>
                                <div class="mb-4 col-md-2 d-flex align-items-end">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger w-100 remove-spouse-btn"
                                        data-spouse-index="{{ $wifeIndex }}"
                                    >
                                        <i class="ti ti-trash me-1"></i> حذف
                                    </button>
                                </div>
                            </div>
                        @endfor
                        <div class="col-12 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-spouse-btn">
                                <i class="ti ti-plus me-1"></i> إضافة زوجة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">بيانات المساعدة</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label" for="office_id">المكتب</label>
                    <select
                        id="office_id"
                        name="office_id"
                        class="form-select @error('office_id') is-invalid @enderror"
                        @if($isEmployee) disabled @endif
                        required
                    >
                        <option value="" @selected(old('office_id', $isEmployee ? $lockedOfficeId : $distribution->office_id) == null)>إختر القيمة</option>
                        @foreach ($offices as $office)
                            <option
                                value="{{ $office->id }}"
                                @selected(old('office_id', $isEmployee ? $lockedOfficeId : $distribution->office_id) == $office->id)
                            >
                                {{ $office->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('office_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($isEmployee)
                        <input type="hidden" name="office_id" value="{{ old('office_id', $lockedOfficeId) }}">
                    @endif
                </div>
                <div class="mb-4">
                    <label class="form-label" for="institution_id">المؤسسة</label>
                    <select
                        id="institution_id"
                        name="institution_id"
                        class="form-select select2 @error('institution_id') is-invalid @enderror"
                        required
                    >
                        <option value="" @selected(old('institution_id', $distribution->institution_id) == null)>إختر القيمة</option>
                        @foreach ($institutions as $institution)
                            <option
                                value="{{ $institution->id }}"
                                @selected(old('institution_id', $distribution->institution_id) == $institution->id)
                            >
                                {{ $institution->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('institution_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="project_id">المشروع</label>
                    <select
                        id="project_id"
                        name="project_id"
                        class="form-select select2 @error('project_id') is-invalid @enderror"
                    >
                        <option value="">اختر المؤسسة أولاً</option>
                        @if($isEdit && $distribution->project_id)
                            <option value="{{ $distribution->project_id }}" selected>
                                {{ $distribution->project?->project_number }} - {{ $distribution->project?->name }}
                            </option>
                        @endif
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="project-full-message" class="alert alert-danger mt-2" style="display: none;">
                        <i class="fa-solid fa-exclamation-triangle me-1"></i>
                        <strong>المشروع ممتلئ!</strong> لا يوجد مستفيدين متبقيين.
                    </div>
                    <div id="project-stats-display" class="mt-2 small text-muted" style="display: none;">
                    </div>
                    <div id="project-notes-display" class="mt-2 small" style="display: none;">
                    </div>
                </div>
                <div class="mb-4">
                    <x-form.select
                        name="aid_mode"
                        id="aid_mode"
                        label="نوع المساعدة"
                        :options="[
                            'cash' => 'نقدية',
                            'in_kind' => 'عينية',
                        ]"
                        :value="$distribution->aid_mode"
                        required
                    />
                </div>
                <div class="mb-4" id="cash-amount-wrapper">
                    <x-form.input
                        id="cash_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        name="cash_amount"
                        label="قيمة المساعدة"
                        :value="$distribution->cash_amount"
                    />
                    <div id="cash-remaining-message" class="mt-2 small" style="display: none;"></div>
                </div>
                <div class="mb-4" id="aid-item-wrapper">
                    <x-form.select
                        id="aid_item_id"
                        name="aid_item_id"
                        label="نوع المساعدة العينية"
                        :optionsId="$aidItems"
                        :value="$distribution->aid_item_id"
                    />
                </div>
                <div class="mb-4" id="quantity-wrapper">
                    <x-form.input
                        id="quantity"
                        type="number"
                        min="0.01"
                        step="0.01"
                        name="quantity"
                        label="كمية الصرف"
                        :value="$distribution->quantity"
                    />
                    <div id="quantity-remaining-message" class="mt-2 small" style="display: none;"></div>
                </div>
                <div class="mb-4">
                    <x-form.input
                        type="date"
                        name="distributed_date"
                        label="تاريخ الصرف"
                        :value="$distribution->distributed_at ? $distribution->distributed_at->format('Y-m-d') : ''"
                    />
                </div>
                <div class="mb-4">
                    <x-form.textarea
                        name="distribution_notes"
                        label="ملاحظات"
                        rows="3"
                        :value="$distribution->notes"
                    />
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'تعديل' : 'حفظ' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Offcanvas Sidebar للعائلة والمساعدات --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="familySidebar" aria-labelledby="familySidebarLabel" style="width: 450px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="familySidebarLabel">معلومات الأسرة</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {{-- Card 1: بيانات الأسرة --}}
        <div class="card mb-3">
            <div class="card-body">
                <div id="match-message" class="alert alert-info mb-3" style="display: none;"></div>
                <h6 class="card-subtitle mb-3 text-muted">البيانات الأساسية</h6>
                <div class="mb-2">
                    <strong>الاسم:</strong> <span id="sidebar-name"></span>
                </div>
                <div class="mb-2">
                    <strong>رقم الهوية:</strong> <span id="sidebar-national-id"></span>
                </div>
                <div class="mb-2">
                    <strong>الجوال:</strong> <span id="sidebar-phone"></span>
                </div>
                <div class="mb-2">
                    <strong>مكان السكن:</strong> <span id="sidebar-address"></span>
                </div>
                <div class="mb-2">
                    <strong>عدد أفراد الأسرة:</strong> <span id="sidebar-members"></span>
                </div>
                <div class="mb-2">
                    <strong>الحالة الزوجية:</strong> <span id="sidebar-marital"></span>
                </div>
                <div id="sidebar-spouse-info" style="display: none;">
                    <div class="mb-2">
                        <strong>الزوجات:</strong>
                        <div id="sidebar-spouses-list" class="mt-2"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-success w-100 mt-3" id="copy-family-btn">
                    <i class="ti ti-copy me-1"></i> نسخ بيانات الأسرة
                </button>
            </div>
        </div>

        {{-- Card 2: آخر المساعدات --}}
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-3 text-muted">
                    آخر المساعدات (<span id="aids-count">0</span>)
                </h6>
                <div id="aids-list" class="list-group list-group-flush"></div>
                <button type="button" class="btn btn-outline-primary w-100 mt-3" id="load-all-aids-btn" style="display: none;">
                    <i class="ti ti-list me-1"></i> عرض كل المساعدات
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal لتفاصيل المساعدة --}}
<div class="modal fade" id="aidDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المساعدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">بيانات العملية</h6>
                        <div class="mb-2">
                            <strong>المكتب:</strong> <span id="modal-office"></span>
                        </div>
                        <div class="mb-2">
                            <strong>المؤسسة:</strong> <span id="modal-institution"></span>
                        </div>
                        <div class="mb-2">
                            <strong>نوع المساعدة:</strong> <span id="modal-aid-mode"></span>
                        </div>
                        <div class="mb-2">
                            <strong>القيمة:</strong> <span id="modal-aid-value"></span>
                        </div>
                        <div class="mb-2" id="modal-quantity-wrapper" style="display: none;">
                            <strong>الكمية:</strong> <span id="modal-aid-quantity"></span>
                        </div>
                        <div class="mb-2">
                            <strong>التاريخ:</strong> <span id="modal-date"></span>
                        </div>
                        <div class="mb-2">
                            <strong>الحالة:</strong> <span id="modal-status"></span>
                        </div>
                        <div class="mb-2">
                            <strong>المُنشئ:</strong> <span id="modal-creator"></span>
                        </div>
                        <div class="mb-2">
                            <strong>الملاحظات:</strong> <span id="modal-notes"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">بيانات الأسرة</h6>
                        <div class="mb-2">
                            <strong>الاسم:</strong> <span id="modal-family-name"></span>
                        </div>
                        <div class="mb-2">
                            <strong>رقم الهوية:</strong> <span id="modal-family-id"></span>
                        </div>
                        <div class="mb-2">
                            <strong>الجوال:</strong> <span id="modal-family-phone"></span>
                        </div>
                        <div class="mb-2">
                            <strong>عدد الأفراد:</strong> <span id="modal-family-members"></span>
                        </div>
                        <div class="mb-2">
                            <strong>مكان السكن:</strong> <span id="modal-family-address"></span>
                        </div>
                        <div class="mb-2">
                            <strong>الحالة الزوجية:</strong> <span id="modal-family-marital"></span>
                        </div>
                        <div id="modal-spouse-info" style="display: none;">
                            <div class="mb-2">
                                <strong>الزوجات:</strong>
                                <div id="modal-spouses-list" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

{{-- Decision Box Modal (يظهر فقط في spouse_match) --}}
<div class="modal fade" id="decisionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="ti ti-alert-triangle me-2"></i>
                    هذه الهوية مسجلة كزوج/زوجة في أسرة أخرى
                </h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="mb-2">معلومات الأسرة الموجودة:</h6>
                    <div class="mb-1">
                        <strong>المستفيد الأساسي:</strong> <span id="decision-primary-name"></span>
                    </div>
                    <div class="mb-1">
                        <strong>رقم الهوية الأساسي:</strong> <span id="decision-primary-id"></span>
                    </div>
                    <div class="mb-1">
                        <strong>عدد المساعدات السابقة:</strong> <span id="decision-aids-count"></span>
                    </div>
                </div>

                <h6 class="mt-4 mb-3">الرجاء اختيار أحد الخيارات التالية:</h6>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-outline-primary btn-lg text-start" id="attach-to-existing-btn">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-link ti-lg me-3"></i>
                            <div>
                                <div class="fw-bold">إضافة المساعدة للأسرة القديمة فقط</div>
                                <small class="text-muted">لن يتم تعديل بيانات الأسرة</small>
                            </div>
                        </div>
                    </button>

                    <button type="button" class="btn btn-outline-success btn-lg text-start" id="create-new-family-btn">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-user-plus ti-lg me-3"></i>
                            <div>
                                <div class="fw-bold">إنشاء أسرة جديدة بهذه الهوية</div>
                                <small class="text-muted">سيتم إنشاء سجل جديد مستقل</small>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/plugins/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
        $(document).ready(function () {
            let currentFamilyData = null;
            let visiblePolygamousRows = 2;
            let currentProjectStats = null;

            function countFilledSpouses() {
                let filledCount = 0;
                $('.spouse-row').each(function () {
                    const spouseIndex = parseInt($(this).data('spouse-index'), 10);
                    const hasValue = $(`#spouses_${spouseIndex}_full_name`).val().trim() !== '' ||
                        $(`#spouses_${spouseIndex}_national_id`).val().trim() !== '';
                    if (hasValue) {
                        filledCount += 1;
                    }
                });
                return filledCount;
            }

            function toggleAddSpouseButton(isPolygamous) {
                const canAddMore = visiblePolygamousRows < 4;
                $('#add-spouse-btn').toggle(isPolygamous && canAddMore);
            }

            function toggleRemoveSpouseButtons(isPolygamous, isMarried) {
                $('.remove-spouse-btn').each(function () {
                    const spouseIndex = parseInt($(this).data('spouse-index'), 10);
                    const isVisibleRow = isPolygamous
                        ? spouseIndex < visiblePolygamousRows
                        : (isMarried && spouseIndex === 0);
                    const canRemove = isPolygamous && visiblePolygamousRows > 2;
                    $(this).toggle(isVisibleRow && canRemove);
                });
            }

            function getSpouseRowValue(spouseIndex) {
                return {
                    full_name: $(`#spouses_${spouseIndex}_full_name`).val(),
                    national_id: $(`#spouses_${spouseIndex}_national_id`).val(),
                };
            }

            function setSpouseRowValue(spouseIndex, spouse) {
                $(`#spouses_${spouseIndex}_full_name`).val(spouse.full_name || '');
                $(`#spouses_${spouseIndex}_national_id`).val(spouse.national_id || '');
            }

            function toggleSpouseFields() {
                const maritalStatus = $('#marital_status').val();
                const isMarried = maritalStatus === 'married';
                const isPolygamous = maritalStatus === 'polygamous';
                const shouldShowSpouses = isMarried || isPolygamous;
                if (isPolygamous && visiblePolygamousRows < 2) {
                    visiblePolygamousRows = 2;
                }

                $('#spouse-fields').toggle(shouldShowSpouses);

                $('.spouse-row').each(function () {
                    const spouseIndex = parseInt($(this).data('spouse-index'), 10);
                    const shouldShowRow = (isPolygamous && spouseIndex < visiblePolygamousRows) || (isMarried && spouseIndex === 0);

                    $(this).toggle(shouldShowRow);
                    $(this).find('input').prop('disabled', !shouldShowRow);

                    const isRequired = shouldShowRow && (
                        (isMarried && spouseIndex === 0) ||
                        (isPolygamous && (spouseIndex === 0 || spouseIndex === 1))
                    );

                    $(`#spouses_${spouseIndex}_national_id`).prop('required', isRequired);

                    if (!shouldShowRow) {
                        $(this).find('input').val('');
                    }
                });

                toggleAddSpouseButton(isPolygamous);
                toggleRemoveSpouseButtons(isPolygamous, isMarried);
            }

            function clearSpouseFields() {
                $('.spouse-row input').val('');
            }

            function getFamilySpouses(family) {
                if (Array.isArray(family.spouses) && family.spouses.length) {
                    return family.spouses;
                }

                if (family.spouse_national_id || family.spouse_full_name) {
                    return [{
                        full_name: family.spouse_full_name || '',
                        national_id: family.spouse_national_id || '',
                    }];
                }

                return [];
            }

            function fillSpouseFields(spouses) {
                clearSpouseFields();
                spouses.slice(0, 4).forEach(function (spouse, idx) {
                    $(`#spouses_${idx}_full_name`).val(spouse.full_name || '');
                    $(`#spouses_${idx}_national_id`).val(spouse.national_id || '');
                });

                if ($('#marital_status').val() === 'polygamous') {
                    visiblePolygamousRows = Math.max(2, Math.min(4, spouses.length || 2));
                    toggleSpouseFields();
                }
            }

            function renderSpousesList($target, spouses) {
                $target.empty();
                if (!spouses.length) {
                    $target.append('<div class="text-muted">-</div>');
                    return;
                }

                spouses.forEach(function (spouse, idx) {
                    const item = `
                        <div class="small mb-1">
                            <strong>${idx + 1})</strong> ${spouse.full_name || '-'} - ${spouse.national_id || '-'}
                        </div>
                    `;
                    $target.append(item);
                });
            }

            function toggleAidModeFields() {
                const mode = $('#aid_mode').val();
                $('#cash-amount-wrapper').toggle(mode === 'cash');
                $('#aid-item-wrapper').toggle(mode === 'in_kind');
                $('#quantity-wrapper').toggle(mode === 'in_kind');

                $('#cash_amount')
                    .prop('required', mode === 'cash')
                    .prop('disabled', mode !== 'cash');

                $('#aid_item_id')
                    .prop('required', mode === 'in_kind')
                    .prop('disabled', mode !== 'in_kind');

                $('#quantity')
                    .prop('required', mode === 'in_kind')
                    .prop('disabled', mode !== 'in_kind');

                if (mode === 'cash') {
                    $('#aid_item_id').val('').trigger('change');
                    $('#quantity').val('');
                } else if (mode === 'in_kind') {
                    $('#cash_amount').val('');
                }
            }

            $('#marital_status').on('change', toggleSpouseFields);
            $('#aid_mode').on('change', toggleAidModeFields);

            function loadProjectsByInstitution(institutionId, selectedProjectId = null, includeProjectId = null) {
                if (!institutionId) {
                    $('#project_id').html('<option value="">اختر المؤسسة أولاً</option>').prop('disabled', false);
                    return;
                }

                const officeId = $('#office_id').val() || '';
                $('#project_id').html('<option value="">جاري التحميل...</option>').prop('disabled', true);

                let url = officeId
                    ? `/api/institutions/${institutionId}/projects?office_id=${officeId}`
                    : `/api/institutions/${institutionId}/projects`;
                if (includeProjectId) {
                    url += (url.includes('?') ? '&' : '?') + `include_project_id=${includeProjectId}`;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(projects) {
                        let options = '<option value="">اختر المشروع</option>';
                        projects.forEach(function(project) {
                            const suffix = project.by_office ? ' (متبقي للمكتب: ' + project.remaining_beneficiaries + ')' : ' (متبقي: ' + project.remaining_beneficiaries + ')';
                            const closedLabel = project.is_closed ? ' (مغلق)' : '';
                            const displayText = `${project.project_number} - ${project.name}${suffix}${closedLabel}`;
                            const selected = selectedProjectId && project.id == selectedProjectId ? 'selected' : '';
                            options += `<option value="${project.id}" ${selected} data-by-office="${project.by_office ? '1' : '0'}">${displayText}</option>`;
                        });
                        $('#project_id').html(options).prop('disabled', false);

                        if (selectedProjectId) {
                            $('#project_id').trigger('change');
                        }
                    },
                    error: function() {
                        toastr.error('فشل تحميل المشاريع');
                        $('#project_id').html('<option value="">خطأ في التحميل</option>').prop('disabled', false);
                    }
                });
            }

            $('#office_id').on('change', function() {
                const institutionId = $('#institution_id').val();
                currentProjectStats = null;
                updateRemainingMessages();
                if (institutionId) {
                    const includeProjectId = {{ $isEdit ? 'true' : 'false' }} ? ($('#project_id').val() || {{ $distribution->project_id ?? 'null' }}) : null;
                    loadProjectsByInstitution(institutionId, null, includeProjectId);
                }
            });

            $('#institution_id').on('change', function() {
                const institutionId = $(this).val();
                currentProjectStats = null;
                $('#project-notes-display').hide();
                updateRemainingMessages();
                const includeProjectId = {{ $isEdit ? 'true' : 'false' }} ? ($('#project_id').val() || {{ $distribution->project_id ?? 'null' }}) : null;
                loadProjectsByInstitution(institutionId, null, includeProjectId);
            });

            $('#project_id').on('change', function() {
                const projectId = $(this).val();
                currentProjectStats = null;
                $('#project-full-message').hide();
                $('#project-stats-display').hide();
                $('#project-notes-display').hide();
                updateRemainingMessages();

                if (!projectId) {
                    return;
                }

                const officeId = $('#office_id').val() || '';
                const statsUrl = officeId
                    ? `/api/projects/${projectId}/stats?office_id=${officeId}`
                    : `/api/projects/${projectId}/stats`;

                $.ajax({
                    url: statsUrl,
                    method: 'GET',
                    success: function(stats) {
                        currentProjectStats = stats;

                        if (stats.remaining_beneficiaries <= 0) {
                            $('#project-full-message').show();
                            $('button[type="submit"]').prop('disabled', true);
                        } else {
                            $('#project-full-message').hide();
                            $('button[type="submit"]').prop('disabled', false);

                            const label = stats.by_office ? 'متبقي للمكتب' : 'متبقي في المشروع';
                            let displayText = `<i class="fa-solid fa-info-circle me-1"></i>`;
                            if (stats.project_type === 'cash') {
                                displayText += `${label}: ${parseFloat(stats.remaining_amount).toFixed(2)} ₪ | مستفيدين: ${stats.remaining_beneficiaries}`;
                            } else {
                                displayText += `${label}: ${parseFloat(stats.remaining_quantity).toFixed(2)} | مستفيدين: ${stats.remaining_beneficiaries}`;
                            }
                            $('#project-stats-display').html(displayText).show();
                        }

                        if (stats.notes && stats.notes.trim() !== '') {
                            const escapedNotes = $('<div>').text(stats.notes).html().replace(/\n/g, '<br>');
                            $('#project-notes-display')
                                .html(`<div class="d-flex align-items-start gap-2 p-2 rounded bg-light border-start border-warning border-3"><i class="fa-solid fa-sticky-note text-warning mt-1"></i><div><strong class="text-dark small">ملاحظات المشروع:</strong><div class="text-muted small mt-1">${escapedNotes}</div></div></div>`)
                                .show();
                        } else {
                            $('#project-notes-display').hide();
                        }

                        updateRemainingMessages();
                    },
                    error: function() {
                        toastr.error('فشل تحميل بيانات المشروع');
                    }
                });
            });

            $('#cash_amount').on('input', updateRemainingMessages);
            $('#quantity').on('input', updateRemainingMessages);

            function updateRemainingMessages() {
                if (!currentProjectStats) {
                    $('#cash-remaining-message').hide();
                    $('#quantity-remaining-message').hide();
                    $('button[type="submit"]').prop('disabled', false);
                    return;
                }

                let isValid = true;
                const isEdit = {{ $isEdit ? 'true' : 'false' }};

                if (currentProjectStats.project_type === 'cash') {
                    const inputAmount = parseFloat($('#cash_amount').val()) || 0;
                    let availableAmount = currentProjectStats.remaining_amount;
                    
                    if (isEdit) {
                        const oldAmount = parseFloat('{{ $distribution->cash_amount ?? 0 }}') || 0;
                        availableAmount += oldAmount;
                    }
                    
                    const remaining = availableAmount - inputAmount;
                    const color = remaining < 0 ? 'text-danger' : 'text-success';
                    
                    if (remaining < 0) {
                        isValid = false;
                    }

                    $('#cash-remaining-message')
                        .html(`<i class="fa-solid fa-calculator me-1"></i>المتبقي بعد هذه العملية: <span class="${color}">${remaining.toFixed(2)} ₪</span>`)
                        .show();
                    $('#quantity-remaining-message').hide();
                } else if (currentProjectStats.project_type === 'in_kind') {
                    const inputQuantity = parseFloat($('#quantity').val()) || 0;
                    let availableQuantity = currentProjectStats.remaining_quantity;
                    
                    if (isEdit) {
                        const oldQuantity = parseFloat('{{ $distribution->quantity ?? 0 }}') || 0;
                        availableQuantity += oldQuantity;
                    }
                    
                    const remaining = availableQuantity - inputQuantity;
                    const color = remaining < 0 ? 'text-danger' : 'text-success';
                    
                    if (remaining < 0) {
                        isValid = false;
                    }

                    $('#quantity-remaining-message')
                        .html(`<i class="fa-solid fa-calculator me-1"></i>المتبقي بعد هذه العملية: <span class="${color}">${remaining.toFixed(2)}</span>`)
                        .show();
                    $('#cash-remaining-message').hide();
                }

                if (currentProjectStats.remaining_beneficiaries <= 0) {
                    isValid = false;
                }

                $('button[type="submit"]').prop('disabled', !isValid);
            }
            $('#add-spouse-btn').on('click', function () {
                if ($('#marital_status').val() !== 'polygamous') return;
                if (visiblePolygamousRows < 4) {
                    visiblePolygamousRows += 1;
                    toggleSpouseFields();
                }
            });
            $(document).on('click', '.remove-spouse-btn', function () {
                if ($('#marital_status').val() !== 'polygamous') return;
                if (visiblePolygamousRows <= 2) return;

                const removeIndex = parseInt($(this).data('spouse-index'), 10);
                if (removeIndex >= visiblePolygamousRows) return;

                for (let i = removeIndex; i < visiblePolygamousRows - 1; i += 1) {
                    setSpouseRowValue(i, getSpouseRowValue(i + 1));
                }

                setSpouseRowValue(visiblePolygamousRows - 1, { full_name: '', national_id: '' });
                visiblePolygamousRows -= 1;
                toggleSpouseFields();
            });

            if ($('#marital_status').val() === 'polygamous') {
                visiblePolygamousRows = Math.max(2, Math.min(4, countFilledSpouses() || 2));
            }

            toggleSpouseFields();
            toggleAidModeFields();

            @if($isEdit)
                const initialInstitutionId = $('#institution_id').val();
                const initialProjectId = {{ $distribution->project_id ?? 'null' }};
                if (initialInstitutionId) {
                    loadProjectsByInstitution(initialInstitutionId, initialProjectId, initialProjectId);
                }
            @endif

            // حماية UX: إذا تغيّر national_id يدوياً، امسح family_id و resolution_mode وأخفِ Sidebar
            $('#national_id').on('input', function() {
                $('#family_id').val('');
                $('#resolution_mode').val('');
                const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('familySidebar'));
                if (offcanvas) {
                    offcanvas.hide();
                }
            });

            function searchFamily() {
                const nationalId = $('#national_id').val().trim();
                
                if (!nationalId) {
                    toastr.warning('الرجاء إدخال رقم الهوية');
                    return;
                }

                const $btn = $('#search-family-btn');
                const $spinner = $btn.find('.spinner-border');
                const $text = $btn.find('.btn-text');

                // تعطيل الزر وإظهار spinner
                $btn.prop('disabled', true);
                $text.addClass('d-none');
                $spinner.removeClass('d-none');

                // طلب AJAX
                $.ajax({
                    url: `/api/families/search-by-national-id/${nationalId}`,
                    method: 'GET',
                    success: function(response) {
                        const matchType = response.match_type;

                        if (matchType === 'no_match') {
                            toastr.info('لم يتم العثور على سجل لهذه الهوية - يمكنك إنشاء أسرة جديدة');
                            return;
                        }

                        currentFamilyData = response;

                        if (matchType === 'primary_match') {
                            // تطابق أساسي - عرض Sidebar عادي
                            displayFamilyInSidebar(response, 'primary');
                            const offcanvas = new bootstrap.Offcanvas(document.getElementById('familySidebar'));
                            offcanvas.show();
                        } else if (matchType === 'spouse_match') {
                            // تطابق زوج - عرض Decision Box
                            showDecisionBox(response);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('حدث خطأ أثناء البحث');
                        console.error(xhr);
                    },
                    complete: function() {
                        // إعادة تفعيل الزر
                        $btn.prop('disabled', false);
                        $text.removeClass('d-none');
                        $spinner.addClass('d-none');
                    }
                });
            }
            // زر البحث
            $('#search-family-btn').on('click', function() {
                searchFamily();
            });
            $('#national_id').on('blur', function() {
                if ($('#national_id').val().trim()) {
                    searchFamily();
                }
            });

            // عرض بيانات الأسرة في Sidebar
            function displayFamilyInSidebar(data, matchType) {
                const family = data.family;
                const aids = data.last_10_aids || [];
                const aidsTotal = data.total_aids || 0;

                // رسالة التطابق (فقط للـ primary_match)
                if (matchType === 'primary') {
                    $('#match-message').text('تم العثور على نفس الشخص').show();
                } else {
                    $('#match-message').hide();
                }

                // البيانات الأساسية
                $('#sidebar-name').text(family.full_name || '-');
                $('#sidebar-national-id').text(family.national_id || '-');
                $('#sidebar-phone').text(family.phone || '-');
                $('#sidebar-address').text(family.address || '-');
                $('#sidebar-members').text(family.family_members_count || '-');
                
                const maritalStatusText = {
                    'single': 'أعزب/عزباء',
                    'married': 'متزوج/ة',
                    'polygamous': 'متعدد الزوجات',
                    'widowed': 'أرمل/ة',
                    'divorced': 'مطلق/ة'
                };
                $('#sidebar-marital').text(maritalStatusText[family.marital_status] || '-');

                // بيانات الزوجات
                const spouses = getFamilySpouses(family);
                if ((family.marital_status === 'married' || family.marital_status === 'polygamous') && spouses.length) {
                    renderSpousesList($('#sidebar-spouses-list'), spouses);
                    $('#sidebar-spouse-info').show();
                } else {
                    $('#sidebar-spouse-info').hide();
                }

                // عرض المساعدات
                $('#aids-count').text(aidsTotal);
                displayAidsList(aids);

                // زر "عرض كل المساعدات"
                if (aidsTotal > 10) {
                    $('#load-all-aids-btn').show();
                } else {
                    $('#load-all-aids-btn').hide();
                }
            }

            // عرض قائمة المساعدات
            function displayAidsList(aids) {
                const $list = $('#aids-list');
                $list.empty();

                if (aids.length === 0) {
                    $list.append('<div class="text-muted text-center py-3">لا توجد مساعدات سابقة</div>');
                    return;
                }

                aids.forEach(function(aid) {
                    const quantityBadge = aid.aid_mode === 'عينية' && aid.quantity && aid.quantity !== '-'
                        ? `<div><small class="text-muted">الكمية: ${aid.quantity}</small></div>`
                        : '';
                    const item = `
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-auto">
                                <div class="fw-bold">${aid.office_name}</div>
                                <small>${aid.institution_name || '-'}</small>
                                <br>
                                <small>${aid.distributed_at} - ${aid.aid_mode}</small>
                                <div>${aid.aid_value}</div>
                                ${quantityBadge}
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary view-aid-btn" data-aid-id="${aid.id}">
                                <i class="ti ti-eye"></i> عرض
                            </button>
                        </div>
                    `;
                    $list.append(item);
                });
            }

            // عرض Decision Box (فقط في spouse_match)
            function showDecisionBox(data) {
                const family = data.family;
                const aidsTotal = data.total_aids || 0;

                // تعبئة معلومات الأسرة الموجودة
                $('#decision-primary-name').text(family.full_name || '-');
                $('#decision-primary-id').text(family.national_id || '-');
                $('#decision-aids-count').text(aidsTotal);

                // فتح Modal
                const modal = new bootstrap.Modal(document.getElementById('decisionModal'));
                modal.show();
            }

            // الخيار 1: إضافة للأسرة القديمة
            $('#attach-to-existing-btn').on('click', function() {
                if (!currentFamilyData) return;

                const family = currentFamilyData.family;

                // نسخ بيانات الأسرة القديمة إلى الفورم
                $('#primary_name').val(family.full_name || '');
                $('#national_id').val(family.national_id || $('#national_id').val()); // نبقي الهوية المُدخلة
                $('#mobile').val(family.phone || '');
                $('#family_members_count').val(family.family_members_count || '');
                $('#housing_location').val(family.address || '');
                $('#marital_status').val(family.marital_status || 'single').trigger('change');
                fillSpouseFields(getFamilySpouses(family));

                // نضع family_id و resolution_mode
                $('#family_id').val(family.id);
                $('#resolution_mode').val('attach_to_existing');

                // إغلاق Decision Modal
                const decisionModal = bootstrap.Modal.getInstance(document.getElementById('decisionModal'));
                decisionModal.hide();

                // عرض Sidebar مع البيانات
                displayFamilyInSidebar(currentFamilyData, 'spouse_attach');
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('familySidebar'));
                offcanvas.show();

                toastr.success('تم نسخ بيانات الأسرة - سيتم إضافة المساعدة للأسرة الموجودة');
            });

            // الخيار 2: إنشاء أسرة جديدة
            $('#create-new-family-btn').on('click', function() {
                // نضع resolution_mode فقط
                $('#resolution_mode').val('create_new_family');
                $('#family_id').val('');

                // إغلاق Decision Modal
                const decisionModal = bootstrap.Modal.getInstance(document.getElementById('decisionModal'));
                decisionModal.hide();

                toastr.info('سيتم إنشاء أسرة جديدة - أكمل البيانات واحفظ');
            });

            // نسخ بيانات الأسرة (فقط في primary_match)
            $('#copy-family-btn').on('click', function() {
                if (!currentFamilyData) return;

                const family = currentFamilyData.family;

                // تعبئة الحقول
                $('#primary_name').val(family.full_name || '');
                $('#national_id').val(family.national_id || '');
                $('#mobile').val(family.phone || '');
                $('#family_members_count').val(family.family_members_count || '');
                $('#housing_location').val(family.address || '');
                $('#marital_status').val(family.marital_status || 'single').trigger('change');
                fillSpouseFields(getFamilySpouses(family));

                // تخزين family_id
                $('#family_id').val(family.id);

                toastr.success('تم نسخ بيانات الأسرة بنجاح');
            });

            // عرض تفاصيل مساعدة في Modal
            $(document).on('click', '.view-aid-btn', function() {
                const aidId = $(this).data('aid-id');

                $.ajax({
                    url: `/api/aid-distributions/${aidId}`,
                    method: 'GET',
                    success: function(response) {
                        const dist = response.distribution;
                        const family = response.family;

                        // بيانات العملية
                        $('#modal-office').text(dist.office_name);
                        $('#modal-institution').text(dist.institution_name || '-');
                        $('#modal-aid-mode').text(dist.aid_mode);
                        $('#modal-aid-value').text(
                            dist.aid_mode === 'نقدية' 
                                ? (dist.cash_amount ? dist.cash_amount + ' ₪' : '-')
                                : dist.aid_item_name
                        );
                        if (dist.aid_mode === 'عينية') {
                            $('#modal-aid-quantity').text(dist.quantity ? dist.quantity : '-');
                            $('#modal-quantity-wrapper').show();
                        } else {
                            $('#modal-aid-quantity').text('-');
                            $('#modal-quantity-wrapper').hide();
                        }
                        $('#modal-date').text(dist.distributed_at);
                        $('#modal-status').text(dist.status === 'active' ? 'نشط' : 'ملغي');
                        $('#modal-creator').text(dist.creator_name);
                        $('#modal-notes').text(dist.notes || '-');

                        // بيانات الأسرة
                        $('#modal-family-name').text(family.full_name);
                        $('#modal-family-id').text(family.national_id);
                        $('#modal-family-phone').text(family.phone);
                        $('#modal-family-members').text(family.family_members_count);
                        $('#modal-family-address').text(family.address);
                        $('#modal-family-marital').text(family.marital_status);

                        const spouses = getFamilySpouses(family);
                        if (spouses.length) {
                            renderSpousesList($('#modal-spouses-list'), spouses);
                            $('#modal-spouse-info').show();
                        } else {
                            $('#modal-spouse-info').hide();
                        }

                        // فتح Modal
                        const modal = new bootstrap.Modal(document.getElementById('aidDetailModal'));
                        modal.show();
                    },
                    error: function(xhr) {
                        toastr.error('حدث خطأ أثناء جلب التفاصيل');
                        console.error(xhr);
                    }
                });
            });

            // عرض كل المساعدات (Lazy Load)
            $('#load-all-aids-btn').on('click', function() {
                if (!currentFamilyData) return;

                const familyId = currentFamilyData.family.id;
                const $btn = $(this);

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> جاري التحميل...');

                $.ajax({
                    url: `/api/families/${familyId}/all-aids`,
                    method: 'GET',
                    success: function(response) {
                        displayAidsList(response.aids);
                        $btn.hide();
                        toastr.success(`تم تحميل ${response.total} مساعدة`);
                    },
                    error: function(xhr) {
                        toastr.error('حدث خطأ أثناء تحميل المساعدات');
                        console.error(xhr);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="ti ti-list me-1"></i> عرض كل المساعدات');
                    }
                });
            });
        });
    </script>
@endpush
