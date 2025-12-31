<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\LogActivity;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.pages.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $remember_me = $request->boolean('remember_me');

        if (!Auth::attempt($credentials, $remember_me)) {
            // Log failed login attempt
            activity()
                ->withProperties([
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log(__('activity.failed_login_attempt', ['email' => $request->email]));
                
            return redirect()->back()->withInput($request->only('email', 'remember_me'))->with('error', __('Invalid Credentials'));
        }

        // Log successful login
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log(__('activity.successful_login', ['name' => Auth::user()->name]));

        return redirect()->intended(route('dashboard.index'))->with('success', __('Login successfully.'));
    }
}
