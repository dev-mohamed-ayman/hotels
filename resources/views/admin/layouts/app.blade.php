<!doctype html>
@php
    $sidebarLayout = $_COOKIE['sidebarLayout'] ?? 'vertical';
@endphp

<html lang="{{ app()->getLocale() }}" class=" layout-navbar-fixed layout-menu-fixed layout-compact "
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-skin="default" data-bs-theme="light"
    data-assets-path="assets/" data-template="{{ $sidebarLayout }}-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('title')</title>


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-advance.css') }}" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />

    @yield('styles')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>


    <!-- Layout wrapper -->
    <div
        class="{{ $sidebarLayout == 'vertical' ? 'layout-wrapper layout-content-navbar' : 'layout-wrapper layout-navbar-full layout-horizontal layout-without-menu' }}">
        <div class="layout-container">
            @if ($sidebarLayout == 'vertical')
                <!-- Menu -->
                @include('admin.layouts.vertical.sidebar')
                <!-- / Menu -->
            @endif
            @if ($sidebarLayout == 'horizontal')
                <!-- Navbar -->

                @include('admin.layouts.horizontal.navbar')

                <!-- / Navbar -->
            @endif

            <!-- Layout container -->
            <div class="layout-page">

                @if ($sidebarLayout == 'vertical')
                    <!-- Navbar -->
                    @include('admin.layouts.vertical.navbar')
                    <!-- / Navbar -->
                @endif


                <!-- Content wrapper -->
                <div class="content-wrapper">
                    @if ($sidebarLayout == 'horizontal')
                        <!-- Menu -->
                        @include('admin.layouts.horizontal.sidebar')
                        <!-- / Menu -->
                    @endif
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('admin.layouts.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Main JS -->

    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script>
        // Initialize all date inputs with Flatpickr (DD-MM-YYYY display, YYYY-MM-DD submit)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="date"]').forEach(function(input) {
                // Get current value and min date before converting
                const currentValue = input.value;
                const minDate = input.getAttribute('min');

                // Change input type to text for Flatpickr
                input.type = 'text';
                input.classList.add('flatpickr-date');

                // Initialize Flatpickr with altInput for proper display and submission
                flatpickr(input, {
                    dateFormat: 'Y-m-d', // Value sent to server (Laravel format)
                    altInput: true, // Enable alternative input
                    altFormat: 'd-m-Y', // Display format (DD-MM-YYYY)
                    allowInput: true,
                    defaultDate: currentValue ? currentValue : null,
                    minDate: minDate ? minDate : null,
                    locale: {
                        firstDayOfWeek: 6 // Saturday
                    }
                });
            });
        });
    </script>

    @include('admin.layouts.toast')

    <!-- Shared number formatting (decimal places come from config/numbers.php) -->
    <script>
        window.NUMBER_DECIMALS = {{ (int) config('numbers.decimals', 3) }};
        window.NUMBER_TRIM_ZEROS = @json((bool) config('numbers.trim_trailing_zeros', true));

        // Plain numeric string, safe to write into an <input value>. No separators.
        window.roundNumber = function(value, decimals) {
            var d = decimals === undefined ? window.NUMBER_DECIMALS : decimals;
            var n = parseFloat(value);
            if (isNaN(n)) n = 0;
            var out = n.toFixed(d);
            if (window.NUMBER_TRIM_ZEROS && out.indexOf('.') !== -1) {
                out = out.replace(/0+$/, '').replace(/\.$/, '');
            }
            return out;
        };

        // Display string with thousand separators, for textContent only.
        window.formatNumber = function(value, decimals) {
            var d = decimals === undefined ? window.NUMBER_DECIMALS : decimals;
            var n = parseFloat(value);
            if (isNaN(n)) n = 0;
            var out = n.toLocaleString('en-US', {
                minimumFractionDigits: window.NUMBER_TRIM_ZEROS ? 0 : d,
                maximumFractionDigits: d
            });
            return out;
        };
    </script>

    @yield('scripts')

    <!-- Timezone Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timezoneSearch = document.getElementById('timezone-search');
            if (timezoneSearch) {
                timezoneSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const timezoneForms = document.querySelectorAll('.timezone-form');
                    
                    timezoneForms.forEach(function(form) {
                        const label = form.getAttribute('data-timezone-label');
                        if (label.includes(searchTerm)) {
                            form.style.display = 'block';
                        } else {
                            form.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>
