<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Purchase::with(['supplier', 'user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('supplier', fn($row) => $row->supplier->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('supplier', function ($query, $keyword) {
                    $query->whereHas('supplier', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('purchase.show', $row->id);
                    $deleteUrl = route('purchase.destroy', $row->id);

                    return '<div class="btn-group">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info">Tampil</a>
                                <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('purchase.index');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['user', 'supplier', 'product']);
        return view('purchase.show', compact('purchase'));
    }

    public function create()
    {
        return view('purchase.create', [
            'suppliers' => Supplier::all(),
            'payment_gateways' => Account::where('is_payment_gateway', 1)->get(),
            'products' => Product::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'subtotal' => 'required',
            'discount' => 'required',
            'taxes' => 'required',
            'expenses' => 'required',
            'grand_total' => 'required',
            'account_id' => 'required|exists:accounts,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required',
        ]);

        $request = self::cleanRequest($request);

        DB::beginTransaction();

        try {
            $purchase = Purchase::catatPembelian($request);

            DB::commit();

            return redirect()->route('purchase.index')->with('success', 'Pembelian berhasil dibuat.');
        } catch (Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('product');

        return view('purchase.edit', [
            'purchase' => $purchase,
            'suppliers' => Supplier::all(),
            'payment_gateways' => Account::where('is_payment_gateway', 1)->get(),
            'products' => Product::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'subtotal' => 'required',
            'discount' => 'required',
            'taxes' => 'required',
            'expenses' => 'required',
            'grand_total' => 'required',
            'account_id' => 'required|exists:accounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required',
        ]);

        $request = self::cleanRequest($request);

        DB::beginTransaction();

        try {
            $purchase = Purchase::updatePembelian($id, $request);

            DB::commit();

            return redirect()->route('purchase.index')->with('success', 'Pembelian berhasil diupdate.');
        } catch (Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchase.index')->with('success', 'Pembelian berhasil dihapus.');
    }

    public static function cleanCurrencyFormatting($val){
        return (double) str_replace('.', '', $val ?? 0);
    }

    public static function cleanRequest($request)
    {
        $request->request->add([
            'subtotal' => self::cleanCurrencyFormatting($request->subtotal),
            'discount' => self::cleanCurrencyFormatting($request->discount),
            'taxes' => self::cleanCurrencyFormatting($request->taxes),
            'expenses' => self::cleanCurrencyFormatting($request->expenses),
            'grand_total' => self::cleanCurrencyFormatting($request->grand_total),
        ]);

        $items = collect($request->items)->map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'qty'        => $item['qty'],
                'price'      => self::cleanCurrencyFormatting($item['price']),
                'amount'      => self::cleanCurrencyFormatting($item['amount']),
            ];
        })->toArray();

        $request->merge([
            'items' => $items
        ]);

        return $request;
    }
}
