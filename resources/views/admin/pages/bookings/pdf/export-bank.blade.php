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
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
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
            font-size: 9pt;
        }

        th {
            background-color: #0d3c47;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #fff;
        }

        td {
            padding: 12px 10px;
            text-align: center;
            background-color: #eef5fa;
            border: 1px solid #fff;
            color: #000;
        }

        tbody tr:nth-child(even) td {
            background-color: #dae8f2;
        }

        .empty-row td {
            height: 30px;
            background-color: #dae8f2;
            border: 1px solid #fff;
        }

        .total-row td {
            background-color: #0d3c47;
            color: white;
            font-weight: bold;
            padding: 15px 10px;
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="{{ public_path('./472228932_903900521859408_2733195805942687837_n.jpg') }}" alt="AZHA Travel Logo"
            class="logo-img" />
    </div>

    <div style="margin-bottom: 15px; text-align: center;">
        <strong>{{ __('Total Bookings') }}: {{ $totalBookingsCount ?? count($bookingsData) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>File Code</th>
                <th>Hotel Name</th>
                <th>Bank Name</th>
                <th>Bank Account</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookingsData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['booking']->code }}</td>
                    <td>{{ $data['booking']->hotel->name }}</td>
                    <td>{{ $data['bank_account'] ? $data['bank_account']->bank_name : '-' }}</td>
                    <td>{{ $data['bank_account'] ? $data['bank_account']->account_number : '-' }}</td>
                    <td>{{ $data['booking']->currency->symbol }}{{ number_format($data['total'], 0) }}</td>
                </tr>
            @endforeach
            <tr class="empty-row">
                <td colspan="6"></td>
            </tr>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">{{ __('Total') }}:</td>
                <td>
                    @if (count($bookingsData) > 0)
                        {{ $bookingsData[0]['booking']->currency->symbol }}{{ number_format($totalAmount, 0) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
