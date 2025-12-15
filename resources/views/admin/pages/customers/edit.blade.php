@extends('admin.layouts.app')

@section('title', __('Edit Customer'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Edit Customer') }}</h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">{{ __('Name') }} *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $customer->name) }}" required/>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">{{ __('Email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $customer->email) }}"/>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone_1">{{ __('Phone 1') }} *</label>
                                <input type="text" class="form-control @error('phone_1') is-invalid @enderror"
                                       id="phone_1" name="phone_1" value="{{ old('phone_1', $customer->phone_1) }}"
                                       required/>
                                @error('phone_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone_2">{{ __('Phone 2') }}</label>
                                <input type="text" class="form-control @error('phone_2') is-invalid @enderror"
                                       id="phone_2" name="phone_2" value="{{ old('phone_2', $customer->phone_2) }}"/>
                                @error('phone_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="address">{{ __('Address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                          name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Classification Fields -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="type">{{ __('Type') }} *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                        name="type" required>
                                    <option value="individual"
                                        {{ old('type', $customer->type) == 'individual' ? 'selected' : '' }}>
                                        {{ __('Individual') }}</option>
                                    <option value="corporate"
                                        {{ old('type', $customer->type) == 'corporate' ? 'selected' : '' }}>
                                        {{ __('Corporate') }}</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="status">{{ __('Status') }} *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                    <option value="potential"
                                        {{ old('status', $customer->status) == 'potential' ? 'selected' : '' }}>
                                        {{ __('Potential') }}</option>
                                    <option value="active"
                                        {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="cancelled"
                                        {{ old('status', $customer->status) == 'cancelled' ? 'selected' : '' }}>
                                        {{ __('Cancelled') }}</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="priority">{{ __('Priority') }} *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority"
                                        name="priority" required>
                                    <option value="low"
                                        {{ old('priority', $customer->priority) == 'low' ? 'selected' : '' }}>
                                        {{ __('Low') }}</option>
                                    <option value="medium"
                                        {{ old('priority', $customer->priority) == 'medium' ? 'selected' : '' }}>
                                        {{ __('Medium') }}</option>
                                    <option value="high"
                                        {{ old('priority', $customer->priority) == 'high' ? 'selected' : '' }}>
                                        {{ __('High') }}</option>
                                    <option value="urgent"
                                        {{ old('priority', $customer->priority) == 'urgent' ? 'selected' : '' }}>
                                        {{ __('Urgent') }}</option>
                                </select>
                                @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="source">{{ __('Source') }}</label>
                                <select class="form-select @error('source') is-invalid @enderror" id="source"
                                        name="source">
                                    <option value="">{{ __('Select Source') }}</option>
                                    <option value="phone"
                                        {{ old('source', $customer->source) == 'phone' ? 'selected' : '' }}>
                                        {{ __('Phone') }}</option>
                                    <option value="website"
                                        {{ old('source', $customer->source) == 'website' ? 'selected' : '' }}>
                                        {{ __('Website') }}</option>
                                    <option value="social_media"
                                        {{ old('source', $customer->source) == 'social_media' ? 'selected' : '' }}>
                                        {{ __('Social Media') }}</option>
                                    <option value="referral"
                                        {{ old('source', $customer->source) == 'referral' ? 'selected' : '' }}>
                                        {{ __('Referral') }}</option>
                                    <option value="direct_visit"
                                        {{ old('source', $customer->source) == 'direct_visit' ? 'selected' : '' }}>
                                        {{ __('Direct Visit') }}</option>
                                </select>
                                @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Interested Hotels -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="hotels">{{ __('Interested Hotels') }}</label>
                                <select class="form-select select2 @error('hotels') is-invalid @enderror" id="hotels"
                                        name="hotels[]" multiple>
                                    @foreach ($hotels as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ in_array($hotel->id, old('hotels', $customer->hotels->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hotels')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="notes">{{ __('Notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                                          name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Update Customer') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
          rel="stylesheet"/>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#hotels').select2({
                theme: 'bootstrap-5',
                placeholder: '{{ __('Select Hotels') }}',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function () {
                        return '{{ __('No hotels found') }}';
                    }
                }
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            background-color: transparent !important;
            border: var(--bs-border-width) solid color-mix(in sRGB, var(--bs-base-color) 22%, var(--bs-paper-bg)) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            color: #fff !important;
        }
    </style>
@endsection
