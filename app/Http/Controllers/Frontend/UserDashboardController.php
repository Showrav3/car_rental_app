<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserDashboardController extends Controller
{
    public function showBookingHistory($id)
    {
        $id = Auth::guard('customer')->user()->id;
        $rentals = Rental::where('user_id', $id)->with('user', 'car')->orderBy('id', 'desc')->get();
        return Inertia::render('Frontend/Dashboard/BookingHistoryPage', [
            'rentals' => $rentals,
        ]);
    }

    public function cancelBooking($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status === 'Pending')
        {
            $rental->update(['status' => 'Cancelled']);
        }

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    public function showUserProfile($id)
    {
        $id = Auth::guard('customer')->user()->id;
        $profile = User::where('role', 'customer')->where('id', $id)->first();

        return Inertia::render('Frontend/Dashboard/ProfilePage', [
            'profile' => $profile,
        ]);
    }

    public function updateUserProfile(Request $request)
    {
        $validated_data = $request->validate([
                'name' => 'required|string',
                'phone' => 'required|numeric|digits:11|starts_with:01|regex:/^01[0-9]{9}$/|unique:users,phone,' . Auth::guard('customer')->id(),
                'address' => 'nullable|string|max:255',
            ]
        );

        $user = Auth::guard('customer')->user();
        $user->update($validated_data);

        return redirect()->back()->with('message', 'Profile updated successfully.');
    }

    public function updateUserPassword(Request $request)
    {
        $request->validate([
                'password' => 'required|string',
                'newPassword' => 'required|string|min:4|confirmed',
            ]
        );

        $user = Auth::guard('customer')->user();

        if (!Hash::check($request->password, $user->password))
        {
            return redirect()->back()->with(['message' => 'Old password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->newPassword)]);

        return redirect()->back()->with('message', 'Password updated successfully.');
    }
}
