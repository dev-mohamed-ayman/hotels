<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Wallet Statement') }} - {{ $model->name }}</title>
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
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="{{ asset('./472228932_903900521859408_2733195805942687837_n.jpg') }}" alt="Logo" class="logo-img" />
        <h2>{{ __('Wallet Statement') }}</h2>
    </div>

    <div class="header-info">
        <div class="customer-info">
            {{ $type == 'customer' ? __('Customer') : __('Hotel') }}: {{ $model->name }}
        </div>
        <div>
            {{ __('Date') }}: {{ now()->format('Y-m-d') }}
        </div>
    </div>

    <!-- Balance Summary -->
    @if ($balances->count() > 0)
        <h3>{{ __('Current Balances') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Currency') }}</th>
                    <th>{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($balances as $balance)
                    <tr>
                        <td>{{ $balance->currency->name }} ({{ $balance->currency->code }})</td>
                        <td style="color: {{ $balance->balance < 0 ? 'red' : 'green' }}; font-weight: bold;">
                            @formatNumber($balance->balance) {{ $balance->currency->symbol }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Transactions Table -->
    <h3>{{ __('Transactions History') }}</h3>
    @php
        $columnLabels = [
            'date' => __('Date'),
            'type' => __('Type'),
            'amount' => __('Amount'),
            'currency' => __('Currency'),
            'description' => __('Description'),
            'balance' => __('Cumulative Balance'),
        ];

        // mPDF renders table columns in DOM order (left to right) when the
        // table is LTR — so mirror exactly what the on-screen transactions
        // table shows: in the Arabic RTL UI the running balance is the
        // left-most column, in the English LTR UI the date is.
        $columns = app()->getLocale() === 'ar'
            ? ['balance', 'type', 'amount', 'currency', 'description', 'date']
            : ['date', 'description', 'amount', 'currency', 'type', 'balance'];

        $balanceFirst = $columns[0] === 'balance';
    @endphp
    <table dir="ltr">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $columnLabels[$column] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- With filters applied, open the statement with the balance carried into the range. --}}
            @foreach ($openingRows as $openingRow)
                <tr>
                    @php
                        $openingBalance = '<td style="color: '.($openingRow['balance'] < 0 ? '#dc3545' : '#198754').'; font-weight: bold;">'.formatNumber($openingRow['balance']).'</td>';
                        $openingLabel = '<td colspan="5" style="font-weight: bold;">'
                            .e(__('Opening Balance'))
                            .($openingRow['currency'] ? ' ('.e($openingRow['currency']->code).')' : '')
                            .'</td>';
                    @endphp
                    @if ($balanceFirst)
                        {!! $openingBalance !!}
                        {!! $openingLabel !!}
                    @else
                        {!! $openingLabel !!}
                        {!! $openingBalance !!}
                    @endif
                </tr>
            @endforeach

            @foreach ($transactions as $transaction)
                @php
                    $credit = $transaction->type == 'credit';
                    $cells = [
                        'date' => '<td>'.$transaction->created_at->format('Y-m-d H:i').'</td>',
                        'type' => '<td><span style="color: '.($credit ? '#dc3545' : '#198754').'; font-weight: bold;">'
                            .e($credit ? __('Credit') : __('Debit')).'</span></td>',
                        'amount' => '<td style="color: '.($credit ? '#dc3545' : '#198754').'; font-weight: bold;">'
                            .formatNumber($credit ? -$transaction->amount : $transaction->amount).'</td>',
                        'currency' => '<td>'.e($transaction->currency->code).'</td>',
                        'description' => '<td>'.($type == 'hotel'
                            ? e($model->name).(filled($transaction->description) ? ' — '.e($transaction->description) : '')
                            : e($transaction->description)).'</td>',
                        'balance' => '<td style="color: '.($transaction->running_balance < 0 ? '#dc3545' : '#198754').'; font-weight: bold;">'
                            .formatNumber($transaction->running_balance).'</td>',
                    ];
                @endphp
                <tr>
                    @foreach ($columns as $column)
                        {!! $cells[$column] !!}
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
