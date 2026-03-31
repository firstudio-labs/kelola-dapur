<!DOCTYPE html>

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

        <title>Kepala Dapur</title>

        <meta
            name="description"
            content="Dashboard Admin untuk mengelola Profile"
        />

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

        <link
            rel="icon"
            href="{{ asset("admin") }}/assets/img/favicon/favicon.ico"
        />

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
            rel="stylesheet"
        />

        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css"
        />

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
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/css/custom-badge.css"
        />

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

        <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>

        <script src="{{ asset("admin") }}/assets/js/config.js"></script>
    </head>

    <body>
        
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                
                @include("template_kepala_dapur.sidebar")
                
                <div class="layout-page">
                    
                    @include("template_kepala_dapur.navbar")
                    
                    <div class="content-wrapper">
                        
                        @yield("content")
                        
                        <div class="content-backdrop fade"></div>
                    </div>
                    
                </div>
                
            </div>

            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        
        @yield("script")
        
        <script src="{{ asset("admin") }}/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/popper/popper.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/js/bootstrap.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

        <script src="{{ asset("admin") }}/assets/vendor/js/menu.js"></script>
        
        <script src="{{ asset("admin") }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <script src="{{ asset("admin") }}/assets/js/main.js"></script>

        <script src="{{ asset("admin") }}/assets/js/dashboards-analytics.js"></script>

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
