@extends('admin.layouts.app')

@section('title', __('Edit User'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Edit User') }}</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">{{ __('Name') }} *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">{{ __('Email') }} *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}" />
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="ti tabler-info-circle me-2"></i>
                                {{ __('Leave password fields empty if you do not want to change the password.') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password">{{ __('New Password') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" />
                            </div>
                        </div>

                        <!-- Roles -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Roles') }}</label>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]"
                                                value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-check me-2"></i>{{ __('Update User') }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection










