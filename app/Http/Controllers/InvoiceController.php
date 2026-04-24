<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Member;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Invoice::with(['customer', 'user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
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
                    $editUrl = route('invoice.edit', $row->id);
                    $showUrl = route('invoice.show', $row->id);
                    $deleteUrl = route('invoice.destroy', $row->id);
                    // $recap = route('invoice.payment_record_per_invoice', $row->id);

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

        return view('invoice.index');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'user', 'member', 'item']);
        return view('invoice.show', compact('invoice'));
    }

    public function create()
    {
        return view('invoice.create', [
            'customers' => Customer::all(),
            'members' => Member::all(),
            'payment_gateways' => Account::where('is_payment_gateway', 1)->get(),
        ]);
    }

    public function store(Request $request)
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
            'items' => 'required|array',
            'items.*.name' => 'required',
            'items.*.description' => 'required',
            'items.*.amount' => 'required',
        ]);

        $request = self::cleanRequest($request);

        DB::beginTransaction();

        try {
            $invoice = Invoice::catatTagihan($request);

            DB::commit();

            return redirect()->route('invoice.index')->with('success', 'Tagihan berhasil dibuat.');
        } catch (Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('item');

        return view('invoice.edit', [
            'invoice' => $invoice,
            'customers' => Customer::all(),
            'members' => Member::all(),
            'payment_gateways' => Account::where('is_payment_gateway', 1)->get(),
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
            'items.*.name' => 'required',
            'items.*.description' => 'required',
            'items.*.amount' => 'required',
        ]);

        $request = self::cleanRequest($request);

        DB::beginTransaction();

        try {
            $invoice = Invoice::updateTagihan($id, $request);

            DB::commit();

            return redirect()->route('invoice.index')->with('success', 'Tagihan berhasil diupdate.');
        } catch (Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoice.index')->with('success', 'Tagihan berhasil dihapus.');
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
            'paid' => self::cleanCurrencyFormatting($request->paid),
            'left' => self::cleanCurrencyFormatting($request->left),
        ]);

        $items = collect($request->items)->map(function ($item) {
            return [
                'name'              => $item['name'],
                'description'       => $item['description'],
                'amount'            => self::cleanCurrencyFormatting($item['amount']),
            ];
        })->toArray();

        $request->merge([
            'items' => $items
        ]);

        return $request;
    }
}
