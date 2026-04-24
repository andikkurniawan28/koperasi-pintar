@extends('template.invoice')

@section('transaksi_toko_active', 'active')
@section('purchase_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Faktur Pembelian</strong></h3>
                    {{-- <p class="mb-0">Invoice Pembelian</p> --}}
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $purchase->code }}</h5>
                    <p class="mb-0">Tanggal: {{ $purchase->date }}</p>
                    {{-- <p class="mb-0">Status:
                        <span class="badge bg-label-primary">{{ $purchase->status }}</span>
                    </p> --}}
                </div>
            </div>

            <hr>

            {{-- CUSTOMER --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6><strong>Dari:</strong></h6>
                    <p class="mb-0">{{ $purchase->supplier->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Dibuat oleh:</strong></h6>
                    <p class="mb-0">{{ $purchase->user->name }}</p>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="table-responsive mb-4">
                <table class="table table-bpurchaseed">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->product as $i => $item)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            {{-- <td>
                                {{ $item->product->productCategory->name }} -
                                {{ $item->product->name }}
                                <br>
                                <small class="text-muted">
                                    {{ $item->product->packaging->name ?? '-' }}
                                </small>
                            </td> --}}
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ number_format($item->price,0,',','.') }}</td>
                            <td>{{ number_format($item->amount,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY --}}
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Catatan:</strong></p>
                    <p class="text-muted">Terima kasih telah berbelanja 🙏</p>
                </div>

                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">{{ number_format($purchase->subtotal,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon</th>
                            <td class="text-end">{{ number_format($purchase->discount,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Lain</th>
                            <td class="text-end">{{ number_format($purchase->expenses,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Pajak</th>
                            <td class="text-end">{{ number_format($purchase->taxes,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <th>Grand Total</th>
                            <td class="text-end"><strong>{{ number_format($purchase->grand_total,0,',','.') }}</strong></td>
                        </tr>
                        {{-- <tr>
                            <th>Dibayar</th>
                            <td class="text-end">{{ number_format($purchase->paid,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa</th>
                            <td class="text-end">{{ number_format($purchase->left,0,',','.') }}</td>
                        </tr> --}}
                    </table>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('purchase.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
