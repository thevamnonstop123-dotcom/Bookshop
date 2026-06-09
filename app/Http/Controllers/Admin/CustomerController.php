<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Admin\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display all customers.
     */
    public function index()
    {
        $customers = $this->customerService->getAll();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show customer details.
     */
    public function show(Customer $customer)
    {
        $customer = $this->customerService->getDetail($customer);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Update customer status.
     */
    public function updateStatus(Request $request, Customer $customer)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,banned',
        ]);

        $this->customerService->updateStatus($customer, $request->status);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer status updated to ' . ucfirst($request->status) . '.');
    }
}