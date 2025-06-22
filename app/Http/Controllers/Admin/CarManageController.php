<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use Inertia\Inertia;
use App\Models\CarDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CarManageController extends Controller
{
    public function showCarList()
    {
        $cars = Car::orderBy('id', 'desc')->get();

        return Inertia::render('Admin/Car/CarListPage', [
            'cars' => $cars,
        ]);
    }

    public function showCarSave($id = null)
    {
        $car = Car::find($id);

        return Inertia::render('Admin/Car/Add_Edit_CarPage', [
            'car' => $car,
        ]);
    }

    public function carStore(Request $request)
    {
        $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|string',
                'car_type' => 'required|string',
                'daily_rent_price' => 'required|numeric',
                'weekly_rent_price' => 'nullable|numeric',
                'status' => 'required|boolean',
                'availability' => 'required|string',
                'image' => 'nullable|max:2048',
            ]
        );

        if ($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageNameToStore = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_url = $image->storeAs('cars', $imageNameToStore, 'public');
            $validatedData['image'] = $image_url;
        }

        $car = Car::create($validatedData);

        if ($car)
        {
            return redirect()
                ->route('show.car.list')
                ->with([
                    'message' => 'Car created successfully',
                    'status' => true,
                    'code' => 200,
                ]);
        }
    }

    public function updateCar(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|string',
                'car_type' => 'required|string',
                'daily_rent_price' => 'required|numeric',
                'weekly_rent_price' => 'nullable|numeric',
                'status' => 'required|boolean',
                'availability' => 'required|string',
                'image' => 'sometimes|nullable|max:2048',
            ]
        );

        if ($request->hasFile('image'))
        {
            if ($car->image && Storage::disk('public')->exists($car->image))
            {
                Storage::disk('public')->delete($car->image);
            }

            $image = $request->file('image');
            $imageNameToStore = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_url = $image->storeAs('cars', $imageNameToStore, 'public');
            $validatedData['image'] = $image_url;
        }

        $car->update($validatedData);

        if ($car)
        {
            return redirect()
                ->route('show.car.list')
                ->with([
                    'message' => 'Car updated successfully',
                    'status' => true,
                    'code' => 200,
                ]);
        }
    }

    public function changeCarStatus(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        if (!$car)
        {
            $data = ['message' => 'Car not found', 'status' => false, 'code' => 404];
            return redirect()->back()->with($data);
        }
        
        $car->status = $request->status;
        $car->save();
        $data = ['message' => 'Car status changed successfully', 'status' => true, 'code' => 200];
        
        return redirect()->back()->with($data);
    }

    public function deleteCar($id)
    {
        $car = Car::findOrFail($id);

        if ($car->image && Storage::disk('public')->exists($car->image))
        {
            Storage::disk('public')->delete($car->image);
        }
        $car->delete();

        if ($car)
        {
            $data = ['message' => 'Car deleted successfully', 'status' => true, 'code' => 200];
            return redirect()->back()->with($data);
        }
    }

    public function showSaveCarDetailsPage($id = null)
    {
        $car_details = null;

        if ($id)
        {
            $car = Car::findOrFail($id);
            $car_details = CarDetails::where('car_id', $id)->first();
        }

        return Inertia::render('Admin/Car/SaveCarDetailsPage', [
            'car' => $car,
            'car_details' => $car_details,
        ]);
    }

    public function saveCarDetails(Request $request)
    {
        $validated_data = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'short_description' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'seats' => 'required|integer|min:1',
                'fuel_type' => 'required|in:Petrol,Diesel,CNG,Electric',
                'mileage' => 'nullable|numeric|min:0',
                'transmission' => 'required|in:Manual,Automatic',
                'air_conditioning' => 'boolean',
                'gps' => 'boolean',
                'bluetooth' => 'boolean',
                'usb_port' => 'boolean',
            ]
        );

        $car_details = CarDetails::create($validated_data);

        if ($car_details)
        {
            $data = ['message' => 'Car details saved successfully', 'status' => true];
            return redirect()->route('show.car.list')->with($data);
        }
    }

    public function updateCarDetails(Request $request)
    {
        $validated_data = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'short_description' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'seats' => 'required|integer|min:1',
                'fuel_type' => 'required|in:Petrol,Diesel,CNG,Electric',
                'mileage' => 'nullable|numeric|min:0',
                'transmission' => 'required|in:Manual,Automatic',
                'air_conditioning' => 'boolean',
                'gps' => 'boolean',
                'bluetooth' => 'boolean',
                'usb_port' => 'boolean',
            ]
        );

        $car_details = CarDetails::where('car_id', $validated_data['car_id'])->first();
        $car_details->update($validated_data);

        if($car_details)
        {
            $data = ['message' => 'Car details updated successfully', 'status' => true];
            return redirect()->route('show.car.list')->with($data);
        }
    }
}
