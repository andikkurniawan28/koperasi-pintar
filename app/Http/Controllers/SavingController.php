<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SavingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Saving::with(['member','savingType', 'user'])->where('direction', 'in')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
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
                    $editUrl = route('saving.edit', $row->id);
                    $showUrl = route('saving.show', $row->id);
                    $deleteUrl = route('saving.destroy', $row->id);

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

        return view('saving.index');
    }

    public function show(Saving $saving)
    {
        $saving->load(['user', 'member', 'account', 'savingType']);
        return view('saving.show', compact('saving'));
    }

    public function create()
    {
        return view('saving.create', [
            'members' => Member::all(),
            'types'   => SavingType::all(),
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
            $saving = Saving::createData($request);

            DB::commit();
            return redirect()->route('saving.index')->with('success','Setoran Simpanan berhasil disimpan');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function edit(Saving $saving)
    {
        return view('saving.edit', [
            'saving' => $saving,
            'members' => Member::all(),
            'types'   => SavingType::all(),
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
            $saving = Saving::updateData($id, $request);

            DB::commit();
            return redirect()->route('saving.index')->with('success','Setoran Simpanan berhasil diupdate');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy(Saving $saving)
    {
        $saving->delete();

        return back()->with('success','Setoran Simpanan berhasil dihapus');
    }

    private static function clean($val){
        return (double) str_replace('.', '', $val ?? 0);
    }
}
