<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockAdjustment::with(['product', 'user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product', fn($row) => $row->product->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('product', function ($query, $keyword) {
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('stock_adjustment.edit', $row->id);
                    $showUrl = route('stock_adjustment.show', $row->id);
                    $deleteUrl = route('stock_adjustment.destroy', $row->id);
                    // $recap = route('stock_adjustment.payment_record_per_invoice', $row->id);

                    return '<div class="btn-group">
                                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
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

        return view('stock_adjustment.index');
    }

    public function create()
    {
        return view('stock_adjustment.create', [
            'products' => Product::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'inOut' => 'required|in:in,out',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:1',
            'total'      => 'required'
    ]);

    // 🔥 CLEAN FORMAT ANGKA
    $request->merge([
        'total' => $this->cleanCurrency($request->total)
    ]);

        DB::beginTransaction();

        try {
            StockAdjustment::createData($request);

            DB::commit();
            return redirect()->route('stock_adjustment.index')->with('success', 'Stok Opname berhasil ditambah.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function edit(StockAdjustment $stock_adjustment)
    {
        return view('stock_adjustment.edit', [
            'data' => $stock_adjustment,
            'products' => Product::all()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'inOut' => 'required|in:in,out',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            StockAdjustment::updateData($id, $request);

            DB::commit();
            return redirect()->route('stock_adjustment.index')->with('success', 'Stok Opname berhasil diupdate.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy(StockAdjustment $stock_adjustment)
    {
        StockAdjustment::deleteData($stock_adjustment);

        return redirect()->route('stock_adjustment.index')->with('success', 'Stok Opname berhasil dihapus.');
    }

    public static function cleanCurrency($val){
        return (double) str_replace('.', '', $val ?? 0);
    }
}
