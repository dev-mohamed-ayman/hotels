<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Wallet Statement') }} - {{ $customer->name }}</title>
    <style>
        @page {
            margin: 10mm;
            header: page-header;
            footer: page-footer;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
        }

        .logo-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .logo-img {
            width: 120px;
            height: auto;
        }

        .header-info {
            margin-bottom: 20px;
            width: 100%;
        }

        .customer-info {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }

        th {
            background-color: #0d3c47;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #fff;
        }

        td {
            padding: 8px;
            text-align: center;
            background-color: #eef5fa;
            border: 1px solid #fff;
            color: #000;
        }

        tbody tr:nth-child(even) td {
            background-color: #dae8f2;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .badge-credit {
            color: red;
            font-weight: bold;
        }

        .badge-debit {
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="{{ asset('./472228932_903900521859408_2733195805942687837_n.jpg') }}" alt="Logo" class="logo-img" />
        <h2>{{ __('Wallet Statement') }}</h2>
    </div>

    <div class="header-info">
        <div class="customer-info">
            {{ __('Customer') }}: {{ $customer->name }}
        </div>
        <div>
            {{ __('Date') }}: {{ now()->format('Y-m-d') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">{{ __('Date') }}</th>
                <th style="width: 15%;">{{ __('Reference') }}</th>
                <th style="width: 30%;">{{ __('Description') }}</th>
                <th style="width: 15%;">{{ __('DR / CR') }}</th>
                <th style="width: 20%;">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $index => $transaction)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                    <td>{{ $transaction->reference }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>
                        @if ($transaction->type == 'debit')
                            <span class="badge-debit">{{ __('DR') }}</span>
                        @else
                            <span class="badge-credit">{{ __('CR') }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $transaction->currency->symbol ?? '' }} {{ number_format($transaction->amount, 2) }}
                    </td>
                </tr>
            @endforeach
            @foreach ($balances as $balance)
                <tr style="background-color: #0d3c47; color: white;">
                    <td colspan="5" style="background-color: #0d3c47; color: white;">
                        {{ $balance->currency->code ?? '' }}</td>
                    <td
                        style="background-color: #0d3c47; color: white;font-weight: bold; {{ $balance->balance < 0 ? 'color: red;' : 'color: white;' }}">
                        {{ number_format($balance->balance, 2) }} {{ $balance->currency->symbol ?? '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
