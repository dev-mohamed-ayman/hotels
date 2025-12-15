<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Detailed Export') }}</title>
    <style>
        @page {
            margin: 5mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            text-align: center;
            table-layout: auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            white-space: nowrap;
        }

        th {
            background-color: #fce4d6;
            font-weight: bold;
            vertical-align: middle;
        }

        .header-green {
            background-color: #e2efda;
        }

        .header-red {
            background-color: #ff0000;
            color: white;
            font-weight: bold;
        }

        .header-dark-green {
            background-color: #70ad47;
            color: white;
        }

        .bg-gray {
            background-color: #f2f2f2;
        }

        .text-red {
            color: #ff0000;
            font-weight: bold;
        }

        .booking-separator {
            background-color: #d0d0d0;
            height: 10px;
        }
    </style>
</head>

<body>

    <div style="margin-bottom: 15px; text-align: center; font-size: 9pt;">
        <strong>{{ __('Total Bookings') }}: {{ $totalBookingsCount ?? count($bookingsData) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #fff;">File Code</th>
                <th rowspan="2" style="background-color: #fff;">Hotel Name</th>
                <th rowspan="2">Meals Plan</th>
                <th rowspan="2">Chk In Date</th>
                <th rowspan="2">Chk out Date</th>
                <th rowspan="2" class="text-red">Nights<br>(Dynamic)</th>
                <th rowspan="2" class="header-green">Rooms<br>Qty.</th>
                <th rowspan="2" class="header-green">Room<br>Type</th>
                <th rowspan="2" class="header-green">Category</th>
                <th rowspan="2" class="header-green">Net Rate</th>
                <th rowspan="2" class="header-green">Margin $</th>
                <th rowspan="2" class="header-green">Guest Rate<br>(Dynamic)</th>
                <th rowspan="2" class="header-dark-green">Total Net Rate<br>(Dynamic)</th>
                <th rowspan="2" class="header-dark-green">Total Guest Rate<br>(Dynamic)</th>
            </tr>
            <tr>
                <th class="header-green">CHD<br>Qty.</th>
                <th class="header-green">CHD Rate</th>
                <th class="header-green">CHD Margin</th>
                <th class="header-green">CHD Guest Rate<br>(Dynamic)</th>

                <th class="header-red">Net<br>Extras</th>
                <th class="header-dark-green">Guest<br>Extras</th>

                <th class="header-red">Net<br>Reducts</th>
                <th class="header-dark-green">Guest<br>Reducts</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookingsData as $bookingIndex => $data)
                @php
                    $booking = $data['booking'];
                    $firstRow = true;
                @endphp

                @foreach ($data['roomsData'] as $room)
                    <tr>
                        <td class="text-red" style="border: 1px solid #000;">
                            @if ($firstRow)
                                <strong>{{ $booking->code }}</strong>
                            @endif
                        </td>
                        <td style="border: 1px solid #000;">
                            @if ($firstRow)
                                <strong>{{ $booking->hotel->name }}</strong>
                            @endif
                        </td>
                        <td>
                            @if ($firstRow)
                                {{ $booking->meals_plan ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if ($firstRow)
                                {{ $booking->check_in->format('d-M-y') }}
                            @endif
                        </td>
                        <td>
                            @if ($firstRow)
                                {{ $booking->check_out->format('d-M-y') }}
                            @endif
                        </td>
                        <td>
                            @if ($firstRow)
                                {{ $booking->nights }} nights
                            @endif
                        </td>
                        <td>{{ $room['room_count'] }}</td>
                        <td>{{ $room['room_type'] }}</td>
                        <td>{{ $room['category'] ?? '-' }}</td>
                        @php
                            // Calculate per night rates (divide by room_count and nights)
                            $roomNetRatePerNight = $room['net_rate'] / ($room['room_count'] * $booking->nights);
                            $roomMarginPerNight = $room['margin'] / ($room['room_count'] * $booking->nights);
                            $roomGuestRatePerNight = $room['guest_rate'] / ($room['room_count'] * $booking->nights);
                            
                            // Calculate room totals for this row
                            $roomTotalNet = $room['net_rate'];
                            $roomTotalGuest = $room['guest_rate'];
                            
                            if ($room['child_count'] > 0) {
                                $roomTotalNet += $room['child_net_rate'];
                                $roomTotalGuest += $room['child_guest_rate'];
                            }
                            
                            if ($firstRow) {
                                $roomTotalNet += $data['additionsNetTotal'];
                                $roomTotalNet -= $data['discountsNetTotal'];
                                $roomTotalGuest += $data['additionsGuestTotal'];
                                $roomTotalGuest -= $data['discountsGuestTotal'];
                            }
                        @endphp
                        <td>${{ number_format($roomNetRatePerNight, 0) }}</td>
                        <td>${{ number_format($roomMarginPerNight, 0) }}</td>
                        <td>${{ number_format($roomGuestRatePerNight, 0) }}</td>

                        @if ($room['child_count'] > 0)
                            <td>{{ $room['child_count'] }}</td>
                            @php
                                $childNetRatePerNight = $room['child_net_rate'] / ($room['child_count'] * $booking->nights);
                                $childMarginPerNight = $room['child_margin'] / ($room['child_count'] * $booking->nights);
                                $childGuestRatePerNight = $room['child_guest_rate'] / ($room['child_count'] * $booking->nights);
                            @endphp
                            <td>${{ number_format($childNetRatePerNight, 0) }}</td>
                            <td>${{ number_format($childMarginPerNight, 0) }}</td>
                            <td>${{ number_format($childGuestRatePerNight, 0) }}</td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif

                        @if ($firstRow)
                            <td>${{ number_format($data['additionsNetTotal'], 0) }}</td>
                            <td>${{ number_format($data['additionsGuestTotal'], 0) }}</td>
                        @else
                            <td></td>
                            <td></td>
                        @endif

                        @if ($firstRow)
                            <td>${{ number_format($data['discountsNetTotal'], 0) }}</td>
                            <td>${{ number_format($data['discountsGuestTotal'], 0) }}</td>
                        @else
                            <td></td>
                            <td></td>
                        @endif

                        <td>${{ number_format($roomTotalNet, 0) }}</td>
                        <td>${{ number_format($roomTotalGuest, 0) }}</td>
                    </tr>

                    @php $firstRow = false; @endphp
                @endforeach

                <!-- Footer Section for each booking -->
                <tr style="border-top: 2px solid #000;">
                    <td colspan="3" style="border: 2px solid #000; font-weight: bold;">Option Date</td>
                    <td colspan="2" style="border: 2px solid #000;">
                        {{ $booking->option_date ? $booking->option_date->format('d-M-y') : '-' }}
                    </td>
                    <td colspan="3" style="border: 1px solid #000;"></td>
                    <td colspan="2" style="border: 2px solid #000; font-weight: bold;">Total Net Rate</td>
                    <td style="border: 2px solid #000; font-weight: bold;">
                        ${{ number_format($data['totalNetRate'], 0) }}
                    </td>
                    <td colspan="3" style="border: 1px solid #000;"></td>
                    <td colspan="3" style="border: 2px solid #000; font-weight: bold;">Total Guest Rate</td>
                    <td style="border: 2px solid #000; font-weight: bold;">
                        ${{ number_format($data['totalGuestRate'], 0) }}
                    </td>
                    <td colspan="4" style="border: 1px solid #000;"></td>
                </tr>

                <tr>
                    <td colspan="3" style="border: 2px solid #000; font-weight: bold;">Remaining Days</td>
                    <td colspan="2" style="border: 2px solid #000;">
                        @php
                            $remainingDays = $booking->option_date
                                ? max(0, floor(now()->diffInDays($booking->option_date, false)))
                                : 0;
                        @endphp
                        {{ $remainingDays > 0 ? $remainingDays . ' Days' : '-' }}
                    </td>
                    <td colspan="3" style="border: 1px solid #000;"></td>
                    <td colspan="2" style="border: 2px solid #000; font-weight: bold;">Paid Amount</td>
                    <td style="border: 2px solid #000;">
                        ${{ number_format($booking->paid_amount, 0) }}
                    </td>
                    <td colspan="3" style="border: 1px solid #000;"></td>
                    <td colspan="3" style="border: 2px solid #000; font-weight: bold;">Remaining Amount</td>
                    <td style="border: 2px solid #000;">
                        ${{ number_format($booking->total_amount - $booking->paid_amount, 0) }}
                    </td>
                    <td colspan="4" style="border: 1px solid #000;"></td>
                </tr>

                <!-- Empty row between bookings -->
                @if ($bookingIndex < count($bookingsData) - 1)
                    <tr class="bg-gray">
                        <td colspan="22" style="border:1px solid #000; height: 20px;"></td>
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>

</body>

</html>

