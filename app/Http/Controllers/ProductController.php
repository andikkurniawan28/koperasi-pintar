<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('product.edit', $row->id);
                    $deleteUrl = route('product.destroy', $row->id);

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

        return view('product.index');
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:255|unique:products,barcode',
            'name' => 'required|string|max:255',
            'buy_price' => 'required|numeric|min:0',
            'price_for_member' => 'required|numeric|min:0',
            'price_for_customer' => 'required|numeric|min:0',
            'minimum_alert' => 'required|integer|min:0',
        ]);

        Product::create([
            'barcode' => $request->barcode,
            'name' => $request->name,
            'buy_price' => $request->buy_price,
            'price_for_member' => $request->price_for_member,
            'price_for_customer' => $request->price_for_customer,
            'minimum_alert' => $request->minimum_alert,
        ]);

        return redirect()->route('product.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'barcode' => 'required|string|max:255|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'buy_price' => 'required|numeric|min:0',
            'price_for_member' => 'required|numeric|min:0',
            'price_for_customer' => 'required|numeric|min:0',
            'minimum_alert' => 'required|integer|min:0',
        ]);

        $product->update([
            'barcode' => $request->barcode,
            'name' => $request->name,
            'buy_price' => $request->buy_price,
            'price_for_member' => $request->price_for_member,
            'price_for_customer' => $request->price_for_customer,
            'minimum_alert' => $request->minimum_alert,
        ]);

        return redirect()->route('product.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Produk berhasil dihapus.');
    }
}
