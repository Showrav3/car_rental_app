<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function showCustomerList()
    {
        $customers = User::where('role', 'customer')->get();

        return Inertia::render('Admin/Customer/CustomerListPage', [
            'customers' => $customers,
        ]);
    }

    public function rentalHistoryForThisCustomer($id)
    {
        $rent_history = User::where('role', 'customer')
            ->with('rents.car')
            ->find($id);

        return response()->json([
            'rents' => $rent_history->rents ?? [],
        ]);
    }

    public function deleteCustomer($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->delete();

        return redirect()->back()->with('message', 'Customer deleted successfully');
    }
}
