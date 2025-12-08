@extends('admin.layouts.app')

@section('title', __('Booking Details'))

@section('content')
    <div class="row">
        <!-- Booking Information Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Booking Details') }} - {{ $booking->code }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-primary btn-sm">
                            <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                        </a>
                        <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">
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
                                <label class="form-label text-muted small">{{ __('Booking Code') }}</label>
                                <p class="mb-0 fw-semibold">{{ $booking->code }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Customer') }}</label>
                                <p class="mb-0">
                                    <a href="{{ route('customers.show', $booking->customer_id) }}" class="text-decoration-none">
                                        {{ $booking->customer->name }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Hotel') }}</label>
                                <p class="mb-0">
                                    <a href="{{ route('hotels.show', $booking->hotel_id) }}" class="text-decoration-none">
                                        {{ $booking->hotel->name }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Check In') }}</label>
                                <p class="mb-0">{{ $booking->check_in->format('Y-m-d') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Check Out') }}</label>
                                <p class="mb-0">{{ $booking->check_out->format('Y-m-d') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Nights') }}</label>
                                <p class="mb-0">{{ $booking->nights }}</p>
                            </div>
                        </div>

                        <!-- Financial Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-3">{{ __('Financial Information') }}</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Currency') }}</label>
                                <p class="mb-0">
                                    <span class="badge bg-label-primary">{{ $booking->currency->code ?? '-' }}</span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Total Amount') }}</label>
                                <p class="mb-0 fw-semibold text-success">
                                    {{ number_format($booking->total_amount, 2) }} {{ $booking->currency->code ?? '' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Paid Amount') }}</label>
                                <p class="mb-0 fw-semibold text-primary">
                                    {{ number_format($booking->paid_amount, 2) }} {{ $booking->currency->code ?? '' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Pending Amount') }}</label>
                                <p class="mb-0 fw-semibold text-warning">
                                    {{ number_format($booking->total_amount - $booking->paid_amount, 2) }} {{ $booking->currency->code ?? '' }}
                                </p>
                            </div>
                            @if($booking->child_price > 0 || $booking->child_margin > 0)
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Child Price') }}</label>
                                <p class="mb-0">{{ number_format($booking->child_price, 2) }} {{ $booking->currency->code ?? '' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Child Margin') }}</label>
                                <p class="mb-0">{{ number_format($booking->child_margin, 2) }} {{ $booking->currency->code ?? '' }}</p>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Option Date') }}</label>
                                <p class="mb-0">{{ $booking->option_date ? $booking->option_date->format('Y-m-d') : ($booking->payment_date ? $booking->payment_date->format('Y-m-d') : '-') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('Status') }}</label>
                                <p class="mb-0">
                                    @if($booking->status == 'pending')
                                        <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                    @elseif($booking->status == 'confirmed')
                                        <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <h6 class="text-muted mb-3">{{ __('Notes') }}</h6>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rooms Information -->
        @if($booking->rooms->count() > 0)
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Rooms') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Room Type') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Room Count') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Margin') }}</th>
                                    <th>{{ __('Child Count') }}</th>
                                    <th>{{ __('Child Price') }}</th>
                                    <th>{{ __('Child Margin') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->rooms as $room)
                                <tr>
                                    <td>{{ $room->room_type }}</td>
                                    <td>{{ $room->category ?? '-' }}</td>
                                    <td>{{ $room->room_count }}</td>
                                    <td>{{ number_format($room->price, 2) }}</td>
                                    <td>{{ number_format($room->margin, 2) }}</td>
                                    <td>{{ $room->child_count ?? 0 }}</td>
                                    <td>{{ $room->child_price ? number_format($room->child_price, 2) : '-' }}</td>
                                    <td>{{ $room->child_margin ? number_format($room->child_margin, 2) : '-' }}</td>
                                    <td>
                                        {{ number_format(($room->price + $room->margin) * $room->room_count, 2) }}
                                        {{ $booking->currency->code ?? '' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Adjustments (Additions & Discounts) -->
        @if($booking->adjustments->count() > 0)
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Adjustments') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Additions -->
                        @if($booking->adjustments->where('type', 'addition')->count() > 0)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-success mb-3">{{ __('Additions') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->adjustments->where('type', 'addition') as $addition)
                                        <tr>
                                            <td>{{ $addition->description }}</td>
                                            <td class="text-success">
                                                +{{ number_format($addition->amount, 2) }} {{ $booking->currency->code ?? '' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Discounts -->
                        @if($booking->adjustments->where('type', 'discount')->count() > 0)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-danger mb-3">{{ __('Discounts') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->adjustments->where('type', 'discount') as $discount)
                                        <tr>
                                            <td>{{ $discount->description }}</td>
                                            <td class="text-danger">
                                                -{{ number_format($discount->amount, 2) }} {{ $booking->currency->code ?? '' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">{{ __('Total Amount') }}</h6>
                                    <h4 class="mb-0 text-success">
                                        {{ number_format($booking->total_amount, 2) }}<br>
                                        <small class="text-muted">{{ $booking->currency->code ?? '' }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">{{ __('Paid Amount') }}</h6>
                                    <h4 class="mb-0 text-primary">
                                        {{ number_format($booking->paid_amount, 2) }}<br>
                                        <small class="text-muted">{{ $booking->currency->code ?? '' }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">{{ __('Pending Amount') }}</h6>
                                    <h4 class="mb-0 text-warning">
                                        {{ number_format($booking->total_amount - $booking->paid_amount, 2) }}<br>
                                        <small class="text-muted">{{ $booking->currency->code ?? '' }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">{{ __('Created At') }}</h6>
                                    <p class="mb-0">{{ formatDateTime($booking->created_at) }}</p>
                                    <h6 class="text-muted mb-2 mt-3">{{ __('Updated At') }}</h6>
                                    <p class="mb-0">{{ formatDateTime($booking->updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

