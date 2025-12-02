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
                            <button class="accordion-button {{ request('search') ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="ti tabler-filter me-2"></i>
                                {{ __('Filter Bookings') }}
                                @if (request('search'))
                                    <span class="badge bg-primary ms-2">1</span>
                                @endif
                            </button>
                        </h2>
                        <div id="filterCollapse" class="accordion-collapse collapse {{ request('search') ? 'show' : '' }}">
                            <div class="accordion-body">
                                <form method="GET" action="{{ route('hotels.index') }}">
                                    <div class="row g-3">
                                        <!-- Search -->
                                        <div class="col-md-12">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" name="search" class="form-control"
                                                value="{{ request('search') }}"
                                                placeholder="{{ __('Hotel name or address') }}">
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
            @if (request('search'))
                <div class="mb-3">
                    <strong>{{ __('Active Filters') }}:</strong>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="badge bg-label-primary">
                            {{ __('Search') }}: {{ request('search') }}
                            <a href="{{ route('hotels.index') }}" class="text-white ms-1">×</a>
                        </span>
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
                                <a href="{{ route('hotels.edit', $hotel->id) }}"
                                    class="btn btn-sm btn-icon btn-success text-white" data-bs-toggle="tooltip"
                                    title="{{ __('Edit') }}">
                                    <i class="ti tabler-edit ti-sm"></i>
                                </a>
                                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST"
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
