<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RentalManageController extends Controller
{
    public function showRentalList()
    {
        $rentals = Rental::with('user', 'car')->orderBy('id', 'desc')->get();
        $customers = User::select('id', 'name', 'phone')->where('role', 'customer')->get();
        $cars = Car::select('id', 'name', 'daily_rent_price', 'model')->where('status', 1)->where('availability', 'Available')->get();

        return Inertia::render('Admin/Rental/RentalListPage', [
            'rentals' => $rentals,
            'customers' => $customers,
            'cars' => $cars,
        ]);
    }

    public function rentalStore(Request $request)
    {
        $validated_data = $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'car_id' => 'required|exists:cars,id',
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:Ongoing,Completed,Cancelled',
                'pickup_location' => 'nullable|string|max:255',
                'drop_off_location' => 'nullable|string|max:255',
                'pickup_time' => 'nullable|string|max:255',
                'drop_off_time' => 'nullable|string|max:255',
            ]
        );

        $isCarBooked = Rental::where('car_id', $request->car_id)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->where(function ($query) use ($request) {
                // Check if the rental period overlaps with existing rentals
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

        Rental::create($validated_data);

        return redirect()
            ->back()
            ->with(['message' => 'Rental created successfully.', 'status' => true]);
    }

    public function updateRental(Request $request, $id)
    {
        $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'car_id' => 'required|exists:cars,id',
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:Ongoing,Completed,Cancelled',
            ]
        );

        $rental = Rental::findOrFail($id);
        $isCarChanged = $rental->car_id != $request->car_id;
        $areDatesChanged = $rental->start_date != $request->start_date || $rental->end_date != $request->end_date;

        if ($isCarChanged || $areDatesChanged)
        {
            $isCarBooked = Rental::where('car_id', $request->car_id)
                ->whereNotIn('status', ['Cancelled', 'Completed'])
                ->where('id', '!=', $id)
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
                    ->with(['message' => 'This car is not available for the selected dates.', 'status' => false]);
            }
        }

        $car = Car::findOrFail($request->car_id);
        $days = \Carbon\Carbon::parse($request->start_date)->diffInDays($request->end_date);
        $totalCost = $car->daily_rent_price * $days;

        $rental->update([
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_cost' => $totalCost,
            'status' => $request->status,
        ]);

        return redirect()
            ->back()
            ->with(['message' => 'Rental updated successfully.', 'status' => true]);
    }

    public function deleteRental($id)
    {
        $rental = Rental::findOrFail($id);
        $rental->delete();

        if ($rental)
        {
            $data = ['message' => 'Rent deleted successfully'];
            return redirect()->back()->with($data);
        }
    }

    public function showRentDetails($id)
    {
        $rental_details = Rental::with('user', 'car')->findOrFail($id);

        return Inertia::render('Admin/Rental/RentDetailsPage', [
            'rental_details' => $rental_details,
        ]);
    }

    public function changRentalStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Ongoing,Completed,Cancelled',
        ]);

        $rent = Rental::findOrFail($id);
        $rent->update(['status' => $request->status]);

        return redirect()->back()->with('message', 'Status updated successfully');
    }
}
