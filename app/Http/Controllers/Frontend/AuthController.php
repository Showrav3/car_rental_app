<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function ShowUserLogin()
    {
        return Inertia::render('Frontend/Auth/UserLoginPage');
    }

    public function ShowUserRegistration()
    {
        return Inertia::render('Frontend/Auth/UserRegistrationPage');
    }

    public function UserRegistration(Request $request)
    {
        $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'phone' => 'required|numeric|digits:11|unique:users',
                'password' => 'required|string|min:4|confirmed',
                'address' => 'nullable|string|max:255',
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if ($user)
        {
            $data = ['message' => 'You are registered successfully', 'status' => true];
            return redirect()->route('show.user.login')->with($data);
        }
        else
        {
            $data = ['message' => 'Failed to create user', 'status' => false];
            return redirect()->back()->with($data);
        }
    }

    public function UserLogin(Request $request)
    {
        $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|min:4',
            ]
        );

        if (!Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect()->back()->with([
                'message' => 'Email or Password is incorrect',
                'status' => false,
            ]);
        }

        $user = Auth::guard('customer')->user();
        if ($user->role !== 'customer')
        {
            Auth::guard('customer')->logout();
            return redirect()->back()->with([
                'message' => 'Unauthorized Access! Only registered customer can log in.',
                'status' => false,
            ]);
        }

        $request->session()->regenerate();

        return to_route('show.home')->with([
            'message' => 'Login Successful',
            'status' => true,
            'code' => 200,
        ]);
    }

    public function UserLogout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('show.home');
    }
}
