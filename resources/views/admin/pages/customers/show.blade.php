@extends('admin.layouts.app')

@section('title', __('Customer Details'))

@section('content')
    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Customer Information') }}</h5>
                    <div class="d-flex gap-2">
                        @can('edit customers')
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                                <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                            </a>
                        @endcan
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-3">{{ __('Basic Information') }}</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Name') }}</label>
                                <p class="mb-0 fw-semibold">{{ $customer->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Email') }}</label>
                                <p class="mb-0">
                                    @if ($customer->email)
                                        <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Phone 1') }}</label>
                                <p class="mb-0">
                                    <a href="tel:{{ $customer->phone_1 }}">{{ $customer->phone_1 }}</a>
                                </p>
                            </div>
                            @if ($customer->phone_2)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">{{ __('Phone 2') }}</label>
                                    <p class="mb-0">
                                        <a href="tel:{{ $customer->phone_2 }}">{{ $customer->phone_2 }}</a>
                                    </p>
                                </div>
                            @endif
                            @if ($customer->address)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">{{ __('Address') }}</label>
                                    <p class="mb-0">{{ $customer->address }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Classification Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-3">{{ __('Classification') }}</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Type') }}</label>
                                <p class="mb-0">

                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Status') }}</label>
                                <p class="mb-0">
                                    @if ($customer->status == 'potential')
                                        <span class="badge bg-label-warning">{{ __('Potential') }}</span>
                                    @elseif ($customer->status == 'active')
                                        <span class="badge bg-label-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Priority') }}</label>
                                <p class="mb-0">
                                    @if ($customer->priority == 'low')
                                        <span class="badge bg-label-secondary">{{ __('Low') }}</span>
                                    @elseif ($customer->priority == 'medium')
                                        <span class="badge bg-label-info">{{ __('Medium') }}</span>
                                    @elseif ($customer->priority == 'high')
                                        <span class="badge bg-label-warning">{{ __('High') }}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ __('Urgent') }}</span>
                                    @endif
                                </p>
                            </div>
                            @if ($customer->source)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">{{ __('Source') }}</label>
                                    <p class="mb-0">
                                        <span class="badge bg-label-primary">
                                            {{ __(str_replace('_', ' ', ucfirst($customer->source))) }}
                                        </span>
                                    </p>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Created At') }}</label>
                                <p class="mb-0">{{ formatDateTime($customer->created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($customer->notes)
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <h6 class="text-muted mb-3">{{ __('Notes') }}</h6>
                                <p class="mb-0">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-md-12 mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">{{ __('Total Bookings') }}</h6>
                                    <h3 class="mb-0">{{ $totalBookings == 0 ? '' : \App\Helpers\NumberHelper::format($totalBookings) }}</h3>
                                </div>
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti tabler-calendar ti-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">{{ __('Total Amount') }}</h6>
                                    <h3 class="mb-0">{{ $totalAmount == 0 ? '' : \App\Helpers\NumberHelper::format($totalAmount) }}</h3>
                                </div>
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="ti tabler-currency-dollar ti-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">{{ __('Paid Amount') }}</h6>
                                    <h3 class="mb-0 text-success">
                                        {{ $paidAmount == 0 ? '' : \App\Helpers\NumberHelper::format($paidAmount) }}</h3>
                                </div>
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="ti tabler-check ti-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">{{ __('Pending Amount') }}</h6>
                                    <h3 class="mb-0 text-warning">
                                        {{ $pendingAmount == 0 ? '' : \App\Helpers\NumberHelper::format($pendingAmount) }}</h3>
                                </div>
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="ti tabler-clock ti-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interested Hotels -->
        @if ($customer->hotels->count() > 0)
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Interested Hotels') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($customer->hotels as $hotel)
                                <div class="col-md-4 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="mb-1">
                                                <a href="{{ route('hotels.show', $hotel->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $hotel->name }}
                                                </a>
                                            </h6>
                                            @if ($hotel->address)
                                                <p class="text-muted small mb-0">
                                                    <i class="ti tabler-map-pin me-1"></i>{{ $hotel->address }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Follow-ups Section -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Follow-ups') }}</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addFollowUpModal">
                        <i class="ti tabler-plus me-2"></i>{{ __('Add Follow-up') }}
                    </button>
                </div>
                <div class="card-body">
                    @if ($customer->followUps->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Notes') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Updated At') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer->followUps->sortByDesc('created_at') as $followUp)
                                        <tr>
                                            <td>
                                                @if ($followUp->status == 'none')
                                                    <span class="badge bg-label-secondary">{{ __('None') }}</span>
                                                @elseif($followUp->status == 'in_progress')
                                                    <span class="badge bg-label-primary">{{ __('In Progress') }}</span>
                                                @elseif($followUp->status == 'awaiting_replay')
                                                    <span
                                                        class="badge bg-label-warning">{{ __('Awaiting Replay') }}</span>
                                                @elseif($followUp->status == 'completed')
                                                    <span class="badge bg-label-success">{{ __('Completed') }}</span>
                                                @elseif($followUp->status == 'canceled')
                                                    <span class="badge bg-label-danger">{{ __('Canceled') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $followUp->notes ?? '-' }}</td>
                                            <td>{{ formatDateTime($followUp->created_at) }}</td>
                                            <td>{{ formatDateTime($followUp->updated_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No follow-ups yet') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Recent Bookings') }}</h5>
                    @if ($totalBookings > 10)
                        <a href="{{ route('bookings.index', ['customer_id' => $customer->id]) }}"
                            class="btn btn-sm btn-primary">
                            {{ __('View All') }} ({{ $totalBookings }})
                        </a>
                    @endif
                </div>
                <div class="card-datatable table-responsive">
                    <table class="table border-top table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Hotel') }}</th>
                                <th>{{ __('Check In') }}</th>
                                <th>{{ __('Check Out') }}</th>
                                <th>{{ __('Nights') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Paid Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->code }}</strong>
                                    </td>
                                    <td>{{ $booking->hotel->name }}</td>
                                    <td>{{ $booking->check_in->format('d-m-Y') }}</td>
                                    <td>{{ $booking->check_out->format('d-m-Y') }}</td>
                                    <td>{{ $booking->nights }}</td>
                                    <td>
                                        <strong>{{ $booking->total_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->total_amount) }}</strong>
                                        @if ($booking->currency)
                                            <span class="text-muted small">{{ $booking->currency->symbol }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="text-success">{{ $booking->paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->paid_amount) }}</span>
                                        @if ($booking->currency)
                                            <span class="text-muted small">{{ $booking->currency->symbol }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->status == 'pending')
                                            <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('bookings.edit', $booking->id) }}"
                                            class="btn btn-sm btn-icon btn-success text-white" data-bs-toggle="tooltip"
                                            title="{{ __('View') }}">
                                            <i class="ti tabler-eye ti-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <p class="mb-0 text-muted">{{ __('No bookings found') }}</p>
                                        @can('create bookings')
                                            <a href="{{ route('bookings.create', ['customer_id' => $customer->id]) }}"
                                                class="btn btn-sm btn-primary mt-2">
                                                <i class="ti tabler-plus me-2"></i>{{ __('Create First Booking') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Follow-up Modal -->
    <div class="modal fade" id="addFollowUpModal" tabindex="-1" aria-labelledby="addFollowUpModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFollowUpModalLabel">{{ __('Add Follow-up') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="followUpForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }} *</label>
                            <select class="form-select" name="status" id="followUpStatus" required>
                                <option value="none">{{ __('None') }}</option>
                                <option value="in_progress">{{ __('In Progress') }}</option>
                                <option value="awaiting_replay">{{ __('Awaiting Replay') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="canceled">{{ __('Canceled') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control" name="notes" id="followUpNotes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Handle follow-up form submission
            $('#followUpForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    status: $('#followUpStatus').val(),
                    notes: $('#followUpNotes').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route('follow-ups.store', $customer->id) }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ||
                            '{{ __('Failed to create follow-up') }}');
                    }
                });
            });
        });
    </script>
@endsection
