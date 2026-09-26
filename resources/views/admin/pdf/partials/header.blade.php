<table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; border: 0; border-style: none;">
    <tr>
        <td style="width: 50%; vertical-align: middle; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; padding: 0; border: 0; border-style: none;">
            @php
                $logoPath = public_path('assets/img/branding/azha-logo-horizontal.svg');
                $logoFallback = public_path('assets/img/branding/logo.png');
            @endphp
            @if (file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="{{ __('brand.name_full') }}" style="height: 60px; width: auto;" />
            @elseif (file_exists($logoFallback))
                <img src="{{ $logoFallback }}" alt="{{ __('brand.name_full') }}" style="height: 60px; width: auto;" />
            @else
                <h2 style="color: #12214c; margin: 0; font-size: 20pt;">{{ __('brand.name_full') }}</h2>
            @endif
        </td>
        <td style="width: 50%; vertical-align: middle; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; padding: 0; border: 0; border-style: none;">
            @if (isset($headerTitle) && !empty($headerTitle))
                <div style="font-size: 18pt; font-weight: bold; color: #000000; margin: 0; line-height: 1.2;">
                    {{ $headerTitle }}
                </div>
            @endif
            @if (isset($headerSubtitle) && !empty($headerSubtitle))
                <div style="font-size: 12pt; color: #d4b876; margin-top: 6px; line-height: 1.2;">
                    {{ $headerSubtitle }}
                </div>
            @endif
        </td>
    </tr>
</table>
