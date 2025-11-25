<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->back()->withInput($request->only('email', 'remember_me'))->with('error', __('Invalid Credentials'));
        }
        return redirect()->intended(route('dashboard.index'))->with('success', __('Login successfully.'));
    }
}
