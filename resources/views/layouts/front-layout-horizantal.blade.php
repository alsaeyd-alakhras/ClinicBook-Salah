@include('layouts.partials.head', ['title' => $title, 'template' => $template ?? 'horizontal-menu-template'])
<style>
    @media (min-width: 1200px) {
        .layout-menu-fixed .layout-horizontal .layout-page .menu-horizontal + [class*=container-], .layout-menu-fixed-offcanvas .layout-horizontal .layout-page .menu-horizontal + [class*=container-] {
            padding-top: 3.8rem !important;
        }
    }
    @media (min-width: 1400px) {
        .container-xxl, .container-xl, .container-lg, .container-md, .container-sm, .container {
            max-width: 99%;
        }
    }
</style>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Navbar -->


        @include('layouts.partials.navH')

        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Menu -->
                @include('layouts.partials.asideH')
                <!-- / Menu -->

                <!-- Content -->

                <div class="container-xxl flex-grow-1 container-p-y">
                    <x-alert type="success" />
                    <x-alert type="warning" />
                    <x-alert type="danger" />
                    {{ $slot }}
                </div>
                <!--/ Content -->

                <!-- Footer -->
                @include('layouts.partials.footer')
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
    </div>
</div>
<x-row />
<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>

<!--/ Layout wrapper -->
@include('layouts.partials.end')
