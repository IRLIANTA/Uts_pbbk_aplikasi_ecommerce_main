<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::all(), 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $customer = Customer::create([
            'customer_id' => Str::uuid(),
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => bcrypt($validated['password']),
            'phone'       => $validated['phone'] ?? null,
            'address'     => $validated['address'] ?? null,
        ]);

        return response()->json($customer, 201)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function show($id)
    {
        $customer = Customer::where('customer_id', $id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer, 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::where('customer_id', $id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:50',
            'email'    => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'password' => 'nullable|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $customer->update($validated);

        return response()->json($customer, 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function destroy($id)
    {
        $customer = Customer::where('customer_id', $id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['message' => 'Customer deleted'], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }
}
