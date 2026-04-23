<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-xxl">
        <div class="py-0 navbar-brand app-brand demo d-none d-xl-flex me-4" style="overflow: visible;">
            <a href="https://alsaeyd-alakhras.vercel.app/ar" target="_blank" class="app-brand-link" style="overflow: visible;">
                <span class="app-brand-logo demo" style="overflow: visible; width: 100px !important;">
                    <img src=" {{ asset('imgs/logo-brand.png') }}" alt="Logo" width="100">
                </span>
                <span class="app-brand-text demo menu-text fw-bold">- {{ $title }}</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="align-middle ti ti-x ti-md"></i>
            </a>
        </div>

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="px-0 nav-item nav-link me-xl-4" href="javascript:void(0)">
                <i class="ti ti-menu-2 ti-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="flex-row navbar-nav align-items-center ms-auto">
                {{ $extra_nav ?? '' }}
                @if(Auth::user()->can('view', 'App\\Models\\Office') || Auth::user()->can('view', 'App\\Models\\Institution'))
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                            href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="البيانات">
                            <i class="ti ti-database ti-md"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @can('view', 'App\\Models\\Office')
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.offices.index') }}">
                                        <i class="fa-solid fa-building me-2"></i>
                                        <span class="align-middle">المكاتب</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view', 'App\\Models\\Institution')
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.institutions.index') }}">
                                        <i class="fa-solid fa-landmark me-2"></i>
                                        <span class="align-middle">المؤسسات</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                <!-- Search -->
                {{-- <li class="nav-item navbar-search-wrapper">
                    <a class="nav-link btn btn-text-secondary btn-icon rounded-pill search-toggler"
                        href="javascript:void(0);">
                        <i class="ti ti-search ti-md"></i>
                    </a>
                </li> --}}
                <!-- /Search -->
                <!-- Style Switcher -->
                {{-- <li class="nav-item dropdown-style-switcher dropdown">
                    <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="ti ti-md"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                <span class="align-middle"><i class="ti ti-sun ti-md me-3"></i>Light</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                <span class="align-middle"><i
                                        class="ti ti-moon-stars ti-md me-3"></i>Dark</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                <span class="align-middle"><i
                                        class="ti ti-device-desktop-analytics ti-md me-3"></i>System</span>
                            </a>
                        </li>
                    </ul>
                </li> --}}
                <!-- / Style Switcher-->

                <!-- Quick links  -->
                {{-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
                    <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <i class="ti ti-layout-grid-add ti-md"></i>
                    </a>
                    <div class="p-0 dropdown-menu dropdown-menu-end">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="py-3 dropdown-header d-flex align-items-center">
                                <h6 class="mb-0 me-auto">Shortcuts</h6>
                                <a href="javascript:void(0)"
                                    class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-add"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i
                                        class="ti ti-plus text-heading"></i></a>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container">
                            <div class="overflow-visible row row-bordered g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-calendar ti-26px text-heading"></i>
                                    </span>
                                    <a href="app-calendar.html" class="stretched-link">Calendar</a>
                                    <small>Appointments</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-file-dollar ti-26px text-heading"></i>
                                    </span>
                                    <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>
                                    <small>Manage Accounts</small>
                                </div>
                            </div>
                            <div class="overflow-visible row row-bordered g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-user ti-26px text-heading"></i>
                                    </span>
                                    <a href="app-user-list.html" class="stretched-link">User App</a>
                                    <small>Manage Users</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-users ti-26px text-heading"></i>
                                    </span>
                                    <a href="app-access-roles.html" class="stretched-link">Role Management</a>
                                    <small>Permission</small>
                                </div>
                            </div>
                            <div class="overflow-visible row row-bordered g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-device-desktop-analytics ti-26px text-heading"></i>
                                    </span>
                                    <a href="index.html" class="stretched-link">Dashboard</a>
                                    <small>User Dashboard</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-settings ti-26px text-heading"></i>
                                    </span>
                                    <a href="pages-account-settings-account.html"
                                        class="stretched-link">Setting</a>
                                    <small>Account Settings</small>
                                </div>
                            </div>
                            <div class="overflow-visible row row-bordered g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-help ti-26px text-heading"></i>
                                    </span>
                                    <a href="pages-faq.html" class="stretched-link">FAQs</a>
                                    <small>FAQs & Articles</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="mb-3 dropdown-shortcuts-icon rounded-circle">
                                        <i class="ti ti-square ti-26px text-heading"></i>
                                    </span>
                                    <a href="modal-examples.html" class="stretched-link">Modals</a>
                                    <small>Useful Popups</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </li> --}}
                <!-- Quick links -->

                <!-- Notification -->
                {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <span class="position-relative">
                            <i class="ti ti-bell ti-md"></i>
                            <span
                                class="border badge rounded-pill bg-danger badge-dot badge-notifications"></span>
                        </span>
                    </a>
                    <ul class="p-0 dropdown-menu dropdown-menu-end">
                        <li class="dropdown-menu-header border-bottom">
                            <div class="py-3 dropdown-header d-flex align-items-center">
                                <h6 class="mb-0 me-auto">Notification</h6>
                                <div class="mb-0 d-flex align-items-center h6">
                                    <span class="badge bg-label-primary me-2">8 New</span>
                                    <a href="javascript:void(0)"
                                        class="btn btn-text-secondary rounded-pill btn-icon dropdown-notifications-all"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Mark all as read"><i
                                            class="ti ti-mail-opened text-heading"></i></a>
                                </div>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/1.png" alt
                                                    class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Congratulation Lettie 🎉</h6>
                                            <small class="mb-1 d-block text-body">Won the monthly best seller
                                                gold badge</small>
                                            <small class="text-muted">1h ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-danger">CF</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Charles Franklin</h6>
                                            <small class="mb-1 d-block text-body">Accepted your
                                                connection</small>
                                            <small class="text-muted">12hr ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/2.png" alt
                                                    class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">New Message ✉️</h6>
                                            <small class="mb-1 d-block text-body">You have new message from
                                                Natalie</small>
                                            <small class="text-muted">1h ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                                        class="ti ti-shopping-cart"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Whoo! You have new order 🛒</h6>
                                            <small class="mb-1 d-block text-body">ACME Inc. made new order
                                                $1,154</small>
                                            <small class="text-muted">1 day ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/9.png" alt
                                                    class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Application has been approved 🚀</h6>
                                            <small class="mb-1 d-block text-body">Your ABC project application
                                                has been approved.</small>
                                            <small class="text-muted">2 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                                        class="ti ti-chart-pie"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Monthly report is generated</h6>
                                            <small class="mb-1 d-block text-body">July monthly financial report
                                                is generated </small>
                                            <small class="text-muted">3 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/5.png" alt
                                                    class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Send connection request</h6>
                                            <small class="mb-1 d-block text-body">Peter sent you connection
                                                request</small>
                                            <small class="text-muted">4 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/6.png" alt
                                                    class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">New message from Jane</h6>
                                            <small class="mb-1 d-block text-body">Your have new message from
                                                Jane</small>
                                            <small class="text-muted">5 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-warning"><i
                                                        class="ti ti-alert-triangle"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">CPU is running high</h6>
                                            <small class="mb-1 d-block text-body">CPU Utilization Percent is
                                                currently at 88.63%,</small>
                                            <small class="text-muted">5 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-notifications-archive"><span
                                                    class="ti ti-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="border-top">
                            <div class="p-4 d-grid">
                                <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                                    <small class="align-middle">View all notifications</small>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li> --}}
                <!--/ Notification -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="p-0 nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->avatar_url }}" alt class="rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="mt-0 dropdown-item" href="{{ route('dashboard.users.show', Auth::user()->id) }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->avatar_url }}" alt
                                                class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                        <small class="text-muted">{{ Auth::user()->email ?? Auth::user()->username }}</small>
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

        <!-- Search Small Screens -->
        <div class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
            <input type="text" class="border-0 form-control search-input" placeholder="Search..."
                aria-label="Search..." />
            <i class="cursor-pointer ti ti-x search-toggler"></i>
        </div>
    </div>
</nav>
