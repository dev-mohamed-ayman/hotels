@extends('admin.layouts.app')

@section('title', __('Customers'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Customers List') }}</h5>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Customer') }}
            </a>
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
                                                <option value="individual"
                                                    {{ request('type') == 'individual' ? 'selected' : '' }}>
                                                    {{ __('Individual') }}
                                                </option>
                                                <option value="corporate"
                                                    {{ request('type') == 'corporate' ? 'selected' : '' }}>
                                                    {{ __('Corporate') }}
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
                        <th>{{ __('Interested Hotels') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
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
                                @if ($customer->hotels->count() > 0)
                                    <span class="badge bg-label-success">{{ $customer->hotels->count() }}</span>
                                @else
                                    <span class="badge bg-label-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customers.edit', $customer->id) }}"
                                    class="btn btn-sm btn-icon btn-success text-white" data-bs-toggle="tooltip"
                                    title="{{ __('Edit') }}">
                                    <i class="ti tabler-edit ti-sm"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                    class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger text-white"
                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                        <i class="ti tabler-trash ti-sm"></i>
                                    </button>
                                </form>
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
@endsection
