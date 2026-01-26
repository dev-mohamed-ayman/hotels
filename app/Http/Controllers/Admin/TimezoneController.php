<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class TimezoneController extends Controller
{
    public function change(Request $request)
    {
        $timezone = $request->input('timezone');

        // Validate timezone
        $supportedTimezones = config('timezones.supported');
        if (!array_key_exists($timezone, $supportedTimezones)) {
            return back()->with('error', __('Invalid timezone'));
        }

        // Store timezone in cookie for 1 year
        Cookie::queue('timezone', $timezone, 525600);

        return back()->with('success', __('Timezone updated successfully'));
    }
}
