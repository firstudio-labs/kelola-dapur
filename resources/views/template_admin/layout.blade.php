<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset("admin") }}/assets/"
    data-template="vertical-menu-template-free"
>
    <head>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
        />

        <title>Admin</title>

        <meta
            name="description"
            content="Dashboard Admin untuk mengelola Profile"
        />

        <!-- Leaflet CSS -->
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.css"
        />

        @stack("styles")

        <!-- Favicon -->
        <link
            rel="icon"
            href="{{ asset("admin") }}/assets/img/favicon/favicon.ico"
        />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
            rel="stylesheet"
        />

        <!-- Icons. Uncomment required icon fonts -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css"
        />

        <!-- Core CSS -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/core.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/theme-default.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/css/demo.css"
        />

        <!-- Vendors CSS -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/libs/apex-charts/apex-charts.css"
        />

        <script src="https://cdn.tailwindcss.com"></script>
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />

        <!-- Helpers -->
        <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>

        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="{{ asset("admin") }}/assets/js/config.js"></script>
    </head>

    <body>
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <!-- Menu -->
                @include("template_admin.sidebar")
                <!-- / Menu -->

                <!-- Layout container -->
                <div class="layout-page">
                    <!-- Navbar -->
                    @include("template_admin.navbar")
                    <!-- / Navbar -->

                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        @yield("content")
                        <!-- / Content -->

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->
                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- Core JS -->
        @yield("script")
        <!-- build:js assets/vendor/js/core.js -->
        <script src="{{ asset("admin") }}/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/popper/popper.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/js/bootstrap.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

        <script src="{{ asset("admin") }}/assets/vendor/js/menu.js"></script>
        <!-- endbuild -->

        <!-- Vendors JS -->
        <script src="{{ asset("admin") }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <!-- Main JS -->
        <script src="{{ asset("admin") }}/assets/js/main.js"></script>

        <!-- Page JS -->
        <script src="{{ asset("admin") }}/assets/js/dashboards-analytics.js"></script>

        <!-- Leaflet JS -->
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
        ></script>
        <script src="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.umd.js"></script>

        @stack("js-internal")
        @yield("scripts")
        @stack("scripts")
    </body>
</html>
