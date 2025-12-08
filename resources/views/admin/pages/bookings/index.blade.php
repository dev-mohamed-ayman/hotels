@extends('admin.layouts.app')

@section('title', __('Bookings'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Bookings List') }}</h5>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        <i class="ti tabler-plus me-2"></i>{{ __('Add Booking') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="mb-3">
                        <div class="accordion" id="filterAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="filterHeading">
                                    <button
                                        class="accordion-button {{ request()->hasAny(['hotel_id', 'customer_id', 'payment_status', 'check_in_from', 'check_in_to', 'check_out_from', 'check_out_to', 'currency_id', 'search']) ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
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
                                            if (request('currency_id')) {
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
                                <div id="filterCollapse"
                                    class="accordion-collapse collapse {{ request()->hasAny(['hotel_id', 'customer_id', 'payment_status', 'check_in_from', 'check_in_to', 'check_out_from', 'check_out_to', 'currency_id', 'search']) ? 'show' : '' }}"
                                    data-bs-parent="#filterAccordion">
                                    <div class="accordion-body">
                                        <form method="GET" action="{{ route('bookings.index') }}" id="filterForm">
                                            <div class="row g-3">
                                                <!-- Search -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Search by Code') }}</label>
                                                    <input type="text" name="search" class="form-control"
                                                        value="{{ request('search') }}"
                                                        placeholder="{{ __('Enter booking code') }}">
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

                                                <!-- Sort By -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Sort By') }}</label>
                                                    <select name="sort_by" class="form-select">
                                                        <option value="created_at"
                                                            {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                                            {{ __('Created At') }}</option>
                                                        <option value="code"
                                                            {{ request('sort_by') == 'code' ? 'selected' : '' }}>
                                                            {{ __('Code') }}</option>
                                                        <option value="check_in"
                                                            {{ request('sort_by') == 'check_in' ? 'selected' : '' }}>
                                                            {{ __('Check In') }}</option>
                                                        <option value="check_out"
                                                            {{ request('sort_by') == 'check_out' ? 'selected' : '' }}>
                                                            {{ __('Check Out') }}</option>
                                                        <option value="total_amount"
                                                            {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>
                                                            {{ __('Total Amount') }}</option>
                                                        <option value="paid_amount"
                                                            {{ request('sort_by') == 'paid_amount' ? 'selected' : '' }}>
                                                            {{ __('Paid Amount') }}</option>
                                                        <option value="status"
                                                            {{ request('sort_by') == 'status' ? 'selected' : '' }}>
                                                            {{ __('Status') }}</option>
                                                        <option value="updated_at"
                                                            {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>
                                                            {{ __('Updated At') }}</option>
                                                    </select>
                                                </div>

                                                <!-- Sort Order -->
                                                <div class="col-md-3">
                                                    <label class="form-label">{{ __('Sort Order') }}</label>
                                                    <select name="sort_order" class="form-select">
                                                        <option value="desc"
                                                            {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                                            {{ __('Descending') }}</option>
                                                        <option value="asc"
                                                            {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                                            {{ __('Ascending') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mt-3 d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti tabler-search me-2"></i>{{ __('Apply Filters') }}
                                                </button>
                                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
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
                            'currency_id',
                            'search',
                            'sort_by',
                        ]))
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

                                @if (request('sort_by'))
                                    <span class="badge bg-label-primary">
                                        {{ __('Sort By') }}: {{ __(ucfirst(str_replace('_', ' ', request('sort_by')))) }}
                                        ({{ request('sort_order') == 'asc' ? __('Ascending') : __('Descending') }})
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => null, 'sort_order' => null]) }}"
                                            class="text-white ms-1">×</a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Hotel') }}</th>
                                    <th>{{ __('Check In') }}</th>
                                    <th>{{ __('Check Out') }}</th>
                                    <th>{{ __('Nights') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td><strong>{{ $booking->code }}</strong></td>
                                        <td>{{ $booking->hotel->name }}</td>
                                        <td>{{ $booking->check_in->format('Y-m-d') }}</td>
                                        <td>{{ $booking->check_out->format('Y-m-d') }}</td>
                                        <td>{{ $booking->nights }}</td>
                                        <td>{{ number_format($booking->total_amount, 2) }}
                                            {{ $booking->currency->code }}
                                        </td>
                                        <td>{{ number_format($booking->paid_amount, 2) }} {{ $booking->currency->code }}
                                        </td>
                                        <td>
                                            @if ($booking->paid_amount == 0)
                                                <span class="badge bg-danger" title="{{ __('Unpaid') }}">
                                                    <i class="ti tabler-x"></i>
                                                </span>
                                            @elseif ($booking->paid_amount >= $booking->total_amount)
                                                <span class="badge bg-success" title="{{ __('Paid') }}">
                                                    <i class="ti tabler-check"></i>
                                                </span>
                                            @else
                                                <span class="badge bg-warning" title="{{ __('Partial Payment') }}">
                                                    <i class="ti tabler-question-mark"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                                    id="actionsDropdown{{ $booking->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ti tabler-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="actionsDropdown{{ $booking->id }}">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('bookings.show', $booking) }}">
                                                            <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                                        </a>
                                                    </li>
                                                    @if ($booking->check_in >= now())
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('bookings.edit', $booking) }}">
                                                                <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#paymentModal{{ $booking->id }}">
                                                            <i
                                                                class="ti tabler-currency-dollar me-2"></i>{{ __('Update Payment') }}
                                                        </a>
                                                    </li>
                                                    @if ($booking->check_in >= now())
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('bookings.destroy', $booking) }}"
                                                                method="POST" class="d-inline-block"
                                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i
                                                                        class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Payment Update Modal -->
                                    <div class="modal fade" id="paymentModal{{ $booking->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Update Payment') }} -
                                                        {{ $booking->code }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('bookings.update-payment', $booking) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Total Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ number_format($booking->total_amount, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('Current Paid Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ number_format($booking->paid_amount, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            @php
                                                                $remaining =
                                                                    $booking->total_amount - $booking->paid_amount;
                                                            @endphp
                                                            <label
                                                                class="form-label">{{ __('Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="remaining{{ $booking->id }}"
                                                                value="{{ number_format($remaining, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="payment_amount{{ $booking->id }}">{{ __('Payment Amount') }}
                                                                *</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="payment_amount{{ $booking->id }}"
                                                                    name="payment_amount" min="0.01"
                                                                    max="{{ $remaining }}"
                                                                    data-remaining="{{ $remaining }}"
                                                                    data-currency="{{ $booking->currency->code }}"
                                                                    data-booking-id="{{ $booking->id }}"
                                                                    placeholder="{{ __('Enter amount to pay') }}"
                                                                    required>
                                                                <span
                                                                    class="input-group-text">{{ $booking->currency->code }}</span>
                                                            </div>
                                                            <small class="text-muted">{{ __('Maximum') }}:
                                                                {{ number_format($remaining, 2) }}
                                                                {{ $booking->currency->code }}</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('New Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="new_remaining{{ $booking->id }}"
                                                                value="{{ number_format($remaining, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Update') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">{{ __('No bookings found') }}</td>
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

                // Get all payment amount inputs
                const paymentInputs = document.querySelectorAll('[id^="payment_amount"]');

                paymentInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        const bookingId = this.getAttribute('data-booking-id');
                        const remaining = parseFloat(this.getAttribute('data-remaining'));
                        const currency = this.getAttribute('data-currency');
                        const paymentAmount = parseFloat(this.value) || 0;

                        // Calculate new remaining
                        const newRemaining = remaining - paymentAmount;

                        // Update the new remaining field
                        const newRemainingField = document.getElementById('new_remaining' + bookingId);
                        if (newRemainingField) {
                            newRemainingField.value = newRemaining.toFixed(2) + ' ' + currency;

                            // Add visual feedback
                            if (paymentAmount > remaining) {
                                this.classList.add('is-invalid');
                                newRemainingField.classList.add('text-danger');
                            } else {
                                this.classList.remove('is-invalid');
                                newRemainingField.classList.remove('text-danger');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
