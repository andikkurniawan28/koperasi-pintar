<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LoanTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LoanType::with('account');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('account', function ($row) {
                    return $row->account
                        ? $row->account->code . ' - ' . $row->account->name
                        : '-';
                })

                ->filterColumn('account', function ($query, $keyword) {
                    $query->whereHas('account', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('code', 'like', "%{$keyword}%");
                    });
                })

                ->addColumn('interest', function ($row) {
                    return $row->interest_rate . '% (' . ucfirst($row->interest_type) . ')';
                })

                ->addColumn('tenor', function ($row) {
                    return ($row->tenor_min ?? '-') . ' - ' . ($row->tenor_max ?? '-') . ' bulan';
                })

                ->addColumn('collateral', function ($row) {
                    return $row->requires_collateral
                        ? '<span class="badge bg-warning">Ya</span>'
                        : '<span class="badge bg-secondary">Tidak</span>';
                })

                ->addColumn('action', function ($row) {
                    $editUrl = route('loan_type.edit', $row->id);
                    $deleteUrl = route('loan_type.destroy', $row->id);

                    return '<div class="btn-group">
                                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                                <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>';
                })

                ->rawColumns(['action', 'collateral'])
                ->make(true);
        }

        return view('loan_type.index');
    }

    public function create()
    {
        return view('loan_type.create', [
            'accounts' => Account::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:loan_types,name',
            'account_id' => 'required|exists:accounts,id',
            'interest_rate' => 'required|numeric|min:0',
            'interest_type' => 'required|in:flat,effective',
            'tenor_min' => 'nullable|integer|min:1',
            'tenor_max' => 'nullable|integer|min:1',
            'max_amount' => 'nullable|numeric|min:0',
            'requires_collateral' => 'required|boolean',
        ]);

        LoanType::create($request->all());

        return redirect()->route('loan_type.index')
            ->with('success', 'Jenis Pinjaman berhasil ditambahkan.');
    }

    public function edit(LoanType $loan_type)
    {
        $accounts = Account::all();
        return view('loan_type.edit', compact('loan_type', 'accounts'));
    }

    public function update(Request $request, LoanType $loan_type)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:loan_types,name,' . $loan_type->id,
            'account_id' => 'required|exists:accounts,id',
            'interest_rate' => 'required|numeric|min:0',
            'interest_type' => 'required|in:flat,effective',
            'tenor_min' => 'nullable|integer|min:1',
            'tenor_max' => 'nullable|integer|min:1',
            'max_amount' => 'nullable|numeric|min:0',
            'requires_collateral' => 'required|boolean',
        ]);

        $loan_type->update($request->all());

        return redirect()->route('loan_type.index')
            ->with('success', 'Jenis Pinjaman berhasil diperbarui.');
    }

    public function destroy(LoanType $loan_type)
    {
        $loan_type->delete();

        return redirect()->route('loan_type.index')
            ->with('success', 'Jenis Pinjaman berhasil dihapus.');
    }
}
