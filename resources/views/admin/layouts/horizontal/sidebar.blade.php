<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            <!-- Dashboard -->
            <li class="menu-item {{ isActiveRoute('dashboard.index') }}">
                <a href="{{ route('dashboard.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                    <div data-i18n="@lang('Dashboard')">@lang('Dashboard')</div>
                </a>
            </li>

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

            <!-- Booking History -->
            <li class="menu-item {{ isActiveRoute('booking-history.*') }}">
                <a href="{{ route('booking-history.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-calendar-history"></i>
                    <div data-i18n="@lang('Booking History')">@lang('Booking History')</div>
                </a>
            </li>

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
    </div>
</aside>
