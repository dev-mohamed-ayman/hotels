@extends('admin.layouts.app')

@section('title', __('Booking Details'))

@section('content')
    <div class="row">
        <!-- Booking Information Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 fw-bold">{{ __('Booking Details') }}</h4>
                        <p class="mb-0 text-muted small">{{ __('Code') }}: <span
                                class="fw-semibold text-primary">{{ $booking->code }}</span></p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @can('export bookings')
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="ti tabler-file-download me-2"></i>{{ __('Download PDF') }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('bookings.pdf.bank', $booking->id) }}"
                                            target="_blank">
                                            <i class="ti tabler-building-bank me-2"></i>{{ __('Bank Export') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item " href="{{ route('bookings.pdf.detailed', $booking->id) }}"
                                            target="_blank">
                                            <i class="ti tabler-file-text me-2"></i>{{ __('Detailed Export') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('bookings.pdf.guest', $booking->id) }}"
                                            target="_blank">
                                            <i class="ti tabler-user me-2"></i>{{ __('Guest Export') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('bookings.pdf.netrate', $booking->id) }}"
                                            target="_blank">
                                            <i class="ti tabler-currency-dollar me-2"></i>{{ __('Net Rate Export') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan
                        @can('edit bookings')
                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-primary btn-sm">
                                <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                            </a>
                        @endcan
                        <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti tabler-arrow-left me-2"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Customer & Hotel Info -->
                        <div class="col-lg-4 col-md-6">
                            <div class="card border shadow-none h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-md me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="ti tabler-user ti-md"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Customer') }}</h6>
                                            <small class="text-muted">{{ __('Customer Information') }}</small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <a href="{{ route('customers.show', $booking->customer_id) }}"
                                            class="text-decoration-none fw-semibold">
                                            {{ $booking->customer->name }}
                                        </a>
                                    </div>
                                    @if ($booking->customer->phone_1)
                                        <div class="text-muted small">
                                            <i class="ti tabler-phone me-1"></i>{{ $booking->customer->phone_1 }}
                                        </div>
                                    @endif
                                    @if ($booking->customer->email)
                                        <div class="text-muted small">
                                            <i class="ti tabler-mail me-1"></i>{{ $booking->customer->email }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card border shadow-none h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-md me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="ti tabler-building ti-md"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Hotel') }}</h6>
                                            <small class="text-muted">{{ __('Hotel Information') }}</small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <a href="{{ route('hotels.show', $booking->hotel_id) }}"
                                            class="text-decoration-none fw-semibold">
                                            {{ $booking->hotel->name }}
                                        </a>
                                    </div>
                                    @if ($booking->hotel->address)
                                        <div class="text-muted small">
                                            <i
                                                class="ti tabler-map-pin me-1"></i>{{ Str::limit($booking->hotel->address, 50) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Booking Dates -->
                        <div class="col-lg-4 col-md-6">
                            <div class="card border shadow-none h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-md me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="ti tabler-calendar ti-md"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Booking Dates') }}</h6>
                                            <small class="text-muted">{{ __('Check In/Out') }}</small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">{{ __('Check In') }}:</span>
                                            <span class="fw-semibold">{{ $booking->check_in->format('d-m-Y') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">{{ __('Check Out') }}:</span>
                                            <span class="fw-semibold">{{ $booking->check_out->format('d-m-Y') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">{{ __('Nights') }}:</span>
                                            <span class="badge bg-label-primary">{{ $booking->nights }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Info -->
                        <div class="col-lg-6 col-md-6">
                            <div class="card border shadow-none">
                                <div class="card-header bg-label-primary">
                                    <h6 class="mb-0 text-primary">
                                        <i class="ti tabler-currency-dollar me-2"></i>{{ __('Financial Information') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label text-muted small mb-1">{{ __('Currency') }}</label>
                                                <div>
                                                    <span
                                                        class="badge bg-label-primary fs-6">{{ $booking->currency->symbol ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label text-muted small mb-1">{{ __('Status') }}</label>
                                                <div>
                                                    @if ($booking->status == 'pending')
                                                        <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                                    @elseif($booking->status == 'confirmed')
                                                        <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                                                    @elseif($booking->status == 'cancelled')
                                                        <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                                    @else
                                                        <span
                                                            class="badge bg-label-secondary">{{ __(ucfirst($booking->status)) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-3 bg-label-success rounded mb-2">
                                                <div>
                                                    <span class="text-muted small d-block">{{ __('Total Amount') }}</span>
                                                    <span
                                                        class="fw-bold fs-5 text-success">{{ $booking->total_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->total_amount) }}
                                                        {{ $booking->currency->symbol ?? '' }}</span>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded bg-success">
                                                        <i class="ti tabler-currency-dollar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-2 bg-label-primary rounded">
                                                <span class="text-muted small">{{ __('Paid') }}</span>
                                                <span
                                                    class="fw-semibold text-primary">{{ $booking->paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->paid_amount) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-2 bg-label-warning rounded">
                                                <span class="text-muted small">{{ __('Pending') }}</span>
                                                <span
                                                    class="fw-semibold text-warning">{{ $booking->net_amount - $booking->paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount - $booking->paid_amount) }}</span>
                                            </div>
                                        </div>
                                        @if ($booking->child_price > 0 || $booking->child_margin > 0)
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <label
                                                        class="form-label text-muted small mb-1">{{ __('Child Net Rate') }}</label>
                                                    <div class="fw-semibold">
                                                        {{ $booking->child_price == 0 ? '' : \App\Helpers\NumberHelper::format($booking->child_price) }}
                                                        {{ $booking->currency->symbol ?? '' }}</div>
                                                </div>
                                            </div>
                                            @can('view booking margins')
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <label
                                                            class="form-label text-muted small mb-1">{{ __('Child Margin') }}</label>
                                                        <div class="fw-semibold">
                                                            {{ $booking->child_margin == 0 ? '' : \App\Helpers\NumberHelper::format($booking->child_margin) }}
                                                            {{ $booking->currency->symbol ?? '' }}</div>
                                                    </div>
                                                </div>
                                            @endcan
                                        @endif
                                        @if ($booking->notes && ($booking->option_date || $booking->payment_date))
                                            <div class="col-12">
                                                <div class="mb-2">
                                                    <label
                                                        class="form-label text-muted small mb-1">{{ __('Option Date') }}</label>
                                                    <div class="fw-semibold">
                                                        {{ $booking->option_date ? $booking->option_date->format('d-m-Y') : ($booking->payment_date ? $booking->payment_date->format('d-m-Y') : '-') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Financial Info -->
                        <div class="col-lg-6 col-md-6">
                            <div class="card border shadow-none">
                                <div class="card-header bg-label-secondary">
                                    <h6 class="mb-0 text-secondary">
                                        <i
                                            class="ti tabler-building-bank me-2"></i>{{ __('Hotel Financial Information') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-3 bg-label-secondary rounded mb-2">
                                                <div>
                                                    <span
                                                        class="text-muted small d-block">{{ __('Total Net Amount') }}</span>
                                                    <span
                                                        class="fw-bold fs-5 text-secondary">{{ $booking->net_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount) }}
                                                        {{ $booking->currency->symbol ?? '' }}</span>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded bg-secondary">
                                                        <i class="ti tabler-building-bank"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-2 bg-label-primary rounded">
                                                <span class="text-muted small">{{ __('Paid to Hotel') }}</span>
                                                <span
                                                    class="fw-semibold text-primary">{{ $booking->hotel_paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-2 bg-label-warning rounded">
                                                <span class="text-muted small">{{ __('Pending') }}</span>
                                                <span
                                                    class="fw-semibold text-warning">{{ $booking->net_amount - $booking->hotel_paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount - $booking->hotel_paid_amount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($booking->notes)
                            <!-- Notes -->
                            <div class="col-lg-6 col-md-6">
                                <div class="card border shadow-none h-100">
                                    <div class="card-header bg-label-info">
                                        <h6 class="mb-0 text-info">
                                            <i class="ti tabler-notes me-2"></i>{{ __('Notes') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-3 bg-label-info rounded">
                                            <p class="mb-0">{{ $booking->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Additional Details -->
                            <div class="col-lg-6 col-md-6">
                                <div class="card border shadow-none h-100">
                                    <div class="card-header bg-label-secondary">
                                        <h6 class="mb-0 text-secondary">
                                            <i class="ti tabler-info-circle me-2"></i>{{ __('Additional Details') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($booking->option_date || $booking->payment_date)
                                            <div class="mb-3">
                                                <label
                                                    class="form-label text-muted small mb-1">{{ __('Option Date') }}</label>
                                                <div class="fw-semibold">
                                                    {{ $booking->option_date ? $booking->option_date->format('d-m-Y') : ($booking->payment_date ? $booking->payment_date->format('d-m-Y') : '-') }}
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label class="form-label text-muted small mb-1">{{ __('Created At') }}</label>
                                            <div class="fw-semibold">{{ formatDateTime($booking->created_at) }}</div>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted small mb-1">{{ __('Updated At') }}</label>
                                            <div class="fw-semibold">{{ formatDateTime($booking->updated_at) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Information -->
        @if ($booking->rooms->count() > 0)
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
                                        <th>{{ __('Net Rate') }}</th>
                                        @can('view booking margins')
                                            <th>{{ __('Margin') }}</th>
                                        @endcan
                                        <th>{{ __('Child Count') }}</th>
                                        <th>{{ __('Child Net Rate') }}</th>
                                        @can('view booking margins')
                                            <th>{{ __('Child Margin') }}</th>
                                        @endcan
                                        @can('view booking guest rates')
                                            <th>{{ __('Subtotal') }}</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($booking->rooms as $room)
                                        <tr>
                                            <td>{{ $room->room_type }}</td>
                                            <td>{{ $room->category ?? '-' }}</td>
                                            <td>{{ $room->room_count }}</td>
                                            <td>{{ $room->price == 0 ? '' : \App\Helpers\NumberHelper::format($room->price) }}
                                            </td>
                                            @can('view booking margins')
                                                <td>{{ $room->margin == 0 ? '' : \App\Helpers\NumberHelper::format($room->margin) }}
                                                </td>
                                            @endcan
                                            <td>{{ $room->child_count == 0 ? '' : $room->child_count }}</td>
                                            <td>{{ $room->child_price ? ($room->child_price == 0 ? '' : \App\Helpers\NumberHelper::format($room->child_price)) : '-' }}
                                            </td>
                                            @can('view booking margins')
                                                <td>{{ $room->child_margin ? ($room->child_margin == 0 ? '' : \App\Helpers\NumberHelper::format($room->child_margin)) : '-' }}
                                                </td>
                                            @endcan
                                            @can('view booking guest rates')
                                                <td>
                                                    {{ ($room->price + $room->margin) * $room->room_count * $booking->nights == 0 ? '' : \App\Helpers\NumberHelper::format(($room->price + $room->margin) * $room->room_count * $booking->nights) }}
                                                    {{ $booking->currency->symbol ?? '' }}
                                                </td>
                                            @endcan
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
        @if ($booking->adjustments->count() > 0)
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Extras') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Additions -->
                            @if ($booking->adjustments->where('type', 'addition')->count() > 0)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-success mb-3">{{ __('Additions') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Description') }}</th>
                                                    <th>{{ __('Net Rate') }}</th>
                                                    @can('view booking guest rates')
                                                        <th>{{ __('Guest Rate') }}</th>
                                                    @endcan
                                                    @can('view booking margins')
                                                        <th>{{ __('Margin') }}</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($booking->adjustments->where('type', 'addition') as $addition)
                                                    <tr>
                                                        <td>{{ $addition->description }}</td>
                                                        <td class="text-muted">
                                                            {{ ($val = $addition->net_rate ?? ($addition->amount ?? 0)) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                            {{ $booking->currency->symbol ?? '' }}
                                                        </td>
                                                        @can('view booking guest rates')
                                                            <td class="text-success fw-semibold">
                                                                +{{ ($val = $addition->guest_rate ?? ($addition->amount ?? 0)) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                                {{ $booking->currency->symbol ?? '' }}
                                                            </td>
                                                        @endcan
                                                        @can('view booking margins')
                                                            <td class="text-primary">
                                                                {{ ($val = $addition->margin ?? ($addition->guest_rate ?? ($addition->amount ?? 0)) - ($addition->net_rate ?? 0)) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                                {{ $booking->currency->symbol ?? '' }}
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="fw-bold table-active border-top border-2 border-dark">
                                                    <td class="text-uppercase">{{ __('Total') }}</td>
                                                    <td class="text-muted">
                                                        {{ ($val = $booking->adjustments->where('type', 'addition')->sum('net_rate') ?: $booking->adjustments->where('type', 'addition')->sum('amount')) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                        {{ $booking->currency->symbol ?? '' }}
                                                    </td>
                                                    @can('view booking guest rates')
                                                        <td class="text-success">
                                                            +{{ ($val = $booking->adjustments->where('type', 'addition')->sum('guest_rate') ?: $booking->adjustments->where('type', 'addition')->sum('amount')) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                            {{ $booking->currency->symbol ?? '' }}
                                                        </td>
                                                    @endcan
                                                    @can('view booking margins')
                                                        <td class="text-primary">
                                                            {{ ($val = $booking->adjustments->where('type', 'addition')->sum('margin') ?: $booking->adjustments->where('type', 'addition')->sum('guest_rate') - $booking->adjustments->where('type', 'addition')->sum('net_rate')) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                            {{ $booking->currency->symbol ?? '' }}
                                                        </td>
                                                    @endcan
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Discounts -->
                            @if ($booking->adjustments->where('type', 'discount')->count() > 0)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-danger mb-3">{{ __('Discounts') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Description') }}</th>
                                                    <th>{{ __('Net Rate') }}</th>
                                                    @can('view booking guest rates')
                                                        <th>{{ __('Guest Rate') }}</th>
                                                    @endcan
                                                    @can('view booking margins')
                                                        <th>{{ __('Margin') }}</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($booking->adjustments->where('type', 'discount') as $discount)
                                                    <tr>
                                                        <td>{{ $discount->description }}</td>
                                                        <td class="text-muted">
                                                            {{ ($val = $discount->net_rate ?? ($discount->amount ?? 0)) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                            {{ $booking->currency->symbol ?? '' }}
                                                        </td>
                                                        @can('view booking guest rates')
                                                            <td class="text-danger fw-semibold">
                                                                -{{ ($val = $discount->guest_rate ?? ($discount->amount ?? 0)) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                                {{ $booking->currency->symbol ?? '' }}
                                                            </td>
                                                        @endcan
                                                        @can('view booking margins')
                                                            <td class="text-primary">
                                                                {{ ($val = $discount->margin ?? ($discount->guest_rate ?? ($discount->amount ?? 0)) - ($discount->net_rate ?? 0)) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                                {{ $booking->currency->symbol ?? '' }}
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="fw-bold table-active border-top border-2 border-dark">
                                                    <td class="text-uppercase">{{ __('Total') }}</td>
                                                    <td class="text-muted">
                                                        {{ ($val = $booking->adjustments->where('type', 'discount')->sum('net_rate') ?: $booking->adjustments->where('type', 'discount')->sum('amount')) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                        {{ $booking->currency->symbol ?? '' }}
                                                    </td>
                                                    @can('view booking guest rates')
                                                        <td class="text-danger">
                                                            -{{ ($val = $booking->adjustments->where('type', 'discount')->sum('guest_rate') ?: $booking->adjustments->where('type', 'discount')->sum('amount')) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                            {{ $booking->currency->symbol ?? '' }}
                                                        </td>
                                                    @endcan
                                                    @can('view booking margins')
                                                        <td class="text-primary">
                                                            {{ ($val = $booking->adjustments->where('type', 'discount')->sum('margin') ?: $booking->adjustments->where('type', 'discount')->sum('guest_rate') - $booking->adjustments->where('type', 'discount')->sum('net_rate')) == 0 ? '' : \App\Helpers\NumberHelper::format($val) }}
                                                            {{ $booking->currency->symbol ?? '' }}
                                                        </td>
                                                    @endcan
                                                </tr>
                                            </tfoot>
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
                                    <h6 class="text-muted mb-2">{{ __('Net Amount') }}</h6>
                                    <h4 class="mb-0 text-success">
                                        {{ $booking->net_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount) }}<br>
                                        <small class="text-muted">{{ $booking->currency->symbol ?? '' }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">{{ __('Paid Amount') }}</h6>
                                    <h4 class="mb-0 text-primary">
                                        {{ $booking->paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->paid_amount) }}<br>
                                        <small class="text-muted">{{ $booking->currency->symbol ?? '' }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">{{ __('Pending Amount') }}</h6>
                                    <h4 class="mb-0 text-warning">
                                        {{ $booking->net_amount - $booking->paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount - $booking->paid_amount) }}<br>
                                        <small class="text-muted">{{ $booking->currency->symbol ?? '' }}</small>
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
