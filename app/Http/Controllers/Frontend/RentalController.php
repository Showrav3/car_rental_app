<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Car;
use Inertia\Inertia;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Mail\RentalCreatedForAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RentalCreatedForCustomer;

class RentalController extends Controller
{
    public function createRent(Request $request)
    {
        try {
            $validated_data = $request->validate([
                    'car_id' => 'required|exists:cars,id',
                    'name' => 'nullable|string|max:255',
                    'phone' => 'nullable|string|max:255',
                    'start_date' => 'required|date|after_or_equal:today',
                    'end_date' => 'required|date|after:start_date',
                    'pickup_location' => 'nullable|string|max:255',
                    'drop_off_location' => 'nullable|string|max:255',
                    'pickup_time' => 'nullable|string|max:255',
                    'drop_off_time' => 'nullable|string|max:255',
                ]
            );

            $isCarBooked = Rental::where('car_id', $request->car_id)
                ->whereNotIn('status', ['Cancelled', 'Completed'])
                ->where(function ($query) use ($request) {
                    $query->where(function ($query2) use ($request) {
                        $query2->where('start_date', '<=', $request->end_date)->where('end_date', '>=', $request->start_date);
                    });
                })
                ->exists();

            if ($isCarBooked)
            {
                return redirect()
                    ->back()
                    ->with(['message' => 'This car is not available for the selected dates.']);
            }

            $car = Car::findOrFail($request->car_id);
            $days = \Carbon\Carbon::parse($request->start_date)->diffInDays($request->end_date);
            $totalCost = $car->daily_rent_price * $days;

            $validated_data['total_cost'] = $totalCost;
            $validated_data['user_id'] = Auth::guard('customer')->user()->id;
            $validated_data['status'] = 'Pending';

            $rental_create = Rental::create($validated_data);
            $rental_create->load('user');

            if ($rental_create)
            {
                Mail::to('arifulislam6460@gmail.com')->send(new RentalCreatedForAdmin($rental_create));
                Mail::to(Auth::guard('customer')->user()->email)->send(new RentalCreatedForCustomer($rental_create));

                $data = ['message' => 'Rental created successfully', 'status' => true];
                return redirect()->back()->with($data);
            }
        } catch (Exception $e) {
            $data = ['message' => 'Please log in to preceed', 'status' => false];
            return redirect()->back()->with($data);
        }
    }

    public function rentalSuccess()
    {
        return Inertia::render('Frontend/RentSuccess');
    }
}
