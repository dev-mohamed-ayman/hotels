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
        </ul>
    </div>
</aside>
