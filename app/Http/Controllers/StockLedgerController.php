<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLedger;
use Illuminate\Http\Request;

class StockLedgerController extends Controller
{
    public function index()
    {
        return view('stock_ledger.index', [
            'products' => Product::orderBy('name')->get()
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'product_id' => 'required'
        ]);

        $product = Product::findOrFail($request->product_id);

        $stock_ledger = StockLedger::where('product_id', $request->product_id)
            ->whereBetween('date', [$request->date_from, $request->date_to])
            ->orderBy('stock_ledgers.date')
            ->orderBy('stock_ledgers.id')
            ->select(
                'stock_ledgers.*',
            )
            ->get();


        $balance = 0;

        foreach ($stock_ledger as $row) {
            $balance += ($row->in - $row->out);
            $row->balance = $balance;
        }

        return response()->json([
            'product' => $product,
            'data' => $stock_ledger
        ]);
    }
}
