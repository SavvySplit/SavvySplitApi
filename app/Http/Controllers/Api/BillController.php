<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{
    // GET /api/bills
    /**
     * @authenticated 
     */
    public function index()
    {
        return response()->json(Bill::with('group')->get());
    }

    // POST /api/bills
    /**
     * @authenticated 
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|string|max:50',
            'split_with' => 'required|array',
            'custom_splits' => 'nullable|array',
            'paid' => 'boolean',
            'paid_by' => 'required|array',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $bill = Bill::create($validated->validated());

        return response()->json($bill, 201);
    }

    // GET /api/bills/{id}
    /**
     * @authenticated 
     */
    public function show($id)
    {
        $bill = Bill::with('group')->find($id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        return response()->json($bill);
    }

    // PUT /api/bills/{id}
    /**
     * @authenticated 
     */
    public function update(Request $request, $id)
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $validated = Validator::make($request->all(), [
            'group_id' => 'sometimes|exists:groups,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|string|max:50',
            'split_with' => 'sometimes|required|array',
            'custom_splits' => 'nullable|array',
            'paid' => 'boolean',
            'paid_by' => 'sometimes|required|array',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $bill->update($validated->validated());

        return response()->json($bill);
    }

    // DELETE /api/bills/{id}
    /**
     * @authenticated 
     */
    public function destroy($id)
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $bill->delete();

        return response()->json(['message' => 'Bill deleted']);
    }
}
