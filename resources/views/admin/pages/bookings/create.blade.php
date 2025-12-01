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
                                    <option value="P2C" {{ old('client_type') == 'P2C' ? 'selected' : '' }}>
                                        {{ __('P2C (Corporate)') }}</option>
                                    <option value="P2P" {{ old('client_type') == 'P2P' ? 'selected' : '' }}>
                                        {{ __('P2P (Individual)') }}</option>
                                </select>
                                @error('client_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="customer_id">{{ __('Customer') }}</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                    name="customer_id" required disabled>
                                    <option value="">{{ __('Select Customer') }}</option>
                                </select>
                                @error('customer_id')
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
                                <label class="form-label" for="payment_date">{{ __('Payment Date') }}</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                    id="payment_date" name="payment_date" value="{{ old('payment_date') }}" />
                                @error('payment_date')
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
                        </div>

                        {{-- Rooms & Guest Details --}}
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Rooms & Guest Details') }}</h5>

                        <div id="roomsContainer">
                            <div class="room-row mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Room Type') }}</label>
                                        <select name="rooms[0][room_type]" class="form-select" required>
                                            <option value="">{{ __('Select') }}</option>
                                            <option value="SGL">SGL</option>
                                            <option value="DBL">DBL</option>
                                            <option value="TPL">TPL</option>
                                            <option value="QUD">QUD</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">{{ __('Count') }}</label>
                                        <input type="number" name="rooms[0][room_count]" class="form-control room-count"
                                            value="1" min="1" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Price') }}</label>
                                        <input type="number" step="0.01" name="rooms[0][price]"
                                            class="form-control room-price" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Category') }}</label>
                                        <input type="text" name="rooms[0][category]" class="form-control" />
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">{{ __('Margin') }}</label>
                                        <input type="number" step="0.01" name="rooms[0][margin]"
                                            class="form-control room-margin" required />
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">{{ __('Children') }}</label>
                                        <input type="number" name="rooms[0][child_count]"
                                            class="form-control room-child-count" value="0" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Child Margin') }}</label>
                                        <input type="number" step="0.01" name="rooms[0][child_margin]"
                                            class="form-control room-child-margin" value="0" />
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-room" disabled>
                                            <i class="ti tabler-trash"></i>
                                        </button>
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
                        <h5 class="mb-3">{{ __('Booking Summary') }}</h5>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>{{ __('Total Child Cost') }}</h6>
                                        <h4 id="totalChildCost">0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>{{ __('Pre-Margin Total') }}</h6>
                                        <h4 id="premarginTotal">0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>{{ __('Margin Value') }}</h6>
                                        <h4 id="marginValue">0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>{{ __('Total Additions') }}</h6>
                                        <h4 id="totalAdditions">0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6>{{ __('Total Discounts') }}</h6>
                                        <h4 id="totalDiscounts">0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6>{{ __('Final Total') }}</h6>
                                        <h4 id="finalTotal">0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <label class="form-label">{{ __('Paid Amount') }}</label>
                                <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                                    class="form-control" value="0" />
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>{{ __('Remaining Amount') }}</h6>
                                        <h4 id="remainingAmount">0.00</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label" for="notes">{{ __('Notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
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

    <script>
        // Customer data for filtering
        const customers = @json($customers);
        let roomIndex = 1;
        let additionIndex = 0;
        let discountIndex = 0;

        // Client type change handler
        document.getElementById('client_type').addEventListener('change', function() {
            const clientType = this.value;
            const customerSelect = document.getElementById('customer_id');

            customerSelect.innerHTML = '<option value="">{{ __('Select Customer') }}</option>';

            if (clientType) {
                customerSelect.disabled = false;
                const filterType = clientType === 'P2C' ? 'corporate' : 'individual';

                customers.filter(c => c.type === filterType).forEach(customer => {
                    const option = document.createElement('option');
                    option.value = customer.id;
                    option.textContent = customer.name;
                    customerSelect.appendChild(option);
                });
            } else {
                customerSelect.disabled = true;
            }
        });

        // Calculate nights
        function calculateNights() {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;

            if (checkIn && checkOut) {
                const date1 = new Date(checkIn);
                const date2 = new Date(checkOut);
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                document.getElementById('nights').value = diffDays;
            }
        }

        document.getElementById('check_in').addEventListener('change', calculateNights);
        document.getElementById('check_out').addEventListener('change', calculateNights);

        // Add room
        document.getElementById('addRoom').addEventListener('click', function() {
            const container = document.getElementById('roomsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'room-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Room Type') }}</label>
                        <select name="rooms[${roomIndex}][room_type]" class="form-select" required>
                            <option value="">{{ __('Select') }}</option>
                            <option value="SGL">SGL</option>
                            <option value="DBL">DBL</option>
                            <option value="TPL">TPL</option>
                            <option value="QUD">QUD</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">{{ __('Count') }}</label>
                        <input type="number" name="rooms[${roomIndex}][room_count]" class="form-control room-count" value="1" min="1" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Price') }}</label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][price]" class="form-control room-price" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Category') }}</label>
                        <input type="text" name="rooms[${roomIndex}][category]" class="form-control" />
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">{{ __('Margin') }}</label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][margin]" class="form-control room-margin" required />
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">{{ __('Children') }}</label>
                        <input type="number" name="rooms[${roomIndex}][child_count]" class="form-control room-child-count" value="0" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Child Margin') }}</label>
                        <input type="number" step="0.01" name="rooms[${roomIndex}][child_margin]" class="form-control room-child-margin" value="0" />
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-room">
                            <i class="ti tabler-trash"></i>
                        </button>
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
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="additions[${additionIndex}][amount]" class="form-control addition-amount" required />
                    </div>
                    <div class="col-md-6">
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
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="discounts[${discountIndex}][amount]" class="form-control discount-amount" required />
                    </div>
                    <div class="col-md-6">
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

            // Calculate from rooms (multiplied by room_count)
            document.querySelectorAll('.room-row').forEach(row => {
                const roomCount = parseFloat(row.querySelector('.room-count').value) || 1;
                const price = parseFloat(row.querySelector('.room-price').value) || 0;
                const margin = parseFloat(row.querySelector('.room-margin').value) || 0;
                const childCount = parseFloat(row.querySelector('.room-child-count').value) || 0;
                const childMargin = parseFloat(row.querySelector('.room-child-margin').value) || 0;

                premarginTotal += price * roomCount;
                marginValue += margin * roomCount;
                totalChildCost += (childCount * childMargin) * roomCount;
            });

            // Calculate additions
            let additionsTotal = 0;
            document.querySelectorAll('.addition-amount').forEach(input => {
                additionsTotal += parseFloat(input.value) || 0;
            });

            // Calculate discounts
            let discountsTotal = 0;
            document.querySelectorAll('.discount-amount').forEach(input => {
                discountsTotal += parseFloat(input.value) || 0;
            });

            const finalTotal = premarginTotal + marginValue + totalChildCost + additionsTotal - discountsTotal;
            const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
            const remainingAmount = finalTotal - paidAmount;

            // Update display
            document.getElementById('totalChildCost').textContent = totalChildCost.toFixed(2);
            document.getElementById('premarginTotal').textContent = premarginTotal.toFixed(2);
            document.getElementById('marginValue').textContent = marginValue.toFixed(2);
            document.getElementById('totalAdditions').textContent = additionsTotal.toFixed(2);
            document.getElementById('totalDiscounts').textContent = discountsTotal.toFixed(2);
            document.getElementById('finalTotal').textContent = finalTotal.toFixed(2);
            document.getElementById('remainingAmount').textContent = remainingAmount.toFixed(2);
        }

        function attachCalculationListeners() {
            document.querySelectorAll(
                '.room-count, .room-price, .room-margin, .room-child-count, .room-child-margin, .addition-amount, .discount-amount'
                ).forEach(input => {
                input.removeEventListener('input', calculateSummary);
                input.addEventListener('input', calculateSummary);
            });
        }

        document.getElementById('paid_amount').addEventListener('input', calculateSummary);

        // Initial setup
        attachCalculationListeners();
    </script>
@endsection
