<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showAdminLogin()
    {
        return Inertia::render('Admin/AdminLoginPage');
    }

    public function AdminLogin(Request $request)
    {
        $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|min:4',
            ]
        );

        if (!Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect()->back()->with([
                'message' => 'Email or Password is incorrect',
                'status' => false,
            ]);
        }

        $user = Auth::guard('admin')->user();
        if ($user->role !== 'admin')
        {
            Auth::guard('admin')->logout();

            return redirect()->back()->with([
                'message' => 'Unauthorized Access! Only admins can log in.',
                'status' => false,
            ]);
        }

        $request->session()->regenerate();

        return to_route('show.admin.dashboard')->with([
            'message' => 'Login Successful',
            'status' => true,
            'code' => 200,
        ]);
    }

    public function AdminLogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('show.admin.login');
    }
}
