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
                <th class="bg-light-blush nowrap" style="width: 77px">@lang('File Code')</th>
                <th class="bg-light-blush nowrap" style="width: 182px">@lang('Hotel Name')</th>
                <th class="bg-light-blush" style="width: 50px">@lang('Meals Plan')</th>
                <th class="bg-light-blush nowrap" style="width: 90px">@lang('Check In Date')</th>
                <th class="bg-light-blush nowrap" style="width: 90px">@lang('Check Out Date')</th>
                <th class="bg-light-blush nowrap" style="width: 70px">@lang('Nights')</th>
                <th class="bg-light-green" style="width: 50px">@lang('Rooms Qty')</th>
                <th class="bg-light-green" style="width: 50px">@lang('Room Type')</th>
                <th class="bg-light-green nowrap" style="width: 182px">@lang('Category')</th>
                <th class="bg-light-green nowrap" style="width: 65px">@lang('Net Rate')</th>
                <th class="bg-light-green" style="width: 55px">@lang('Margin')</th>
                <th class="bg-light-green" style="width: 60px">@lang('Guest Rate')</th>
                <th class="bg-light-blue" style="width: 40px">@lang('CHD Qty')</th>
                <th class="bg-light-blue" style="width: 40px">@lang('CHD Rate')</th>
                <th class="bg-light-blue" style="width: 55px">@lang('CHD Margin')</th>
                <th class="bg-light-blue" style="width: 55px">@lang('CHD Guest Rate')</th>
                <th class="bg-red" style="width: 65px">@lang('Net Extras')</th>
                <th class="bg-red" style="width: 65px">@lang('Net Reducts')</th>
                <th class="bg-dark-green" style="width: 65px">@lang('Guest Extra')</th>
                <th class="bg-dark-green" style="width: 65px">@lang('Guest Reducts')</th>
                <th class="bg-light-green-total" style="width: 85px">@lang('Total Net Rate')</th>
                <th class="bg-light-green-total" style="width: 85px">@lang('Total Guest Rate')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                @php
                    $rooms = $booking->rooms;
                    $additions = $booking->adjustments->where('type', 'addition')->values();
                    $discounts = $booking->adjustments->where('type', 'discount')->values();
                    $rowCount = max($rooms->count(), $additions->count(), $discounts->count());
                    if ($rowCount == 0) {
                        $rowCount = 1;
                    }
                    $currencySymbol = $booking->currency->symbol ?? '';
                @endphp

                @for ($i = 0; $i < $rowCount; $i++)
                    <tr>
                        @if ($i == 0)
                            <td rowspan="{{ $rowCount }}">{{ $booking->code }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $booking->hotel->name }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $booking->meals_plan }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $booking->check_in->format('d-M-y') }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $booking->check_out->format('d-M-y') }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $booking->nights }}</td>
                        @endif

                        {{-- Room Details --}}
                        @if (isset($rooms[$i]))
                            @php $room = $rooms[$i]; @endphp
                            <td style="height: 50px">{{ $room->room_count }}</td>
                            <td style="height: 50px">{{ $room->room_type }}</td>
                            <td style="height: 50px">{{ $room->category }}</td>
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($room->price) }}
                            </td>
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($room->margin) }}
                            </td>
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($room->price + $room->margin) }}
                            </td>
                            <td style="height: 50px">{{ $room->child_count }}</td>
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($room->child_price) }}
                            </td>
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($room->child_margin) }}
                            </td>
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($room->child_price + $room->child_margin) }}
                            </td>
                        @else
                            {{-- Empty Room Cells --}}
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif

                        {{-- Extras and Reducts --}}
                        @php
                            $netExtra = 0;
                            $guestExtra = 0;
                            $netReduct = 0;
                            $guestReduct = 0;
                        @endphp

                        {{-- Additions --}}
                        @if (isset($additions[$i]))
                            @php
                                $add = $additions[$i];
                                $netExtra = $add->net_rate;
                                $guestExtra = $add->guest_rate;
                            @endphp
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($netExtra) }}
                            </td>
                        @else
                            <td></td>
                        @endif

                        {{-- Discounts --}}
                        @if (isset($discounts[$i]))
                            @php
                                $disc = $discounts[$i];
                                $netReduct = $disc->net_rate;
                                $guestReduct = $disc->guest_rate;
                            @endphp
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($netReduct) }}
                            </td>
                        @else
                            <td></td>
                        @endif

                        {{-- Guest Extra (Column 19) --}}
                        @if (isset($additions[$i]))
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($guestExtra) }}
                            </td>
                        @else
                            <td></td>
                        @endif

                        {{-- Guest Reducts (Column 20) --}}
                        @if (isset($discounts[$i]))
                            <td style="height: 50px">
                                <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                                {{ \App\Helpers\NumberHelper::format($guestReduct) }}
                            </td>
                        @else
                            <td></td>
                        @endif

                        {{-- Row Totals --}}
                        @php
                            $rowNetTotal = 0;
                            $rowGuestTotal = 0;

                            // Add Room totals
                            if (isset($rooms[$i])) {
                                $r = $rooms[$i];
                                $rowNetTotal += $r->price * $r->room_count * $booking->nights;
                                $rowNetTotal += $r->child_price * $r->child_count * $booking->nights;
                                $rowGuestTotal += ($r->price + $r->margin) * $r->room_count * $booking->nights;
                                $rowGuestTotal +=
                                    ($r->child_price + $r->child_margin) * $r->child_count * $booking->nights;
                            }

                            // Add Adjustments
                            $rowNetTotal += $netExtra - $netReduct;
                            $rowGuestTotal += $guestExtra - $guestReduct;
                        @endphp

                        <td style="height: 50px">
                            <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                            {{ \App\Helpers\NumberHelper::format($rowNetTotal) }}
                        </td>
                        <td style="height: 50px">
                            <span style="font-weight: bold; font-size: 14pt;">{{ $currencySymbol }}</span>
                            {{ \App\Helpers\NumberHelper::format($rowGuestTotal) }}
                        </td>
                    </tr>
                @endfor
                <tr class="bg-gray">
                    <td colspan="22"></td>
                </tr>
            @endforeach

            @php
                // Calculate totals grouped by currency for all numeric columns
                $currencyTotals = [];
                foreach ($bookings as $booking) {
                    $currencyId = $booking->currency_id;
                    $currencySymbol = $booking->currency->symbol ?? '';

                    if (!isset($currencyTotals[$currencyId])) {
                        $currencyTotals[$currencyId] = [
                            'symbol' => $currencySymbol,
                            'netRate' => 0,
                            'margin' => 0,
                            'guestRate' => 0,
                            'chdRate' => 0,
                            'chdMargin' => 0,
                            'chdGuestRate' => 0,
                            'netExtras' => 0,
                            'netReducts' => 0,
                            'guestExtras' => 0,
                            'guestReducts' => 0,
                            'totalNetRate' => 0,
                            'totalGuestRate' => 0,
                        ];
                    }

                    // Calculate totals for this booking (sum all rooms)
                    foreach ($booking->rooms as $room) {
                        $currencyTotals[$currencyId]['netRate'] += $room->price * $room->room_count * $booking->nights;
                        $currencyTotals[$currencyId]['margin'] += $room->margin * $room->room_count * $booking->nights;
                        $currencyTotals[$currencyId]['guestRate'] +=
                            ($room->price + $room->margin) * $room->room_count * $booking->nights;
                        $currencyTotals[$currencyId]['chdRate'] +=
                            $room->child_price * $room->child_count * $booking->nights;
                        $currencyTotals[$currencyId]['chdMargin'] +=
                            $room->child_margin * $room->child_count * $booking->nights;
                        $currencyTotals[$currencyId]['chdGuestRate'] +=
                            ($room->child_price + $room->child_margin) * $room->child_count * $booking->nights;
                    }

                    $netExtras = $booking->adjustments->where('type', 'addition')->sum('net_rate');
                    $netReducts = $booking->adjustments->where('type', 'discount')->sum('net_rate');
                    $guestExtras = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
                    $guestReducts = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

                    $currencyTotals[$currencyId]['netExtras'] += $netExtras;
                    $currencyTotals[$currencyId]['netReducts'] += $netReducts;
                    $currencyTotals[$currencyId]['guestExtras'] += $guestExtras;
                    $currencyTotals[$currencyId]['guestReducts'] += $guestReducts;
                }

                // Calculate final total net rate and total guest rate for each currency
                foreach ($currencyTotals as $currencyId => &$totals) {
                    $totals['totalNetRate'] =
                        $totals['netRate'] + $totals['chdRate'] + $totals['netExtras'] - $totals['netReducts'];
                    $totals['totalGuestRate'] =
                        $totals['guestRate'] +
                        $totals['chdGuestRate'] +
                        $totals['guestExtras'] -
                        $totals['guestReducts'];
                }
            @endphp

            @foreach ($currencyTotals as $currencyTotal)
                <tr class="total-row">
                    <td style="height: 70px; text-align: center;" class="total-label" colspan="20">
                        {{ __('Total') }}
                        (<span style="font-weight: bold; font-size: 14pt;">{{ $currencyTotal['symbol'] }}</span>)
                    </td>
                    <td style="height: 70px;">
                        <span style="font-weight: bold; font-size: 14pt;">{{ $currencyTotal['symbol'] }}</span>
                        {{ \App\Helpers\NumberHelper::format($currencyTotal['totalNetRate']) }}
                    </td>
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
