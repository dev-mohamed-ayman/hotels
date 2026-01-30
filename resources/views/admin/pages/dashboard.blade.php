@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <!-- Upcoming Option Dates -->
    @if ($upcomingOptionDates->isNotEmpty())
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0">{{ __('Upcoming Option Dates') }}</h5>
                            <span class="badge bg-label-warning">{{ __('Next 7 Days') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center table-sm" style="font-size: 0.875rem;">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">{{ __('Code') }}</th>
                                        <th class="text-nowrap">{{ __('Hotel') }}</th>
                                        <th class="text-nowrap">{{ __('Rooms') }}</th>
                                        <th class="text-nowrap">{{ __('Check In') }}</th>
                                        <th class="text-nowrap">{{ __('Check Out') }}</th>
                                        <th class="text-nowrap">{{ __('Nights') }}</th>
                                        <th class="text-nowrap">{{ __('Total Net') }}</th>
                                        <th class="text-nowrap">{{ __('Paid Net') }}</th>
                                        <th class="text-nowrap">{{ __('Remaining Net') }}</th>
                                        <th class="text-nowrap">{{ __('Option Date') }}</th>
                                        <th class="text-nowrap">{{ __('Status') }}</th>
                                        <th class="text-nowrap">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingOptionDates as $booking)
                                        <tr>
                                            <td class="text-nowrap py-2"><strong>{{ $booking->code }}</strong></td>
                                            <td class="text-nowrap py-2">{{ $booking->hotel->name }}</td>
                                            <td class="py-2" style="width: 130px;">
                                                @if ($booking->rooms && $booking->rooms->count() > 0)
                                                    @php
                                                        $roomTypes = ['SGL' => 0, 'DBL' => 0, 'TPL' => 0, 'QUD' => 0];
                                                        $totalRooms = 0;
                                                        foreach ($booking->rooms as $room) {
                                                            $roomTypes[$room->room_type] += $room->room_count;
                                                            $totalRooms += $room->room_count;
                                                        }
                                                    @endphp
                                                    <div class="text-start" style="line-height: 1.4;">
                                                        <div class="mb-1">
                                                            <strong class="text-primary">{{ $totalRooms }}</strong>
                                                            <small
                                                                class="text-muted">{{ $totalRooms == 1 ? __('Room') : __('Rooms') }}</small>
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach (['SGL', 'DBL', 'TPL', 'QUD'] as $type)
                                                                @if ($roomTypes[$type] > 0)
                                                                    <span class="badge bg-label-info"
                                                                        style="font-size: 0.7rem; padding: 0.15rem 0.35rem; line-height: 1.2;">
                                                                        {{ $roomTypes[$type] }}
                                                                        <strong>{{ $type }}</strong>
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap py-2">{{ $booking->check_in->format('d-m-Y') }}</td>
                                            <td class="text-nowrap py-2">{{ $booking->check_out->format('d-m-Y') }}</td>
                                            <td class="text-nowrap py-2">{{ $booking->nights }}</td>
                                            <td class="text-nowrap py-2">
                                                {{ $booking->net_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->net_amount) }}
                                            </td>
                                            <td class="text-nowrap py-2">
                                                <span class="fw-semibold text-success">
                                                    {{ $booking->hotel_paid_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) }}
                                                </span>
                                            </td>
                                            <td class="text-nowrap py-2">
                                                @php
                                                    $remaining = $booking->net_amount - $booking->hotel_paid_amount;
                                                @endphp
                                                @if ($remaining > 0)
                                                    <span class="fw-semibold text-danger">
                                                        {{ $booking->currency->symbol }}
                                                        {{ \App\Helpers\NumberHelper::format($remaining) }}
                                                    </span>
                                                @elseif ($booking->net_amount > 0)
                                                    <span class="badge bg-label-success" style="font-size: 0.75rem;">
                                                        <i class="ti tabler-check me-1"></i>
                                                        {{ __('Paid') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td class="py-2">
                                                @if ($booking->option_date)
                                                    <div class="d-flex flex-column align-items-center gap-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <i class="ti tabler-calendar-event text-primary"
                                                                style="font-size: 0.9rem;"></i>
                                                            <span
                                                                class="fw-semibold text-primary text-nowrap">{{ $booking->option_date->format('d-m-Y') }}</span>
                                                        </div>
                                                        @if ($remaining <= 0)
                                                            <span class="badge bg-label-success"
                                                                style="font-size: 0.75rem;">
                                                                <i class="ti tabler-check me-1"></i>
                                                                {{ __('Paid') }}
                                                            </span>
                                                        @else
                                                            @if ($booking->option_date->isPast())
                                                                <span class="badge bg-label-warning"
                                                                    style="font-size: 0.65rem;">{{ __('Past') }}</span>
                                                            @elseif ($booking->option_date->isToday())
                                                                <span class="badge bg-label-info"
                                                                    style="font-size: 0.65rem;">{{ __('Today') }}</span>
                                                            @else
                                                                <span class="badge bg-label-success"
                                                                    style="font-size: 0.65rem;">{{ __('Upcoming') }}</span>
                                                            @endif
                                                        @endif

                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap py-2">
                                                @if ($booking->status == 'confirmed')
                                                    <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                                                @elseif($booking->status == 'pending')
                                                    <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                                @else
                                                    <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                                        id="upcomingActionsDropdown{{ $booking->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        style="padding: 0.25rem 0.5rem;">
                                                        <i class="ti tabler-dots-vertical" style="font-size: 0.9rem;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="upcomingActionsDropdown{{ $booking->id }}">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('bookings.show', $booking) }}">
                                                                <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                                            </a>
                                                        </li>
                                                        @can('edit bookings')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('bookings.edit', $booking) }}">
                                                                    <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete bookings')
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('bookings.destroy', $booking) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger w-100 text-start">
                                                                        <i class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- All Bookings Starting Within 2 Days -->
    @if ($upcomingBookings->isNotEmpty())
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0">{{ __('All Bookings Starting Within 2 Days') }}</h5>
                            <span class="badge bg-label-info">{{ $upcomingBookings->count() }}
                                {{ __('Bookings') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center table-sm" style="font-size: 0.875rem;">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">{{ __('Code') }}</th>
                                        <th class="text-nowrap">{{ __('Hotel') }}</th>
                                        <th class="text-nowrap">{{ __('Rooms') }}</th>
                                        <th class="text-nowrap">{{ __('Check In') }}</th>
                                        <th class="text-nowrap">{{ __('Check Out') }}</th>
                                        <th class="text-nowrap">{{ __('Nights') }}</th>
                                        <th class="text-nowrap">{{ __('Total Net') }}</th>
                                        <th class="text-nowrap">{{ __('Paid Net') }}</th>
                                        <th class="text-nowrap">{{ __('Remaining Net') }}</th>
                                        <th class="text-nowrap">{{ __('Option Date') }}</th>
                                        <th class="text-nowrap">{{ __('Status') }}</th>
                                        <th class="text-nowrap">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingBookings as $booking)
                                        <tr>
                                            <td class="text-nowrap py-2"><strong>{{ $booking->code }}</strong></td>
                                            <td class="text-nowrap py-2">{{ $booking->hotel->name }}</td>
                                            <td class="py-2" style="width: 130px;">
                                                @if ($booking->rooms && $booking->rooms->count() > 0)
                                                    @php
                                                        $roomTypes = ['SGL' => 0, 'DBL' => 0, 'TPL' => 0, 'QUD' => 0];
                                                        $totalRooms = 0;
                                                        foreach ($booking->rooms as $room) {
                                                            $roomTypes[$room->room_type] += $room->room_count;
                                                            $totalRooms += $room->room_count;
                                                        }
                                                    @endphp
                                                    <div class="text-start" style="line-height: 1.4;">
                                                        <div class="mb-1">
                                                            <strong class="text-primary">{{ $totalRooms }}</strong>
                                                            <small
                                                                class="text-muted">{{ $totalRooms == 1 ? __('Room') : __('Rooms') }}</small>
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach (['SGL', 'DBL', 'TPL', 'QUD'] as $type)
                                                                @if ($roomTypes[$type] > 0)
                                                                    <span class="badge bg-label-info"
                                                                        style="font-size: 0.7rem; padding: 0.15rem 0.35rem; line-height: 1.2;">
                                                                        {{ $roomTypes[$type] }}
                                                                        <strong>{{ $type }}</strong>
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap py-2">{{ $booking->check_in->format('d-m-Y') }}</td>
                                            <td class="text-nowrap py-2">{{ $booking->check_out->format('d-m-Y') }}</td>
                                            <td class="text-nowrap py-2">{{ $booking->nights }}</td>
                                            <td class="text-nowrap py-2">
                                                {{ $booking->net_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->net_amount) }}
                                            </td>
                                            <td class="text-nowrap py-2">
                                                <span class="fw-semibold text-success">
                                                    {{ $booking->hotel_paid_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) }}
                                                </span>
                                            </td>
                                            <td class="text-nowrap py-2">
                                                @php
                                                    $remaining = $booking->net_amount - $booking->hotel_paid_amount;
                                                @endphp
                                                @if ($remaining > 0)
                                                    <span class="badge bg-label-danger" style="font-size: 0.75rem;">
                                                        <i class="ti tabler-alert-circle me-1"></i>
                                                        {{ $booking->currency->symbol }}
                                                        {{ \App\Helpers\NumberHelper::format($remaining) }}
                                                    </span>
                                                @elseif ($booking->net_amount > 0)
                                                    <span class="badge bg-label-success" style="font-size: 0.75rem;">
                                                        <i class="ti tabler-check me-1"></i>
                                                        {{ __('Paid') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                @if ($booking->option_date)
                                                    <div class="d-flex flex-column align-items-center gap-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <i class="ti tabler-calendar-event text-primary"
                                                                style="font-size: 0.9rem;"></i>
                                                            <span
                                                                class="fw-semibold text-primary text-nowrap">{{ $booking->option_date->format('d-m-Y') }}</span>
                                                        </div>
                                                        @if ($remaining <= 0)
                                                            <span class="badge bg-label-success"
                                                                style="font-size: 0.75rem;">
                                                                <i class="ti tabler-check me-1"></i>
                                                                {{ __('Paid') }}
                                                            </span>
                                                        @else
                                                            @if ($booking->option_date->isPast())
                                                                <span class="badge bg-label-warning"
                                                                    style="font-size: 0.65rem;">{{ __('Past') }}</span>
                                                            @elseif ($booking->option_date->isToday())
                                                                <span class="badge bg-label-info"
                                                                    style="font-size: 0.65rem;">{{ __('Today') }}</span>
                                                            @else
                                                                <span class="badge bg-label-success"
                                                                    style="font-size: 0.65rem;">{{ __('Upcoming') }}</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap py-2">
                                                @if ($booking->status == 'confirmed')
                                                    <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                                                @elseif($booking->status == 'pending')
                                                    <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                                @else
                                                    <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                                        id="upcomingActionsDropdown{{ $booking->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        style="padding: 0.25rem 0.5rem;">
                                                        <i class="ti tabler-dots-vertical" style="font-size: 0.9rem;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="upcomingActionsDropdown{{ $booking->id }}">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('bookings.show', $booking) }}">
                                                                <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                                            </a>
                                                        </li>
                                                        @can('edit bookings')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('bookings.edit', $booking) }}">
                                                                    <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete bookings')
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('bookings.destroy', $booking) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger w-100 text-start">
                                                                        <i
                                                                            class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Room Nights Production Chart -->
        <div class="col-xl-8 col-lg-7 col-12">
            <div class="card h-100">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">@lang('Room Nights Production')</h5>
                </div>
                <div class="card-body">
                    <canvas id="barChart" class="chartjs" data-height="400"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="col-xl-4 col-lg-5 col-12">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Payment Status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4 mt-2">
                        <div>
                            <p class="mb-1 text-muted">{{ __('Paid') }}</p>
                            <h4 class="mb-0 text-success fw-bold">
                                {{ $paidBookings == 0 ? '' : \App\Helpers\NumberHelper::format($paidBookings) }}</h4>
                        </div>
                        <div class="avatar avatar-lg">
                            <div class="avatar-initial bg-label-success rounded shadow-sm">
                                <i class="ti tabler-check ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <p class="mb-1 text-muted">{{ __('Partial Payment') }}</p>
                            <h4 class="mb-0 text-warning fw-bold">
                                {{ $partialBookings == 0 ? '' : \App\Helpers\NumberHelper::format($partialBookings) }}</h4>
                        </div>
                        <div class="avatar avatar-lg">
                            <div class="avatar-initial bg-label-warning rounded shadow-sm">
                                <i class="ti tabler-alert-circle ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <p class="mb-1 text-muted">{{ __('Unpaid') }}</p>
                            <h4 class="mb-0 text-danger fw-bold">
                                {{ $unpaidBookings == 0 ? '' : \App\Helpers\NumberHelper::format($unpaidBookings) }}</h4>
                        </div>
                        <div class="avatar avatar-lg">
                            <div class="avatar-initial bg-label-danger rounded shadow-sm">
                                <i class="ti tabler-x ti-md"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Hotels by Sales -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Top Hotels by Sales') }}</h5>
                    <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Hotel') }}</th>
                                    <th>{{ __('Sales') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Bookings') }}</th>
                                    <th>{{ __('Rooms') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotelsSales as $hotel)
                                    <tr>
                                        <td>
                                            <a href="{{ route('hotels.show', $hotel['id']) }}" class="fw-semibold">
                                                {{ $hotel['name'] }}
                                            </a>
                                        </td>
                                        <td>{{ $hotel['total_sales'] == 0 ? '' : \App\Helpers\NumberHelper::format($hotel['total_sales']) }}
                                        </td>
                                        <td>{{ $hotel['paid_sales'] == 0 ? '' : \App\Helpers\NumberHelper::format($hotel['paid_sales']) }}
                                        </td>
                                        <td><span class="badge bg-label-primary">{{ $hotel['bookings_count'] }}</span>
                                        </td>
                                        <td><span class="badge bg-label-info">{{ $hotel['rooms_count'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            {{ __('No hotels found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Recent Bookings') }}</h5>
                    <a href="{{ route('bookings.index') }}"
                        class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover text-center table-sm" style="font-size: 0.875rem;">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">{{ __('Code') }}</th>
                                    <th class="text-nowrap">{{ __('Hotel') }}</th>
                                    <th class="text-nowrap">{{ __('Rooms') }}</th>
                                    <th class="text-nowrap">{{ __('Check In') }}</th>
                                    <th class="text-nowrap">{{ __('Check Out') }}</th>
                                    <th class="text-nowrap">{{ __('Nights') }}</th>
                                    <th class="text-nowrap">{{ __('Total Net') }}</th>
                                    <th class="text-nowrap">{{ __('Paid Net') }}</th>
                                    <th class="text-nowrap">{{ __('Remaining Net') }}</th>
                                    <th class="text-nowrap">{{ __('Option Date') }}</th>
                                    <th class="text-nowrap">{{ __('Status') }}</th>
                                    <th class="text-nowrap">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentBookings as $booking)
                                    <tr>
                                        <td class="text-nowrap py-2"><strong>{{ $booking->code }}</strong></td>
                                        <td class="text-nowrap py-2">{{ $booking->hotel->name }}</td>
                                        <td class="py-2" style="width: 130px;">
                                            @if ($booking->rooms && $booking->rooms->count() > 0)
                                                @php
                                                    $roomTypes = ['SGL' => 0, 'DBL' => 0, 'TPL' => 0, 'QUD' => 0];
                                                    $totalRooms = 0;
                                                    foreach ($booking->rooms as $room) {
                                                        $roomTypes[$room->room_type] += $room->room_count;
                                                        $totalRooms += $room->room_count;
                                                    }
                                                @endphp
                                                <div class="text-start" style="line-height: 1.4;">
                                                    <div class="mb-1">
                                                        <strong class="text-primary">{{ $totalRooms }}</strong>
                                                        <small
                                                            class="text-muted">{{ $totalRooms == 1 ? __('Room') : __('Rooms') }}</small>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach (['SGL', 'DBL', 'TPL', 'QUD'] as $type)
                                                            @if ($roomTypes[$type] > 0)
                                                                <span class="badge bg-label-info"
                                                                    style="font-size: 0.7rem; padding: 0.15rem 0.35rem; line-height: 1.2;">
                                                                    {{ $roomTypes[$type] }}
                                                                    <strong>{{ $type }}</strong>
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap py-2">{{ $booking->check_in->format('d-m-Y') }}</td>
                                        <td class="text-nowrap py-2">{{ $booking->check_out->format('d-m-Y') }}</td>
                                        <td class="text-nowrap py-2">{{ $booking->nights }}</td>
                                        <td class="text-nowrap py-2">
                                            {{ $booking->net_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->net_amount) }}
                                        </td>
                                        <td class="text-nowrap py-2">
                                            <span class="fw-semibold text-success">
                                                {{ $booking->hotel_paid_amount == 0 ? '' : $booking->currency->symbol . ' ' . \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap py-2">
                                            @php
                                                $remaining = $booking->net_amount - $booking->hotel_paid_amount;
                                            @endphp
                                            @if ($remaining > 0)
                                                <span class="badge bg-label-danger" style="font-size: 0.75rem;">
                                                    <i class="ti tabler-alert-circle me-1"></i>
                                                    {{ $booking->currency->symbol }}
                                                    {{ \App\Helpers\NumberHelper::format($remaining) }}
                                                </span>
                                            @elseif ($booking->net_amount > 0)
                                                <span class="badge bg-label-success" style="font-size: 0.75rem;">
                                                    <i class="ti tabler-check me-1"></i>
                                                    {{ __('Paid') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            @if ($booking->option_date)
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="ti tabler-calendar-event text-primary"
                                                            style="font-size: 0.9rem;"></i>
                                                        <span
                                                            class="fw-semibold text-primary text-nowrap">{{ $booking->option_date->format('d-m-Y') }}</span>
                                                    </div>
                                                    @if ($remaining <= 0)
                                                        <span class="badge bg-label-success" style="font-size: 0.75rem;">
                                                            <i class="ti tabler-check me-1"></i>
                                                            {{ __('Paid') }}
                                                        </span>
                                                    @else
                                                        @if ($booking->option_date->isPast())
                                                            <span class="badge bg-label-warning"
                                                                style="font-size: 0.65rem;">{{ __('Past') }}</span>
                                                        @elseif ($booking->option_date->isToday())
                                                            <span class="badge bg-label-info"
                                                                style="font-size: 0.65rem;">{{ __('Today') }}</span>
                                                        @else
                                                            <span class="badge bg-label-success"
                                                                style="font-size: 0.65rem;">{{ __('Upcoming') }}</span>
                                                        @endif
                                                    @endif

                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap py-2">
                                            @if ($booking->hotel_paid_amount == 0)
                                                <span class="badge bg-danger" title="{{ __('Unpaid') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-x"></i>
                                                </span>
                                            @elseif ($booking->hotel_paid_amount >= $booking->net_amount && $booking->net_amount > 0)
                                                <span class="badge bg-success" title="{{ __('Paid') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-check"></i>
                                                </span>
                                            @elseif ($booking->net_amount > 0)
                                                <span class="badge bg-warning" title="{{ __('Partial Payment') }}"
                                                    style="font-size: 0.75rem;">
                                                    <i class="ti tabler-question-mark"></i>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-icon btn-secondary" type="button"
                                                    id="actionsDropdown{{ $booking->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" style="padding: 0.25rem 0.5rem;">
                                                    <i class="ti tabler-dots-vertical" style="font-size: 0.9rem;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="actionsDropdown{{ $booking->id }}">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('bookings.show', $booking) }}">
                                                            <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                                                        </a>
                                                    </li>
                                                    @can('edit bookings')
                                                        @if ($booking->check_in >= now())
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('bookings.edit', $booking) }}">
                                                                    <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        {{-- <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#paymentModal{{ $booking->id }}">
                                                                <i
                                                                    class="ti tabler-currency-dollar me-2"></i>{{ __('Update Payment') }}
                                                            </a>
                                                        </li> --}}
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#hotelPaymentModal{{ $booking->id }}">
                                                                <i
                                                                    class="ti tabler-building me-2"></i>{{ __('Update Hotel Payment') }}
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('delete bookings')
                                                        @if ($booking->check_in >= now())
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('bookings.destroy', $booking) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger w-100 text-start">
                                                                        <i
                                                                            class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Payment Update Modal -->
                                    <div class="modal fade" id="paymentModal{{ $booking->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Update Payment') }} -
                                                        {{ $booking->code }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('bookings.update-payment', $booking) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Total Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $booking->total_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->total_amount) }} {{ $booking->currency->symbol }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('Current Paid Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $booking->paid_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->paid_amount) }} {{ $booking->currency->symbol }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            @php
                                                                $remaining =
                                                                    $booking->total_amount - $booking->paid_amount;
                                                            @endphp
                                                            <label
                                                                class="form-label">{{ __('Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="remaining{{ $booking->id }}"
                                                                value="{{ \App\Helpers\NumberHelper::format($remaining) }} {{ $booking->currency->symbol }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="payment_amount{{ $booking->id }}">{{ __('Payment Amount') }}
                                                                *</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="payment_amount{{ $booking->id }}"
                                                                    name="payment_amount" min="1"
                                                                    max="{{ $remaining }}" step="1"
                                                                    data-remaining="{{ $remaining }}"
                                                                    data-currency="{{ $booking->currency->symbol }}"
                                                                    data-booking-id="{{ $booking->id }}"
                                                                    placeholder="{{ __('Enter amount to pay') }}"
                                                                    required>
                                                                <span
                                                                    class="input-group-text">{{ $booking->currency->symbol }}</span>
                                                            </div>
                                                            <small class="text-muted">{{ __('Maximum') }}:
                                                                {{ \App\Helpers\NumberHelper::format($remaining) }}
                                                                {{ $booking->currency->symbol }}</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('New Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="new_remaining{{ $booking->id }}"
                                                                value="{{ \App\Helpers\NumberHelper::format($remaining) }} {{ $booking->currency->symbol }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Update') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                    </div>

                    <!-- Hotel Payment Update Modal -->
                    <div class="modal fade" id="hotelPaymentModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('Update Hotel Payment') }} -
                                        {{ $booking->code }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('bookings.update-hotel-payment', $booking) }}" method="POST"
                                    onsubmit="return confirmHotelOverpayment(this)">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Total Net Amount') }}</label>
                                            <input type="text" class="form-control"
                                                value="{{ $booking->net_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount) }} {{ $booking->currency->symbol }}"
                                                readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Current Paid to Hotel') }}</label>
                                            <input type="text" class="form-control"
                                                value="{{ $booking->hotel_paid_amount == 0
                                                    ? ''
                                                    : \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) . ' ' . $booking->currency->symbol }}"
                                                readonly>


                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="hotel_paid_amount{{ $booking->id }}">{{ __('Set New Paid to Hotel Amount') }}
                                                *</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    id="hotel_paid_amount{{ $booking->id }}" name="hotel_paid_amount"
                                                    data-net-amount="{{ $booking->net_amount }}"
                                                    data-currency="{{ $booking->currency->symbol }}"
                                                    data-booking-id="{{ $booking->id }}"
                                                    value="{{ $booking->hotel_paid_amount }}"
                                                    placeholder="{{ __('Enter total paid amount') }}" required>
                                                <span class="input-group-text">{{ $booking->currency->symbol }}</span>
                                            </div>
                                            <small
                                                class="text-muted">{{ __('Enter the total amount paid to the hotel. This will replace the current paid amount.') }}</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('New Remaining Amount') }}</label>
                                            <input type="text" class="form-control"
                                                id="new_hotel_remaining{{ $booking->id }}"
                                                value="{{ \App\Helpers\NumberHelper::format($booking->net_amount - $booking->hotel_paid_amount) . ' ' . $booking->currency->symbol }}"
                                                readonly>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked
                                                    name="deduct_from_wallet"
                                                    id="deduct_from_wallet{{ $booking->id }}">
                                                <label class="form-check-label"
                                                    for="deduct_from_wallet{{ $booking->id }}">
                                                    {{ __('Deduct booking price from customer wallet') }}
                                                    ({{ \App\Helpers\NumberHelper::format($booking->total_amount) }}
                                                    {{ $booking->currency->symbol }})
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ti tabler-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                                <p class="mt-3 text-muted">{{ __('No bookings found') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Widget -->
    @can('view activity log')
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                @include('admin.components.recent-activities')
            </div>
        </div>
    @endcan
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>
    <script src="{{ asset('assets//js/charts-chartjs-legend.js') }}"></script>
    <script src="{{ asset('assets/js/charts-chartjs.js') }}"></script>

    <script>
        // Bar Chart - Room Nights Production (Top 5 Hotels)
        // --------------------------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() {
            const barChart = document.getElementById('barChart');
            if (barChart) {
                // Get data from PHP
                const topHotelsData = @json($topHotelsByRoomNights);

                // Extract hotel names and room nights production
                const hotelLabels = topHotelsData.map(hotel => hotel.name);
                const roomNightsData = topHotelsData.map(hotel => hotel.room_nights_production);

                // Calculate max value for Y axis (round up to nearest 50)
                const maxValue = Math.max(...roomNightsData, 1);
                const yAxisMax = Math.ceil(maxValue / 50) * 50; // Round up to nearest 50
                const stepSize = 50; // Fixed step size of 50

                // Color variables
                const cyanColor = '#28dac6';
                const isRtl = document.documentElement.dir === 'rtl';
                const isDarkStyle = window.Helpers ? window.Helpers.isDarkStyle() : false;
                const cardColor = window.Helpers ? window.Helpers.getCssVar('paper-bg', true) : '#fff';
                const headingColor = window.Helpers ? window.Helpers.getCssVar('heading-color', true) : '#5e5873';
                const labelColor = window.Helpers ? window.Helpers.getCssVar('secondary-color', true) : '#a8aaae';
                const legendColor = window.Helpers ? window.Helpers.getCssVar('body-color', true) : '#6e6b7b';
                const borderColor = window.Helpers ? window.Helpers.getCssVar('border-color', true) : '#ebe9f1';

                const barChartVar = new Chart(barChart, {
                    type: 'bar',
                    data: {
                        labels: hotelLabels,
                        datasets: [{
                            label: '{{ __('Room Nights Production') }}',
                            data: roomNightsData,
                            backgroundColor: cyanColor,
                            borderColor: 'transparent',
                            maxBarThickness: 15,
                            borderRadius: {
                                topRight: 15,
                                topLeft: 15
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 500
                        },
                        plugins: {
                            tooltip: {
                                rtl: isRtl,
                                backgroundColor: cardColor,
                                titleColor: headingColor,
                                bodyColor: legendColor,
                                borderWidth: 1,
                                borderColor: borderColor,
                                callbacks: {
                                    label: function(context) {
                                        return '{{ __('Room Nights') }}: ' + context.parsed.y
                                            .toLocaleString();
                                    }
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor
                                },
                                ticks: {
                                    color: labelColor,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                min: 0,
                                max: yAxisMax,
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor
                                },
                                ticks: {
                                    stepSize: stepSize,
                                    color: labelColor,
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        // Payment Update Logic
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Customer Payment Logic (Incremental)
            const paymentInputs = document.querySelectorAll('[id^="payment_amount"]');

            paymentInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    const remaining = parseFloat(this.getAttribute('data-remaining'));
                    const currency = this.getAttribute('data-currency');
                    const paymentAmount = parseFloat(this.value) || 0;

                    // Calculate new remaining
                    const newRemaining = remaining - paymentAmount;

                    // Update the new remaining field
                    const newRemainingField = document.getElementById('new_remaining' + bookingId);
                    if (newRemainingField) {
                        newRemainingField.value = newRemaining.toFixed(0) + ' ' + currency;

                        // Add visual feedback
                        if (paymentAmount > remaining) {
                            this.classList.add('is-invalid');
                            newRemainingField.classList.add('text-danger');
                        } else {
                            this.classList.remove('is-invalid');
                            newRemainingField.classList.remove('text-danger');
                        }
                    }
                });
            });

            // 2. Hotel Payment Logic (Total)
            const hotelPaidInputs = document.querySelectorAll('[id^="hotel_paid_amount"]');

            hotelPaidInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    const netAmount = parseFloat(this.getAttribute('data-net-amount'));
                    const currency = this.getAttribute('data-currency');
                    const paidAmount = parseFloat(this.value) || 0;

                    // Calculate new remaining
                    const newRemaining = netAmount - paidAmount;

                    // Update the new remaining field
                    const newRemainingField = document.getElementById('new_hotel_remaining' +
                        bookingId);
                    if (newRemainingField) {
                        newRemainingField.value = newRemaining.toFixed(2) + ' ' + currency;

                        // Add visual feedback
                        if (newRemaining < 0) {
                            newRemainingField.classList.add('text-danger');
                        } else {
                            newRemainingField.classList.remove('text-danger');
                        }
                    }
                });
            });
        });

        // Global function for Hotel Overpayment Check (Defined globally)
        window.confirmHotelOverpayment = function(form) {
            try {
                const hotelPaidInput = form.querySelector('input[data-net-amount]');

                if (hotelPaidInput) {
                    const netAmountStr = hotelPaidInput.getAttribute('data-net-amount');
                    if (!netAmountStr) return true;

                    // Remove commas just in case it's formatted string
                    const netAmount = parseFloat(netAmountStr.toString().replace(/,/g, ''));
                    const paidAmount = parseFloat(hotelPaidInput.value) || 0;

                    if (paidAmount > netAmount) {
                        const confirmMessage =
                            "{{ __('Warning: The amount you are paying is greater than the total net amount. Do you want to proceed?') }}";
                        return confirm(confirmMessage);
                    }
                }
            } catch (e) {
                console.error('Payment validation error:', e);
            }
            return true;
        };
    </script>
@endsection
