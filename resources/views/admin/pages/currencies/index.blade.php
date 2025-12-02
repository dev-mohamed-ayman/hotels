@extends('admin.layouts.app')

@section('title', __('Currencies'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Currencies List') }}</h5>
            <a href="{{ route('currencies.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Currency') }}
            </a>
        </div>
        <div class="card-body">
            <!-- Filters Section -->
            <div class="mb-3">
                <div class="accordion" id="filterAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button {{ request()->hasAny(['search', 'status']) ? '' : 'collapsed' }}"
                                type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="ti tabler-filter me-2"></i>
                                {{ __('Filter Bookings') }}
                                @php
                                    $activeFilters = 0;
                                    if (request('search')) {
                                        $activeFilters++;
                                    }
                                    if (request('status')) {
                                        $activeFilters++;
                                    }
                                @endphp
                                @if ($activeFilters > 0)
                                    <span class="badge bg-primary ms-2">{{ $activeFilters }}</span>
                                @endif
                            </button>
                        </h2>
                        <div id="filterCollapse"
                            class="accordion-collapse collapse {{ request()->hasAny(['search', 'status']) ? 'show' : '' }}">
                            <div class="accordion-body">
                                <form method="GET" action="{{ route('currencies.index') }}">
                                    <div class="row g-3">
                                        <!-- Search -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" name="search" class="form-control"
                                                value="{{ request('search') }}" placeholder="{{ __('Code or Symbol') }}">
                                        </div>

                                        <!-- Status Filter -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <select name="status" class="form-select">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="active"
                                                    {{ request('status') == 'active' ? 'selected' : '' }}>
                                                    {{ __('Active') }}
                                                </option>
                                                <option value="inactive"
                                                    {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                                    {{ __('Inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti tabler-search me-2"></i>{{ __('Apply Filters') }}
                                        </button>
                                        <a href="{{ route('currencies.index') }}" class="btn btn-secondary">
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
            @if (request()->hasAny(['search', 'status']))
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

                        @if (request('status'))
                            <span class="badge bg-label-primary">
                                {{ __('Status') }}: {{ __(ucfirst(request('status'))) }}
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
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
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Symbol') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currencies as $currency)
                        <tr>
                            <td>{{ $currency->name }}</td>
                            <td><span class="badge bg-label-primary">{{ $currency->code }}</span></td>
                            <td>{{ $currency->symbol }}</td>
                            <td>
                                @if ($currency->is_active)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('currencies.edit', $currency->id) }}"
                                    class="btn btn-sm btn-icon btn-success text-white" data-bs-toggle="tooltip"
                                    title="{{ __('Edit') }}">
                                    <i class="ti tabler-edit ti-sm"></i>
                                </a>
                                <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST"
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
                            <td colspan="5" class="text-center py-5">
                                <p class="mb-0">{{ __('No currencies found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
