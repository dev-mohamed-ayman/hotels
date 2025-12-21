<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Net Rate Export') }} - {{ $booking->code }}</title>
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
                <th style="background-color: #fff;">File Code</th>
                <th style="background-color: #fff;">Hotel Name</th>
                <th>Meals Plan</th>
                <th>Chk In Date</th>
                <th>Chk out Date</th>
                <th class="text-red">Nights<br>(Dynamic)</th>
                <th class="header-green">Rooms<br>Qty.</th>
                <th class="header-green">Room<br>Type</th>
                <th class="header-green">Category</th>
                <th class="header-green">Net Rate</th>
                <th class="header-dark-green">Total Net Rate<br>(Dynamic)</th>
                <th class="header-green">CHD<br>Qty.</th>
                <th class="header-green">CHD Rate<br>(Dynamic)</th>



                <th class="header-red">Net<br>Extras</th>

                <th class="header-red">Net<br>Reducts</th>
            </tr>
        </thead>
        <tbody>
            @php
                $firstRow = true;
                $netExtras = $booking->adjustments->where('type', 'addition')->sum('net_rate');
                $netReducts = $booking->adjustments->where('type', 'discount')->sum('net_rate');
            @endphp

            @php
                // Calculate total for ALL rooms
                $totalNetRate = 0;
                foreach ($booking->rooms as $r) {
                    $totalNetRate += $r->price * $r->room_count * $booking->nights;
                    $totalNetRate += ($r->child_price ?? 0) * ($r->child_count ?? 0) * $booking->nights;
                }
                $totalNetRate += $netExtras - $netReducts;
            @endphp

            @foreach ($booking->rooms as $room)
                @php
                    $roomNetRate = $room->price;
                    $childQty = $room->child_count ?? 0;
                    $childNetRate = $room->child_price ?? 0;
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

                    @if ($childQty > 0)
                        <td>{{ $childQty }}</td>
                        <td>{{ $childNetRate == 0 ? '' : $booking->currency->symbol . number_format($childNetRate, 0) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                    @endif

                    @if ($firstRow)
                        <td>{{ $netExtras == 0 ? '' : $booking->currency->symbol . number_format($netExtras, 0) }}</td>
                        <td>{{ $netReducts == 0 ? '' : $booking->currency->symbol . number_format($netReducts, 0) }}
                        </td>
                        <td>{{ $totalNetRate == 0 ? '' : $booking->currency->symbol . number_format($totalNetRate, 0) }}
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

                <td colspan="3" style="border: 2px solid #000; font-weight: bold;">Option Date</td>
                <td colspan="2" style="border: 2px solid #000;">
                    {{ $booking->option_date ? $booking->option_date->format('d-m-Y') : '-' }}
                </td>

                <td colspan="4" style="border: 1px solid #000;"></td>

                <td colspan="2" style="border: 2px solid #000; font-weight: bold;">Total Net Rate</td>
                <td style="border: 2px solid #000; font-weight: bold;">
                    {{ $totalNetRate == 0 ? '' : $booking->currency->symbol . number_format($totalNetRate, 0) }}</td>
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
                    {{ $booking->paid_amount == 0 ? '' : $booking->currency->symbol . number_format($booking->paid_amount, 0) }}
                </td>
                <td colspan="3" style="border: 1px solid #000;"></td>
            </tr>

        </tbody>
    </table>

</body>

</html>
