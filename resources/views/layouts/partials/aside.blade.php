<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo"  style="overflow: visible">
        <a href="{{ route('dashboard.home') }}" class="app-brand-link">
            <span class="app-brand-logo demo" style="overflow: visible">
                <img src=" {{ asset('imgs/logo-brand.png') }}" alt="Logo" width="60">
            </span>
            {{-- <span class="app-brand-text demo menu-text fw-bold">{{ $title }}</span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="align-middle ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="align-middle ti ti-x d-block d-xl-none ti-md"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="py-1 menu-inner">
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps &amp; Pages">العامة</span>
        </li>
        <!-- Page -->
        <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
            <a href="{{ route('dashboard.home') }}" class="menu-link">
                <i class="fa-solid fa-house me-2"></i>
                <div data-i18n="home">الرئيسية</div>
            </a>
        </li>
        @can('view','App\\Models\AccreditationProject')
        <li class="menu-item {{ request()->is('accreditations/*') || request()->is('accreditations') ? 'active' : '' }}">
            <a href="{{ route('dashboard.accreditations.index') }}" class="menu-link">
                <i class="fa-solid fa-check-circle me-2"></i>
                <div data-i18n="accreditations">الإعتمادية</div>
            </a>
        </li>
        @endcan
        @can('reports.view')
        <li class="menu-item {{ request()->is('reports/*') || request()->is('reports') ? 'active' : '' }}">
            <a href="{{ route('dashboard.reports.index') }}" class="menu-link">
                <i class="fa-solid fa-file-alt me-2"></i>
                <div data-i18n="reports">التقارير</div>
            </a>
        </li>
        @endcan
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps &amp; Pages">البيانات الأساسية</span>
        </li>
        @can('view','App\\Models\Allocation')
        <li class="menu-item {{ request()->is('allocations/*') || request()->is('allocations') ? 'active' : '' }}">
            <a href="{{ route('dashboard.allocations.index') }}" class="menu-link">
                <i class="fa-solid fa-clipboard-list me-2"></i>
                <div data-i18n="allocations">التخصيصات</div>
            </a>
        </li>
        @endcan
        @can('view','App\\Models\Executive')
        <li class="menu-item {{ request()->is('executives/*') || request()->is('executives') ? 'active' : '' }}">
            <a href="{{ route('dashboard.executives.index') }}" class="menu-link">
                <i class="fa-solid fa-users-cog me-2"></i>
                <div data-i18n="executives">التنفيذات</div>
            </a>
        </li>
        @endcan
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps &amp; Pages">إدارة النظام</span>
        </li>
        <li class="menu-item" style="">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="fa-solid fa-database me-2"></i>
                <div data-i18n="our-data">البيانات الأخرى</div>
            </a>
            <ul class="menu-sub">
                @can('view','App\\Models\Broker')
                <li class="menu-item {{ request()->is('brokers/*') || request()->is('brokers') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.brokers.index') }}" class="menu-link">
                        <i class="fa-solid fa-building me-2"></i>
                        <div data-i18n="brokers">المؤسسات</div>
                    </a>
                </li>
                @endcan
                @can('view','App\\Models\Item')
                <li class="menu-item {{ request()->is('items/*') || request()->is('items') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.items.index') }}" class="menu-link">
                        <i class="fa-solid fa-boxes me-2"></i>
                        <div data-i18n="items">الأصناف</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="fa-solid fa-gear me-2"></i>
                <div data-i18n="settings">الإعدادات</div>
            </a>
            <ul class="menu-sub">
                @can('view','App\\Models\User')
                <li class="menu-item {{ request()->is('users/*') || request()->is('users') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.users.index') }}" class="menu-link">
                        <i class="fa-solid fa-users me-2"></i>
                        <div data-i18n="users">المستخدمين</div>
                    </a>
                </li>
                @endcan
                @can('view','App\\Models\ActivityLog')
                <li class="menu-item {{ request()->is('logs/*') || request()->is('logs') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.logs.index') }}" class="menu-link">
                        <i class="fa-solid fa-calendar-days me-2"></i>
                        <div data-i18n="logs">الأحداث</div>
                    </a>
                </li>
                @endcan
                @can('view','App\\Models\Currency')
                <li class="menu-item {{ request()->is('currencies/*') || request()->is('currencies') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.currencies.index') }}" class="menu-link">
                        <i class="fa-solid fa-coins me-2"></i>
                        <div data-i18n="currencies">العملات</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        {{-- <li class="menu-item">
            <a href="page-2.html" class="menu-link">
                <i class="menu-icon tf-icons ti ti-app-window"></i>
                <i class="fa-solid fa-house me-2"></i>
                <div data-i18n="Page 2">Page 2</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboards</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="index.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-chart-pie-2"></i>
                        <div data-i18n="Analytics">Analytics</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="dashboards-crm.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-3d-cube-sphere"></i>
                        <div data-i18n="CRM">CRM</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-ecommerce-dashboard.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                        <div data-i18n="eCommerce">eCommerce</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-logistics-dashboard.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-truck"></i>
                        <div data-i18n="Logistics">Logistics</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-academy-dashboard.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-book"></i>
                        <div data-i18n="Academy">Academy</div>
                    </a>
                </li>
            </ul>
        </li> --}}
    </ul>
    <div class="my-3 text-center text-white text-body">
        ©
        2025
        , تم الإنشاء ❤️ بواسطة <a href="https://saeyd-jamal.github.io/portfolio/" target="_blank"
            class="footer-link">م . السيد الاخرسي</a>
    </div>
</aside>
