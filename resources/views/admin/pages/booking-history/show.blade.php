@extends('admin.layouts.app')

@section('title', __('Booking History') . ' - ' . $booking->code)

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti tabler-calendar-history me-2"></i>
                        {{ __('Booking History') }} - {{ $booking->code }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back to Booking') }}
                        </a>
                        <a href="{{ route('booking-history.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-list me-2"></i>{{ __('All History') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($activities->isEmpty())
                        <div class="text-center py-5">
                            <i class="ti tabler-calendar-history" style="font-size: 3rem; color: #cbd5e0;"></i>
                            <p class="mt-3 text-muted">{{ __('No history records found for this booking') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date & Time') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Changes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $activity)
                                        <tr>
                                            <td class="text-nowrap">
                                                {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                            </td>
                                            <td>
                                                @if($activity->causer)
                                                    <strong>{{ $activity->causer->name }}</strong>
                                                @else
                                                    <span class="text-muted">{{ __('System') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'deleted' ? 'danger' : 'primary') }}">
                                                    {{ ucfirst($activity->event) }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="mb-1">{{ $activity->description }}</p>
                                                @if($activity->properties->has('attributes') || $activity->properties->has('old'))
                                                    <details class="mt-2">
                                                        <summary class="cursor-pointer text-primary">
                                                            <small>{{ __('View Changes') }}</small>
                                                        </summary>
                                                        <div class="mt-2 p-2 bg-light rounded">
                                                            @if($activity->properties->has('old'))
                                                                <strong class="text-danger">{{ __('Old Values') }}:</strong>
                                                                <pre class="mb-2 text-muted">{{ json_encode($activity->properties->get('old'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @endif
                                                            @if($activity->properties->has('attributes'))
                                                                <strong class="text-success">{{ __('New Values') }}:</strong>
                                                                <pre class="text-muted">{{ json_encode($activity->properties->get('attributes'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @endif
                                                        </div>
                                                    </details>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
