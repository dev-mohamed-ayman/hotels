@extends('admin.layouts.app')

@section('title', __('User Details'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('User Details') }}</h5>
                    <div>
                        @can('edit users')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-2">
                                <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                            </a>
                        @endcan
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                    class="rounded-circle mb-3" width="150" height="150">
                            @else
                                <div class="avatar avatar-xl mx-auto mb-3">
                                    <div class="avatar-initial bg-label-primary rounded-circle fs-1">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                            @endif
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Name') }}:</strong></div>
                                <div class="col-sm-8">{{ $user->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Email') }}:</strong></div>
                                <div class="col-sm-8">{{ $user->email }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Phone') }}:</strong></div>
                                <div class="col-sm-8">{{ $user->phone ?? '-' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Roles') }}:</strong></div>
                                <div class="col-sm-8">
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-label-info me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">{{ __('No roles assigned') }}</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Permissions') }}:</strong></div>
                                <div class="col-sm-8">
                                    @if ($user->roles->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($user->getAllPermissions() as $permission)
                                                <span class="badge bg-label-success">{{ $permission->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">{{ __('No permissions') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Created At') }}:</strong></div>
                                <div class="col-sm-8">{{ $user->created_at->format('Y-m-d H:i:s') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>{{ __('Updated At') }}:</strong></div>
                                <div class="col-sm-8">{{ $user->updated_at->format('Y-m-d H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection










