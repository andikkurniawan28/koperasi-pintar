@extends('template.invoice')

@section('transaksi_toko_active', 'active')
@section('sales_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Faktur Penjualan</strong></h3>
                    {{-- <p class="mb-0">Invoice Penjualan</p> --}}
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $sales->code }}</h5>
                    <p class="mb-0">Tanggal: {{ $sales->date }}</p>
                    {{-- <p class="mb-0">Status:
                        <span class="badge bg-label-primary">{{ $sales->status }}</span>
                    </p> --}}
                </div>
            </div>

            <hr>

            {{-- CUSTOMER --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6><strong>Kepada:</strong></h6>
                    <p class="mb-0">{{ $sales->customer->name ?? $sales->member->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Dibuat oleh:</strong></h6>
                    <p class="mb-0">{{ $sales->user->name }}</p>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="table-responsive mb-4">
                <table class="table table-bsalesed">
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
                        @foreach($sales->product as $i => $item)
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
                            <td class="text-end">{{ number_format($sales->subtotal,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon</th>
                            <td class="text-end">{{ number_format($sales->discount,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Lain</th>
                            <td class="text-end">{{ number_format($sales->expenses,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Pajak</th>
                            <td class="text-end">{{ number_format($sales->taxes,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <th>Grand Total</th>
                            <td class="text-end"><strong>{{ number_format($sales->grand_total,0,',','.') }}</strong></td>
                        </tr>
                        {{-- <tr>
                            <th>Dibayar</th>
                            <td class="text-end">{{ number_format($sales->paid,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa</th>
                            <td class="text-end">{{ number_format($sales->left,0,',','.') }}</td>
                        </tr> --}}
                    </table>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
