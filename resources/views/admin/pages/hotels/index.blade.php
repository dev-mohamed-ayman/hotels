@extends('admin.layouts.app')

@section('title', __('Hotels'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Hotels List') }}</h5>
            <a href="{{ route('hotels.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Hotel') }}
            </a>
        </div>
        <div class="card-body">
            <!-- Filters Section -->
            <div class="mb-3">
                <div class="accordion" id="filterAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button {{ request()->hasAny(['search', 'sort_by']) ? '' : 'collapsed' }}"
                                type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="ti tabler-filter me-2"></i>
                                {{ __('Filter Hotels') }}
                                @php
                                    $activeFilters = 0;
                                    if (request('search')) {
                                        $activeFilters++;
                                    }
                                    if (request('sort_by')) {
                                        $activeFilters++;
                                    }
                                @endphp
                                @if ($activeFilters > 0)
                                    <span class="badge bg-primary ms-2">{{ $activeFilters }}</span>
                                @endif
                            </button>
                        </h2>
                        <div id="filterCollapse"
                            class="accordion-collapse collapse {{ request()->hasAny(['search', 'sort_by']) ? 'show' : '' }}">
                            <div class="accordion-body">
                                <form method="GET" action="{{ route('hotels.index') }}">
                                    <div class="row g-3">
                                        <!-- Search -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" name="search" class="form-control"
                                                value="{{ request('search') }}"
                                                placeholder="{{ __('Hotel name or address') }}">
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
                                                <option value="address"
                                                    {{ request('sort_by') == 'address' ? 'selected' : '' }}>
                                                    {{ __('Address') }}</option>
                                                <option value="is_active"
                                                    {{ request('sort_by') == 'is_active' ? 'selected' : '' }}>
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
                                        <a href="{{ route('hotels.index') }}" class="btn btn-secondary">
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
            @if (request()->hasAny(['search', 'sort_by']))
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
                        <th>{{ __('Address') }}</th>
                        <th>{{ __('Bank Accounts') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotels as $hotel)
                        <tr>
                            <td>{{ $hotel->name }}</td>
                            <td>{{ $hotel->address }}</td>
                            <td>
                                @if ($hotel->bankAccounts->count() > 0)
                                    <span class="badge bg-label-success">{{ $hotel->bankAccounts->count() }}</span>
                                @else
                                    <span class="badge bg-label-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                @if ($hotel->is_active)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                        id="actionsDropdown{{ $hotel->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="actionsDropdown{{ $hotel->id }}">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('hotels.show', $hotel->id) }}">
                                                <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('hotels.edit', $hotel->id) }}">
                                                <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST"
                                                class="d-inline-block"
                                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="mb-0">{{ __('No hotels found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($hotels->hasPages())
            <div class="card-footer">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>
@endsection
