<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::with(['customer', 'user', 'invoice', 'member'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice', fn($row) => $row->invoice->code ?? '-')
                ->addColumn('member', fn($row) => $row->member->name ?? '-')
                ->addColumn('customer', fn($row) => $row->customer->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('invoice', function ($query, $keyword) {
                    $query->whereHas('invoice', function ($q) use ($keyword) {
                        $q->where('invoice', 'code', "%{$keyword}%");
                    });
                })
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
                    // $editUrl = route('payment.edit', $row->id);
                    $showUrl = route('payment.show', $row->id);
                    $deleteUrl = route('payment.destroy', $row->id);
                    // $recap = route('invoice.payment_record_per_invoice', $row->id);

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

        return view('payment.index');
    }

    public function show(Payment $payment)
    {
        $payment->load(['customer', 'user', 'member', 'invoice', 'account']);
        return view('payment.show', compact('payment'));
    }

    public function create()
    {
        return view('payment.create', [
            'invoices' => Invoice::where('left','>',0)->get(),
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'invoice_id' => 'required|exists:invoices,id',
            'total' => 'required',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $request->merge([
            'total' => self::clean($request->total)
        ]);

        DB::beginTransaction();

        try {
            Payment::createData($request);

            DB::commit();
            return redirect()->route('payment.index')->with('success','Pembayaran berhasil');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        Payment::deleteData($payment);

        return back()->with('success','Pembayaran dihapus');
    }

    private static function clean($val){
        return (double) str_replace('.', '', $val ?? 0);
    }
}
