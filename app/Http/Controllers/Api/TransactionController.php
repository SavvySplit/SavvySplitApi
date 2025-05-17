<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    // GET /api/transactions
    /**
     * @authenticated 
     */
    public function index()
    {
        return response()->json(Transaction::with(['bill', 'payer'])->get());
    }

    // POST /api/transactions
    /**
     * @authenticated 
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'bill_id' => 'required|exists:bills,id',
            'payer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $transaction = Transaction::create($validated->validated());

        return response()->json($transaction, 201);
    }

    // GET /api/transactions/{id}
    /**
     * @authenticated 
     */
    public function show($id)
    {
        $transaction = Transaction::with(['bill', 'payer'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    // PUT /api/transactions/{id}
    /**
     * @authenticated 
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $validated = Validator::make($request->all(), [
            'bill_id' => 'sometimes|required|exists:bills,id',
            'payer_id' => 'sometimes|required|exists:users,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'date' => 'sometimes|required|date',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $transaction->update($validated->validated());

        return response()->json($transaction);
    }

    // DELETE /api/transactions/{id}
    /**
     * @authenticated 
     */
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted']);
    }
}
