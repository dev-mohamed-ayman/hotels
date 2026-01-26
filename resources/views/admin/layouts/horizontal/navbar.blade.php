<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
            <a href="{{route('dashboard.index')}}" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <span class="text-primary">
                        <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                                  fill="currentColor"/>
                            <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                  d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"/>
                            <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                  d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                                  fill="currentColor"/>
                        </svg>
                    </span>
                </span>
                <span class="app-brand-text demo menu-text fw-bold text-heading">Vuexy</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
            </a>
        </div>

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0  d-xl-none  ">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base ti tabler-menu-2 icon-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

            <h4 class="m-0">@yield('title')</h4>

            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                <!-- Language -->
                <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-language icon-22px text-heading"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() == $localeCode ? 'active' : '' }}"
                                   rel="alternate" hreflang="{{ $localeCode }}"
                                   href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                   data-text-direction="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}">
                                    <span>{{ $properties['native'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <!--/ Language -->

                <!-- Timezone Selector -->
                <li class="nav-item dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" title="{{ __('Timezone') }}">
                        <i class="icon-base ti tabler-clock icon-22px text-heading"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end timezone-dropdown">
                        <li>
                            <h6 class="dropdown-header">{{ __('Select Timezone') }}</h6>
                        </li>
                        <li>
                            <div class="dropdown-item">
                                <input type="text" class="form-control form-control-sm" id="timezone-search" placeholder="{{ __('Search timezone...') }}">
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <div class="timezone-list" style="max-height: 300px; overflow-y: auto;">
                                @foreach(config('timezones.supported') as $timezone => $label)
                                    <form action="{{ route('timezone.change') }}" method="POST" class="timezone-form" data-timezone-label="{{ strtolower($label) }}">
                                        @csrf
                                        <input type="hidden" name="timezone" value="{{ $timezone }}">
                                        <button type="submit" class="dropdown-item {{ (request()->cookie('timezone', config('timezones.default')) == $timezone) ? 'active' : '' }}">
                                            <span>{{ $label }}</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ Timezone Selector -->

                <!-- Layout Toggle (Visual Only) -->
                <li class="nav-item dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                       href="javascript:void(0);" data-bs-toggle="dropdown" title="Sidebar Layout">
                        <i class="icon-base icon-22px text-heading" id="layout-icon"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button type="button" class="dropdown-item align-items-center"
                                    data-layout-value="vertical">
                                <span><i class="icon-base ti tabler-layout-sidebar icon-22px me-3"></i>Vertical
                                    Sidebar</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center"
                                    data-layout-value="horizontal">
                                <span><i class="icon-base ti tabler-layout-navbar icon-22px me-3"></i>Horizontal
                                    Sidebar</span>
                            </button>
                        </li>
                    </ul>
                </li>
                <!--/ Layout Toggle -->

                <!-- Style Switcher -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                       id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                        <span class="d-none ms-2" id="nav-theme-text">{{ __('Toggle theme') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                        <li>
                            <button type="button" class="dropdown-item align-items-center active"
                                    data-bs-theme-value="light" aria-pressed="false">
                                <span><i class="icon-base ti tabler-sun icon-22px me-3"
                                         data-icon="sun"></i>{{ __('Light') }}</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                                    aria-pressed="true">
                                <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                         data-icon="moon-stars"></i>{{ __('Dark') }}</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                                    aria-pressed="false">
                                <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                         data-icon="device-desktop-analytics"></i>{{ __('System') }}</span>
                            </button>
                        </li>
                    </ul>
                </li>
                <!-- / Style Switcher-->

                <script>
                    // Sidebar Layout Toggle (Cookie Only)
                    document.addEventListener('DOMContentLoaded', function () {
                        const layoutButtons = document.querySelectorAll('[data-layout-value]');

                        // Helper function to get cookie value
                        function getCookie(name) {
                            const value = `; ${document.cookie}`;
                            const parts = value.split(`; ${name}=`);
                            if (parts.length === 2) return parts.pop().split(';').shift();
                            return null;
                        }

                        // Helper function to set cookie
                        function setCookie(name, value) {
                            document.cookie = `${name}=${value}; path=/; max-age=31536000; SameSite=Lax`;
                        }

                        const savedLayout = getCookie('sidebarLayout') || 'vertical';
                        const layoutIcon = document.getElementById('layout-icon');

                        // Update main icon based on saved layout
                        if (layoutIcon) {
                            if (savedLayout === 'horizontal') {
                                layoutIcon.className = 'icon-base ti tabler-layout-navbar icon-22px text-heading';
                            } else {
                                layoutIcon.className = 'icon-base ti tabler-layout-sidebar icon-22px text-heading';
                            }
                        }

                        // Set active class based on saved preference
                        layoutButtons.forEach(button => {
                            if (button.getAttribute('data-layout-value') === savedLayout) {
                                button.classList.add('active');
                            } else {
                                button.classList.remove('active');
                            }
                        });

                        // Handle click events
                        layoutButtons.forEach(button => {
                            button.addEventListener('click', function () {
                                const layout = this.getAttribute('data-layout-value');

                                // Save to Cookie only
                                setCookie('sidebarLayout', layout);

                                // Reload page to apply changes
                                window.location.reload();
                            });
                        });
                    });
                </script>


                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown ms-3">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                       data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img
                                src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('assets/img/avatars/1.png') }}"
                                alt class="rounded-circle"/>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="{{ route('profile.index') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img
                                                src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('assets/img/avatars/1.png') }}"
                                                alt class="rounded-circle"/>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                        <small class="text-body-secondary">{{ auth()->user()->email }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="icon-base ti tabler-settings me-3 icon-md"></i><span
                                    class="align-middle">{{ __('Settings') }}</span>
                            </a>
                        </li>

                        <li>
                            <div class="d-grid px-2 pt-2 pb-1">
                                <a class="btn btn-sm btn-danger d-flex" href="{{ route('logout') }}">
                                    <small class="align-middle">{{ __('Logout') }}</small>
                                    <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </div>
</nav>
