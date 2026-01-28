<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Guest Export') }} - {{ $booking->code }}</title>
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

    <table>
        <thead>
            <tr>
                <th style="background-color: #fff;">{{ __('File Code') }}</th>
                <th style="background-color: #fff;">{{ __('Hotel Name') }}</th>
                <th>{{ __('Meals Plan') }}</th>
                <th>{{ __('Chk In Date') }}</th>
                <th>{{ __('Chk out Date') }}</th>
                <th class="text-red">{{ __('Nights') }}<br>({{ __('Dynamic') }})</th>
                <th class="header-green">{{ __('Rooms') }}<br>{{ __('Qty.') }}</th>
                <th class="header-green">{{ __('Room') }}<br>{{ __('Type') }}</th>
                <th class="header-green">{{ __('Category') }}</th>
                <th class="header-green">{{ __('Guest Rate') }}<br>({{ __('Dynamic') }})</th>
                <th class="header-dark-green">{{ __('Total Guest Rate') }}<br>({{ __('Dynamic') }})</th>
                <th class="header-green">{{ __('CHD') }}<br>{{ __('Qty.') }}</th>
                <th class="header-green">{{ __('CHD Guest Rate') }}<br>({{ __('Dynamic') }})</th>

                <th class="header-dark-green">{{ __('Guest') }}<br>{{ __('Extras') }}</th>

                <th class="header-dark-green">{{ __('Guest') }}<br>{{ __('Reducts') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $firstRow = true;
                $guestExtras = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
                $guestReducts = $booking->adjustments->where('type', 'discount')->sum('guest_rate');
            @endphp

            @php
                // Calculate total for ALL rooms
                $totalGuestRate = 0;
                foreach ($booking->rooms as $r) {
                    $totalGuestRate += ($r->price + $r->margin) * $r->room_count * $booking->nights;
                    $totalGuestRate +=
                        (($r->child_price ?? 0) + ($r->child_margin ?? 0)) * ($r->child_count ?? 0) * $booking->nights;
                }
                $totalGuestRate += $guestExtras - $guestReducts;
            @endphp

            @foreach ($booking->rooms as $room)
                @php
                    $roomNetRate = $room->price;
                    $roomMargin = $room->margin;
                    $roomGuestRate = $roomNetRate + $roomMargin;

                    $childQty = $room->child_count ?? 0;
                    $childNetRate = $room->child_price ?? 0;
                    $childMargin = $room->child_margin ?? 0;
                    $childGuestRate = $childNetRate + $childMargin;
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
                    <td>{{ $roomGuestRate == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($roomGuestRate) }}
                    </td>

                    @if ($childQty > 0)
                        <td>{{ $childQty }}</td>
                        <td>{{ $childGuestRate == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($childGuestRate) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                    @endif

                    @if ($firstRow)
                        <td>{{ $guestExtras == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($guestExtras) }}
                        </td>
                        <td>{{ $guestReducts == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($guestReducts) }}
                        </td>
                        <td>{{ $totalGuestRate == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($totalGuestRate) }}
                        </td>
                    @else
                        <td></td>
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

            <tr style="border-top: 2px solid #000;">

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">{{ __('Option Date') }}</td>
                <td colspan="2" style="border: 2px solid #000;">
                    {{ $booking->option_date ? $booking->option_date->format('d-m-Y') : '-' }}
                </td>

                <td colspan="4" style="border: 1px solid #000;"></td>

                <td colspan="2" style="border: 2px solid #000; font-weight: bold;">{{ __('Total Guest Rate') }}</td>
                <td style="border: 2px solid #000; font-weight: bold;">
                    {{ $totalGuestRate == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($totalGuestRate) }}
                </td>
                <td colspan="3" style="border: 1px solid #000;"></td>
            </tr>

            <tr>

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">{{ __('Remaining Days') }}</td>
                <td colspan="2" style="border: 2px solid #000;">
                    @php
                        $remainingDays = $booking->option_date
                            ? max(0, floor(now()->diffInDays($booking->option_date, false)))
                            : 0;
                    @endphp
                    {{ $remainingDays > 0 ? $remainingDays . ' ' . __('Days') : '-' }}
                </td>

                <td colspan="4" style="border: 1px solid #000;"></td>

                <td colspan="2" style="border: 2px solid #000; font-weight: bold;">{{ __('Paid Amount') }}</td>
                <td style="border: 2px solid #000;">
                    {{ $booking->paid_amount == 0 ? '' : $booking->currency->symbol . \App\Helpers\NumberHelper::format($booking->paid_amount) }}
                </td>
                <td colspan="3" style="border: 1px solid #000;"></td>
            </tr>

        </tbody>
    </table>

</body>

</html>
