<div class="row">
    <div class="col-md-12">
        <div class="card">
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-6">
                    <img src="{{ $user->avatar_url }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded"
                        id="uploadedAvatar" style="object-fit: cover;" />
                    <div class="button-wrapper">
                        <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">رفع صورة جديدة</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" name="avatarUpload" id="upload" class="account-file-input" hidden
                                accept="image/png, image/jpeg" />
                        </label>
                        <button type="button" class="btn btn-label-secondary account-image-reset mb-4">
                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">مسح</span>
                        </button>
                        <div>مسموح JPG, GIF or PNG. بأعلى حجم هو 800K</div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="row">
                    <div class="mb-4 col-md-4">
                        <x-form.input label="الاسم" :value="$user->name" name="name" placeholder="محمد ...." required
                            autofocus />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input label="اسم المستخدم" :value="$user->username" name="username"
                            placeholder="username" required />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="email" label="البريد الالكتروني" :value="$user->email" name="email"
                            placeholder="example@gmail.com" />
                    </div>
                    <div class="mb-4 col-md-4">
                        @if (isset($btn_label))
                            <x-form.input type="password" label="كلمة المرور" name="password" placeholder="****" />
                        @else
                            <x-form.input type="password" label="كلمة المرور" name="password" placeholder="****" required />
                        @endif
                    </div>
                    <div class="mb-4 col-md-4">
                        @if (!isset($btn_label))
                            <x-form.input type="password" label="تأكيد كلمة المرور" name="confirm_password"
                                placeholder="****" required />
                        @endif
                    </div>
                    @if(!isset($settings_profile))
                    <div class="mb-4 form-group col-md-4">
                        <label for="user_type">نوع المستخدم</label>
                        <select class="text-center form-select" name="user_type" id="user_type"
                            data-placeholder="اختر شعبة">
                            <option value="" label="فتح القائمة">
                            <option value="employee" @selected($user->user_type == 'employee')>الموظف</option>
                            <option value="admin" @selected($user->user_type == 'admin')>المدير</option>
                        </select>
                    </div>
                    <div class="mb-4 form-group col-md-4">
                        <label for="office">المكتب</label>
                        <select class="text-center form-select" name="office_id" id="office">
                            <option value="" label="فتح القائمة">
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}" @selected($office->id == $user->office_id)>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                        </select>
                    </div>
                    @endif
                    <x-form.input type="hidden" name="is_active" value="1" />
                </div>
                @if(!isset($settings_profile))
                    <div class="row">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>صلاحيات المستخدم</th>
                                        <th colspan="7">التفعيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (app('abilities') as $abilities_name => $ability_array)
                                        @php
                                            // تحقق إذا كانت جميع الصلاحيات الفرعية موجودة في صلاحيات المستخدم
                                            $userAbilities = $user->roles()->pluck('role_name')->toArray();
                                            $allAbilities = array_map(function ($key) use ($abilities_name) {
                                                return $abilities_name . '.' . $key;
                                            }, array_keys(array_filter($ability_array, fn($key) => $key !== 'name', ARRAY_FILTER_USE_KEY)));
                                            $isAllChecked = empty(array_diff($allAbilities, $userAbilities));
                                        @endphp
                                        <tr>
                                            <td class="table-light">
                                                <!-- Checkbox رئيسي لتحديد الكل -->
                                                <input class="form-check-input master-checkbox" type="checkbox"
                                                    id="master-{{ $abilities_name }}"
                                                    data-target="ability-group-{{ $abilities_name }}" @checked($isAllChecked)>
                                                <label for="master-{{ $abilities_name }}">
                                                    {{ $ability_array['name'] }}
                                                </label>
                                            </td>
                                            @foreach ($ability_array as $ability_name => $ability)
                                                @if ($ability_name != 'name')
                                                    <td>
                                                        <div class="custom-control custom-checkbox" style="margin-right: 0;">
                                                            <input class="form-check-input ability-group-{{ $abilities_name }}"
                                                                type="checkbox" name="abilities[]"
                                                                id="ability-{{ $abilities_name . '.' . $ability_name }}"
                                                                value="{{ $abilities_name . '.' . $ability_name }}"
                                                                @checked(in_array($abilities_name . '.' . $ability_name, $user->roles()->pluck('role_name')->toArray()))>
                                                            <label class="form-check-label"
                                                                for="ability-{{ $abilities_name . '.' . $ability_name }}">
                                                                {{ $ability }}
                                                            </label>
                                                        </div>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-3">
                        {{ $btn_label ?? 'أضف' }}
                    </button>
                </div>
            </div>
            <!-- /Account -->
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
        <script>
            $(document).ready(function () {
                const isCreateUserForm = @json(!isset($btn_label));
                const employeeDefaultAbilities = ['aiddistributions.view', 'aiddistributions.create', 'aiddistributions.update'];

                // عند تغيير حالة Master Checkbox
                $('.master-checkbox').on('change', function () {
                    // الحصول على المجموعة المرتبطة بـ Master Checkbox
                    const targetClass = $(this).data('target');

                    // تحديد/إلغاء تحديد جميع الخيارات الفرعية
                    $(`.${targetClass}`).prop('checked', $(this).prop('checked'));
                });

                function syncMasterCheckboxes() {
                    $('.master-checkbox').each(function () {
                        const targetClass = $(this).data('target');
                        const $children = $(`.${targetClass}`);
                        const allChecked = $children.length > 0 && $children.filter(':checked').length === $children.length;
                        $(this).prop('checked', allChecked);
                    });
                }

                function applyEmployeeDefaultAbilitiesOnCreate() {
                    if (!isCreateUserForm || $('#user_type').val() !== 'employee') {
                        return;
                    }

                    $('input[name="abilities[]"]').each(function () {
                        const role = $(this).val();
                        if (employeeDefaultAbilities.includes(role)) {
                            $(this).prop('checked', true);
                        }
                    });

                    syncMasterCheckboxes();
                }

                $('#user_type').on('change', function () {
                    const userType = $(this).val();
                    if (userType != 'employee') {
                        $('#office').prop('disabled', true);
                    } else {
                        $('#office').prop('disabled', false);
                    }
                    applyEmployeeDefaultAbilitiesOnCreate();
                });

                $('#user_type').trigger('change');
            });

        </script>
    @endpush
</div>