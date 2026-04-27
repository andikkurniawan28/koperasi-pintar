<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Installment;
use App\Models\Loan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class InstallmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Installment::with(['loan','member','user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('loan', fn($row) => $row->loan->code ?? '-')
                ->addColumn('member', fn($row) => $row->member->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->addColumn('total', fn($row) => number_format($row->total,0,',','.'))
                ->addColumn('action', function ($row) {
                    $editUrl = route('installment.edit', $row->id);
                    $showUrl = route('installment.show', $row->id);
                    $deleteUrl = route('installment.destroy', $row->id);

                    return '<div class="btn-group">
                        <a href="'.$showUrl.'" class="btn btn-sm btn-info">Tampil</a>
                        <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Hapus?\')" style="display:inline-block;">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('installment.index');
    }

    public function create()
    {
        return view('installment.create', [
            'loans'    => Loan::where('status','ongoing')->get(),
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'loan_id'    => 'required|exists:loans,id',
            'account_id' => 'required|exists:accounts,id',
        ]);

        DB::beginTransaction();

        try {
            Installment::createData($request);

            DB::commit();
            return redirect()->route('installment.index')
                ->with('success','Pembayaran berhasil disimpan');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Installment $installment)
    {
        $installment->load(['loan','member','user','account']);
        return view('installment.show', compact('installment'));
    }

    public function edit(Installment $installment)
    {
        return view('installment.edit', [
            'installment' => $installment,
            'loans'       => Loan::all(),
            'accounts'    => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function update(Request $request, Installment $installment)
    {
        // ⚠️ disarankan TIDAK boleh edit installment
        return back()->with('error', 'Edit angsuran tidak disarankan. Hapus & input ulang.');
    }

    public function destroy(Installment $installment)
    {
        $installment->delete();
        return back()->with('success','Pelunasan pinjaman berhasil dihapus');
    }
}
