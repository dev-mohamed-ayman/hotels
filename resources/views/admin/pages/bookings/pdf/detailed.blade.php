<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Detailed Export') }} - {{ $booking->code }}</title>
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
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
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

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #fff;">{{ __('File Code') }}</th>
                <th rowspan="2" style="background-color: #fff;">{{ __('Hotel Name') }}</th>
                <th rowspan="2">{{ __('Meals Plan') }}</th>
                <th rowspan="2">{{ __('Chk In Date') }}</th>
                <th rowspan="2">{{ __('Chk out Date') }}</th>
                <th rowspan="2" class="text-red">{{ __('Nights') }}<br>({{ __('Dynamic') }})</th>
                <th rowspan="2" class="header-green">{{ __('Rooms') }}<br>{{ __('Qty.') }}</th>
                <th rowspan="2" class="header-green">{{ __('Room') }}<br>{{ __('Type') }}</th>
                <th rowspan="2" class="header-green">{{ __('Category') }}</th>
                <th rowspan="2" class="header-green">{{ __('Net Rate') }}</th>
                <th rowspan="2" class="header-green">{{ __('Margin') }} $</th>
                <th rowspan="2" class="header-green">{{ __('Guest Rate') }}<br>({{ __('Dynamic') }})</th>
                <th rowspan="2" class="header-dark-green">{{ __('Total Net Rate') }}<br>({{ __('Dynamic') }})</th>
                <th rowspan="2" class="header-dark-green">{{ __('Total Guest Rate') }}<br>({{ __('Dynamic') }})
                </th>
            </tr>
            <tr>
                <th class="header-green">{{ __('CHD') }}<br>{{ __('Qty.') }}</th>
                <th class="header-green">{{ __('CHD Net Rate') }}</th>
                <th class="header-green">{{ __('CHD Margin') }}</th>
                <th class="header-green">{{ __('CHD Guest Rate') }}<br>({{ __('Dynamic') }})</th>

                <th class="header-red">{{ __('Net') }}<br>{{ __('Extras') }}</th>
                <th class="header-dark-green">{{ __('Guest') }}<br>{{ __('Extras') }}</th>

                <th class="header-red">{{ __('Net') }}<br>{{ __('Reducts') }}</th>
                <th class="header-dark-green">{{ __('Guest') }}<br>{{ __('Reducts') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $firstRow = true;
                $netExtras = $booking->adjustments->where('type', 'addition')->sum('net_rate');
                $guestExtras = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
                $netReducts = $booking->adjustments->where('type', 'discount')->sum('net_rate');
                $guestReducts = $booking->adjustments->where('type', 'discount')->sum('guest_rate');
            @endphp

            @php
                // Calculate totals for ALL rooms
                $totalNetRate = 0;
                $totalGuestRate = 0;
                foreach ($booking->rooms as $r) {
                    $totalNetRate += $r->price * $r->room_count * $booking->nights;
                    $totalNetRate += ($r->child_price ?? 0) * ($r->child_count ?? 0) * $booking->nights;
                    $totalGuestRate += ($r->price + $r->margin) * $r->room_count * $booking->nights;
                    $totalGuestRate +=
                        (($r->child_price ?? 0) + ($r->child_margin ?? 0)) * ($r->child_count ?? 0) * $booking->nights;
                }
                $totalNetRate += $netExtras - $netReducts;
                $totalGuestRate += $guestExtras - $guestReducts;
            @endphp

            @foreach ($booking->rooms as $room)
                @php
                    $roomNetRate = $room->price; // Per night
                    $roomMargin = $room->margin; // Per night
                    $roomGuestRate = $roomNetRate + $roomMargin; // Per night

                    $childQty = $room->child_count ?? 0;
                    $childNetRate = $room->child_price ?? 0; // Per night
                    $childMargin = $room->child_margin ?? 0; // Per night
                    $childGuestRate = $childNetRate + $childMargin; // Per night
                @endphp
                <tr>
                    <td class="text-red" style="border: 1px solid #000;"><strong>{{ $booking->code }}</strong></td>
                    <td style="border: 1px solid #000;"><strong>{{ $booking->hotel->name }}</strong></td>
                    <td>{{ $booking->meals_plan ?? '-' }}</td>
                    <td>{{ $booking->check_in->format('d-m-Y') }}</td>
                    <td>{{ $booking->check_out->format('d-m-Y') }}</td>
                    <td>{{ $booking->nights }} nights</td>
                    <td>{{ $room->room_count }}</td>
                    <td>{{ $room->room_type }}</td>
                    <td>{{ $room->category ?? '-' }}</td>
                    <td>{{ $roomNetRate == 0 ? '' : $booking->currency->symbol . number_format($roomNetRate, 0) }}</td>
                    <td>{{ $roomMargin == 0 ? '' : $booking->currency->symbol . number_format($roomMargin, 0) }}</td>
                    <td>{{ $roomGuestRate == 0 ? '' : $booking->currency->symbol . number_format($roomGuestRate, 0) }}
                    </td>

                    @if ($childQty > 0)
                        <td>{{ $childQty }}</td>
                        <td>{{ $childNetRate == 0 ? '' : $booking->currency->symbol . number_format($childNetRate, 0) }}
                        </td>
                        <td>{{ $childMargin == 0 ? '' : $booking->currency->symbol . number_format($childMargin, 0) }}
                        </td>
                        <td>{{ $childGuestRate == 0 ? '' : $booking->currency->symbol . number_format($childGuestRate, 0) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif

                    @if ($firstRow)
                        <td>{{ $netExtras == 0 ? '' : $booking->currency->symbol . number_format($netExtras, 0) }}</td>
                        <td>{{ $guestExtras == 0 ? '' : $booking->currency->symbol . number_format($guestExtras, 0) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                    @endif

                    @if ($firstRow)
                        <td>{{ $netReducts == 0 ? '' : $booking->currency->symbol . number_format($netReducts, 0) }}
                        </td>
                        <td>{{ $guestReducts == 0 ? '' : $booking->currency->symbol . number_format($guestReducts, 0) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                    @endif

                    @if ($firstRow)
                        <td>{{ $totalNetRate == 0 ? '' : $booking->currency->symbol . number_format($totalNetRate, 0) }}
                        </td>
                        <td>{{ $totalGuestRate == 0 ? '' : $booking->currency->symbol . number_format($totalGuestRate, 0) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                </tr>

                @php $firstRow = false; @endphp
            @endforeach

            <!-- Empty row filling -->
            <tr class="bg-gray">
                <td colspan="22" style="border:1px solid #000; height: 20px;"></td>
            </tr>

            <!-- Footer Section -->
            <tr style="border-top: 2px solid #000;">

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">{{ __('Option Date') }}</td>
                <td colspan="2" style="border: 2px solid #000;">
                    {{ $booking->option_date ? $booking->option_date->format('d-m-Y') : '-' }}
                </td>

                <td colspan="3" style="border: 1px solid #000;"></td>

                <td colspan="2" style="border: 2px solid #000; font-weight: bold;">{{ __('Total Net Rate') }}</td>
                <td style="border: 2px solid #000; font-weight: bold;">
                    {{ $totalNetRate == 0 ? '' : $booking->currency->symbol . number_format($totalNetRate, 0) }}
                </td>

                <td colspan="3" style="border: 1px solid #000;"></td>

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">{{ __('Total Guest Rate') }}</td>
                <td style="border: 2px solid #000; font-weight: bold;">
                    {{ $totalGuestRate == 0 ? '' : $booking->currency->symbol . number_format($totalGuestRate, 0) }}
                </td>
                <td colspan="4" style="border: 1px solid #000;"></td>
            </tr>

            <tr>

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">{{ __('Remaining Days') }}</td>
                <td colspan="2" style="border: 2px solid #000;">
                    @php
                        $remainingDays = $booking->option_date
                            ? max(0, now()->diffInDays($booking->option_date, false))
                            : 0;
                    @endphp
                    {{ $remainingDays > 0 ? $remainingDays . ' ' . __('Days') : '-' }}
                </td>

                <td colspan="3" style="border: 1px solid #000;"></td>

                <td colspan="2" style="border: 2px solid #000; font-weight: bold;">{{ __('Paid Amount') }}</td>
                <td style="border: 2px solid #000;">
                    {{ $booking->paid_amount == 0 ? '' : $booking->currency->symbol . number_format($booking->paid_amount, 0) }}
                </td>

                <td colspan="3" style="border: 1px solid #000;"></td>

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">{{ __('Remaining Amount') }}</td>
                <td style="border: 2px solid #000;">
                    {{ $booking->total_amount - $booking->paid_amount == 0 ? '' : $booking->currency->symbol . number_format($booking->total_amount - $booking->paid_amount, 0) }}
                </td>
                <td colspan="4" style="border: 1px solid #000;"></td>
            </tr>

        </tbody>
    </table>

</body>

</html>
