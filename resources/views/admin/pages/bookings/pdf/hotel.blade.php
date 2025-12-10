<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Hotel Booking Invoice') }} - {{ $booking->code }}</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
            direction: rtl;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
        }

        .info-table .label {
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }

        .info-table .value {
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }

        .header h1 {
            font-size: 20pt;
            margin: 0 0 8px 0;
            font-weight: bold;
        }

        .header .code {
            font-size: 11pt;
            color: #666;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .info-table .label {
            width: 25%;
            background: #f5f5f5;
            font-weight: bold;
            font-size: 9pt;
        }

        .info-table .value {
            width: 25%;
            font-size: 10pt;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        table th {
            background: #000;
            color: #fff;
            padding: 8px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            font-weight: bold;
            border: 1px solid #000;
        }

        table td {
            padding: 6px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            border: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .totals {
            margin-top: 20px;
            padding: 12px;
            border: 2px solid #000;
        }

        .total-row {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eee;
        }

        .total-row:last-child {
            border-bottom: none;
        }

        .total-label {
            display: inline-block;
            width: 70%;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }

        .total-amount {
            display: inline-block;
            width: 28%;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            font-weight: bold;
            direction: ltr;
            unicode-bidi: bidi-override;
        }

        .total-final {
            border-top: 2px solid #000;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 12pt;
        }

        .note-box {
            margin-top: 20px;
            padding: 12px;
            background: #fff9e6;
            border: 2px solid #ffc107;
        }

        .note-title {
            font-weight: bold;
            margin-bottom: 6px;
            color: #856404;
        }

        .notes {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-right: 3px solid #000;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .currency {
            direction: ltr;
            unicode-bidi: bidi-override;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ __('Hotel Booking Invoice') }}</h1>
        <div class="code">{{ __('Code') }}: {{ $booking->code }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">{{ __('Customer') }}</td>
            <td class="value">{{ $booking->customer->name }}</td>
            <td class="label">{{ __('Hotel') }}</td>
            <td class="value">{{ $booking->hotel->name }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Check In') }}</td>
            <td class="value">{{ $booking->check_in->format('Y-m-d') }}</td>
            <td class="label">{{ __('Check Out') }}</td>
            <td class="value">{{ $booking->check_out->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Nights') }}</td>
            <td class="value">{{ $booking->nights }}</td>
            <td class="label">{{ __('Status') }}</td>
            <td class="value">{{ ucfirst($booking->status) }}</td>
        </tr>
        @if ($booking->customer->phone_1)
            <tr>
                <td class="label">{{ __('Phone') }}</td>
                <td class="value">{{ $booking->customer->phone_1 }}</td>
                @if ($booking->hotel->address)
                    <td class="label">{{ __('Address') }}</td>
                    <td class="value">{{ $booking->hotel->address }}</td>
                @else
                    <td class="label"></td>
                    <td class="value"></td>
                @endif
            </tr>
        @elseif ($booking->hotel->address)
            <tr>
                <td class="label">{{ __('Address') }}</td>
                <td class="value">{{ $booking->hotel->address }}</td>
                <td class="label"></td>
                <td class="value"></td>
            </tr>
        @endif
    </table>

    <div class="section-title">{{ __('Rooms Details') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('Room Type') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Count') }}</th>
                <th>{{ __('Base Price') }}</th>
                <th>{{ __('Subtotal') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roomsData as $room)
                <tr>
                    <td>{{ $room['room_type'] }}</td>
                    <td>{{ $room['category'] ?? '-' }}</td>
                    <td>{{ $room['room_count'] }}</td>
                    <td>{{ number_format($room['price'], 2) }}<span
                            class="currency">{{ $booking->currency->symbol }}</span></td>
                    <td><strong>{{ number_format($room['subtotal'], 2) }}<span
                                class="currency">{{ $booking->currency->symbol }}</span></strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="total-label">{{ __('Rooms Total') }}:</span>
            <span class="total-amount">{{ number_format($roomsTotal, 2) }}<span
                    class="currency">{{ $booking->currency->symbol }}</span></span>
        </div>
        @if ($childTotal > 0)
            <div class="total-row">
                <span class="total-label">{{ __('Children Total') }}:</span>
                <span class="total-amount">{{ number_format($childTotal, 2) }}<span
                        class="currency">{{ $booking->currency->symbol }}</span></span>
            </div>
        @endif
        @if ($additionsTotal > 0)
            <div class="total-row">
                <span class="total-label">{{ __('Additions') }}:</span>
                <span class="total-amount">+{{ number_format($additionsTotal, 2) }}<span
                        class="currency">{{ $booking->currency->symbol }}</span></span>
            </div>
        @endif
        @if ($discountsTotal > 0)
            <div class="total-row">
                <span class="total-label">{{ __('Discounts') }}:</span>
                <span class="total-amount">-{{ number_format($discountsTotal, 2) }}<span
                        class="currency">{{ $booking->currency->symbol }}</span></span>
            </div>
        @endif
        <div class="total-row total-final">
            <span class="total-label">{{ __('Total Amount (Base Price)') }}:</span>
            <span
                class="total-amount">{{ number_format($roomsTotal + $childTotal + $additionsTotal - $discountsTotal, 2) }}<span
                    class="currency">{{ $booking->currency->symbol }}</span></span>
        </div>
    </div>

    <div class="note-box">
        <div class="note-title">{{ __('Important Note') }}:</div>
        <div>
            {{ __('This invoice shows the base prices only, without platform margins or fees. This is the amount that will be paid to the hotel.') }}
        </div>
    </div>

    @if ($booking->notes)
        <div class="notes">
            <strong>{{ __('Booking Notes') }}:</strong> {{ $booking->notes }}
        </div>
    @endif

    <div class="footer">
        <div>{{ __('Generated on') }}: {{ now()->format('Y-m-d H:i:s') }}</div>
        <div>{{ __('Hotel Invoice - Base Price Only') }}</div>
    </div>
</body>

</html>
