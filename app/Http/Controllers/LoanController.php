<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Member;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Loan::with(['member','loanType','user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('member', fn($row) => $row->member->name ?? '-')
                ->addColumn('type', fn($row) => $row->loanType->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->addColumn('status', function ($row) {
                    return match($row->status) {
                        'ongoing' => '<span class="badge bg-warning">Berjalan</span>',
                        'paid_off' => '<span class="badge bg-success">Lunas</span>',
                        'default' => '<span class="badge bg-danger">Macet</span>',
                        default => '-'
                    };
                })
                ->filterColumn('type', function ($query, $keyword) {
                    $query->whereHas('loanType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('member', function ($query, $keyword) {
                    $query->whereHas('member', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('loan.show', $row->id);
                    $deleteUrl = route('loan.destroy', $row->id);

                    return '<div class="btn-group">
                        <a href="'.$showUrl.'" class="btn btn-sm btn-info">Tampil</a>
                        <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Hapus?\')" style="display:inline-block;">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view('loan.index');
    }

    public function show(Loan $loan)
    {
        $loan->load(['user','member','loanType']);
        return view('loan.show', compact('loan'));
    }

    public function create()
    {
        return view('loan.create', [
            'members' => Member::all(),
            'types'   => LoanType::all(),
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'principal' => 'required',
            'tenor' => 'required|integer|min:1',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $request->merge([
            'principal' => self::clean($request->principal)
        ]);

        DB::beginTransaction();

        try {
            $loan = Loan::createData($request);

            DB::commit();
            return redirect()->route('loan.index')->with('success','Pinjaman berhasil dibuat');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function edit(Loan $loan)
    {
        return view('loan.edit', [
            'loan' => $loan,
            'members' => Member::all(),
            'types'   => LoanType::all(),
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'principal' => 'required',
            'tenor' => 'required|integer|min:1'
        ]);

        $request->merge([
            'principal' => self::clean($request->principal)
        ]);

        DB::beginTransaction();

        try {
            $loan = Loan::updateData($id, $request);

            DB::commit();
            return redirect()->route('loan.index')->with('success','Pinjaman berhasil diupdate');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();

        return back()->with('success','Pinjaman berhasil dihapus');
    }

    private static function clean($val){
        return (double) str_replace('.', '', $val ?? 0);
    }
}
