<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesRequest;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Member;
use App\Models\Product;
use App\Models\Sales;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Sales::with(['customer', 'user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('member', fn($row) => $row->member->name ?? '-')
                ->addColumn('customer', fn($row) => $row->customer->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('member', function ($query, $keyword) {
                    $query->whereHas('member', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->whereHas('customer', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('sales.edit', $row->id);
                    $showUrl = route('sales.show', $row->id);
                    $deleteUrl = route('sales.destroy', $row->id);
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

        return view('sales.index');
    }

    public function show(Sales $sales)
    {
        $sales->load(['customer', 'user', 'member', 'product']);
        return view('sales.show', compact('sales'));
    }

    public function create()
    {
        return view('sales.create', [
            'customers' => Customer::all(),
            'members' => Member::all(),
            'payment_gateways' => Account::where('is_payment_gateway', 1)->get(),
            'products' => Product::all(),
        ]);
    }

    public function store(StoreSalesRequest  $request)
    {
        $request = self::cleanRequest($request);
        DB::beginTransaction();
        try {
            $sales = Sales::catatPenjualan($request);
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dibuat.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Sales $sales)
    {
        return view('sales.edit', [
            'sales' => $sales,
            'customers' => Customer::all(),
            'members' => Member::all(),
            'payment_gateways' => Account::where('is_payment_gateway', 1)->get(),
            'products' => Product::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:member,customer',
            'customer_id' => 'nullable|exists:customers,id',
            'member_id' => 'nullable|exists:members,id',
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
            $sales = Sales::updatePenjualan($id, $request);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diupdate.');
        } catch (Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Sales $sales)
    {
        $sales->delete();

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
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
