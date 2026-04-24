@extends('template.invoice')

@section('transaksi_toko_active', 'active')
@section('stock_adjustment_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Faktur Stok Opname</strong></h3>
                    {{-- <p class="mb-0">Invoice Stok Opname</p> --}}
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $stock_adjustment->code }}</h5>
                    <p class="mb-0">Tanggal: {{ $stock_adjustment->date }}</p>
                    {{-- <p class="mb-0">Status:
                        <span class="badge bg-label-primary">{{ $stock_adjustment->status }}</span>
                    </p> --}}
                </div>
            </div>

            <hr>

            {{-- CUSTOMER --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    {{-- <h6><strong>Kepada:</strong></h6>
                    <p class="mb-0">{{ $stock_adjustment->customer->name ?? $stock_adjustment->member->name }}</p> --}}
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Dibuat oleh:</strong></h6>
                    <p class="mb-0">{{ $stock_adjustment->user->name }}</p>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="table-responsive mb-4">
                <table class="table table-bstock_adjustmented">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach($stock_adjustment->product as $i => $stock_adjustment) --}}
                        <tr>
                            <td>1</td>
                            {{-- <td>
                                {{ $stock_adjustment->product->productCategory->name }} -
                                {{ $stock_adjustment->product->name }}
                                <br>
                                <small class="text-muted">
                                    {{ $stock_adjustment->product->packaging->name ?? '-' }}
                                </small>
                            </td> --}}
                            <td>{{ $stock_adjustment->inOut }}</td>
                            <td>{{ $stock_adjustment->product->name }}</td>
                            <td>{{ $stock_adjustment->qty }}</td>
                            <td>{{ number_format($stock_adjustment->price,0,',','.') }}</td>
                            <td>{{ number_format($stock_adjustment->total,0,',','.') }}</td>
                        </tr>
                        {{-- @endforeach --}}
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY --}}
            {{-- <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Catatan:</strong></p>
                    <p class="text-muted">Terima kasih telah berbelanja 🙏</p>
                </div>

                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">{{ number_format($stock_adjustment->subtotal,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon</th>
                            <td class="text-end">{{ number_format($stock_adjustment->discount,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Lain</th>
                            <td class="text-end">{{ number_format($stock_adjustment->expenses,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Pajak</th>
                            <td class="text-end">{{ number_format($stock_adjustment->taxes,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <th>Grand Total</th>
                            <td class="text-end"><strong>{{ number_format($stock_adjustment->grand_total,0,',','.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div> --}}

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('stock_adjustment.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
