<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset("admin") }}/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Produksi - Kelola Dapur</title>
    @stack("styles")
    <link rel="icon" href="{{ asset("admin") }}/assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/css/demo.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/css/custom-badge.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ asset("admin") }}/assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include("template_produksi.sidebar")
            <div class="layout-page">
                @include("template_produksi.navbar")
                <div class="content-wrapper">
                    @yield("content")
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="{{ asset("admin") }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/js/menu.js"></script>
    <script src="{{ asset("admin") }}/assets/js/main.js"></script>
    @stack("scripts")
</body>
</html>
