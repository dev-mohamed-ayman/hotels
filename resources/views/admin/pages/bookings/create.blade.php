@extends('admin.layouts.app')

@section('title', __('Add Booking'))

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Add New Booking') }}</h5>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ __('Whoops! Something went wrong.') }}</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                        @csrf

                        {{-- Booking Details --}}
                        <h5 class="mb-3">{{ __('Booking Details') }}</h5>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="code">{{ __('Booking Code') }}</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" value="{{ old('code') }}" required />
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="client_type">{{ __('Client Type') }}</label>
                                <select class="form-select @error('client_type') is-invalid @enderror" id="client_type"
                                    required>
                                    <option value="">{{ __('Select Client Type') }}</option>
                                    <option value="individual" {{ old('client_type') == 'individual' ? 'selected' : '' }}>
                                        {{ __('B2C (Individual)') }}</option>
                                    <option value="corporate" {{ old('client_type') == 'corporate' ? 'selected' : '' }}>
                                        {{ __('B2B (Corporate)') }}</option>
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
                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3 d-none client-name-field" id="client_first_name_container">
                                <label class="form-label" for="client_first_name">{{ __('Client First Name') }}</label>
                                <input type="text" class="form-control @error('client_first_name') is-invalid @enderror"
                                    id="client_first_name" name="client_first_name"
                                    value="{{ old('client_first_name') }}" />
                                @error('client_first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3 d-none client-name-field" id="client_last_name_container">
                                <label class="form-label" for="client_last_name">{{ __('Client Last Name') }}</label>
                                <input type="text" class="form-control @error('client_last_name') is-invalid @enderror"
                                    id="client_last_name" name="client_last_name"
                                    value="{{ old('client_last_name') }}" />
                                @error('client_last_name')
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
                                            {{ old('customer_nationality') == $nationality['nationality'] ? 'selected' : '' }}>
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
                                            {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
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
                                    id="check_in" name="check_in" value="{{ old('check_in') }}" required />
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="check_out">{{ __('Check Out Date') }}</label>
                                <input type="date" class="form-control @error('check_out') is-invalid @enderror"
                                    id="check_out" name="check_out" value="{{ old('check_out') }}" required />
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="nights">{{ __('Number of Nights') }}</label>
                                <input type="number" class="form-control" id="nights" readonly />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="option_date">{{ __('Option Date') }}</label>
                                <input type="date" class="form-control @error('option_date') is-invalid @enderror"
                                    id="option_date" name="option_date" value="{{ old('option_date') }}" />
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
                                            {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
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
                                    id="meals_plan" name="meals_plan" value="{{ old('meals_plan') }}" />
                                @error('meals_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Payment Status') }}</label>
                                <div class="form-control bg-light text-muted" style="cursor: default;">
                                    <i class="ti tabler-calculator me-1"></i>
                                    {{ __('Auto-calculated from paid amount') }}
                                </div>
                                <small class="text-muted">
                                    <i class="ti tabler-info-circle me-1"></i>
                                    {{ __('0 = Unpaid · Partial > 0 · Full = Paid · Over = Overpaid') }}
                                </small>
                            </div>
                        </div>

                        {{-- Rooms & Guest Details --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Rooms & Guest Details') }}</h5>

                        <div id="roomsContainer">
                            <div class="room-row mb-3 p-3 border rounded bg-light">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">{{ __('Room Type') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="rooms[0][room_type]" class="form-select" required>
                                            <option value="">{{ __('Select') }}</option>
                                            <option value="SGL">SGL</option>
                                            <option value="DBL">DBL</option>
                                            <option value="TPL">TPL</option>
                                            <option value="QUD">QUD</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">{{ __('Category') }}</label>
                                        <input type="text" name="rooms[0][category]" class="form-control"
                                            placeholder="{{ __('Optional') }}" />
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">{{ __('Count') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="rooms[0][room_count]" class="form-control room-count"
                                            value="1" min="1" required />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">{{ __('Net Rate') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="any" name="rooms[0][price]"
                                            class="form-control room-price" min="0" required />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">{{ __('Margin') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="any" name="rooms[0][margin]"
                                            class="form-control room-margin" min="0" required />
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-room w-100" disabled>
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-info">{{ __('Children') }}</label>
                                        <input type="number" name="rooms[0][child_count]"
                                            class="form-control room-child-count" value="0" min="0" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-info">{{ __('Child Net Rate') }}</label>
                                        <input type="number" step="any" name="rooms[0][child_price]"
                                            class="form-control room-child-price" value="0" min="0" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-info">{{ __('Child Margin') }}</label>
                                        <input type="number" step="any" name="rooms[0][child_margin]"
                                            class="form-control room-child-margin" value="0" min="0" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary mb-4" id="addRoom">
                            <i class="ti tabler-plus me-2"></i>{{ __('Add Room') }}
                        </button>

                        {{-- Additions --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Additions') }}</h5>

                        <div id="additionsContainer">
                            <!-- Additions will be added here -->
                        </div>

                        <button type="button" class="btn btn-success mb-4" id="addAddition">
                            <i class="ti tabler-plus me-2"></i>{{ __('Add Addition') }}
                        </button>

                        {{-- Discounts --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Discounts') }}</h5>

                        <div id="discountsContainer">
                            <!-- Discounts will be added here -->
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
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="paid_amount">{{ __('Paid Amount') }}</label>
                                <input type="number" step="any" name="paid_amount" id="paid_amount"
                                    class="form-control form-control-lg" value="{{ old('paid_amount', 0) }}" min="0" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Save Booking') }}</button>
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
        let roomIndex = 1;
        let additionIndex = 0;
        let discountIndex = 0;

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
        // document.addEventListener('keydown', function(e) {
        //     if (e.target.type === 'number') {
        //         // Prevent negative, e, E, + for all number inputs
        //         if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+') {
        //             e.preventDefault();
        //         }
        //
        //         // Prevent decimal point (.) and comma (,) for integer fields
        //         if ((e.target.classList.contains('room-count') || e.target.classList.contains(
        //                 'room-child-count')) &&
        //             (e.key === '.' || e.key === ',')) {
        //             e.preventDefault();
        //         }
        //     }
        // });

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

            customerSelect.innerHTML = '<option value="">{{ __('Select Customer') }}</option>';

            if (clientType) {
                customerSelect.disabled = false;
                customers.filter(c => c.type === clientType).forEach(customer => {
                    const option = document.createElement('option');
                    option.value = customer.id;
                    option.textContent = customer.name;
                    option.dataset.type = customer.type;
                    customerSelect.appendChild(option);
                });
            } else {
                customerSelect.disabled = true;
            }
            toggleClientName();
        });

        // Customer change handler
        document.getElementById('customer_id').addEventListener('change', toggleClientName);

        function toggleClientName() {
            const customerSelect = document.getElementById('customer_id');
            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            const isCorporate = selectedOption && selectedOption.dataset.type === 'corporate';
            const inputs = [
                document.getElementById('client_first_name'),
                document.getElementById('client_last_name'),
            ];

            document.querySelectorAll('.client-name-field').forEach(container => {
                container.classList.toggle('d-none', !isCorporate);
            });

            inputs.forEach(input => {
                input.required = isCorporate;
                if (!isCorporate) {
                    input.value = '';
                }
            });
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
                        <input type="number" step="any" name="rooms[${roomIndex}][price]" class="form-control room-price" min="0" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Margin') }} <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="rooms[${roomIndex}][margin]" class="form-control room-margin" min="0" required />
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
                        <input type="number" step="any" name="rooms[${roomIndex}][child_price]" class="form-control room-child-price" value="0" min="0" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-info">{{ __('Child Margin') }}</label>
                        <input type="number" step="any" name="rooms[${roomIndex}][child_margin]" class="form-control room-child-margin" value="0" min="0" />
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
                        <input type="number" step="any" name="additions[${additionIndex}][net_rate]" class="form-control addition-net-rate" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Guest Rate') }}</label>
                        <input type="number" step="any" name="additions[${additionIndex}][guest_rate]" class="form-control addition-guest-rate" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Margin') }}</label>
                        <input type="number" step="any" name="additions[${additionIndex}][margin]" class="form-control addition-margin" readonly />
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
                        <input type="number" step="any" name="discounts[${discountIndex}][net_rate]" class="form-control discount-net-rate" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Guest Rate') }}</label>
                        <input type="number" step="any" name="discounts[${discountIndex}][guest_rate]" class="form-control discount-guest-rate" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Margin') }}</label>
                        <input type="number" step="any" name="discounts[${discountIndex}][margin]" class="form-control discount-margin" readonly />
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

            // Update max attribute for paid_amount input
            // if (paidAmountInput) {
            //    paidAmountInput.setAttribute('max', finalTotal);
            // }

            let paidAmount = paidAmountInput ? parseFloat(paidAmountInput.value) || 0 : 0;

            // Validate paid amount doesn't exceed total guest rate
            // if (paidAmount > finalTotal) {
            //    if (paidAmountInput) {
            //        paidAmountInput.setCustomValidity('{{ __('Paid amount cannot exceed total guest rate') }}');
            //        paidAmountInput.value = finalTotal.toFixed(2);
            //    }
            //    // Use corrected value for calculation
            //    paidAmount = finalTotal;
            // } else {
            //    if (paidAmountInput) {
            //        paidAmountInput.setCustomValidity('');
            //    }
            // }

            const remainingAmount = netRateTotal - paidAmount;

            // Update display
            const premarginTotalEl = document.getElementById('premarginTotal');
            const marginValueEl = document.getElementById('marginValue');
            const finalTotalEl = document.getElementById('finalTotal');
            const remainingAmountEl = document.getElementById('remainingAmount');

            if (premarginTotalEl) premarginTotalEl.textContent = formatNumber(netRateTotal);
            if (marginValueEl) marginValueEl.textContent = formatNumber(totalMarginValue);
            if (finalTotalEl) finalTotalEl.textContent = formatNumber(finalTotal);
            if (remainingAmountEl) remainingAmountEl.textContent = formatNumber(remainingAmount);
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
                    marginInput.value = roundNumber(margin);
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
                    marginInput.value = roundNumber(margin);
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
                // Additional validation on input
                const finalTotalEl = document.getElementById('finalTotal');
                if (finalTotalEl) {
                    const finalTotal = parseFloat(finalTotalEl.textContent) || 0;
                    const paidAmount = parseFloat(this.value) || 0;
                    if (paidAmount > finalTotal) {
                        this.setCustomValidity('{{ __('Paid amount cannot exceed total guest rate') }}');
                    } else {
                        this.setCustomValidity('');
                    }
                }
            });
        }

        // Initial setup
        attachCalculationListeners();
    </script>
@endsection
