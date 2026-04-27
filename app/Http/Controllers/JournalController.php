<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Journal::with(['entry', 'user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('member', function ($query, $keyword) {
                    $query->whereHas('member', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('journal.edit', $row->id);
                    $showUrl = route('journal.show', $row->id);
                    $deleteUrl = route('journal.destroy', $row->id);
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

        return view('journal.index');
    }

    public function show(Journal $journal)
    {
        return view('journal.show', compact('journal'));
    }

    public function create()
    {
        return view('journal.create', [
            'accounts' => Account::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request = self::cleanRequest($request);
        DB::beginTransaction();
        try {
            $journal = Journal::catatJurnal($request);
            DB::commit();
            return redirect()->route('journal.index')->with('success', 'Jurnal berhasil dibuat.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Journal $journal)
    {
        $journal->delete();

        return redirect()->route('journal.index')->with('success', 'Jurnal berhasil dihapus.');
    }

    public static function cleanCurrencyFormatting($val){
        return (double) str_replace('.', '', $val ?? 0);
    }

    public static function cleanRequest($request)
    {
        $items = collect($request->items)->map(function ($item) {
            return [
                'account_id' => $item['account_id'],
                'debit'      => self::cleanCurrencyFormatting($item['debit']),
                'credit'     => self::cleanCurrencyFormatting($item['credit']),
            ];
        })->toArray();

        $request->merge([
            'items' => $items
        ]);

        return $request;
    }
}
