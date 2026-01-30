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
            font-family: 'Aptos', sans-serif !important;
            font-size: 12pt !important;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
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

        .total-row {
            background-color: #d9e1f2;
            font-weight: bold;
        }

        .total-label {
            background-color: #d9e1f2;
            font-weight: bold;
            text-align: right;
        }

        thead tr th {
            height: 85px !important;
        }
    </style>
</head>

<body>

    <div style="margin-bottom: 15px; text-align: center; font-size: 12pt;">
        <strong>{{ __('Total Bookings') }}: {{ count($bookings) }}</strong>
    </div>
    {{-- @dd($bookings) --}}

    <table>
        <thead>
            <tr class="border">
                <th class="bg-light-blush nowrap" style="width: 70px">@lang('File Code')</th>
                <th class="bg-light-blush nowrap" style="width: 200px">@lang('Hotel Name')</th>
                <th class="bg-light-blush" style="width: 50px">@lang('Meals Plan')</th>
                <th class="bg-light-blush nowrap" style="width: 90px">@lang('Check In Date')</th>
                <th class="bg-light-blush nowrap" style="width: 90px">@lang('Check Out Date')</th>
                <th class="bg-light-blush nowrap" style="width: 70px">@lang('Nights')</th>
                <th class="bg-light-green" style="width: 60px">@lang('Rooms Qty')</th>
                <th class="bg-light-green" style="width: 60px">@lang('Room Type')</th>
                <th class="bg-light-green nowrap" style="width: 200px">@lang('Category')</th>
                <th class="bg-light-green" style="width: 65px">@lang('Guest Rate')</th>
                <th class="bg-light-blue" style="width: 45px">@lang('CHD Qty')</th>
                <th class="bg-light-blue" style="width: 45px">@lang('CHD Guest Rate')</th>
                <th class="bg-dark-green" style="width: 65px">@lang('Guest Extra')</th>
                <th class="bg-dark-green" style="width: 65px">@lang('Guest Reducts')</th>
                <th class="bg-light-green-total" style="width: 95px">@lang('Total Guest Rate')</th>
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
                        <td style="height: 50px">{{ $room->room_count }}</td>
                        <td style="height: 50px">{{ $room->room_type }}</td>
                        <td style="height: 50px">{{ $room->category }}</td>
                        <td style="height: 50px">
                            <span style="font-weight: bold; font-size: 14pt;">{{ $room->currency->symbol }}</span>
                            {{ \App\Helpers\NumberHelper::format($room->price + $room->margin) }}
                        </td>
                        <td style="height: 50px">{{ $room->child_count }}</td>
                        <td style="height: 50px">
                            <span style="font-weight: bold; font-size: 14pt;">{{ $room->currency->symbol }}</span>
                            {{ \App\Helpers\NumberHelper::format($room->child_price + $room->child_margin) }}
                        </td>
                        @php
                            $netExtras = $booking->adjustments->where('type', 'addition')->sum('net_rate');
                            $netReducts = $booking->adjustments->where('type', 'discount')->sum('net_rate');
                            $guestExtras = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
                            $guestReducts = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

                            // Calculate totals for ALL rooms in the booking
                            $totalGuestRate = 0;
                            foreach ($booking->rooms as $r) {
                                $totalGuestRate += ($r->price + $r->margin) * $r->room_count * $booking->nights;
                                $totalGuestRate +=
                                    ($r->child_price + $r->child_margin) * $r->child_count * $booking->nights;
                            }
                            $totalGuestRate += $guestExtras - $guestReducts;
                        @endphp
                        @if ($loop->first)
                            <td rowspan="{{ count($booking->rooms) }}">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $room->currency->symbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($guestExtras) }}
                            </td>
                            <td rowspan="{{ count($booking->rooms) }}">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $room->currency->symbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($guestReducts) }}
                            </td>
                            <td rowspan="{{ count($booking->rooms) }}">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $room->currency->symbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($totalGuestRate) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
                <tr class="bg-gray">
                    <td colspan="15"></td>
                </tr>
            @endforeach

            @php
                // Calculate totals grouped by currency
                $currencyTotals = [];
                foreach ($bookings as $booking) {
                    $currencyId = $booking->currency_id;
                    $currencySymbol = $booking->currency->symbol ?? '';

                    if (!isset($currencyTotals[$currencyId])) {
                        $currencyTotals[$currencyId] = [
                            'symbol' => $currencySymbol,
                            'guestRate' => 0,
                            'chdGuestRate' => 0,
                            'guestExtras' => 0,
                            'guestReducts' => 0,
                            'totalGuestRate' => 0,
                        ];
                    }

                    // Calculate totals for this booking (sum all rooms)
                    foreach ($booking->rooms as $room) {
                        $currencyTotals[$currencyId]['guestRate'] +=
                            ($room->price + $room->margin) * $room->room_count * $booking->nights;
                        $currencyTotals[$currencyId]['chdGuestRate'] +=
                            ($room->child_price + $room->child_margin) * $room->child_count * $booking->nights;
                    }

                    $guestExtras = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
                    $guestReducts = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

                    $currencyTotals[$currencyId]['guestExtras'] += $guestExtras;
                    $currencyTotals[$currencyId]['guestReducts'] += $guestReducts;
                }

                // Calculate final total guest rate for each currency
                foreach ($currencyTotals as $currencyId => &$totals) {
                    $totals['totalGuestRate'] =
                        $totals['guestRate'] +
                        $totals['chdGuestRate'] +
                        $totals['guestExtras'] -
                        $totals['guestReducts'];
                }
            @endphp

            @foreach ($currencyTotals as $currencyTotal)
                <tr class="total-row">
                    <td style="height: 70px; text-align: center;" class="total-label" colspan="14">
                        {{ __('Total') }} (<span
                            style="font-weight: bold; font-size: 14pt;">{{ $currencyTotal['symbol'] }}</span>)</td>

                    <td style="height: 70px;">
                        <span style="font-weight: bold; font-size: 14pt;">{{ $currencyTotal['symbol'] }}</span>
                        {{ \App\Helpers\NumberHelper::format($currencyTotal['totalGuestRate']) }}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

</body>

</html>
