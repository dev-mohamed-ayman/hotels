@extends('admin.layouts.app')

@section('title', __('Users'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Users List') }}</h5>
            @can('create users')
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-2"></i>{{ __('Add User') }}
                </a>
            @endcan
        </div>
        <div class="card-body">
            <!-- Filters Section -->
            <div class="mb-3">
                <div class="accordion" id="filterAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ request()->hasAny(['search', 'role']) ? '' : 'collapsed' }}"
                                type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="ti tabler-filter me-2"></i>
                                {{ __('Filters') }}
                                @php
                                    $activeFilters = 0;
                                    if (request('search')) {
                                        $activeFilters++;
                                    }
                                    if (request('role')) {
                                        $activeFilters++;
                                    }
                                @endphp
                                @if ($activeFilters > 0)
                                    <span class="badge bg-primary ms-2">{{ $activeFilters }}</span>
                                @endif
                            </button>
                        </h2>
                        <div id="filterCollapse"
                            class="accordion-collapse collapse {{ request()->hasAny(['search', 'role']) ? 'show' : '' }}">
                            <div class="accordion-body">
                                <form method="GET" action="{{ route('users.index') }}">
                                    <div class="row g-3">
                                        <!-- Search -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" name="search" class="form-control"
                                                value="{{ request('search') }}"
                                                placeholder="{{ __('Name, Email or Phone') }}">
                                        </div>

                                        <!-- Role Filter -->
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Role') }}</label>
                                            <select name="role" class="form-select">
                                                <option value="">{{ __('All Roles') }}</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ request('role') == $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Sort By -->
                                        <div class="col-md-6">
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
                                                <option value="phone"
                                                    {{ request('sort_by') == 'phone' ? 'selected' : '' }}>
                                                    {{ __('Phone') }}</option>
                                                <option value="updated_at"
                                                    {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>
                                                    {{ __('Updated At') }}</option>
                                            </select>
                                        </div>

                                        <!-- Sort Order -->
                                        <div class="col-md-6">
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
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
            @if (request()->hasAny(['search', 'role']))
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

                        @if (request('role'))
                            <span class="badge bg-label-primary">
                                {{ __('Role') }}: {{ request('role') }}
                                <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="text-white ms-1">×</a>
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
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Roles') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                            class="rounded-circle me-2" width="32" height="32">
                                    @else
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-initial bg-label-primary rounded-circle">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        </div>
                                    @endif
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-label-info me-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('No roles') }}</span>
                                @endforelse
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                        id="actionsDropdown{{ $user->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="actionsDropdown{{ $user->id }}">
                                        @can('view users')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                                    <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('edit users')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                                    <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('delete users')
                                            @if ($user->id !== auth()->id())
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item text-danger w-100 text-start">
                                                            <i class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="mb-0">{{ __('No users found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection











