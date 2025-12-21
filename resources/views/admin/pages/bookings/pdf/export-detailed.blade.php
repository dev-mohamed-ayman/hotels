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
        }

        .nowrap {
            white-space: nowrap;
        }

        th {
            background-color: #fce4d6;
            font-weight: bold;
            vertical-align: middle;
        }

        .bg-light-blush {
            background-color: #fbe5d6;
        }

        .header-green {
            background-color: #e2efda;
        }

        .bg-light-green {
            background-color: #e2f0d9;
        }

        .bg-light-blue {
            background-color: #bdd7ee;
        }

        .bg-red {
            background-color: #c00000;
            color: white;
        }

        .bg-dark-green {
            background-color: #385724;
            color: white;
        }

        .bg-light-green-total {
            background-color: #a9d18e;
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
        <strong>{{ __('Total Bookings') }}: {{ count($bookings) }}</strong>
    </div>
    {{-- @dd($bookings) --}}

    <table>
        <thead>
            <tr class="border">
                <th class="bg-light-blush nowrap">@lang('File Code')</th>
                <th class="bg-light-blush nowrap">@lang('Hotel Name')</th>
                <th class="bg-light-blush">@lang('Meals Plan')</th>
                <th class="bg-light-blush nowrap">@lang('Check In Date')</th>
                <th class="bg-light-blush nowrap">@lang('Check Out Date')</th>
                <th class="bg-light-blush nowrap">@lang('Nights')</th>
                <th class="bg-light-green">@lang('Rooms Qty')</th>
                <th class="bg-light-green">@lang('Roome Type')</th>
                <th class="bg-light-green nowrap">@lang('Category')</th>
                <th class="bg-light-green nowrap">@lang('Net Rate')</th>
                <th class="bg-light-green">@lang('Margin')</th>
                <th class="bg-light-green">@lang('Guest Rate')</th>
                <th class="bg-light-blue">@lang('CHD Qty')</th>
                <th class="bg-light-blue">@lang('CHD Rate')</th>
                <th class="bg-light-blue">@lang('CHD Margin')</th>
                <th class="bg-light-blue">@lang('CHD Guest Rate')</th>
                <th class="bg-red">@lang('Net Extras')</th>
                <th class="bg-red">@lang('Net Reducts')</th>
                <th class="bg-dark-green">@lang('Guest Extra')</th>
                <th class="bg-dark-green">@lang('Guest Reducts')</th>
                <th class="bg-light-green-total">@lang('Total Net Rate')</th>
                <th class="bg-light-green-total">@lang('Total Guest Rate')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                @foreach ($booking->rooms as $room)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ count($booking->rooms) }}">{{ $booking->code }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ $booking->hotel->name }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ $booking->meals_plan }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ $booking->check_in->format('d-M-y') }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ $booking->check_out->format('d-M-y') }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ $booking->nights }}</td>
                        @endif
                        <td>{{ $room->room_count }}</td>
                        <td>{{ $room->room_type }}</td>
                        <td>{{ $room->category }}</td>
                        <td>{{ number_format($room->price) }}</td>
                        <td>{{ number_format($room->margin) }}</td>
                        <td>{{ number_format($room->price + $room->margin) }}</td>
                        <td>{{ $room->child_count }}</td>
                        <td>{{ number_format($room->child_price) }}</td>
                        <td>{{ number_format($room->child_margin) }}</td>
                        <td>{{ number_format($room->child_price + $room->child_margin) }}</td>
                        @php
                            $netExtras = $booking->adjustments->where('type', 'addition')->sum('net_rate');
                            $netReducts = $booking->adjustments->where('type', 'discount')->sum('net_rate');
                            $guestExtras = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
                            $guestReducts = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

                            // Calculate totals for ALL rooms in the booking
                            $totalNetRate = 0;
                            $totalGuestRate = 0;
                            foreach ($booking->rooms as $r) {
                                $totalNetRate += $r->price * $r->room_count * $booking->nights;
                                $totalNetRate += $r->child_price * $r->child_count * $booking->nights;
                                $totalGuestRate += ($r->price + $r->margin) * $r->room_count * $booking->nights;
                                $totalGuestRate +=
                                    ($r->child_price + $r->child_margin) * $r->child_count * $booking->nights;
                            }
                            $totalNetRate += $netExtras - $netReducts;
                            $totalGuestRate += $guestExtras - $guestReducts;
                        @endphp
                        @if ($loop->first)
                            <td rowspan="{{ count($booking->rooms) }}">{{ number_format($netExtras) }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ number_format($netReducts) }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ number_format($guestExtras) }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ number_format($guestReducts) }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ number_format($totalNetRate) }}</td>
                            <td rowspan="{{ count($booking->rooms) }}">{{ number_format($totalGuestRate) }}</td>
                        @endif
                    </tr>
                @endforeach
                <tr class="bg-gray">
                    <td colspan="22"></td>
                </tr>
            @endforeach

        </tbody>
    </table>

</body>

</html>
