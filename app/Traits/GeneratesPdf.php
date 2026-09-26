<?php

namespace App\Traits;

use Mpdf\Mpdf;

trait GeneratesPdf
{
    protected function makePdf(array $config = []): Mpdf
    {
        $fontDir = storage_path('fonts/mpdf/ttfonts');
        $tempDir = storage_path('fonts/mpdf/tmp');

        if (! is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $defaultConfig = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
            'tempDir' => $tempDir,
            'fontDir' => $fontDir,
            'autoScriptToLang' => true,
            'autoLangToFont' => false,
            'useSubstitutions' => true,
            'useSubstitution' => true,
            'debug' => false,
        ];

        $config = array_merge($defaultConfig, $config);

        $mpdf = new Mpdf($config);

        $locale = app()->getLocale();
        $mpdf->SetDirectionality($locale === 'ar' ? 'rtl' : 'ltr');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = false;
        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;

        $this->registerBrandFontFamily($mpdf, $fontDir, 'montserrat', [
            'R' => 'Montserrat-Regular.ttf',
            'B' => 'Montserrat-Bold.ttf',
            'I' => 'Montserrat-Italic.ttf',
            'BI' => 'Montserrat-BoldItalic.ttf',
        ], [
            'R' => 'DejaVuSansCondensed.ttf',
            'B' => 'DejaVuSansCondensed-Bold.ttf',
            'I' => 'DejaVuSansCondensed-Oblique.ttf',
            'BI' => 'DejaVuSansCondensed-BoldOblique.ttf',
        ]);

        $this->registerBrandFontFamily($mpdf, $fontDir, 'cairo', [
            'R' => 'Cairo-Regular.ttf',
            'B' => 'Cairo-Bold.ttf',
            'I' => null,
            'BI' => null,
        ], [
            'R' => 'DejaVuSansCondensed.ttf',
            'B' => 'DejaVuSansCondensed-Bold.ttf',
            'I' => 'DejaVuSansCondensed-Oblique.ttf',
            'BI' => 'DejaVuSansCondensed-BoldOblique.ttf',
        ]);

        return $mpdf;
    }

    private function registerBrandFontFamily(
        Mpdf $mpdf,
        string $fontDir,
        string $family,
        array $preferredFiles,
        array $fallbackFiles
    ): void {
        $resolved = [];
        $styles = [
            ''   => 'R',
            'B'  => 'B',
            'I'  => 'I',
            'BI' => 'BI',
        ];

        $allResolved = true;
        foreach ($styles as $styleKey => $slotKey) {
            $preferred = $preferredFiles[$slotKey] ?? null;
            $fallback = $fallbackFiles[$slotKey] ?? null;

            if ($preferred !== null && file_exists($fontDir . '/' . $preferred) && $this->looksLikeValidTtf($fontDir . '/' . $preferred)) {
                $resolved[$styleKey] = $preferred;
                continue;
            }
            if ($fallback !== null && file_exists($fontDir . '/' . $fallback) && $this->looksLikeValidTtf($fontDir . '/' . $fallback)) {
                $resolved[$styleKey] = $fallback;
                $allResolved = false;
                continue;
            }
            $resolved[$styleKey] = null;
            $allResolved = false;
        }

        $entry = [
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ];
        foreach ($styles as $styleKey => $slotKey) {
            if ($resolved[$styleKey] !== null) {
                $entry[$slotKey] = $resolved[$styleKey];
            }
        }

        $mpdf->fontdata[$family] = $entry;

        foreach (array_keys($styles) as $styleKey) {
            if ($resolved[$styleKey] !== null) {
                try {
                    $mpdf->AddFont($family, $styleKey);
                } catch (\Throwable $e) {
                    // Swallow and keep going — the CSS stack still has dejavusans as fallback.
                }
            }
        }
    }

    private function looksLikeValidTtf(string $path): bool
    {
        if (! is_file($path) || ! is_readable($path) || filesize($path) < 20000) {
            return false;
        }

        $h = @file_get_contents($path, false, null, 0, 4);
        if ($h === false || strlen($h) < 4) {
            return false;
        }

        // TrueType signature: 0x00010000 (ttf), 0x74727565 ("true"), or 0x4F54544F ("OTTO" / OpenType with CFF).
        $signatures = ['00010000', '74727565', '4f54544f'];

        return in_array(bin2hex($h), $signatures, true);
    }
}
