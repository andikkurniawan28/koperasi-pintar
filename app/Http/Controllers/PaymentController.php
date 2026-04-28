<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::with(['customer', 'user', 'invoice', 'member'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
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
                    $showUrl = route('payment.show', $row->id);
                    $deleteUrl = route('payment.destroy', $row->id);

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

        $invoice = Invoice::findOrFail($request->invoice_id);
        if($request->total > $invoice->left)
        {
            return redirect()->back()->with('error', 'Data tidak valid!!');
        }

        DB::beginTransaction();

        try {
            Payment::createData($request);
            DB::commit();
            return redirect()->route('payment.index')->with('success','Pelunasan Tagihan berhasil');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        Payment::deleteData($payment);

        return back()->with('success','Pelunasan Tagihan berhasil dihapus');
    }

    private static function clean($val){
        return (double) str_replace('.', '', $val ?? 0);
    }

    public function edit(Payment $payment)
    {
        return view('payment.edit', [
            'payment'  => $payment,
            // 'invoices' => Invoice::all(), // boleh difilter kalau mau
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function update(Request $request, $id)
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

        $invoice = Invoice::findOrFail($request->invoice_id);
        if($request->total > $invoice->left)
        {
            return redirect()->back()->with('error', 'Data tidak valid!!');
        }

        DB::beginTransaction();

        try {
            $payment = Payment::findOrFail($id);
            $invoice = $payment->invoice;

            // =========================
            // UPDATE PAYMENT
            // =========================
            $payment->update([
                'date'       => $request->date,
                'invoice_id' => $request->invoice_id,
                'total'      => $request->total,
                'account_id' => $request->account_id,
            ]);

            // =========================
            // UPDATE ULANG INVOICE
            // =========================
            $invoice = Invoice::findOrFail($request->invoice_id);

            $paid = Payment::where('invoice_id', $invoice->id)->sum('total');
            $left = $invoice->grand_total - $paid;

            if ($paid == 0) {
                $status = 'Belum Bayar';
            } elseif ($left <= 0) {
                $status = 'Lunas';
                $left = 0;
            } else {
                $status = 'DP';
            }

            $invoice->update([
                'paid'   => $paid,
                'left'   => $left,
                'status' => $status
            ]);

            // =========================
            // RESET LEDGER PAYMENT
            // =========================
            Ledger::where('payment_id', $payment->id)->delete();
            Ledger::catatPembayaran($payment);

            DB::commit();

            return redirect()->route('payment.index')->with('success','Pelunasan Tagihan berhasil diupdate');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }
}
