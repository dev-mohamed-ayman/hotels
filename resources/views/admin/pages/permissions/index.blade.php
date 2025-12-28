@extends('admin.layouts.app')

@section('title', __('Permissions'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Permissions List') }}</h5>
            @can('create permissions')
                <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-2"></i>{{ __('Add Permission') }}
                </a>
            @endcan
        </div>
        <div class="card-body">
            <!-- Filters Section -->
            @if (request()->hasAny(['search']))
                <div class="mb-3">
                    <form method="GET" action="{{ route('permissions.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                    placeholder="{{ __('Permission Name') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti tabler-search me-2"></i>{{ __('Search') }}
                                </button>
                                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                    <i class="ti tabler-x me-2"></i>{{ __('Clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="mb-3">
                    <a class="btn btn-sm btn-label-primary" data-bs-toggle="collapse" href="#searchCollapse">
                        <i class="ti tabler-filter me-2"></i>{{ __('Filters') }}
                    </a>
                </div>
                <div class="collapse mb-3" id="searchCollapse">
                    <form method="GET" action="{{ route('permissions.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                    placeholder="{{ __('Permission Name') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti tabler-search me-2"></i>{{ __('Search') }}
                                </button>
                                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                    <i class="ti tabler-x me-2"></i>{{ __('Clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top table-bordered table-hover table-sm text-center">
                <thead>
                    <tr>
                        <th>{{ __('Permission Name') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>
                                <span class="badge bg-label-success">{{ $permission->name }}</span>
                            </td>
                            <td>{{ $permission->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                        id="actionsDropdown{{ $permission->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="actionsDropdown{{ $permission->id }}">
                                        @can('edit permissions')
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('permissions.edit', $permission->id) }}">
                                                    <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('delete permissions')
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('permissions.destroy', $permission->id) }}"
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
                            <td colspan="3" class="text-center py-5">
                                <p class="mb-0">{{ __('No permissions found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($permissions->hasPages())
            <div class="card-footer">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
@endsection










