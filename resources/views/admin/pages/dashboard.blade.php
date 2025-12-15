@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- View Dashboard -->
                <div class="col-12">
                    <div class="card">
                        <div class="d-flex align-items-start row">
                            <div class="col-7">
                                <div class="card-body text-nowrap">
                                    <h5 class="mb-2">Welcome back,<span class="h4"> {{ auth()->user()->name }}
                                            👋🏻</span></h5>
                                    {{-- <p class="mb-2">Best seller of the month</p>
                                    <h4 class="text-primary mb-1">$48.9k</h4>
                                    <a href="javascript:;" class="btn btn-primary">View Sales</a> --}}
                                </div>
                            </div>
                            <div class="col-5 text-center text-sm-left">
                                <div class="card-body pb-0 px-0 px-md-4 pt-4">
                                    <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" height="140"
                                        alt="Show Dashboard" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- View Dashboard -->
                <!-- Bar Charts -->
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header header-elements">
                            <h5 class="card-title mb-0">@lang('Room Nights Production')</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart" class="chartjs" data-height="400"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /Bar Charts -->
            </div>
        </div>
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Total Bookings -->
                <div class="col-xl-6 col-md-12">
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

                <!-- Total Revenue -->
                <div class="col-xl-6 col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-2">{{ __('Total Margin') }}</h6>
                                    <h4 class="mb-0">{{ number_format($totalAmount, 2) }}</h4>
                                </div>
                                <div class="avatar avatar-md">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="ti tabler-currency-dollar ti-md"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 flex-wrap gap-1">
                                <span class="badge bg-label-success opacity-0">{{ __('Paid') }}:
                                    {{ number_format($paidAmount, 2) }}</span>
                                {{-- <span class="badge bg-label-warning">{{ __('Pending') }}: {{ number_format($pendingAmount, 2) }}</span> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Payment Status -->
                <div class="col-12 mb-4">
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
                <div class="col-12">
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
            </div>
        </div>
    </div>

    <!-- Hotels Sales & Rooms -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Top Hotels by Sales -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Top Hotels by Sales') }}</h5>
                            @can('view hotels')
                                <a href="{{ route('hotels.index') }}"
                                    class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                            @endcan
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
                                                    <a href="{{ route('hotels.show', $hotel['id']) }}"
                                                        class="fw-semibold">
                                                        {{ $hotel['name'] }}
                                                    </a>
                                                </td>
                                                <td>{{ number_format($hotel['total_sales'], 2) }}</td>
                                                <td>{{ number_format($hotel['paid_sales'], 2) }}</td>
                                                <td><span
                                                        class="badge bg-label-primary">{{ $hotel['bookings_count'] }}</span>
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
        </div>
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Top Hotels by Rooms -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Top Hotels by Rooms') }}</h5>
                            @can('view hotels')
                                <a href="{{ route('hotels.index') }}"
                                    class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Hotel') }}</th>
                                            <th>{{ __('Rooms') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($hotelsSales->sortByDesc('rooms_count')->take(10) as $hotel)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('hotels.show', $hotel['id']) }}"
                                                        class="fw-semibold">
                                                        {{ $hotel['name'] }}
                                                    </a>
                                                </td>
                                                <td><span class="badge bg-label-info">{{ $hotel['rooms_count'] }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">
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
        </div>
    </div>

    <!-- Customers -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Top Customers by Bookings -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Top Customers by Bookings') }}</h5>
                            @can('view customers')
                                <a href="{{ route('customers.index') }}"
                                    class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                            @endcan
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
                                                    <a href="{{ route('customers.show', $customer['id']) }}"
                                                        class="fw-semibold">
                                                        {{ $customer['name'] }}
                                                    </a>
                                                </td>
                                                <td><span
                                                        class="badge bg-label-primary">{{ $customer['bookings_count'] }}</span>
                                                </td>
                                                <td>{{ number_format($customer['total_revenue'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    {{ __('No customers found') }}
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
        </div>
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Top Customers by Revenue -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Top Customers by Revenue') }}</h5>
                            <a href="{{ route('customers.index') }}"
                                class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
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
                                                    <a href="{{ route('customers.show', $customer['id']) }}"
                                                        class="fw-semibold">
                                                        {{ $customer['name'] }}
                                                    </a>
                                                </td>
                                                <td class="text-success fw-semibold">
                                                    {{ number_format($customer['total_revenue'], 2) }}</td>
                                                <td><span
                                                        class="badge bg-label-primary">{{ $customer['bookings_count'] }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    {{ __('No customers found') }}
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
        </div>
    </div>

    <!-- Most Used Codes & Recent Bookings -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Most Used Booking Codes -->
                <div class="col-12">
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
                                                <td><span class="badge bg-label-primary">{{ $code->usage_count }}
                                                        {{ __('Times') }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">
                                                    {{ __('No codes found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12 mb-4">
            <div class="row">
                <!-- Recent Bookings -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Recent Bookings') }}</h5>
                            @can('view bookings')
                                <a href="{{ route('bookings.index') }}"
                                    class="btn btn-sm btn-label-primary">{{ __('View All') }}</a>
                            @endcan
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
                                                    <a href="{{ route('bookings.show', $booking->id) }}"
                                                        class="fw-semibold">
                                                        {{ $booking->code }}
                                                    </a>
                                                </td>
                                                <td>{{ $booking->customer->name }}</td>
                                                <td>{{ $booking->hotel->name }}</td>
                                                <td>{{ $booking->check_in->format('Y-m-d') }}</td>
                                                <td>{{ number_format($booking->total_amount, 2) }}
                                                    {{ $booking->currency->symbol }}</td>
                                                <td>
                                                    @if ($booking->status == 'confirmed')
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
                                                <td colspan="6" class="text-center text-muted">
                                                    {{ __('No bookings found') }}
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
        </div>
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
                                    title: function(context) {
                                        // Show full hotel name in tooltip title
                                        const index = context[0].dataIndex;
                                        return hotelLabels[index];
                                    },
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
                                    maxRotation: 0,
                                    minRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 10,
                                    callback: function(value, index, ticks) {
                                        const label = this.getLabelForValue(value);
                                        // Truncate label if too long
                                        if (label.length > 15) {
                                            return label.substring(0, 12) + '...';
                                        }
                                        return label;
                                    }
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
    </script>
@endsection
