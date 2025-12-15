@extends('admin.layouts.app')

@section('title', __('Role Details'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Role Details') }}</h5>
                    <div>
                        @can('edit roles')
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary me-2">
                                <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                            </a>
                        @endcan
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>{{ __('Role Name') }}:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-label-info fs-6">{{ $role->name }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>{{ __('Users Count') }}:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-label-primary">{{ $role->users->count() }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>{{ __('Permissions') }}:</strong></div>
                        <div class="col-sm-9">
                            @if ($role->permissions->count() > 0)
                                <div class="card">
                                    <div class="card-body">
                                        @foreach ($permissions as $group => $groupPermissions)
                                            @php
                                                $hasPermissions =
                                                    $role->permissions
                                                        ->whereIn('id', $groupPermissions->pluck('id'))
                                                        ->count() > 0;
                                            @endphp
                                            @if ($hasPermissions)
                                                <div class="mb-3">
                                                    <h6 class="text-uppercase text-muted mb-2">{{ ucfirst($group) }}</h6>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($groupPermissions as $permission)
                                                            @if ($role->hasPermissionTo($permission))
                                                                <span
                                                                    class="badge bg-label-success">{{ $permission->name }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">{{ __('No permissions assigned') }}</span>
                            @endif
                        </div>
                    </div>
                    @if ($role->users->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>{{ __('Users') }}:</strong></div>
                            <div class="col-sm-9">
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($role->users as $user)
                                        <span class="badge bg-label-primary">{{ $user->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>{{ __('Created At') }}:</strong></div>
                        <div class="col-sm-9">{{ $role->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>{{ __('Updated At') }}:</strong></div>
                        <div class="col-sm-9">{{ $role->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


