<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Bank Export') }}</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: Montserrat, "Neue Frutiger World", Cairo, Tajawal, "DejaVu Sans Condensed", sans-serif;
            font-size: 12px;
        }

        /* Azha brand palette (Contract 5): Navy #12214c headers, Navy-subtle row
           bands #f0f1f5/#e0e3eb, Gold #af934e totals. mPDF renders this inline
           <style> block; it never loads the web azha-brand.css. If Montserrat /
           Cairo are registered in the mPDF font directory they are used,
           otherwise mPDF falls back to its built-in fonts (graceful degradation). */
        html[dir="rtl"] body {
            direction: rtl;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-img {
            width: 150px;
            height: auto;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background-color: #d4b876;
            color: #000000;
            padding: 12px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #fff;
            text-wrap: nowrap !important;
        }

        td {
            padding: 12px 4px;
            text-align: center;
            background-color: #f0f1f5;
            border: 1px solid #fff;
            color: #000;
        }

        tbody tr:nth-child(even) td {
            background-color: #e0e3eb;
        }

        .empty-row td {
            height: 30px;
            background-color: #e0e3eb;
            border: 1px solid #fff;
        }

        tfoot tr.total-row td {
            background-color: #af934e !important;
            color: white !important;
            font-weight: bold;
            padding: 15px 7px;
        }
    </style>
</head>

<body>

    @include('admin.pdf.partials.header', [
        'headerTitle' => __('Bank Export'),
        'headerSubtitle' => __('Total Bookings') . ': ' . ($totalBookingsCount ?? count($bookingsData))
    ])

    <table>
        <thead>
            <tr style="text-wrap: nowrap">
                <th>No.</th>
                <th style="text-wrap: nowrap;">File Code</th>
                <th style="text-wrap: nowrap;">Hotel Name</th>
                <th style="text-wrap: nowrap;">Company Name</th>
                <th class="nowrap" style="width: 130px">Bank Name</th>
                <th style="text-wrap: nowrap;">Bank Account</th>
                <th style="text-wrap: nowrap;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Group the rows by File Code so the same code is printed once
                // and merged (rowspan) across all of its rows.
                $rowsByFileCode = collect($bookingsData)->groupBy(fn($data) => $data['booking']->code);
                $rowNumber = 0;
            @endphp
            @foreach ($rowsByFileCode as $fileCodeRows)
                @foreach ($fileCodeRows as $data)
                    @php $rowNumber++; @endphp
                <tr>
                    <td>{{ $rowNumber }}</td>
                    @if ($loop->first)
                        <td rowspan="{{ count($fileCodeRows) }}">{{ $data['booking']->code }}</td>
                    @endif
                    <td>{{ $data['booking']->hotel->name }}</td>
                    <td>{{ $data['booking']->hotel->company_name ?? '-' }}</td>
                    <td>{{ $data['bank_account'] ? $data['bank_account']->bank_name : '-' }}</td>
                    <td>{{ $data['bank_account'] ? $data['bank_account']->account_number : '-' }}</td>
                    <td>
                        <span style="font-weight: bold;">
                            {{ $data['booking']->currency->symbol }}
                        </span>
                        {{ \App\Helpers\NumberHelper::format($data['booking']->hotel_paid_amount >= $data['booking']->net_amount ? 0 : $data['booking']->net_amount - $data['booking']->hotel_paid_amount) }}
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        @php
            $currencyTotals = [];
            foreach ($bookingsData as $data) {
                $booking = $data['booking'];
                $currencyId = $booking->currency_id;
                $currencySymbol = $booking->currency->symbol;

                if (!isset($currencyTotals[$currencyId])) {
                    $currencyTotals[$currencyId] = [
                        'symbol' => $currencySymbol,
                        'amount' => 0,
                    ];
                }
                $total =
                    $booking->hotel_paid_amount >= $booking->net_amount
                        ? 0
                        : $booking->net_amount - $booking->hotel_paid_amount;
                //            $currencyTotals[$currencyId]['amount'] += $data['total'];
                $currencyTotals[$currencyId]['amount'] += $total;
            }
        @endphp
        <tfoot>
            @foreach ($currencyTotals as $currencyTotal)
                <tr class="total-row">
                    <td colspan="6" style="text-align: center;">{{ __('Total') }}
                        ({{ $currencyTotal['symbol'] }})
                    </td>
                    <td>
                        {{ $currencyTotal['symbol'] }}{{ \App\Helpers\NumberHelper::format($currencyTotal['amount']) }}
                    </td>
                </tr>
            @endforeach
        </tfoot>
    </table>

</body>

</html>
