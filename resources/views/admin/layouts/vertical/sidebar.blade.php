<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo ">
        <a href="{{route('dashboard.index')}}" class="app-brand-link">
            <span class="app-brand-logo demo"><img src="{{ asset('assets/img/branding/azha-logo-horizontal.svg') }}" alt="{{ __('brand.name_full') }}" height="32" style="width:auto;max-width:180px;" /></span>
            {{-- <span class="app-brand-text demo menu-text fw-bold ms-3">{{ __('brand.name_short') }}</span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Single Level Menu Item Example -->
        <li class="menu-item {{ isActiveRoute('dashboard.index') }}">
            <a href="{{ route('dashboard.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="@lang('Dashboard')">@lang('Dashboard')</div>
            </a>
        </li>

        <!-- Multi Level Menu Item Example -->
        {{-- <li class="menu-item {{ isOpenMenu(['profile.*']) }} {{ isActiveRoute(['profile.*']) }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-user"></i>
                <div data-i18n="@lang('Profile')">@lang('Profile')</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ isActiveRoute('profile.index') }}">
                    <a href="{{ route('profile.index') }}" class="menu-link">
                        <div data-i18n="@lang('View Profile')">@lang('View Profile')</div>
                    </a>
                </li>
            </ul>
        </li> --}}

        <!-- Currencies -->
        <li class="menu-item {{ isActiveRoute('currencies.*') }}">
            <a href="{{ route('currencies.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-currency-dollar"></i>
                <div data-i18n="@lang('Currencies')">@lang('Currencies')</div>
            </a>
        </li>

        <!-- Hotels -->
        <li class="menu-item {{ isActiveRoute('hotels.*') }}">
            <a href="{{ route('hotels.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building"></i>
                <div data-i18n="@lang('Hotels')">@lang('Hotels')</div>
            </a>
        </li>

        <!-- Customers -->
        <li class="menu-item {{ isActiveRoute('customers.*') }}">
            <a href="{{ route('customers.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div data-i18n="@lang('Customers')">@lang('Customers')</div>
            </a>
        </li>

        <!-- Bookings -->
        <li class="menu-item {{ isActiveRoute('bookings.*') }}">
            <a href="{{ route('bookings.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-calendar"></i>
                <div data-i18n="@lang('Bookings')">@lang('Bookings')</div>
            </a>
        </li>

        <!-- Activity Log Bookings -->
        <li class="menu-item {{ isActiveRoute('booking-history.*') }}">
            <a href="{{ route('booking-history.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-history"></i>
                <div data-i18n="@lang('Activity Log Bookings')">@lang('Activity Log Bookings')</div>
            </a>
        </li>

        <!-- Activity Log -->
        @can('view activity log')
            <li class="menu-item {{ isActiveRoute('activity-log.*') }}">
                <a href="{{ route('activity-log.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-history"></i>
                    <div data-i18n="@lang('Activity Log')">@lang('Activity Log')</div>
                </a>
            </li>
        @endcan

        <!-- Users & Roles -->
        @canany(['view users', 'view roles', 'view permissions'])
            <li
                class="menu-item {{ isOpenMenu(['users.*', 'roles.*', 'permissions.*']) }} {{ isActiveRoute(['users.*', 'roles.*', 'permissions.*']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-shield-lock"></i>
                    <div data-i18n="@lang('Users & Roles')">@lang('Users & Roles')</div>
                </a>
                <ul class="menu-sub">
                    @can('view users')
                        <li class="menu-item {{ isActiveRoute('users.*') }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div data-i18n="@lang('Users')">@lang('Users')</div>
                            </a>
                        </li>
                    @endcan
                    @can('view roles')
                        <li class="menu-item {{ isActiveRoute('roles.*') }}">
                            <a href="{{ route('roles.index') }}" class="menu-link">
                                <div data-i18n="@lang('Roles')">@lang('Roles')</div>
                            </a>
                        </li>
                    @endcan
                    @can('view permissions')
                        <li class="menu-item {{ isActiveRoute('permissions.*') }}">
                            <a href="{{ route('permissions.index') }}" class="menu-link">
                                <div data-i18n="@lang('Permissions')">@lang('Permissions')</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="ti tabler-menu icon-base"></i>
        <i class="ti tabler-chevron-right icon-base"></i>
    </a>
</div>
