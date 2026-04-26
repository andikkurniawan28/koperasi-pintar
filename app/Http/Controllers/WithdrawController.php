<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Member;
use App\Models\Withdraw;
use App\Models\SavingType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Withdraw::with(['member','savingType', 'user'])->where('direction', 'out')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('member', fn($row) => $row->member->name ?? '-')
                ->addColumn('type', fn($row) => $row->savingType->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('type', function ($query, $keyword) {
                    $query->whereHas('savingType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('member', function ($query, $keyword) {
                    $query->whereHas('member', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('withdraw.edit', $row->id);
                    $showUrl = route('withdraw.show', $row->id);
                    $deleteUrl = route('withdraw.destroy', $row->id);

                    return '<div class="btn-group">
                        <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a>
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

        return view('withdraw.index');
    }

    public function show(Withdraw $withdraw)
    {
        $withdraw->load(['user', 'member', 'account', 'savingType']);
        return view('withdraw.show', compact('withdraw'));
    }

    public function create()
    {
        return view('withdraw.create', [
            'members' => Member::all(),
            'types'   => SavingType::where('is_withdrawable', 1)->get(),
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'member_id' => 'required|exists:members,id',
            'saving_type_id' => 'required|exists:saving_types,id',
            'total' => 'required'
        ]);

        $request->merge([
            'total' => self::clean($request->total)
        ]);

        DB::beginTransaction();

        try {
            $withdraw = Withdraw::createData($request);

            DB::commit();
            return redirect()->route('withdraw.index')->with('success','Penarikan Simpanan berhasil disimpan');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function edit(Withdraw $withdraw)
    {
        return view('withdraw.edit', [
            'withdraw' => $withdraw,
            'members' => Member::all(),
            'types'   => SavingType::where('is_withdrawable', 1)->get(),
            'accounts' => Account::where('is_payment_gateway',1)->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'member_id' => 'required|exists:members,id',
            'saving_type_id' => 'required|exists:saving_types,id',
            'total' => 'required'
        ]);

        $request->merge([
            'total' => self::clean($request->total)
        ]);

        DB::beginTransaction();

        try {
            $withdraw = Withdraw::updateData($id, $request);

            DB::commit();
            return redirect()->route('withdraw.index')->with('success','Penarikan Simpanan berhasil diupdate');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy(Withdraw $withdraw)
    {
        $withdraw->delete();

        return back()->with('success','Penarikan Simpanan berhasil dihapus');
    }

    private static function clean($val){
        return (double) str_replace('.', '', $val ?? 0);
    }
}
