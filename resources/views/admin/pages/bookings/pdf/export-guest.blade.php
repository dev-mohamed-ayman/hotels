<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Guest Export') }}</title>
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
    </style>
</head>

<body>

    <div style="margin-bottom: 15px; text-align: center; font-size: 9pt;">
        <strong>{{ __('Total Bookings') }}: {{ $totalBookingsCount ?? count($bookingsData) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="background-color: #fff;">File Code</th>
                <th style="background-color: #fff;">Hotel Name</th>
                <th>Meals Plan</th>
                <th>Chk In Date</th>
                <th>Chk out Date</th>
                <th class="text-red">Nights<br>(Dynamic)</th>
                <th class="header-green">Rooms<br>Qty.</th>
                <th class="header-green">Room<br>Type</th>
                <th class="header-green">Category</th>
                <th class="header-green">Guest Rate<br>(Dynamic)</th>
                <th class="header-dark-green">Total Guest Rate<br>(Dynamic)</th>
                <th class="header-green">CHD<br>Qty.</th>
                <th class="header-green">CHD Guest Rate<br>(Dynamic)</th>
                <th class="header-dark-green">Guest<br>Extras</th>
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
                            $roomGuestRatePerNight = $room['guest_rate'] / ($room['room_count'] * $booking->nights);
                            
                            // Calculate room total for this row
                            $roomTotalGuest = $room['guest_rate'];
                            if ($room['child_count'] > 0) {
                                $roomTotalGuest += $room['child_guest_rate'];
                            }
                            if ($firstRow) {
                                $roomTotalGuest += $data['additionsGuestTotal'];
                                $roomTotalGuest -= $data['discountsGuestTotal'];
                            }
                        @endphp
                        <td>${{ number_format($roomGuestRatePerNight, 0) }}</td>

                        @if ($room['child_count'] > 0)
                            <td>{{ $room['child_count'] }}</td>
                            @php
                                $childGuestRatePerNight = $room['child_guest_rate'] / ($room['child_count'] * $booking->nights);
                            @endphp
                            <td>${{ number_format($childGuestRatePerNight, 0) }}</td>
                        @else
                            <td></td>
                            <td></td>
                        @endif

                        @if ($firstRow)
                            <td>${{ number_format($data['additionsGuestTotal'], 0) }}</td>
                        @else
                            <td></td>
                        @endif

                        @if ($firstRow)
                            <td>${{ number_format($data['discountsGuestTotal'], 0) }}</td>
                        @else
                            <td></td>
                        @endif

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
                    <td colspan="4" style="border: 1px solid #000;"></td>
                    <td colspan="2" style="border: 2px solid #000; font-weight: bold;">Total Guest Rate</td>
                    <td style="border: 2px solid #000; font-weight: bold;">${{ number_format($data['totalGuestRate'], 0) }}</td>
                    <td colspan="3" style="border: 1px solid #000;"></td>
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
                    <td colspan="4" style="border: 1px solid #000;"></td>
                    <td colspan="2" style="border: 2px solid #000; font-weight: bold;">Paid Amount</td>
                    <td style="border: 2px solid #000;">
                        ${{ number_format($booking->paid_amount, 0) }}
                    </td>
                    <td colspan="3" style="border: 1px solid #000;"></td>
                </tr>

                <!-- Empty row between bookings -->
                @if ($bookingIndex < count($bookingsData) - 1)
                    <tr class="bg-gray">
                        <td colspan="15" style="border:1px solid #000; height: 20px;"></td>
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>

</body>

</html>

