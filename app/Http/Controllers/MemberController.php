<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Member::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('member.edit', $row->id);
                    $deleteUrl = route('member.destroy', $row->id);

                    return '<div class="btn-group" role="group">
                                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                                <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('member.index');
    }

    public function create()
    {
        return view('member.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:members,code',
            'name' => 'required|string|max:255|unique:members,name',
            'whatsapp' => 'required|string|max:20|unique:members,whatsapp',
            'description' => 'nullable|string',
        ]);

        Member::create([
            'code' => $request->code,
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'description' => $request->description,
        ]);

        return redirect()->route('member.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member)
    {
        return view('member.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:members,code,' . $member->id,
            'name' => 'required|string|max:255|unique:members,name,' . $member->id,
            'whatsapp' => 'required|string|max:20|unique:members,whatsapp,' . $member->id,
            'description' => 'nullable|string',
        ]);

        $member->update([
            'code' => $request->code,
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'description' => $request->description,
        ]);

        return redirect()->route('member.index')->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('member.index')->with('success', 'Anggota berhasil dihapus.');
    }
}
