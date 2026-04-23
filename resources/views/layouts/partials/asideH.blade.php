<aside id="layout-menu" class="flex-grow-0 layout-menu-horizontal menu-horizontal menu bg-menu-theme">
    <div class="container-xxl d-flex h-100">
        <ul class="pb-2 menu-inner pb-xl-0">
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ route('dashboard.home') }}" class="menu-link">
                    <i class="fa-solid fa-house me-2"></i>
                    <div data-i18n="home">الرئيسية</div>
                </a>
            </li>
            {{-- @can('view','App\\Models\AccreditationProject')
            <li class="menu-item {{ request()->is('accreditations/*') || request()->is('accreditations') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <i class="fa-solid fa-check-circle me-2"></i>
                    <div data-i18n="accreditations">الإعتمادية</div>
                </a>
            </li>
            @endcan --}}
            @can('view','App\\Models\AidDistribution')
            <li class="menu-item {{ request()->is('aid-distributions/*') || request()->is('aid-distributions') ? 'active' : '' }}">
                <a href="{{ route('dashboard.aid-distributions.index') }}" class="menu-link">
                    <i class="fa-solid fa-hand-holding-heart me-2"></i>
                    <div data-i18n="aid_distributions">سجل المساعدات</div>
                </a>
            </li>
            @endcan
            {{-- @can('view','App\\Models\Executive')
            <li class="menu-item {{ request()->is('executives/*') || request()->is('executives') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <i class="fa-solid fa-users-cog me-2"></i>
                    <div data-i18n="executives">التنفيذات</div>
                </a>
            </li>
            @endcan --}}
            {{-- @can('reports.view')
            <li class="menu-item {{ request()->is('reports/*') || request()->is('reports') ? 'active' : '' }}">
                <a href="{{ route('dashboard.reports.index') }}" class="menu-link">
                    <i class="fa-solid fa-file-alt me-2"></i>
                    <div data-i18n="reports">التقارير</div>
                </a>
            </li>
            @endcan --}}
            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="fa-solid fa-database me-2"></i>

                    <div data-i18n="Pages">البيانات</div>
                </a>
                <ul class="menu-sub">
                    @can('view','App\\Models\Office')
                    <li class="menu-item {{ request()->is('offices/*') || request()->is('offices') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.offices.index') }}" class="menu-link">
                            <i class="fa-solid fa-building me-2"></i>
                            <div data-i18n="offices">المكاتب</div>
                        </a>
                    </li>
                    @endcan
                    @can('view','App\\Models\Institution')
                    <li class="menu-item {{ request()->is('institutions/*') || request()->is('institutions') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.institutions.index') }}" class="menu-link">
                            <i class="fa-solid fa-landmark me-2"></i>
                            <div data-i18n="institutions">المؤسسات</div>
                        </a>
                    </li>
                    @endcan
                    @can('view','App\\Models\Project')
                    <li class="menu-item {{ request()->is('projects/*') || request()->is('projects') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.projects.index') }}" class="menu-link">
                            <i class="fa-solid fa-diagram-project me-2"></i>
                            <div data-i18n="projects">المشاريع</div>
                        </a>
                    </li>
                    @endcan
                    @can('view','App\\Models\AidItem')
                    <li class="menu-item {{ request()->is('aid-items/*') || request()->is('aid-items') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.aid-items.index') }}" class="menu-link">
                            <i class="fa-solid fa-boxes me-2"></i>
                            <div data-i18n="items">أنواع المساعدات</div>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="fa-solid fa-gear me-2"></i>

                    <div data-i18n="Pages">الإعدادات</div>
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
            {{-- <!-- Apps -->
            <li class="menu-item active">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-layout-grid-add"></i>
                    <div data-i18n="Apps">Apps</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="app-email.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-mail"></i>
                            <div data-i18n="Email">Email</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-chat.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-messages"></i>
                            <div data-i18n="Chat">Chat</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-calendar.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-calendar"></i>
                            <div data-i18n="Calendar">Calendar</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-kanban.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-layout-kanban"></i>
                            <div data-i18n="Kanban">Kanban</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                            <div data-i18n="eCommerce">eCommerce</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="app-ecommerce-dashboard.html" class="menu-link">
                                    <div data-i18n="Dashboard">Dashboard</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Products">Products</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="app-ecommerce-product-list.html" class="menu-link">
                                            <div data-i18n="Product List">Product List</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-product-add.html" class="menu-link">
                                            <div data-i18n="Add Product">Add Product</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-category-list.html" class="menu-link">
                                            <div data-i18n="Category List">Category List</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Order">Order</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="app-ecommerce-order-list.html" class="menu-link">
                                            <div data-i18n="Order List">Order List</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-order-details.html" class="menu-link">
                                            <div data-i18n="Order Details">Order Details</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Customer">Customer</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="app-ecommerce-customer-all.html" class="menu-link">
                                            <div data-i18n="All Customers">All Customers</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                                            <div data-i18n="Customer Details">Customer Details</div>
                                        </a>
                                        <ul class="menu-sub">
                                            <li class="menu-item">
                                                <a href="app-ecommerce-customer-details-overview.html"
                                                    class="menu-link">
                                                    <div data-i18n="Overview">Overview</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="app-ecommerce-customer-details-security.html"
                                                    class="menu-link">
                                                    <div data-i18n="Security">Security</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="app-ecommerce-customer-details-billing.html"
                                                    class="menu-link">
                                                    <div data-i18n="Address & Billing">Address &
                                                        Billing</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="app-ecommerce-customer-details-notifications.html"
                                                    class="menu-link">
                                                    <div data-i18n="Notifications">Notifications</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="app-ecommerce-manage-reviews.html" class="menu-link">
                                    <div data-i18n="Manage Reviews">Manage Reviews</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-ecommerce-referral.html" class="menu-link">
                                    <div data-i18n="Referrals">Referrals</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Settings">Settings</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="app-ecommerce-settings-detail.html"
                                            class="menu-link">
                                            <div data-i18n="Store Details">Store Details</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-settings-payments.html"
                                            class="menu-link">
                                            <div data-i18n="Payments">Payments</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-settings-checkout.html"
                                            class="menu-link">
                                            <div data-i18n="Checkout">Checkout</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-settings-shipping.html"
                                            class="menu-link">
                                            <div data-i18n="Shipping & Delivery">Shipping & Delivery
                                            </div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-settings-locations.html"
                                            class="menu-link">
                                            <div data-i18n="Locations">Locations</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-ecommerce-settings-notifications.html"
                                            class="menu-link">
                                            <div data-i18n="Notifications">Notifications</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-book"></i>
                            <div data-i18n="Academy">Academy</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="app-academy-dashboard.html" class="menu-link">
                                    <div data-i18n="Dashboard">Dashboard</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-academy-course.html" class="menu-link">
                                    <div data-i18n="My Course">My Course</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-academy-course-details.html" class="menu-link">
                                    <div data-i18n="Course Details">Course Details</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-truck"></i>
                            <div data-i18n="Logistics">Logistics</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="app-logistics-dashboard.html" class="menu-link">
                                    <div data-i18n="Dashboard">Dashboard</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-logistics-fleet.html" class="menu-link">
                                    <div data-i18n="Fleet">Fleet</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                            <div data-i18n="Invoice">Invoice</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="app-invoice-list.html" class="menu-link">
                                    <div data-i18n="List">List</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-invoice-preview.html" class="menu-link">
                                    <div data-i18n="Preview">Preview</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-invoice-edit.html" class="menu-link">
                                    <div data-i18n="Edit">Edit</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-invoice-add.html" class="menu-link">
                                    <div data-i18n="Add">Add</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-users"></i>
                            <div data-i18n="Users">Users</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="app-user-list.html" class="menu-link">
                                    <div data-i18n="List">List</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="View">View</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="app-user-view-account.html" class="menu-link">
                                            <div data-i18n="Account">Account</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-user-view-security.html" class="menu-link">
                                            <div data-i18n="Security">Security</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-user-view-billing.html" class="menu-link">
                                            <div data-i18n="Billing & Plans">Billing & Plans</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-user-view-notifications.html" class="menu-link">
                                            <div data-i18n="Notifications">Notifications</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="app-user-view-connections.html" class="menu-link">
                                            <div data-i18n="Connections">Connections</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item active">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-settings"></i>
                            <div data-i18n="Roles & Permissions">Roles & Permission</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="app-access-roles.html" class="menu-link">
                                    <div data-i18n="Roles">Roles</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="app-access-permission.html" class="menu-link">
                                    <div data-i18n="Permission">Permission</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <!-- Pages -->
            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-file"></i>

                    <div data-i18n="Pages">Pages</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-files"></i>
                            <div data-i18n="Front Pages">Front Pages</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="../front-pages/landing-page.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Landing">Landing</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="../front-pages/pricing-page.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Pricing">Pricing</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="../front-pages/payment-page.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Payment">Payment</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="../front-pages/checkout-page.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Checkout">Checkout</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="../front-pages/help-center-landing.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Help Center">Help Center</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-user-circle"></i>
                            <div data-i18n="User Profile">User Profile</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="pages-profile-user.html" class="menu-link">
                                    <div data-i18n="Profile">Profile</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-profile-teams.html" class="menu-link">
                                    <div data-i18n="Teams">Teams</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-profile-projects.html" class="menu-link">
                                    <div data-i18n="Projects">Projects</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-profile-connections.html" class="menu-link">
                                    <div data-i18n="Connections">Connections</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-settings"></i>
                            <div data-i18n="Account Settings">Account Settings</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="pages-account-settings-account.html" class="menu-link">
                                    <div data-i18n="Account">Account</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-account-settings-security.html" class="menu-link">
                                    <div data-i18n="Security">Security</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-account-settings-billing.html" class="menu-link">
                                    <div data-i18n="Billing & Plans">Billing & Plans</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-account-settings-notifications.html" class="menu-link">
                                    <div data-i18n="Notifications">Notifications</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-account-settings-connections.html" class="menu-link">
                                    <div data-i18n="Connections">Connections</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="pages-faq.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-help"></i>
                            <div data-i18n="FAQ">FAQ</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="pages-pricing.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-diamond"></i>
                            <div data-i18n="Pricing">Pricing</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-3d-cube-sphere"></i>
                            <div data-i18n="Misc">Misc</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="pages-misc-error.html" class="menu-link" target="_blank">
                                    <div data-i18n="Error">Error</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-misc-under-maintenance.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Under Maintenance">Under Maintenance</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-misc-comingsoon.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Coming Soon">Coming Soon</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pages-misc-not-authorized.html" class="menu-link"
                                    target="_blank">
                                    <div data-i18n="Not Authorized">Not Authorized</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-lock"></i>
                            <div data-i18n="Authentications">Authentications</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Login">Login</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="auth-login-basic.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Basic">Basic</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-login-cover.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Cover">Cover</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Register">Register</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="auth-register-basic.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Basic">Basic</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-register-cover.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Cover">Cover</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-register-multisteps.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Multi-steps">Multi-steps</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Verify Email">Verify Email</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="auth-verify-email-basic.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Basic">Basic</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-verify-email-cover.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Cover">Cover</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Reset Password">Reset Password</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="auth-reset-password-basic.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Basic">Basic</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-reset-password-cover.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Cover">Cover</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Forgot Password">Forgot Password</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="auth-forgot-password-basic.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Basic">Basic</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-forgot-password-cover.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Cover">Cover</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <div data-i18n="Two Steps">Two Steps</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="auth-two-steps-basic.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Basic">Basic</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="auth-two-steps-cover.html" class="menu-link"
                                            target="_blank">
                                            <div data-i18n="Cover">Cover</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-forms"></i>
                            <div data-i18n="Wizard Examples">Wizard Examples</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="wizard-ex-checkout.html" class="menu-link">
                                    <div data-i18n="Checkout">Checkout</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="wizard-ex-property-listing.html" class="menu-link">
                                    <div data-i18n="Property Listing">Property Listing</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="wizard-ex-create-deal.html" class="menu-link">
                                    <div data-i18n="Create Deal">Create Deal</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="modal-examples.html" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-square"></i>
                            <div data-i18n="Modal Examples">Modal Examples</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
        </ul>
    </div>
</aside>
