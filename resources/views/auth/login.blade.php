@push('styles')
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
@endpush
@include('layouts.partials.head', ['title' => Config::get('app.name'),'template' => 'horizontal-menu-template'])
<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="{{ route('dashboard.home') }}" class="app-brand auth-cover-brand" style="overflow: visible">
        <span class="app-brand-logo demo" style="overflow: visible; width: 100px !important;">
            <img src="{{ asset('imgs/logo-brand.png') }}" width="100" alt="" />
        </span>
        <span class="app-brand-text demo text-heading fw-bold ms-6" style="font-size: 35px;"> - {{ Config::get('app.name') }}</span>
    </a>
    <!-- /Logo -->
    <div class="m-0 authentication-inner row">
        <!-- /Left Text -->
        <div class="p-0 d-none d-lg-flex col-lg-8">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/img/illustrations/auth-login-illustration-light.png')}}" alt="auth-login-cover"
                    class="my-5 auth-illustration" data-app-light-img="illustrations/auth-login-illustration-light.png"
                    data-app-dark-img="illustrations/auth-login-illustration-dark.png" />

                <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png')}}" alt="auth-login-cover"
                    class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png" />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="p-6 d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12">
            <div class="pt-5 mx-auto mt-12 w-px-400">
                <h4 class="mb-1">
                    👋 مرحبا بك مع
                    <img src="{{ asset('imgs/logo-brand.png') }}" width="100" alt="" />
                </h4>
                <p class="mb-6">الرجاء تسجيل الدخول لبداية العمل </p>

                <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="username" class="form-label">اسم المستخدم / الإيميل</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="الرجاء إدخال اسم المستخدم أو الإيميل" autofocus />
                    </div>
                    <div class="mb-6 form-password-toggle">
                        <label class="form-label" for="password">كلمة المرور</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="cursor-pointer input-group-text"><i class="ti ti-eye-off"></i></span>
                        </div>
                    </div>
                    <div class="my-8">
                        <div class="d-flex justify-content-between">
                            <div class="mb-0 form-check ms-2">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="rememberMe" />
                                <label class="form-check-label" for="remember-me">تذكرني </label>
                            </div>
                            {{-- <a href="auth-forgot-password-cover.html">
                                <p class="mb-0">Forgot Password?</p>
                            </a> --}}
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100">تسجيل الدخول</button>
                </form>

                {{-- <p class="text-center">
                    <span>New on our platform?</span>
                    <a href="auth-register-cover.html">
                        <span>Create an account</span>
                    </a>
                </p> --}}
                {{--
                <div class="my-6 divider">
                    <div class="divider-text">or</div>
                </div>

                <div class="d-flex justify-content-center">
                    <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-facebook me-1_5">
                        <i class="tf-icons ti ti-brand-facebook-filled"></i>
                    </a>

                    <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-twitter me-1_5">
                        <i class="tf-icons ti ti-brand-twitter-filled"></i>
                    </a>

                    <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-github me-1_5">
                        <i class="tf-icons ti ti-brand-github-filled"></i>
                    </a>

                    <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-google-plus">
                        <i class="tf-icons ti ti-brand-google-filled"></i>
                    </a>
                </div> --}}
            </div>
        </div>
        <!-- /Login -->
    </div>
</div>

@push('vendor-js')
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/pages-auth.js?v=0.2') }}"></script>
@endpush
@include('layouts.partials.end')
