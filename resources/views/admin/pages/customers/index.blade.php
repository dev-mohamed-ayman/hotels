@extends('admin.layouts.app')

@section('title', __('Customers'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Customers List') }}</h5>
            @can('create customers')
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-2"></i>{{ __('Add Customer') }}
                </a>
            @endcan
        </div>
        <div class="card-body">
            <!-- Filters Section -->
            <div class="mb-3">
                <div class="accordion" id="filterAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button {{ request()->hasAny(['search', 'type', 'priority', 'source']) ? '' : 'collapsed' }}"
                                type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="ti tabler-filter me-2"></i>
                                {{ __('Filter Bookings') }}
                                @php
                                    $activeFilters = 0;
                                    if (request('search')) {
                                        $activeFilters++;
                                    }
                                    if (request('type')) {
                                        $activeFilters++;
                                    }
                                    if (request('priority')) {
                                        $activeFilters++;
                                    }
                                    if (request('source')) {
                                        $activeFilters++;
                                    }
                                @endphp
                                @if ($activeFilters > 0)
                                    <span class="badge bg-primary ms-2">{{ $activeFilters }}</span>
                                @endif
                            </button>
                        </h2>
                        <div id="filterCollapse"
                            class="accordion-collapse collapse {{ request()->hasAny(['search', 'type', 'priority', 'source']) ? 'show' : '' }}">
                            <div class="accordion-body">
                                <form method="GET" action="{{ route('customers.index') }}">
                                    <div class="row g-3">
                                        <!-- Search -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" name="search" class="form-control"
                                                value="{{ request('search') }}" placeholder="{{ __('Name or Phone') }}">
                                        </div>

                                        <!-- Type Filter -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Type') }}</label>
                                            <select name="type" class="form-select">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="B2C" {{ request('type') == 'B2C' ? 'selected' : '' }}>
                                                    {{ __('B2C (Individual)') }}
                                                </option>
                                                <option value="B2B" {{ request('type') == 'B2B' ? 'selected' : '' }}>
                                                    {{ __('B2B (Corporate)') }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Priority Filter -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Priority') }}</label>
                                            <select name="priority" class="form-select">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="low"
                                                    {{ request('priority') == 'low' ? 'selected' : '' }}>
                                                    {{ __('Low') }}
                                                </option>
                                                <option value="medium"
                                                    {{ request('priority') == 'medium' ? 'selected' : '' }}>
                                                    {{ __('Medium') }}
                                                </option>
                                                <option value="high"
                                                    {{ request('priority') == 'high' ? 'selected' : '' }}>
                                                    {{ __('High') }}
                                                </option>
                                                <option value="urgent"
                                                    {{ request('priority') == 'urgent' ? 'selected' : '' }}>
                                                    {{ __('Urgent') }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Source Filter -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Source') }}</label>
                                            <select name="source" class="form-select">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="website"
                                                    {{ request('source') == 'website' ? 'selected' : '' }}>
                                                    {{ __('Website') }}
                                                </option>
                                                <option value="social_media"
                                                    {{ request('source') == 'social_media' ? 'selected' : '' }}>
                                                    {{ __('Social Media') }}
                                                </option>
                                                <option value="referral"
                                                    {{ request('source') == 'referral' ? 'selected' : '' }}>
                                                    {{ __('Referral') }}
                                                </option>
                                                <option value="direct_visit"
                                                    {{ request('source') == 'direct_visit' ? 'selected' : '' }}>
                                                    {{ __('Direct Visit') }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Status Filter -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <select name="status" class="form-select">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="potential"
                                                    {{ request('status') == 'potential' ? 'selected' : '' }}>
                                                    {{ __('Potential') }}
                                                </option>
                                                <option value="active"
                                                    {{ request('status') == 'active' ? 'selected' : '' }}>
                                                    {{ __('Active') }}
                                                </option>
                                                <option value="cancelled"
                                                    {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                                    {{ __('Cancelled') }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Sort By -->
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Sort By') }}</label>
                                            <select name="sort_by" class="form-select">
                                                <option value="created_at"
                                                    {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                                    {{ __('Created At') }}</option>
                                                <option value="name"
                                                    {{ request('sort_by') == 'name' ? 'selected' : '' }}>
                                                    {{ __('Name') }}</option>
                                                <option value="email"
                                                    {{ request('sort_by') == 'email' ? 'selected' : '' }}>
                                                    {{ __('Email') }}</option>
                                                <option value="phone_1"
                                                    {{ request('sort_by') == 'phone_1' ? 'selected' : '' }}>
                                                    {{ __('Phone') }}</option>
                                                <option value="type"
                                                    {{ request('sort_by') == 'type' ? 'selected' : '' }}>
                                                    {{ __('Type') }}</option>
                                                <option value="status"
                                                    {{ request('sort_by') == 'status' ? 'selected' : '' }}>
                                                    {{ __('Status') }}</option>
                                                <option value="priority"
                                                    {{ request('sort_by') == 'priority' ? 'selected' : '' }}>
                                                    {{ __('Priority') }}</option>
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
                                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
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
            @if (request()->hasAny(['search', 'type', 'priority', 'source']))
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

                        @if (request('type'))
                            <span class="badge bg-label-primary">
                                {{ __('Type') }}: {{ __(ucfirst(request('type'))) }}
                                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="text-white ms-1">×</a>
                            </span>
                        @endif

                        @if (request('priority'))
                            <span class="badge bg-label-primary">
                                {{ __('Priority') }}: {{ __(ucfirst(request('priority'))) }}
                                <a href="{{ request()->fullUrlWithQuery(['priority' => null]) }}"
                                    class="text-white ms-1">×</a>
                            </span>
                        @endif

                        @if (request('source'))
                            <span class="badge bg-label-primary">
                                {{ __('Source') }}: {{ __(str_replace('_', ' ', ucfirst(request('source')))) }}
                                <a href="{{ request()->fullUrlWithQuery(['source' => null]) }}"
                                    class="text-white ms-1">×</a>
                            </span>
                        @endif

                        @if (request('status'))
                            <span class="badge bg-label-primary">
                                {{ __('Status') }}: {{ __(ucfirst(request('status'))) }}
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
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
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top table-bordered table-hover table-sm text-center">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Phone 1') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Last Follow-up') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone_1 }}</td>
                            <td>{{ $customer->email ?? '-' }}</td>
                            {{-- <td>
                                @foreach ($customer->walletTransactions->groupBy('currency_id') as $currencyId => $transactions)
                                    @php
                                        $balance = $transactions->sum(function ($t) {
                                            return $t->type == 'credit' ? $t->amount : -$t->amount;
                                        });
                                        $currency = $transactions->first()->currency;
                                    @endphp
                                    @if ($balance != 0)
                                        <div>
                                            <small>{{ number_format($balance, 2) }} {{ $currency->code ?? '' }}</small>
                                        </div>
                                    @endif
                                @endforeach
                                @if ($customer->walletTransactions->isEmpty())
                                    0.00
                                @endif
                            </td> --}}
                            <td>
                                @if ($customer->type == 'individual')
                                    <span class="badge bg-label-primary">{{ __('B2C (Individual)') }}</span>
                                @else
                                    <span class="badge bg-label-info">{{ __('B2B (Corporate)') }}</span>
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
                                @if ($customer->priority == 'low')
                                    <span class="badge bg-label-secondary">{{ __('Low') }}</span>
                                @elseif ($customer->priority == 'medium')
                                    <span class="badge bg-label-info">{{ __('Medium') }}</span>
                                @elseif ($customer->priority == 'high')
                                    <span class="badge bg-label-warning">{{ __('High') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Urgent') }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $latestFollowUp = $customer->latestFollowUp;
                                @endphp
                                @if ($latestFollowUp)
                                    <button type="button" class="btn btn-sm p-0 border-0 follow-up-status-btn"
                                        data-customer-id="{{ $customer->id }}"
                                        data-follow-up-id="{{ $latestFollowUp->id }}"
                                        data-current-status="{{ $latestFollowUp->status }}" data-bs-toggle="modal"
                                        data-bs-target="#changeFollowUpStatusModal">
                                        @if ($latestFollowUp->status == 'none')
                                            <span class="badge bg-label-secondary">{{ __('None') }}</span>
                                        @elseif($latestFollowUp->status == 'in_progress')
                                            <span class="badge bg-label-primary">{{ __('In Progress') }}</span>
                                        @elseif($latestFollowUp->status == 'awaiting_replay')
                                            <span class="badge bg-label-warning">{{ __('Awaiting Replay') }}</span>
                                        @elseif($latestFollowUp->status == 'completed')
                                            <span class="badge bg-label-success">{{ __('Completed') }}</span>
                                        @elseif($latestFollowUp->status == 'canceled')
                                            <span class="badge bg-label-danger">{{ __('Canceled') }}</span>
                                        @endif
                                    </button>
                                @else
                                    <span class="badge bg-label-secondary">{{ __('None') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                        id="actionsDropdown{{ $customer->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="actionsDropdown{{ $customer->id }}">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('customers.show', $customer->id) }}">
                                                <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                            </a>
                                        </li>
                                        @can('edit customers')
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('customers.edit', $customer->id) }}">
                                                    <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('delete customers')
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('customers.destroy', $customer->id) }}"
                                                    method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                                        <i class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <p class="mb-0">{{ __('No customers found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="card-footer">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Change Follow-up Status Modal -->
    <div class="modal fade" id="changeFollowUpStatusModal" tabindex="-1"
        aria-labelledby="changeFollowUpStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeFollowUpStatusModalLabel">{{ __('Change Follow-up Status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changeFollowUpStatusForm">
                    <div class="modal-body">
                        <input type="hidden" id="modalCustomerId" name="customer_id">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }} *</label>
                            <select class="form-select" name="status" id="modalFollowUpStatus" required>
                                <option value="none">{{ __('None') }}</option>
                                <option value="in_progress">{{ __('In Progress') }}</option>
                                <option value="awaiting_replay">{{ __('Awaiting Replay') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="canceled">{{ __('Canceled') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Handle follow-up status button click
            $('.follow-up-status-btn').on('click', function() {
                const customerId = $(this).data('customer-id');
                const currentStatus = $(this).data('current-status');

                $('#modalCustomerId').val(customerId);
                $('#modalFollowUpStatus').val(currentStatus);
            });

            // Handle change follow-up status form submission
            $('#changeFollowUpStatusForm').on('submit', function(e) {
                e.preventDefault();

                const customerId = $('#modalCustomerId').val();
                const status = $('#modalFollowUpStatus').val();

                $.ajax({
                    url: '{{ route('follow-ups.update-latest', ':id') }}'.replace(':id',
                        customerId),
                    method: 'PUT',
                    data: {
                        status: status,
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ||
                            '{{ __('Failed to update follow-up status') }}');
                    }
                });
            });
        });
    </script>
@endsection
