@extends('admin.layouts.app')

@section('title', __('activity.activity_details'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0">{{ __('activity.activity_details') }}</h5>
                        <a href="{{ route('activity-log.index') }}" class="btn btn-label-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> {{ __('activity.back_to_list') }}
                        </a>
                    </div>

                    <div class="card-body pt-4">
                        <div class="row g-4">
                            <!-- User Info -->
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ti tabler-user"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-0 text-uppercase text-muted">{{ __('activity.user') }}</h6>
                                </div>
                                <div class="ps-4 ms-2">
                                    @if ($activity->causer)
                                        <h5 class="mb-0">{{ $activity->causer->name }}</h5>
                                        <small class="text-muted">{{ $activity->causer->email }}</small>
                                    @else
                                        <span class="badge bg-label-secondary">{{ __('activity.system') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Date/Time -->
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="ti tabler-clock"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-0 text-uppercase text-muted">{{ __('activity.date_time') }}</h6>
                                </div>
                                <div class="ps-4 ms-2">
                                    <h5 class="mb-0">{{ $activity->created_at->format('Y-m-d') }}</h5>
                                    <small class="text-muted">{{ $activity->created_at->format('H:i:s') }}
                                        ({{ $activity->created_at->diffForHumans() }})</small>
                                </div>
                            </div>

                            <!-- Activity Type -->
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="ti tabler-settings"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-0 text-uppercase text-muted">{{ __('activity.activity') }}</h6>
                                </div>
                                <div class="ps-4 ms-2">
                                    @php
                                        $eventColors = [
                                            'created' => 'success',
                                            'updated' => 'warning',
                                            'deleted' => 'danger',
                                            'login' => 'info',
                                            'logout' => 'secondary',
                                        ];
                                        $color = $eventColors[$activity->event] ?? 'primary';
                                    @endphp
                                    <span
                                        class="badge rounded-pill bg-label-{{ $color }} text-capitalize fs-6 px-3">
                                        {{ __('activity.' . $activity->event) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="ti tabler-database"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-0 text-uppercase text-muted">{{ __('activity.type') }}</h6>
                                </div>
                                <div class="ps-4 ms-2">
                                    @if ($activity->subject_type)
                                        <h5 class="mb-0">{{ class_basename($activity->subject_type) }}</h5>
                                        @if ($activity->subject_id)
                                            <small class="text-muted">ID: {{ $activity->subject_id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ __('activity.not_specified') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="divider text-start">
                                    <div class="divider-text fw-semibold">
                                        <i class="ti tabler-file-description me-1"></i> {{ __('activity.description') }}
                                    </div>
                                </div>
                                <div class="p-3 bg-lighter rounded">
                                    <p class="mb-0 fs-5">{{ $activity->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($activity->properties && count($activity->properties) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">{{ __('activity.additional_details') }}</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                @if (isset($activity->properties['attributes']) && count($activity->properties['attributes']) > 0)
                                    <div class="col-md-6">
                                        <div class="card shadow-none bg-label-success h-100">
                                            <div class="card-header border-bottom border-success p-3">
                                                <h6 class="card-title mb-0 text-success">
                                                    <i class="ti tabler-plus me-1"></i> {{ __('activity.new_data') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-borderless table-striped mb-0">
                                                    <tbody>
                                                        @foreach ($activity->properties['attributes'] as $key => $value)
                                                            <tr>
                                                                <td class="fw-semibold ps-3" width="30%">
                                                                    {{ $key }}</td>
                                                                <td class="pe-3 font-monospace small">
                                                                    @if (is_array($value) || is_object($value))
                                                                        {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (isset($activity->properties['old']) && count($activity->properties['old']) > 0)
                                    <div class="col-md-6">
                                        <div class="card shadow-none bg-label-warning h-100">
                                            <div class="card-header border-bottom border-warning p-3">
                                                <h6 class="card-title mb-0 text-warning">
                                                    <i class="ti tabler-history me-1"></i> {{ __('activity.old_data') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-borderless table-striped mb-0">
                                                    <tbody>
                                                        @foreach ($activity->properties['old'] as $key => $value)
                                                            <tr>
                                                                <td class="fw-semibold ps-3" width="30%">
                                                                    {{ $key }}</td>
                                                                <td class="pe-3 font-monospace small">
                                                                    @if (is_array($value) || is_object($value))
                                                                        {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @php
                                $otherProperties = collect($activity->properties)->except(['attributes', 'old']);
                            @endphp

                            @if ($otherProperties->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">{{ __('activity.additional_info') }}</h6>
                                        <div class="table-responsive border rounded">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('activity.key') }}</th>
                                                        <th>{{ __('activity.value') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($otherProperties as $key => $value)
                                                        <tr>
                                                            <td class="fw-bold">{{ $key }}</td>
                                                            <td class="font-monospace">
                                                                @if (is_array($value) || is_object($value))
                                                                    <pre class="mb-0 bg-lighter p-2 rounded">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activity->subject)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">{{ __('activity.related_object') }}</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="d-flex mb-3">
                                <div class="me-4">
                                    <small class="text-uppercase text-muted d-block">{{ __('activity.type') }}</small>
                                    <span class="fw-semibold fs-5">{{ class_basename($activity->subject_type) }}</span>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted d-block">{{ __('ID') }}</small>
                                    <span class="fw-semibold fs-5">{{ $activity->subject_id }}</span>
                                </div>
                            </div>

                            @if (method_exists($activity->subject, 'toArray'))
                                <div>
                                    <small
                                        class="text-uppercase text-muted d-block mb-2">{{ __('activity.current_data') }}</small>
                                    <pre class="bg-label-dark p-3 rounded text-white mb-0" style="max-height: 400px;">{{ json_encode($activity->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between border-top py-3">
                            <a href="{{ route('activity-log.index') }}" class="btn btn-label-secondary">
                                <i class="ti tabler-arrow-left me-1"></i> {{ __('activity.back_to_list') }}
                            </a>

                            <form action="{{ route('activity-log.destroy', $activity) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('{{ __('activity.confirm_delete_record') }}')">
                                    <i class="ti tabler-trash me-1"></i> {{ __('activity.delete_record') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
