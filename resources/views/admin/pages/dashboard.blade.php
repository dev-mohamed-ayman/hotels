@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Bookings -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Total Bookings') }}</h6>
                            <h4 class="mb-0">{{ number_format($totalBookings) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="ti tabler-calendar-check ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap gap-1">
                        <span class="badge bg-label-success">{{ $confirmedBookings }} {{ __('Confirmed') }}</span>
                        <span class="badge bg-label-warning">{{ $pendingBookings }} {{ __('Pending') }}</span>
                        <span class="badge bg-label-danger">{{ $cancelledBookings }} {{ __('Cancelled') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Nights Production -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Room Nights Production') }}</h6>
                            <h4 class="mb-0">{{ number_format($roomNightsProduction) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="ti tabler-door ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <small class="text-muted">{{ __('Total rooms × nights') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Total Revenue') }}</h6>
                            <h4 class="mb-0">{{ number_format($totalAmount, 2) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="ti tabler-currency-dollar ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap gap-1">
                        <span class="badge bg-label-success">{{ __('Paid') }}: {{ number_format($paidAmount, 2) }}</span>
                        <span class="badge bg-label-warning">{{ __('Pending') }}: {{ number_format($pendingAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Total Customers') }}</h6>
                            <h4 class="mb-0">{{ number_format($totalCustomers) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ti tabler-users ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap gap-1">
                        <span class="badge bg-label-success">{{ $activeCustomers }} {{ __('Active') }}</span>
                        <span class="badge bg-label-info">{{ $potentialCustomers }} {{ __('Potential') }}</span>
                        <span class="badge bg-label-danger">{{ $cancelledCustomers }} {{ __('Cancelled') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Statistics -->
    <div class="row g-4 mb-4">
        <!-- Total Rooms -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Total Rooms') }}</h6>
                            <h4 class="mb-0">{{ number_format($totalRooms) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="ti tabler-door ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <small class="text-muted">{{ __('All booking rooms') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Booking Value -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Average Booking Value') }}</h6>
                            <h4 class="mb-0">{{ number_format($averageBookingValue, 2) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="ti tabler-chart-line ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <small class="text-muted">{{ __('Per booking average') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Nights -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Average Nights') }}</h6>
                            <h4 class="mb-0">{{ number_format($averageNights, 1) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="ti tabler-moon ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <small class="text-muted">{{ __('Per booking average') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Hotels -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2">{{ __('Total Hotels') }}</h6>
                            <h4 class="mb-0">{{ number_format($totalHotels) }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="ti tabler-building ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap gap-1">
                        <span class="badge bg-label-success">{{ $activeHotels }} {{ __('Active') }}</span>
                        <span class="badge bg-label-secondary">{{ $inactiveHotels }} {{ __('Inactive') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Row -->
    <div class="row g-4 mb-4">
        <!-- Payment Status -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Payment Status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <p class="mb-1">{{ __('Paid') }}</p>
                            <h5 class="mb-0 text-success">{{ number_format($paidBookings) }}</h5>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="ti tabler-check ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <p class="mb-1">{{ __('Partial Payment') }}</p>
                            <h5 class="mb-0 text-warning">{{ number_format($partialBookings) }}</h5>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ti tabler-alert-circle ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">{{ __('Unpaid') }}</p>
                            <h5 class="mb-0 text-danger">{{ number_format($unpaidBookings) }}</h5>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="ti tabler-x ti-md"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Status -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Booking Status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <p class="mb-1">{{ __('Confirmed') }}</p>
                            <h5 class="mb-0 text-success">{{ number_format($confirmedBookings) }}</h5>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="ti tabler-check ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <p class="mb-1">{{ __('Pending') }}</p>
                            <h5 class="mb-0 text-warning">{{ number_format($pendingBookings) }}</h5>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ti tabler-clock ti-md"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">{{ __('Cancelled') }}</p>
                            <h5 class="mb-0 text-danger">{{ number_format($cancelledBookings) }}</h5>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="ti tabler-x ti-md"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Types Distribution -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Room Types Distribution') }}</h5>
                </div>
                <div class="card-body">
                    @foreach($roomTypeDistribution as $roomType)
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <p class="mb-1"><strong>{{ $roomType->room_type }}</strong></p>
                                <h5 class="mb-0">{{ number_format($roomType->count) }} {{ __('Rooms') }}</h5>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="ti tabler-door ti-md"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Hotels Sales & Rooms -->
    <div class="row g-4 mb-4">
        <!-- Top Hotels by Sales -->
        <div class="col-xl-6 col-md-12">
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
                                        <td>{{ number_format($hotel['total_sales'], 2) }}</td>
                                        <td>{{ number_format($hotel['paid_sales'], 2) }}</td>
                                        <td><span class="badge bg-label-primary">{{ $hotel['bookings_count'] }}</span></td>
                                        <td><span class="badge bg-label-info">{{ $hotel['rooms_count'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('No hotels found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Hotels by Rooms -->
        <div class="col-xl-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Top Hotels by Rooms') }}</h5>
                    <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Hotel') }}</th>
                                    <th>{{ __('Rooms') }}</th>
                                    <th>{{ __('Bookings') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotelsWithRooms as $hotel)
                                    <tr>
                                        <td>
                                            <a href="{{ route('hotels.show', $hotel['id']) }}" class="fw-semibold">
                                                {{ $hotel['name'] }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-label-info">{{ number_format($hotel['rooms_count']) }}</span></td>
                                        <td><span class="badge bg-label-primary">{{ $hotel['bookings_count'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('No hotels found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers & Codes -->
    <div class="row g-4 mb-4">
        <!-- Top Customers by Bookings -->
        <div class="col-xl-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Top Customers by Bookings') }}</h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Bookings') }}</th>
                                    <th>{{ __('Total Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('customers.show', $customer['id']) }}" class="fw-semibold">
                                                {{ $customer['name'] }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-label-primary">{{ $customer['bookings_count'] }}</span></td>
                                        <td>{{ number_format($customer['total_revenue'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('No customers found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers by Revenue -->
        <div class="col-xl-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Top Customers by Revenue') }}</h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Revenue') }}</th>
                                    <th>{{ __('Bookings') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomersByRevenue as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('customers.show', $customer['id']) }}" class="fw-semibold">
                                                {{ $customer['name'] }}
                                            </a>
                                        </td>
                                        <td class="text-success fw-semibold">{{ number_format($customer['total_revenue'], 2) }}</td>
                                        <td><span class="badge bg-label-primary">{{ $customer['bookings_count'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('No customers found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Used Codes & Currency Stats -->
    <div class="row g-4 mb-4">
        <!-- Most Used Booking Codes -->
        <div class="col-xl-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Most Used Booking Codes') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Usage Count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostUsedCodes as $code)
                                    <tr>
                                        <td><strong class="text-primary">{{ $code->code }}</strong></td>
                                        <td><span class="badge bg-label-primary">{{ $code->usage_count }} {{ __('Times') }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">{{ __('No codes found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Currency Statistics -->
        <div class="col-xl-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Currency Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Currency') }}</th>
                                    <th>{{ __('Bookings') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currencyStats as $currency)
                                    <tr>
                                        <td><strong>{{ $currency['symbol'] ?? $currency['code'] }}</strong></td>
                                        <td><span class="badge bg-label-primary">{{ $currency['bookings_count'] }}</span></td>
                                        <td>{{ number_format($currency['total_amount'], 2) }}</td>
                                        <td>{{ number_format($currency['paid_amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">{{ __('No currencies found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings & Top Hotels -->
    <div class="row g-4 mb-4">
        <!-- Recent Bookings -->
        <div class="col-xl-8 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Recent Bookings') }}</h5>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Hotel') }}</th>
                                    <th>{{ __('Check In') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('bookings.show', $booking->id) }}" class="fw-semibold">
                                                {{ $booking->code }}
                                            </a>
                                        </td>
                                        <td>{{ $booking->customer->name }}</td>
                                        <td>{{ $booking->hotel->name }}</td>
                                        <td>{{ $booking->check_in->format('Y-m-d') }}</td>
                                        <td>{{ number_format($booking->total_amount, 2) }} {{ $booking->currency->symbol }}</td>
                                        <td>
                                            @if($booking->status == 'confirmed')
                                                <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                                            @elseif($booking->status == 'pending')
                                                <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                            @else
                                                <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">{{ __('No bookings found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Hotels by Bookings -->
        <div class="col-xl-4 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Top Hotels') }}</h5>
                    <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($topHotels as $hotel)
                            <div class="list-group-item d-flex align-items-center justify-content-between px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <i class="ti tabler-building"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $hotel->name }}</h6>
                                        <small class="text-muted">{{ $hotel->address }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-label-primary">{{ $hotel->bookings_count }} {{ __('Bookings') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">{{ __('No hotels found') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
