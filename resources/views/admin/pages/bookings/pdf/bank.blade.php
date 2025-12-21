<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Bank Export') }} - {{ $booking->code }}</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            /* color: #000; */
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
            /* Dark Teal */
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

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="{{ asset('./472228932_903900521859408_2733195805942687837_n.jpg') }}" alt="AZHA Travel Logo"
            class="logo-img" />
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
            <tr>
                <td>1</td>
                <td>{{ $booking->code }}</td>
                <td>{{ $booking->hotel->name }}</td>
                <td>{{ $bankAccount ? $bankAccount->bank_name : '-' }}</td>
                <td>{{ $bankAccount ? $bankAccount->account_number : '-' }}</td>
                <td>{{ $totalAmount == 0 ? '' : $booking->currency->symbol . number_format($totalAmount, 0) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
