<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="py-0 navbar-brand app-brand demo d-none d-xl-flex me-4" style="overflow: visible">
        <a href="{{ route('dashboard.home') }}" class="app-brand-link" style="overflow: visible;">
            <span class="app-brand-logo demo" style="overflow: visible; width: 100px !important;">
                <img src=" {{ asset('imgs/logo-brand.png') }}" alt="Logo" width="100">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ $title }}</span>
        </a>
    </div>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="align-middle ti ti-x ti-md"></i>
        </a>
    </div>


    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        {{ $extra_nav ?? '' }}
        <div class="navbar-nav align-items-center">
            <div class="nav-item dropdown-style-switcher dropdown">
                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                    href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-start dropdown-styles" style="right: auto; left: 0;">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class="ti ti-sun me-3"></i>فاتح</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="ti ti-moon-stars me-3"></i>مظلم</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                            <span class="align-middle"><i class="ti ti-device-desktop-analytics me-3"></i>حسب
                                النظام</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <ul class="flex-row navbar-nav align-items-center">
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="p-0 nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ Auth::user()->avatar_url }}" alt class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="mt-0 dropdown-item" href="#">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar avatar-online">
                                        <img src="{{ Auth::user()->avatar_url }}" alt class="rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="my-1 dropdown-divider mx-n2"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard.users.show', Auth::user()->id) }}">
                            <i class="ti ti-user me-3 ti-md"></i><span class="align-middle">
                                الملف الشخصي</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard.profile.settings') }}">
                            <i class="ti ti-settings me-3 ti-md"></i><span class="align-middle">الإعدادات</span>
                        </a>
                    </li>
                    {{-- <li>
                        <a class="dropdown-item" href="#">
                            <span class="align-middle d-flex align-items-center">
                                <i class="flex-shrink-0 ti ti-file-dollar me-3 ti-md"></i>
                                <span class="align-middle flex-grow-1"></span>
                                <span
                                    class="flex-shrink-0 badge bg-danger d-flex align-items-center justify-content-center">4</span>
                            </span>
                        </a>
                    </li> --}}
                    <li>
                        <div class="my-1 dropdown-divider mx-n2"></div>
                    </li>
                    <li>
                        <div class="px-2 pt-2 pb-1 d-grid">
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger d-flex" href="javascript:void(0);">
                                    <small class="align-middle">تسجيل الخروج</small>
                                    <i class="ti ti-logout ms-2 ti-14px"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
