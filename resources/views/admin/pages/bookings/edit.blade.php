@extends('admin.layouts.app')

@section('title', __('Edit Booking'))
{{-- @if ($errors->any()) --}}
{{--    @foreach ($errors->all() as $error) --}}
{{--        <code>{{$error}}</code> --}}
{{--    @endforeach --}}
{{-- @endif --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Edit Booking') }} - {{ $booking->code }}</h5>
                    <div class="d-flex gap-2">
                        @can('delete bookings')
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                            </button>
                        @endcan
                        <a href="{{ route('bookings.index', request()->query()) }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('bookings.update', $booking) }}" method="POST" id="bookingForm">
                        @csrf
                        @method('PUT')

                        {{-- Booking Details --}}
                        <h5 class="mb-3">{{ __('Booking Details') }}</h5>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="code">{{ __('Booking Code') }}</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" value="{{ old('code', $booking->code) }}" required />
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="client_type">{{ __('Client Type') }}</label>
                                <select class="form-select @error('client_type') is-invalid @enderror" id="client_type"
                                    required>
                                    <option value="">{{ __('Select Client Type') }}</option>
                                    <option value="corporate"
                                        {{ old('client_type', $booking->customer->type) == 'corporate' ? 'selected' : '' }}>
                                        {{ __('B2B (Corporate)') }}</option>
                                    <option value="individual"
                                        {{ old('client_type', $booking->customer->type) == 'individual' ? 'selected' : '' }}>
                                        {{ __('B2C (Individual)') }}</option>
                                </select>
                                @error('client_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="customer_id">{{ __('Customer') }}</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                    name="customer_id" required>
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-type="{{ $customer->type }}"
                                            {{ old('customer_id', $booking->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3 d-none" id="client_name_container">
                                <label class="form-label" for="client_name">{{ __('Client Name') }}</label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                    id="client_name" name="client_name"
                                    value="{{ old('client_name', $booking->client_name) }}" />
                                @error('client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="customer_nationality">{{ __('Nationality') }}</label>
                                <select class="form-select @error('customer_nationality') is-invalid @enderror"
                                    id="customer_nationality" name="customer_nationality">
                                    <option value="">{{ __('Select Nationality') }}</option>
                                    @foreach ($nationalities as $nationality)
                                        <option value="{{ $nationality['nationality'] }}"
                                            {{ old('customer_nationality', $booking->customer->nationality ?? '') == $nationality['nationality'] ? 'selected' : '' }}>
                                            {{ $nationality['nationality'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="hotel_id">{{ __('Hotel') }}</label>
                                <select class="form-select @error('hotel_id') is-invalid @enderror" id="hotel_id"
                                    name="hotel_id" required>
                                    <option value="">{{ __('Select Hotel') }}</option>
                                    @foreach ($hotels as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ old('hotel_id', $booking->hotel_id) == $hotel->id ? 'selected' : '' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hotel_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="check_in">{{ __('Check In Date') }}</label>
                                <input type="date" class="form-control @error('check_in') is-invalid @enderror"
                                    id="check_in" name="check_in"
                                    value="{{ old('check_in', $booking->check_in->format('Y-m-d')) }}"
                                    min="{{ $booking->check_in->lt(now()->startOfDay()) ? $booking->check_in->format('Y-m-d') : now()->format('Y-m-d') }}"
                                    required />
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="check_out">{{ __('Check Out Date') }}</label>
                                <input type="date" class="form-control @error('check_out') is-invalid @enderror"
                                    id="check_out" name="check_out"
                                    value="{{ old('check_out', $booking->check_out->format('Y-m-d')) }}"
                                    min="{{ $booking->check_out->lt(now()->startOfDay()) ? $booking->check_out->format('Y-m-d') : now()->format('Y-m-d') }}"
                                    required />
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="nights">{{ __('Number of Nights') }}</label>
                                <input type="number" class="form-control" id="nights"
                                    value="{{ $booking->nights }}" readonly />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="option_date">{{ __('Option Date') }}</label>
                                <input type="date" class="form-control @error('option_date') is-invalid @enderror"
                                    id="option_date" name="option_date"
                                    value="{{ old('option_date', $booking->option_date?->format('Y-m-d') ?? $booking->payment_date?->format('Y-m-d')) }}"
                                    min="{{ now()->format('Y-m-d') }}" />
                                @error('option_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="currency_id">{{ __('Currency') }}</label>
                                <select class="form-select @error('currency_id') is-invalid @enderror" id="currency_id"
                                    name="currency_id" required>
                                    <option value="">{{ __('Select Currency') }}</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ old('currency_id', $booking->currency_id) == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="meals_plan">{{ __('Meals Plan') }}</label>
                                <input type="text" class="form-control @error('meals_plan') is-invalid @enderror"
                                    id="meals_plan" name="meals_plan"
                                    value="{{ old('meals_plan', $booking->meals_plan) }}" />
                                @error('meals_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Payment Status') }}</label>
                                @php
                                    $statusColors = [
                                        'paid'    => 'success',
                                        'partial' => 'warning',
                                        'unpaid'  => 'danger',
                                        'overpaid'=> 'primary',
                                        'revised' => 'info',
                                    ];
                                    $statusLabels = [
                                        'paid'    => __('Paid'),
                                        'partial' => __('Partial Payment'),
                                        'unpaid'  => __('Unpaid'),
                                        'overpaid'=> __('Over Paid'),
                                        'revised' => __('Revised'),
                                    ];
                                    $currentStatus = $booking->payment_status ?? 'unpaid';
                                    $statusColor = $statusColors[$currentStatus] ?? 'secondary';
                                    $statusLabel = $statusLabels[$currentStatus] ?? ucfirst($currentStatus);
                                @endphp
                                <div class="form-control bg-light d-flex align-items-center gap-2" style="cursor: default;">
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                    <small class="text-muted">{{ __('Auto-calculated') }}</small>
                                </div>
                                <small class="text-muted">
                                    <i class="ti tabler-info-circle me-1"></i>
                                    {{ __('Status is auto-calculated from paid amount vs net amount. Use "Update Payment" to change.') }}
                                </small>
                            </div>


                        </div>

                        {{-- Rooms & Guest Details --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Rooms & Guest Details') }}</h5>

                        <div id="roomsContainer">
                            @foreach ($booking->rooms as $index => $room)
                                <div class="room-row mb-3 p-3 border rounded bg-light">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">{{ __('Room Type') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="rooms[{{ $index }}][room_type]" class="form-select"
                                                required>
                                                <option value="">{{ __('Select') }}</option>
                                                <option value="SGL" {{ $room->room_type == 'SGL' ? 'selected' : '' }}>
                                                    SGL
                                                </option>
                                                <option value="DBL" {{ $room->room_type == 'DBL' ? 'selected' : '' }}>
                                                    DBL
                                                </option>
                                                <option value="TPL" {{ $room->room_type == 'TPL' ? 'selected' : '' }}>
                                                    TPL
                                                </option>
                                                <option value="QUD" {{ $room->room_type == 'QUD' ? 'selected' : '' }}>
                                                    QUD
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">{{ __('Category') }}</label>
                                            <input type="text" name="rooms[{{ $index }}][category]"
                                                class="form-control" value="{{ $room->category }}"
                                                placeholder="{{ __('Optional') }}" />
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Count') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="rooms[{{ $index }}][room_count]"
                                                class="form-control room-count" value="{{ $room->room_count }}"
                                                min="1" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Net Rate') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01"
                                                name="rooms[{{ $index }}][price]" class="form-control room-price"
                                                value="{{ $room->price }}" min="0" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Margin') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01"
                                                name="rooms[{{ $index }}][margin]"
                                                class="form-control room-margin" value="{{ $room->margin }}"
                                                min="0" required />
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-room w-100">
                                                <i class="ti tabler-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-info">{{ __('Children') }}</label>
                                            <input type="number" name="rooms[{{ $index }}][child_count]"
                                                class="form-control room-child-count" value="{{ $room->child_count }}"
                                                min="0" />
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="form-label fw-semibold text-info">{{ __('Child Net Rate') }}</label>
                                            <input type="number" step="0.01"
                                                name="rooms[{{ $index }}][child_price]"
                                                class="form-control room-child-price"
                                                value="{{ $room->child_price ?? 0 }}" min="0" />
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="form-label fw-semibold text-info">{{ __('Child Margin') }}</label>
                                            <input type="number" step="0.01"
                                                name="rooms[{{ $index }}][child_margin]"
                                                class="form-control room-child-margin" value="{{ $room->child_margin }}"
                                                min="0" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-secondary mb-4" id="addRoom">
                            <i class="ti tabler-plus me-2"></i>{{ __('Add Room') }}
                        </button>

                        {{-- Additions --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Additions') }}</h5>

                        <div id="additionsContainer">
                            @foreach ($booking->adjustments->where('type', 'addition') as $index => $addition)
                                @php
                                    $netRate = $addition->net_rate ?? ($addition->amount ?? 0);
                                    $guestRate = $addition->guest_rate ?? ($addition->amount ?? 0);
                                    $margin = $guestRate - $netRate;
                                @endphp
                                <div class="addition-row mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Net Rate') }}</label>
                                            <input type="number" step="0.01"
                                                name="additions[{{ $index }}][net_rate]"
                                                class="form-control addition-net-rate" value="{{ $netRate }}"
                                                required />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Guest Rate') }}</label>
                                            <input type="number" step="0.01"
                                                name="additions[{{ $index }}][guest_rate]"
                                                class="form-control addition-guest-rate" value="{{ $guestRate }}"
                                                required />
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Margin') }}</label>
                                            <input type="number" step="0.01"
                                                name="additions[{{ $index }}][margin]"
                                                class="form-control addition-margin" value="{{ $margin }}"
                                                readonly />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Description') }}</label>
                                            <input type="text" name="additions[{{ $index }}][description]"
                                                class="form-control" value="{{ $addition->description }}" required />
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-addition">
                                                <i class="ti tabler-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-success mb-4" id="addAddition">
                            <i class="ti tabler-plus me-2"></i>{{ __('Add Addition') }}
                        </button>

                        {{-- Discounts --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Discounts') }}</h5>

                        <div id="discountsContainer">
                            @foreach ($booking->adjustments->where('type', 'discount') as $index => $discount)
                                @php
                                    $netRate = $discount->net_rate ?? ($discount->amount ?? 0);
                                    $guestRate = $discount->guest_rate ?? ($discount->amount ?? 0);
                                    $margin = $netRate - $guestRate;
                                @endphp
                                <div class="discount-row mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Net Rate') }}</label>
                                            <input type="number" step="0.01"
                                                name="discounts[{{ $index }}][net_rate]"
                                                class="form-control discount-net-rate" value="{{ $netRate }}"
                                                required />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Guest Rate') }}</label>
                                            <input type="number" step="0.01"
                                                name="discounts[{{ $index }}][guest_rate]"
                                                class="form-control discount-guest-rate" value="{{ $guestRate }}"
                                                required />
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Margin') }}</label>
                                            <input type="number" step="0.01"
                                                name="discounts[{{ $index }}][margin]"
                                                class="form-control discount-margin" value="{{ $margin }}"
                                                readonly />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Description') }}</label>
                                            <input type="text" name="discounts[{{ $index }}][description]"
                                                class="form-control" value="{{ $discount->description }}" required />
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-discount">
                                                <i class="ti tabler-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-warning mb-4" id="addDiscount">
                            <i class="ti tabler-plus me-2"></i>{{ __('Add Discount') }}
                        </button>

                        {{-- Booking Summary --}}
                        <hr class="my-4">
                        <h4 class="mb-4 fw-bold">{{ __('Booking Summary') }}</h4>

                        <div class="row g-3 mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="text-muted small mb-2 text-uppercase">{{ __('Total Net Rate') }}
                                        </div>
                                        <h3 class="mb-0 fw-bold text-primary" id="premarginTotal">0.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="text-muted small mb-2 text-uppercase">{{ __('Margin Value') }}</div>
                                        <h3 class="mb-0 fw-bold text-primary" id="marginValue">0.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="text-muted small mb-2 text-uppercase">{{ __('Total Guest Rate') }}
                                        </div>
                                        <h3 class="mb-0 fw-bold text-primary" id="finalTotal">0.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="text-muted small mb-2 text-uppercase">{{ __('Remaining Amount') }}
                                        </div>
                                        <h3 class="mb-0 fw-bold text-primary" id="remainingAmount">0.00</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="notes">{{ __('Notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="paid_amount">{{ __('Paid Amount') }}</label>
                                <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                                    class="form-control form-control-lg"
                                    value="{{ old('paid_amount', $booking->paid_amount) }}" min="0" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Update Booking') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hide number input spinners */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>

    <script>
        // Customer data for filtering
        const customers = @json($customers);
        let roomIndex = {{ $booking->rooms->count() }};
        let additionIndex = {{ $booking->adjustments->where('type', 'addition')->count() }};
        let discountIndex = {{ $booking->adjustments->where('type', 'discount')->count() }};

        // Prevent negative values in number inputs
        document.addEventListener('input', function(e) {
            if (e.target.type === 'number' && e.target.value < 0) {
                e.target.value = 0;
            }

            // Prevent decimal values in integer fields (room-count, child-count)
            if (e.target.classList.contains('room-count') || e.target.classList.contains('room-child-count')) {
                const value = e.target.value;
                if (value.includes('.') || value.includes(',')) {
                    e.target.value = Math.floor(value);
                }
            }
        });

        // Prevent negative values and decimals on keydown
        document.addEventListener('keydown', function(e) {
            if (e.target.type === 'number') {
                // Prevent negative, e, E, + for all number inputs
                if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+') {
                    e.preventDefault();
                }

                // Prevent decimal point (.) and comma (,) for integer fields
                if ((e.target.classList.contains('room-count') || e.target.classList.contains(
                        'room-child-count')) &&
                    (e.key === '.' || e.key === ',')) {
                    e.preventDefault();
                }
            }
        });

        // Prevent paste of non-integer values in integer fields
        document.addEventListener('paste', function(e) {
            if (e.target.classList.contains('room-count') || e.target.classList.contains('room-child-count')) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const integerValue = parseInt(paste);
                if (!isNaN(integerValue) && integerValue >= 0) {
                    e.target.value = integerValue;
                }
            }
        });

        // Client type change handler
        document.getElementById('client_type').addEventListener('change', function() {
            const clientType = this.value;
            const customerSelect = document.getElementById('customer_id');
            const currentCustomerId = "{{ $booking->customer_id }}";

            customerSelect.innerHTML = '<option value="">{{ __('Select Customer') }}</option>';

            if (clientType) {
                customers.filter(c => c.type === clientType).forEach(customer => {
                    const option = document.createElement('option');
                    option.value = customer.id;
                    option.textContent = customer.name;
                    option.dataset.type = customer.type;
                    if (customer.id == currentCustomerId) {
                        option.selected = true;
                    }
                    customerSelect.appendChild(option);
                });
            }
            toggleClientName();
        });

        // Customer change handler
        document.getElementById('customer_id').addEventListener('change', toggleClientName);

        function toggleClientName() {
            const customerSelect = document.getElementById('customer_id');
            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            const clientNameContainer = document.getElementById('client_name_container');
            const clientNameInput = document.getElementById('client_name');

            if (selectedOption && selectedOption.dataset.type === 'corporate') {
                clientNameContainer.classList.remove('d-none');
                clientNameInput.required = true;
            } else {
                clientNameContainer.classList.add('d-none');
                clientNameInput.required = false;
                clientNameInput.value = '';
            }
        }

        // Calculate nights and manage dates
        function manageDates() {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const checkIn = checkInInput.value;

            // Update check_out min date
            if (checkIn) {
                const checkInDate = new Date(checkIn);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(checkInDate.getDate() + 1);

                const yyyy = nextDay.getFullYear();
                const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
                const dd = String(nextDay.getDate()).padStart(2, '0');
                const nextDayStr = `${yyyy}-${mm}-${dd}`;

                checkOutInput.min = nextDayStr;

                if (checkOutInput.value && checkOutInput.value < nextDayStr) {
                    checkOutInput.value = nextDayStr;
                }
            }

            const checkOut = checkOutInput.value;

            if (checkIn && checkOut) {
                const date1 = new Date(checkIn);
                const date2 = new Date(checkOut);
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                document.getElementById('nights').value = diffDays;
            }
        }

        document.getElementById('check_in').addEventListener('change', manageDates);
        document.getElementById('check_out').addEventListener('change', manageDates);

        // Initialize on load
        manageDates();
        toggleClientName();

        // Add room
        document.getElementById('addRoom').addEventListener('click', function() {
            const container = document.getElementById('roomsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'room-row mb-3 p-3 border rounded bg-light';
            newRow.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Room Type') }} <span class="text-danger">*</span></label>
                        <select name="rooms[${roomIndex}][room_type]" class="form-select" required>
                            <option value="">{{ __('Select') }}</option>
                            <option value="SGL">SGL</option>
                            <option value="DBL">DBL</option>
                            <option value="TPL">TPL</option>
                            <option value="QUD">QUD</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Category') }}</label>
                        <input type="text" name="rooms[${roomIndex}][category]" class="form-control" placeholder="{{ __('Optional') }}" />
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Count') }} <span class="text-danger">*</span></label>
                        <input type="number" name="rooms[${roomIndex}][room_count]" class="form-control room-count" value="1" min="1" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Net Rate') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][price]" class="form-control room-price" min="0" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Margin') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][margin]" class="form-control room-margin" min="0" required />
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-room w-100">
                            <i class="ti tabler-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-info">{{ __('Children') }}</label>
                        <input type="number" name="rooms[${roomIndex}][child_count]" class="form-control room-child-count" value="0" min="0" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-info">{{ __('Child Net Rate') }}</label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][child_price]" class="form-control room-child-price" value="0" min="0" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-info">{{ __('Child Margin') }}</label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][child_margin]" class="form-control room-child-margin" value="0" min="0" />
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            roomIndex++;
            updateRemoveButtons();
            attachCalculationListeners();
        });

        // Remove room
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-room') || e.target.closest('.remove-room')) {
                const row = e.target.closest('.room-row');
                row.remove();
                updateRemoveButtons();
                calculateSummary();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.room-row');
            rows.forEach((row) => {
                const removeBtn = row.querySelector('.remove-room');
                removeBtn.disabled = rows.length === 1;
            });
        }

        // Add addition
        document.getElementById('addAddition').addEventListener('click', function() {
            const container = document.getElementById('additionsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'addition-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Net Rate') }}</label>
                        <input type="number" step="0.01" name="additions[${additionIndex}][net_rate]" class="form-control addition-net-rate" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Guest Rate') }}</label>
                        <input type="number" step="0.01" name="additions[${additionIndex}][guest_rate]" class="form-control addition-guest-rate" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Margin') }}</label>
                        <input type="number" step="0.01" name="additions[${additionIndex}][margin]" class="form-control addition-margin" readonly />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <input type="text" name="additions[${additionIndex}][description]" class="form-control" required />
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-addition">
                            <i class="ti tabler-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            additionIndex++;
            attachCalculationListeners();
        });

        // Remove addition
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-addition') || e.target.closest('.remove-addition')) {
                const row = e.target.closest('.addition-row');
                row.remove();
                calculateSummary();
            }
        });

        // Add discount
        document.getElementById('addDiscount').addEventListener('click', function() {
            const container = document.getElementById('discountsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'discount-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Net Rate') }}</label>
                        <input type="number" step="0.01" name="discounts[${discountIndex}][net_rate]" class="form-control discount-net-rate" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Guest Rate') }}</label>
                        <input type="number" step="0.01" name="discounts[${discountIndex}][guest_rate]" class="form-control discount-guest-rate" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Margin') }}</label>
                        <input type="number" step="0.01" name="discounts[${discountIndex}][margin]" class="form-control discount-margin" readonly />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <input type="text" name="discounts[${discountIndex}][description]" class="form-control" required />
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-discount">
                            <i class="ti tabler-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            discountIndex++;
            attachCalculationListeners();
        });

        // Remove discount
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-discount') || e.target.closest('.remove-discount')) {
                const row = e.target.closest('.discount-row');
                row.remove();
                calculateSummary();
            }
        });

        // Calculate summary
        function calculateSummary() {
            let totalChildCost = 0;
            let premarginTotal = 0;
            let marginValue = 0;

            // Get number of nights
            const nightsInput = document.getElementById('nights');
            const nights = nightsInput ? parseFloat(nightsInput.value) || 1 : 1;

            // Calculate from rooms (multiplied by room_count and nights)
            document.querySelectorAll('.room-row').forEach(row => {
                const roomCount = parseFloat(row.querySelector('.room-count').value) || 1;
                const price = parseFloat(row.querySelector('.room-price').value) || 0;
                const margin = parseFloat(row.querySelector('.room-margin').value) || 0;
                const childCount = parseFloat(row.querySelector('.room-child-count').value) || 0;
                const childPrice = parseFloat(row.querySelector('.room-child-price').value) || 0;
                const childMargin = parseFloat(row.querySelector('.room-child-margin').value) || 0;

                premarginTotal += price * roomCount * nights;
                marginValue += margin * roomCount * nights;
                // Child margin should be added to marginValue (multiplied by nights)
                marginValue += childCount * childMargin * nights;
                // Child price is added to totalChildCost (multiplied by nights)
                totalChildCost += childCount * childPrice * nights;
            });

            // Calculate additions (using net_rate) - NOT multiplied by nights
            let additionsTotal = 0;
            document.querySelectorAll('.addition-net-rate').forEach(input => {
                additionsTotal += parseFloat(input.value) || 0;
            });

            // Calculate discounts (using net_rate) - NOT multiplied by nights
            let discountsTotal = 0;
            document.querySelectorAll('.discount-net-rate').forEach(input => {
                discountsTotal += parseFloat(input.value) || 0;
            });

            // Calculate additions margin and discounts margin for margin value
            let additionsMarginTotal = 0;
            document.querySelectorAll('.addition-margin').forEach(input => {
                additionsMarginTotal += parseFloat(input.value) || 0;
            });

            let discountsMarginTotal = 0;
            document.querySelectorAll('.discount-margin').forEach(input => {
                discountsMarginTotal += parseFloat(input.value) || 0;
            });

            // Net Rate includes child cost, additions (net_rate), and discounts (net_rate)
            const netRateTotal = premarginTotal + totalChildCost + additionsTotal - discountsTotal;
            // Margin Value includes additions margin and discounts margin
            const totalMarginValue = marginValue + additionsMarginTotal + discountsMarginTotal;
            const finalTotal = netRateTotal + totalMarginValue;
            const paidAmountInput = document.getElementById('paid_amount');

            // Update max attribute for paid_amount input - REMOVED to allow overpayment
            if (paidAmountInput) {
                paidAmountInput.removeAttribute('max');
                paidAmountInput.setCustomValidity('');
            }

            let paidAmount = paidAmountInput ? parseFloat(paidAmountInput.value) || 0 : 0;

            // Validation removed to allow overpayment
            const remainingAmount = netRateTotal - paidAmount;

            // Update display
            const premarginTotalEl = document.getElementById('premarginTotal');
            const marginValueEl = document.getElementById('marginValue');
            const finalTotalEl = document.getElementById('finalTotal');
            const remainingAmountEl = document.getElementById('remainingAmount');

            if (premarginTotalEl) premarginTotalEl.textContent = netRateTotal.toFixed(2);
            if (marginValueEl) marginValueEl.textContent = totalMarginValue.toFixed(2);
            if (finalTotalEl) finalTotalEl.textContent = finalTotal.toFixed(2);
            if (remainingAmountEl) remainingAmountEl.textContent = remainingAmount.toFixed(2);
        }

        // Calculate addition margin automatically
        function calculateAdditionMargin(input) {
            const row = input.closest('.addition-row');
            if (row) {
                const netRateInput = row.querySelector('.addition-net-rate');
                const guestRateInput = row.querySelector('.addition-guest-rate');
                const marginInput = row.querySelector('.addition-margin');

                if (netRateInput && guestRateInput && marginInput) {
                    const netRate = parseFloat(netRateInput.value) || 0;
                    const guestRate = parseFloat(guestRateInput.value) || 0;
                    const margin = guestRate - netRate;
                    marginInput.value = margin.toFixed(2);
                }
            }
        }

        // Calculate discount margin automatically
        function calculateDiscountMargin(input) {
            const row = input.closest('.discount-row');
            if (row) {
                const netRateInput = row.querySelector('.discount-net-rate');
                const guestRateInput = row.querySelector('.discount-guest-rate');
                const marginInput = row.querySelector('.discount-margin');

                if (netRateInput && guestRateInput && marginInput) {
                    const netRate = parseFloat(netRateInput.value) || 0;
                    const guestRate = parseFloat(guestRateInput.value) || 0;
                    const margin = netRate - guestRate;
                    marginInput.value = margin.toFixed(2);
                }
            }
        }

        function attachCalculationListeners() {
            document.querySelectorAll(
                '.room-count, .room-price, .room-margin, .room-child-count, .room-child-price, .room-child-margin, .addition-net-rate, .addition-guest-rate, .discount-net-rate, .discount-guest-rate, #nights'
            ).forEach(input => {
                input.removeEventListener('input', calculateSummary);
                input.addEventListener('input', calculateSummary);

                // Calculate addition margin when net_rate or guest_rate changes
                if (input.classList.contains('addition-net-rate') || input.classList.contains(
                        'addition-guest-rate')) {
                    input.removeEventListener('input', function() {
                        calculateAdditionMargin(input);
                    });
                    input.addEventListener('input', function() {
                        calculateAdditionMargin(input);
                        calculateSummary();
                    });
                }

                // Calculate discount margin when net_rate or guest_rate changes
                if (input.classList.contains('discount-net-rate') || input.classList.contains(
                        'discount-guest-rate')) {
                    input.removeEventListener('input', function() {
                        calculateDiscountMargin(input);
                    });
                    input.addEventListener('input', function() {
                        calculateDiscountMargin(input);
                        calculateSummary();
                    });
                }
            });
        }

        const paidAmountInput = document.getElementById('paid_amount');
        if (paidAmountInput) {
            paidAmountInput.addEventListener('input', function() {
                calculateSummary();
                // Additional validation on input - REMOVED to allow overpayment
                // const finalTotalEl = document.getElementById('finalTotal');
                // if (finalTotalEl) {
                //    const finalTotal = parseFloat(finalTotalEl.textContent) || 0;
                //    const paidAmount = parseFloat(this.value) || 0;
                //    if (paidAmount > finalTotal) {
                //        this.setCustomValidity('{{ __('Paid amount cannot exceed total guest rate') }}');
                //    } else {
                //        this.setCustomValidity('');
                //    }
                // }
            });
        }

        // Initial setup
        updateRemoveButtons();
        attachCalculationListeners();
        calculateSummary();

        // Open date picker when clicking on date inputs or their labels
        document.querySelectorAll('input[type="date"]').forEach(dateInput => {
            // Open picker when clicking on the input
            dateInput.addEventListener('click', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Open picker when clicking on associated label
            if (dateInput.id) {
                const label = document.querySelector(`label[for="${dateInput.id}"]`);
                if (label) {
                    label.addEventListener('click', function(e) {
                        e.preventDefault();
                        const input = document.getElementById(dateInput.id);
                        if (input && input.showPicker) {
                            input.showPicker();
                        }
                    });
                }
            }
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Delete Booking') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete this booking?') }}</p>
                    <p class="text-danger"><strong>{{ __('This action cannot be undone.') }}</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
