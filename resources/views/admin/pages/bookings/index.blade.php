@extends('admin.layouts.app')

@section('title', __('Bookings'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="overflow: visible;">
                    <h5 class="mb-0">{{ __('Bookings List') }}</h5>
                    <div class="d-flex gap-2 align-items-center">
                        @if (isset($totalFilteredBookings) && $totalFilteredBookings > 0)
                            <span class="text-muted small me-2">
                                {{ __('Total') }}: <strong>{{ $totalFilteredBookings }}</strong> {{ __('booking(s)') }}
                            </span>
                        @endif
                        @can('export bookings')
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="true">
                                    <i class="ti tabler-file-download me-2"></i>{{ __('Export PDF') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @can('export Bank')
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('bookings.export.bank') }}?{{ http_build_query(request()->query()) }}"
                                                target="_blank">
                                                <i class="ti tabler-building-bank me-2"></i>{{ __('Bank Export') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('export Detailed')
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('bookings.export.detailed') }}?{{ http_build_query(request()->query()) }}"
                                                target="_blank">
                                                <i class="ti tabler-file-text me-2"></i>{{ __('Detailed Export') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('export Guest')
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('bookings.export.guest') }}?{{ http_build_query(request()->query()) }}"
                                                target="_blank">
                                                <i class="ti tabler-user me-2"></i>{{ __('Guest Export') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('export Client')
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('bookings.export.client') }}?{{ http_build_query(request()->query()) }}"
                                                target="_blank">
                                                <i class="ti tabler-users me-2"></i>{{ __('Client Export') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('export Net Rate')
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('bookings.export.netrate') }}?{{ http_build_query(request()->query()) }}"
                                                target="_blank">
                                                <i class="ti tabler-currency-dollar me-2"></i>{{ __('Net Rate Export') }}
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        @endcan
                        @can('create bookings')
                            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                                <i class="ti tabler-plus me-2"></i>{{ __('Add Booking') }}
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="mb-3">
                        <div class="accordion" id="filterAccordion">
                            <div class="accordion-item ">
                                <h2 class="accordion-header active" id="filterHeading">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#filterCollapse">
                                        <i class="ti tabler-filter me-2"></i>
                                        {{ __('Filter Bookings') }}
                                        @php
                                            $activeFilters = 0;
                                            if (request('hotel_id')) {
                                                $activeFilters++;
                                            }
                                            if (request('customer_id')) {
                                                $activeFilters++;
                                            }
                                            if (request('payment_status')) {
                                                $activeFilters++;
                                            }
                                            if (request('check_in_from') || request('check_in_to')) {
                                                $activeFilters++;
                                            }
                                            if (request('check_out_from') || request('check_out_to')) {
                                                $activeFilters++;
                                            }
                                            if (request('option_date_from') || request('option_date_to')) {
                                                $activeFilters++;
                                            }
                                            if (request('currency_id')) {
                                                $activeFilters++;
                                            }
                                            if (request()->boolean('in_payment_list')) {
                                                $activeFilters++;
                                            }
                                            if (request('search')) {
                                                $activeFilters++;
                                            }
                                        @endphp
                                        @if ($activeFilters > 0)
                                            <span class="badge bg-primary ms-2">{{ $activeFilters }}</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="filterCollapse" class="accordion-collapse collapse show"
                                    data-bs-parent="#filterAccordion">
                                    <div class="accordion-body">
                                        <form method="GET" action="{{ route('bookings.index') }}" id="filterForm">
                                            <div class="row g-3">
                                                <!-- Search -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Search by Code or Confirmation Number') }}</label>
                                                    <input type="text" name="search" class="form-control"
                                                        value="{{ request('search') }}"
                                                        placeholder="{{ __('Enter booking code or confirmation number') }}">
                                                </div>

                                                <!-- Hotel Filter -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Hotel') }}</label>
                                                    <select name="hotel_id" class="form-select select2-filter">
                                                        <option value="">{{ __('All') }}</option>
                                                        @foreach ($hotels as $hotel)
                                                            <option value="{{ $hotel->id }}"
                                                                {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                                                {{ $hotel->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Customer Filter -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Customer') }}</label>
                                                    <select name="customer_id" class="form-select select2-filter">
                                                        <option value="">{{ __('All') }}</option>
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}"
                                                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                                {{ $customer->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Payment Status Filter -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Payment Status') }}</label>
                                                    <select name="payment_status" class="form-select">
                                                        <option value="">{{ __('All') }}</option>
                                                        <option value="paid"
                                                            {{ request('payment_status') == 'paid' ? 'selected' : '' }}>
                                                            {{ __('Paid') }}
                                                        </option>
                                                        <option value="unpaid"
                                                            {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>
                                                            {{ __('Unpaid') }}
                                                        </option>
                                                        <option value="partial"
                                                            {{ request('payment_status') == 'partial' ? 'selected' : '' }}>
                                                            {{ __('Partial Payment') }}
                                                        </option>
                                                        <option value="revised"
                                                            {{ request('payment_status') == 'revised' ? 'selected' : '' }}>
                                                            {{ __('Revised') }}
                                                        </option>
                                                        <option value="overpaid"
                                                            {{ request('payment_status') == 'overpaid' ? 'selected' : '' }}>
                                                            {{ __('Over Paid') }}
                                                        </option>
                                                        <option value="missed"
                                                            {{ request('payment_status') == 'missed' ? 'selected' : '' }}>
                                                            {{ __('Missed') }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- Check-in From -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Check In') }} -
                                                        {{ __('From Date') }}</label>
                                                    <input type="date" name="check_in_from" class="form-control"
                                                        value="{{ request('check_in_from') }}">
                                                </div>

                                                <!-- Check-in To -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Check In') }} -
                                                        {{ __('To Date') }}</label>
                                                    <input type="date" name="check_in_to" class="form-control"
                                                        value="{{ request('check_in_to') }}">
                                                </div>

                                                <!-- Check-out From -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Check Out') }} -
                                                        {{ __('From Date') }}</label>
                                                    <input type="date" name="check_out_from" class="form-control"
                                                        value="{{ request('check_out_from') }}">
                                                </div>

                                                <!-- Check-out To -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Check Out') }} -
                                                        {{ __('To Date') }}</label>
                                                    <input type="date" name="check_out_to" class="form-control"
                                                        value="{{ request('check_out_to') }}">
                                                </div>

                                                <!-- Option Date From -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Option Date') }} -
                                                        {{ __('From Date') }}</label>
                                                    <input type="date" name="option_date_from" class="form-control"
                                                        value="{{ request('option_date_from') }}">
                                                </div>

                                                <!-- Option Date To -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Option Date') }} -
                                                        {{ __('To Date') }}</label>
                                                    <input type="date" name="option_date_to" class="form-control"
                                                        value="{{ request('option_date_to') }}">
                                                </div>

                                                <!-- Currency Filter -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Currency') }}</label>
                                                    <select name="currency_id" class="form-select">
                                                        <option value="">{{ __('All') }}</option>
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->id }}"
                                                                {{ request('currency_id') == $currency->id ? 'selected' : '' }}>
                                                                {{ $currency->code }} - {{ $currency->symbol }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Payment List Filter -->
                                                <div class="col-md-3">
                                                    <label class="form-label d-block">{{ __('Payment List') }}</label>
                                                    {{-- Always submitted so unchecking the box actually clears the filter --}}
                                                    <input type="hidden" name="in_payment_list" value="0">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="in_payment_list" value="1" id="inPaymentListFilter"
                                                            {{ request()->boolean('in_payment_list') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="inPaymentListFilter">
                                                            {{ __('Only Payment List') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Sort By -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Sort By') }}</label>
                                                    <select name="sort_by" class="form-select">
                                                        <option value="created_at"
                                                            {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                                            {{ __('Created At') }}
                                                        </option>
                                                        <option value="code"
                                                            {{ request('sort_by') == 'code' ? 'selected' : '' }}>
                                                            {{ __('Code') }}
                                                        </option>
                                                        <option value="check_in"
                                                            {{ request('sort_by') == 'check_in' ? 'selected' : '' }}>
                                                            {{ __('Check In') }}
                                                        </option>
                                                        <option value="check_out"
                                                            {{ request('sort_by') == 'check_out' ? 'selected' : '' }}>
                                                            {{ __('Check Out') }}
                                                        </option>
                                                        <option value="option_date"
                                                            {{ request('sort_by') == 'option_date' ? 'selected' : '' }}>
                                                            {{ __('Option Date') }}
                                                        </option>
                                                        <option value="total_amount"
                                                            {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>
                                                            {{ __('Total Amount') }}
                                                        </option>
                                                        <option value="paid_amount"
                                                            {{ request('sort_by') == 'paid_amount' ? 'selected' : '' }}>
                                                            {{ __('Paid Amount') }}
                                                        </option>
                                                        <option value="status"
                                                            {{ request('sort_by') == 'status' ? 'selected' : '' }}>
                                                            {{ __('Status') }}
                                                        </option>
                                                        <option value="updated_at"
                                                            {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>
                                                            {{ __('Updated At') }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- Sort Order -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Sort Order') }}</label>
                                                    <select name="sort_order" class="form-select">
                                                        <option value="asc"
                                                            {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                                            {{ __('Ascending') }}
                                                        </option>
                                                        <option value="desc"
                                                            {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                                            {{ __('Descending') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mt-3 d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti tabler-search me-2"></i>{{ __('Apply Filters') }}
                                                </button>
                                                <a href="{{ route('bookings.index', ['clear_filters' => 1]) }}"
                                                    class="btn btn-secondary">
                                                    <i class="ti tabler-x me-2"></i>{{ __('Clear All Filters') }}
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    @if (request()->hasAny([
                            'hotel_id',
                            'customer_id',
                            'payment_status',
                            'check_in_from',
                            'check_in_to',
                            'check_out_from',
                            'check_out_to',
                            'option_date_from',
                            'option_date_to',
                            'currency_id',
                            'search',
                            'sort_by',
                        ]) || request()->boolean('in_payment_list'))
                        <div class="mb-3">
                            <strong>{{ __('Active Filters') }}:</strong>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @if (request('search'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Search') }}: {{ request('search') }}
                                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('hotel_id'))
                                    @php
                                        $selectedHotel = $hotels->firstWhere('id', request('hotel_id'));
                                    @endphp
                                    <span class="badge bg-label-primary">
                                        {{ __('Hotel') }}: {{ $selectedHotel?->name }}
                                        <a href="{{ request()->fullUrlWithQuery(['hotel_id' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('customer_id'))
                                    @php
                                        $selectedCustomer = $customers->firstWhere('id', request('customer_id'));
                                    @endphp
                                    <span class="badge bg-label-primary">
                                        {{ __('Customer') }}: {{ $selectedCustomer?->name }}
                                        <a href="{{ request()->fullUrlWithQuery(['customer_id' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('payment_status'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Payment Status') }}: {{ __(ucfirst(request('payment_status'))) }}
                                        <a href="{{ request()->fullUrlWithQuery(['payment_status' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('check_in_from') || request('check_in_to'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Check In') }}:
                                        {{ request('check_in_from') ? request('check_in_from') : '...' }}
                                        →
                                        {{ request('check_in_to') ? request('check_in_to') : '...' }}
                                        <a href="{{ request()->fullUrlWithQuery(['check_in_from' => null, 'check_in_to' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('check_out_from') || request('check_out_to'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Check Out') }}:
                                        {{ request('check_out_from') ? request('check_out_from') : '...' }}
                                        →
                                        {{ request('check_out_to') ? request('check_out_to') : '...' }}
                                        <a href="{{ request()->fullUrlWithQuery(['check_out_from' => null, 'check_out_to' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('option_date_from') || request('option_date_to'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Option Date') }}:
                                        {{ request('option_date_from') ? request('option_date_from') : '...' }}
                                        →
                                        {{ request('option_date_to') ? request('option_date_to') : '...' }}
                                        <a href="{{ request()->fullUrlWithQuery(['option_date_from' => null, 'option_date_to' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('currency_id'))
                                    @php
                                        $selectedCurrency = $currencies->firstWhere('id', request('currency_id'));
                                    @endphp
                                    <span class="badge bg-label-primary">
                                        {{ __('Currency') }}: {{ $selectedCurrency?->code }}
                                        <a href="{{ request()->fullUrlWithQuery(['currency_id' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request()->boolean('in_payment_list'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Only Payment List') }}
                                        <a href="{{ request()->fullUrlWithQuery(['in_payment_list' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif

                                @if (request('sort_by'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Sort By') }}:
                                        {{ __(ucfirst(str_replace('_', ' ', request('sort_by')))) }}
                                        ({{ request('sort_order') == 'asc' ? __('Ascending') : __('Descending') }})
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => null, 'sort_order' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Per Page Selector -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="per_page" class="form-label mb-0">{{ __('Show') }}:</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm"
                                style="width: auto;"
                                onchange="window.location.href='{{ route('bookings.index') }}?' + new URLSearchParams({{ json_encode(request()->query()) }}).toString().replace(/&per_page=[^&]*/, '').replace(/per_page=[^&]*/, '') + '&per_page=' + this.value">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100
                                </option>
                            </select>
                            <span class="text-muted small">{{ __('entries') }}</span>
                        </div>
                    </div>

                    <!-- Horizontal Scroll Container (Top) -->
                    <div class="scroll-top-container mb-2" style="overflow-x: auto; overflow-y: hidden; height: 20px;"
                        id="scrollTop">
                        <div id="scrollTopContent" style="height: 1px;"></div>
                    </div>

                    <div class="table-responsive" style="overflow-x: auto; overflow-y: visible; max-height: 70vh;">
                        <table class="table table-hover text-center table-sm table-bordered" style="font-size: 0.875rem;">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">{{ __('Code') }}</th>
                                    <th class="text-nowrap">{{ __('Hotel') }}</th>
                                    <th class="text-nowrap">{{ __('Rooms') }}</th>
                                    <th class="text-nowrap">{{ __('Check In') }}</th>
                                    <th class="text-nowrap">{{ __('Check Out') }}</th>
                                    <th class="text-nowrap">{{ __('Nights') }}</th>
                                    <th class="text-nowrap">{{ __('Total Net') }}</th>
                                    <th class="text-nowrap">{{ __('Paid Net') }}</th>
                                    <th class="text-nowrap">{{ __('Remaining Net') }}</th>
                                    <th class="text-nowrap">{{ __('Option Date') }}</th>
                                    <th class="text-nowrap">{{ __('Status') }}</th>
                                    <th class="text-nowrap">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td class="text-nowrap py-2"><strong>{{ $booking->code }}</strong></td>
                                        <td class="text-nowrap py-2">
                                            {{ $booking->hotel->name }}
                                            @if ($booking->hotel_confirmation_number)
                                                <br><small
                                                    class="text-muted">{{ $booking->hotel_confirmation_number }}</small>
                                            @endif
                                        </td>
                                        <td class="py-2" style="width: 130px;">
                                            @if ($booking->rooms && $booking->rooms->count() > 0)
                                                @php
                                                    $roomTypes = ['SGL' => 0, 'DBL' => 0, 'TPL' => 0, 'QUD' => 0];
                                                    $totalRooms = 0;
                                                    foreach ($booking->rooms as $room) {
                                                        $roomTypes[$room->room_type] += $room->room_count;
                                                        $totalRooms += $room->room_count;
                                                    }
                                                @endphp
                                                <div class="text-start" style="line-height: 1.4;">
                                                    <div class="mb-1">
                                                        <strong class="text-primary">{{ $totalRooms }}</strong>
                                                        <small
                                                            class="text-muted">{{ $totalRooms == 1 ? __('Room') : __('Rooms') }}</small>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach (['SGL', 'DBL', 'TPL', 'QUD'] as $type)
                                                            @if ($roomTypes[$type] > 0)
                                                                <span class="badge bg-label-info"
                                                                    style="font-size: 0.7rem; padding: 0.15rem 0.35rem; line-height: 1.2;">
                                                                    {{ $roomTypes[$type] }}
                                                                    <strong>{{ $type }}</strong>
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap py-2">{{ $booking->check_in->format('d-m-Y') }}</td>
                                        <td class="text-nowrap py-2">{{ $booking->check_out->format('d-m-Y') }}</td>
                                        <td class="text-nowrap py-2">{{ $booking->nights }}</td>
                                        <td class="text-nowrap py-2">
                                            {{ $booking->net_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->net_amount) }}
                                        </td>
                                        <td class="text-nowrap py-2">
                                            <span class="fw-semibold text-success">
                                                {{ $booking->hotel_paid_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap py-2">
                                            @php
                                                $remaining = $booking->net_amount - $booking->hotel_paid_amount;
                                            @endphp
                                            @if ($remaining > 0)
                                                <span class="badge bg-label-danger" style="font-size: 0.75rem;">
                                                    <i class="ti tabler-alert-circle me-1"></i>
                                                    {{ $booking->currency->symbol }}
                                                    {{ \App\Helpers\NumberHelper::format($remaining) }}
                                                </span>
                                            @elseif ($booking->net_amount > 0)
                                                <span class="badge bg-label-success" style="font-size: 0.75rem;">
                                                    <i class="ti tabler-check me-1"></i>
                                                    {{ __('Paid') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td class="py-2">
                                            @if ($booking->option_date)
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="ti tabler-calendar-event text-primary"
                                                            style="font-size: 0.9rem;"></i>
                                                        <span
                                                            class="fw-semibold text-primary text-nowrap">{{ $booking->option_date->format('d-m-Y') }}</span>
                                                    </div>
                                                    @if ($remaining <= 0)
                                                    @else
                                                        @if ($booking->option_date->isToday())
                                                            <span class="badge bg-label-info"
                                                                style="font-size: 0.65rem;">{{ __('Today') }}</span>
                                                        @elseif ($booking->option_date->isPast())
                                                            <span class="badge bg-label-warning"
                                                                style="font-size: 0.65rem;">{{ __('Past') }}</span>
                                                        @else
                                                            <span class="badge bg-label-success"
                                                                style="font-size: 0.65rem;">{{ __('Upcoming') }}</span>
                                                        @endif
                                                    @endif

                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap py-2">
                                            @if (empty($booking->payment_status) || $booking->payment_status == 'unpaid')
                                                <span class="badge bg-danger" title="{{ __('Unpaid') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-x"></i> {{ __('Unpaid') }}
                                                </span>
                                            @elseif ($booking->payment_status == 'paid')
                                                <span class="badge bg-success" title="{{ __('Paid') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-check"></i> {{ __('Paid') }}
                                                </span>
                                            @elseif ($booking->payment_status == 'partial')
                                                <span class="badge bg-warning" title="{{ __('Partial') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-question-mark"></i> {{ __('Partial') }}
                                                </span>
                                            @elseif ($booking->payment_status == 'revised')
                                                <span class="badge bg-info" title="{{ __('Revised') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-refresh"></i> {{ __('Revised') }}
                                                </span>
                                            @elseif ($booking->payment_status == 'overpaid')
                                                <span class="badge bg-primary" title="{{ __('Over Paid') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-arrow-up"></i> {{ __('Over Paid') }}
                                                </span>
                                            @elseif ($booking->payment_status == 'missed')
                                                <span class="badge bg-dark" title="{{ __('Payment deadline passed') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-clock-exclamation"></i> {{ __('Missed') }}
                                                </span>
                                            @endif
                                        </td>
                                        @include('admin.pages.bookings.partials.actions')
                                    </tr>

                                    @include('admin.pages.bookings.partials.hotel-payment-modal')
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ti tabler-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                                                <p class="mt-3 text-muted">{{ __('No bookings found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2 for filter dropdowns
                if (typeof $.fn.select2 !== 'undefined') {
                    $('.select2-filter').select2({
                        theme: 'bootstrap-5',
                        placeholder: '{{ __('Select') }}',
                        allowClear: true,
                        width: '100%'
                    });
                }

                // Get all hotel paid amount inputs
                const hotelPaidInputs = document.querySelectorAll('[id^="hotel_paid_amount"]');

                hotelPaidInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        const bookingId = this.getAttribute('data-booking-id');
                        const netAmount = parseFloat(this.getAttribute('data-net-amount'));
                        const currency = this.getAttribute('data-currency');
                        const paidAmount = parseFloat(this.value) || 0;

                        // Calculate new remaining
                        const newRemaining = netAmount - paidAmount;

                        // Update the new remaining field
                        const newRemainingField = document.getElementById('new_hotel_remaining' +
                            bookingId);
                        if (newRemainingField) {
                            newRemainingField.value = roundNumber(newRemaining) + ' ' + currency;

                            // Add visual feedback
                            if (newRemaining < 0) {
                                newRemainingField.classList.add('text-danger');
                            } else {
                                newRemainingField.classList.remove('text-danger');
                            }
                        }
                    });
                });



                // Handle per page change
                const perPageSelect = document.getElementById('per_page');
                if (perPageSelect) {
                    perPageSelect.addEventListener('change', function() {
                        const url = new URL(window.location.href);
                        url.searchParams.set('per_page', this.value);
                        url.searchParams.set('page', '1'); // Reset to first page
                        window.location.href = url.toString();
                    });
                }

                // Synchronize horizontal scrolling between top and bottom containers
                const scrollContainerTop = document.getElementById('scrollTop');
                const scrollContainerBottom = document.querySelector('.table-responsive');
                const scrollContent = document.getElementById('scrollTopContent');
                const table = document.querySelector('.table');
                if (scrollContainerTop && scrollContainerBottom && table) {
                    // Set the width of the top scroll content to match the table
                    const updateScrollWidth = function() {
                        // Get the actual width of the table content
                        const tableWidth = table.scrollWidth;
                        scrollContent.style.width = tableWidth + 'px';
                    };

                    // Initial width update after a short delay to ensure table is rendered
                    setTimeout(updateScrollWidth, 100);

                    // Update width on window resize
                    window.addEventListener('resize', updateScrollWidth);

                    // Update width when table content changes
                    const observer = new MutationObserver(function(mutations) {
                        updateScrollWidth();
                    });
                    observer.observe(table, {
                        childList: true,
                        subtree: true
                    });

                    let isScrollingTop = false;
                    let isScrollingBottom = false;

                    scrollContainerTop.addEventListener('scroll', function() {
                        if (!isScrollingBottom) {
                            isScrollingTop = true;
                            scrollContainerBottom.scrollLeft = this.scrollLeft;
                            setTimeout(() => {
                                isScrollingTop = false;
                            }, 50);
                        }
                    });

                    scrollContainerBottom.addEventListener('scroll', function() {
                        if (!isScrollingTop) {
                            isScrollingBottom = true;
                            scrollContainerTop.scrollLeft = this.scrollLeft;
                            setTimeout(() => {
                                isScrollingBottom = false;
                            }, 50);
                        }
                    });
                }
            });

            // Preserve filter values from session
            @if (!empty($savedFilters))
                const savedFilters = @json($savedFilters);

                // Apply saved filters to form fields
                Object.keys(savedFilters).forEach(function(key) {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = savedFilters[key];

                        // Trigger change event for select elements to update dependent fields
                        if (element.tagName === 'SELECT') {
                            element.dispatchEvent(new Event('change'));
                        }
                    }
                });
            @endif
            // Global function for Hotel Overpayment Check (Defined globally)
            window.confirmHotelOverpayment = function(form) {
                try {
                    const hotelPaidInput = form.querySelector('input[data-net-amount]');

                    if (hotelPaidInput) {
                        const netAmountStr = hotelPaidInput.getAttribute('data-net-amount');
                        if (!netAmountStr) return true;

                        // Remove commas just in case it's formatted string
                        const netAmount = parseFloat(netAmountStr.toString().replace(/,/g, ''));
                        const paidAmount = parseFloat(hotelPaidInput.value) || 0;

                        if (paidAmount > netAmount) {
                            const confirmMessage =
                                "{{ __('Warning: The amount you are paying is greater than the total net amount. Do you want to proceed?') }}";
                            return confirm(confirmMessage);
                        }
                    }
                } catch (e) {
                    console.error('Payment validation error:', e);
                }
                return true;
            };
        </script>
    @endpush
@endsection
