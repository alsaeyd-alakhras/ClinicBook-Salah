<x-front-layout>
    <div class="col-xl-12">
        <h6 class="text-muted">إعدادات ثوابت النظام</h6>
        <div class="card">
            <div class="card-body">
                <div class="nav-align-top mb-6">
                    <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
                        <li class="nav-item mb-1 mb-sm-0">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#menu1" aria-controls="menu1"
                                aria-selected="true">
                                <span class="d-none d-sm-block">
                                    <i class="fa-solid fa-wallet"></i> مبلغ السلفة
                                    <i class="ti ti-home ti-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item mb-1 mb-sm-0">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#menu2" aria-controls="menu2"
                                aria-selected="false">
                                <span class="d-none d-sm-block"> % نسبة نهاية الخدمة</span><i
                                    class="ti ti-user ti-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#menu3" aria-controls="menu3"
                                aria-selected="false">
                                <span class="d-none d-sm-block"><i class="fa-solid fa-dollar-sign"></i>
                                    رواتب الصحة المثبتين</span><i class="ti ti-message-dots ti-sm d-sm-none"></i>
                            </button>
                        </li>
                    </ul>
                    <form action="{{ route('dashboard.constants.store') }}" method="post">
                        @csrf
                        <div class="tab-content">
                            <div  class="tab-pane fade show active" role="tabpanel" id="menu1">
                                <h2 class="h3 mt-4">تحديد ملبغ السلفة حسب حالة الدوام للثبتين</h2>
                                <div class="row">
                                    <div class="col-md-6 my-2">
                                        <label for="advance_payment_permanent" class="form-label" style="font-size: 18px;">مبلغ السلفة - مداوم</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'advance_payment_permanent')->first() ? $constants->where('key', 'advance_payment_permanent')->first()->value : 0 }}" min="0" name="advance_payment_permanent" placeholder="1000" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="advance_payment_non_permanent" class="form-label" style="font-size: 18px;">مبلغ السلفة - غير مداوم</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'advance_payment_non_permanent')->first() ? $constants->where('key', 'advance_payment_non_permanent')->first()->value : 0 }}" min="0" name="advance_payment_non_permanent" placeholder="1000" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="advance_payment_rate" class="form-label" style="font-size: 18px;">مبلغ السلفة - نسبة</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'advance_payment_rate')->first() ? $constants->where('key', 'advance_payment_rate')->first()->value : 0 }}" min="0" name="advance_payment_rate" placeholder="1000" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="advance_payment_riyadh" class="form-label" style="font-size: 18px;">مبلغ السلفة - رياض</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'advance_payment_riyadh')->first() ? $constants->where('key', 'advance_payment_riyadh')->first()->value : 0 }}" min="0" name="advance_payment_riyadh" placeholder="1000" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end align-items-center mt-4">
                                    <button type="submit" class="btn btn-primary mx-2 col-2">
                                        تعديل
                                    </button>
                                </div>
                            </div>
                            <div  class="tab-pane fade" role="tabpanel" id="menu2">
                                <h2 class="h3 mt-4">تحديد نسبة الخدمة</h2>
                                <div class="row">
                                    <div class="col-md-6 my-2">
                                        <label for="termination_service" class="form-label" style="font-size: 18px;">نسبة نهاية الخدمة للمؤسسة (الإدخار للمؤسسة)</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'termination_service')->first() ? $constants->where('key', 'termination_service')->first()->value : 0 }}" min="0" name="termination_service" placeholder="10" class="d-inline" />
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="termination_employee" class="form-label" style="font-size: 18px;">نسبة إدخار للموظف</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'termination_employee')->first() ? $constants->where('key', 'termination_employee')->first()->value : 0 }}" min="0" name="termination_employee" placeholder="5" class="d-inline" />
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row justify-content-end align-items-center mt-4">
                                    <button type="submit" class="btn btn-primary mx-2 col-2">
                                        تعديل
                                    </button>
                                </div>
                            </div>
                            <div  class="tab-pane fade" role="tabpanel" id="menu3">
                                <h2 class="h3 mt-4">تحديد رواتب الصحة المثبتين</h2>
                                <div class="row">
                                    <div class="col-md-6 my-2">
                                        <label for="health_bachelor" class="form-label" style="font-size: 18px;">البكالوريوس</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'health_bachelor')->first() ? $constants->where('key', 'health_bachelor')->first()->value : 0 }}" min="0" name="health_bachelor" placeholder="900" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="health_diploma" class="form-label" style="font-size: 18px;">الدبلوم</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'health_diploma')->first() ? $constants->where('key', 'health_diploma')->first()->value : 0 }}" min="0" name="health_diploma" placeholder="800" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="health_secondary" class="form-label" style="font-size: 18px;">ثانوية عامة</label>
                                        <div class="input-group">
                                            <x-form.input required type="number" value="{{ $constants->where('key', 'health_secondary')->first() ? $constants->where('key', 'health_secondary')->first()->value : 0 }}" min="0" name="health_secondary" placeholder="700" class="d-inline" />
                                            <span class="input-group-text">₪</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end align-items-center mt-4">
                                    <button type="submit" class="btn btn-primary mx-2 col-2">
                                        تعديل
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab panes -->
    

    @push('scripts')
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    @endpush



</x-front-layout>
