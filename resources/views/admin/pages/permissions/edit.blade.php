@extends('admin.layouts.app')

@section('title', __('Edit Permission'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Edit Permission') }}</h5>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Permission Name') }} *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $permission->name) }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-check me-2"></i>{{ __('Update Permission') }}
                            </button>
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection








