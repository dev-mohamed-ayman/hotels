@extends('admin.layouts.app')

@section('title', __('Hotel Details'))

@section('content')
    <div class="row">
        <!-- Hotel Information Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Hotel Information') }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('hotels.edit', $hotel->id) }}" class="btn btn-primary btn-sm">
                            <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                        </a>
                        <a href="{{ route('hotels.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-3">{{ __('Basic Information') }}</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Name') }}</label>
                                <p class="mb-0 fw-semibold">{{ $hotel->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Address') }}</label>
                                <p class="mb-0">{{ $hotel->address }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Status') }}</label>
                                <p class="mb-0">
                                    @if ($hotel->is_active)
                                        <span class="badge bg-label-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Created At') }}</label>
                                <p class="mb-0">{{ formatDateTime($hotel->created_at) }}</p>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-3">{{ __('Statistics') }}</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Interested Customers') }}</label>
                                <p class="mb-0 fw-semibold">{{ $hotel->customers->count() }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Bank Accounts') }}</label>
                                <p class="mb-0 fw-semibold">{{ $hotel->bankAccounts->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Accounts -->
        @if ($hotel->bankAccounts->count() > 0)
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Bank Accounts') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Currency') }}</th>
                                        <th>{{ __('Bank Name') }}</th>
                                        <th>{{ __('Account Number') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hotel->bankAccounts as $account)
                                        <tr>
                                            <td>
                                                @if ($account->currency)
                                                    <span
                                                        class="badge bg-label-primary">{{ $account->currency->symbol }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $account->bank_name }}</td>
                                            <td>{{ $account->account_number }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Interested Customers -->
        @if ($hotel->customers->count() > 0)
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Interested Customers') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hotel->customers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->phone_1 }}</td>
                                            <td>{{ $customer->email ?? '-' }}</td>
                                            <td>
                                                @if ($customer->type == 'individual')
                                                    <span class="badge bg-label-primary">{{ __('Individual') }}</span>
                                                @else
                                                    <span class="badge bg-label-info">{{ __('Corporate') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($customer->status == 'potential')
                                                    <span class="badge bg-label-warning">{{ __('Potential') }}</span>
                                                @elseif ($customer->status == 'active')
                                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('customers.show', $customer->id) }}"
                                                    class="btn btn-sm btn-icon btn-info text-white" data-bs-toggle="tooltip"
                                                    title="{{ __('View') }}">
                                                    <i class="ti tabler-eye ti-sm"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
