@extends('admin.layouts.app')

@section('title', __('Add Hotel'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Add New Hotel') }}</h5>
                    <a href="{{ route('hotels.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotels.store') }}" method="POST" id="hotelForm">
                        @csrf

                        <!-- Hotel Basic Info -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">{{ __('Hotel Name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="address">{{ __('Address') }}</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address') }}" required />
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_active"
                                        name="is_active" {{ old('is_active', true) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="is_active">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Accounts Section -->
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Bank Accounts') }}</h5>

                        <div id="bankAccountsContainer">
                            <!-- Initial bank account row -->
                            <div class="bank-account-row mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Currency') }}</label>
                                        <select name="bank_accounts[0][currency_id]" class="form-select">
                                            <option value="">{{ __('Select Currency') }}</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}
                                                    ({{ $currency->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Bank Name') }}</label>
                                        <input type="text" name="bank_accounts[0][bank_name]" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Account Number') }}</label>
                                        <input type="text" name="bank_accounts[0][account_number]"
                                            class="form-control" />
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-account" disabled>
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary mb-4" id="addBankAccount">
                            <i class="ti tabler-plus me-2"></i>{{ __('Add Bank Account') }}
                        </button>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Save Hotel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let accountIndex = 1;

        document.getElementById('addBankAccount').addEventListener('click', function() {
            const container = document.getElementById('bankAccountsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'bank-account-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Currency') }}</label>
                        <select name="bank_accounts[${accountIndex}][currency_id]" class="form-select">
                            <option value="">{{ __('Select Currency') }}</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Bank Name') }}</label>
                        <input type="text" name="bank_accounts[${accountIndex}][bank_name]" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Account Number') }}</label>
                        <input type="text" name="bank_accounts[${accountIndex}][account_number]" class="form-control" />
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-account">
                            <i class="ti tabler-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            accountIndex++;
            updateRemoveButtons();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-account') || e.target.closest('.remove-account')) {
                const row = e.target.closest('.bank-account-row');
                row.remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.bank-account-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-account');
                if (rows.length === 1) {
                    removeBtn.disabled = true;
                } else {
                    removeBtn.disabled = false;
                }
            });
        }
    </script>
@endsection
