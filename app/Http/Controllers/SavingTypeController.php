<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SavingTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SavingType::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('account', fn($row) => $row->account->code .' - '. $row->account->name ?? '-')
                ->filterColumn('account', function ($query, $keyword) {
                    $query->whereHas('account', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('account', function ($query, $keyword) {
                    $query->whereHas('account', function ($q) use ($keyword) {
                        $q->where('code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('is_withdrawable', function ($row) {
                    return $row->is_withdrawable
                        ? '<span class="badge bg-success">Ya</span>'
                        : '<span class="badge bg-danger">Tidak</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('saving_type.edit', $row->id);
                    $deleteUrl = route('saving_type.destroy', $row->id);

                    return '<div class="btn-group" role="group">
                                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                                <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>';
                })
                ->rawColumns(['action', 'is_withdrawable'])
                ->make(true);
        }

        return view('saving_type.index');
    }

    public function create()
    {
        return view('saving_type.create', [
            'accounts' => Account::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:saving_types,name',
            'account_id' => 'required|exists:accounts,id',
            'is_withdrawable' => 'required',
        ]);

        SavingType::create([
            'name' => $request->name,
            'account_id' => $request->account_id,
            'is_withdrawable' => $request->is_withdrawable,
        ]);

        return redirect()->route('saving_type.index')->with('success', 'Jenis Simpanan berhasil ditambahkan.');
    }

    public function edit(SavingType $saving_type)
    {
        $accounts = Account::all();
        return view('saving_type.edit', compact('saving_type', 'accounts'));
    }

    public function update(Request $request, SavingType $saving_type)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:saving_types,name,' . $saving_type->id,
            'account_id' => 'required|exists:accounts,id',
            'is_withdrawable' => 'required',
        ]);

        $saving_type->update([
            'name' => $request->name,
            'account_id' => $request->account_id,
            'is_withdrawable' => $request->is_withdrawable,
        ]);

        return redirect()->route('saving_type.index')->with('success', 'Jenis Simpanan berhasil diperbarui.');
    }

    public function destroy(SavingType $saving_type)
    {
        $saving_type->delete();

        return redirect()->route('saving_type.index')->with('success', 'Jenis Simpanan berhasil dihapus.');
    }
}
