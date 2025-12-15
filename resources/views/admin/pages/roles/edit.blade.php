@extends('admin.layouts.app')

@section('title', __('Edit Role'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Edit Role') }}</h5>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Role Name -->
                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Role Name') }} *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $role->name) }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permissions -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Permissions') }}</label>
                            <div class="card">
                                <div class="card-body">
                                    @php
                                        $rolePermissionIds = old(
                                            'permissions',
                                            $role->permissions->pluck('id')->toArray(),
                                        );
                                    @endphp
                                    @foreach ($permissions as $group => $groupPermissions)
                                        <div class="mb-4">
                                            <h6 class="text-uppercase text-muted mb-3">{{ ucfirst($group) }}</h6>
                                            <div class="row">
                                                @foreach ($groupPermissions as $permission)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="permissions[]" value="{{ $permission->id }}"
                                                                id="permission_{{ $permission->id }}"
                                                                {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="permission_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-label-primary" id="selectAllPermissions">
                                    {{ __('Select All') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-label-secondary" id="deselectAllPermissions">
                                    {{ __('Deselect All') }}
                                </button>
                            </div>
                            @error('permissions')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-check me-2"></i>{{ __('Update Role') }}
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
    <script>
        document.getElementById('selectAllPermissions')?.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });

        document.getElementById('deselectAllPermissions')?.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    </script>
@endsection
@endsection


