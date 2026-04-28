<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ActivityLog::with(['user'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)
                        ->locale('id')
                        ->translatedFormat('d F Y H:i:s');
                })
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->filterColumn('member', function ($query, $keyword) {
                    $query->whereHas('member', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->make(true);
        }

        return view('activity_log.index');
    }
}
