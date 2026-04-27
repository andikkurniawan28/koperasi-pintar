@extends('template.invoice')

@section('transaksi_toko_active', 'active')
@section('invoice_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Faktur Tagihan</strong></h3>
                    {{-- <p class="mb-0">Invoice Penjualan</p> --}}
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $invoice->code }}</h5>
                    <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($invoice->date)->locale('id')->translatedFormat('d F Y') }}</p>
                    <p class="mb-0">Status:
                        <span class="badge bg-label-primary">{{ $invoice->status }}</span>
                    </p>
                </div>
            </div>

            <hr>

            {{-- CUSTOMER --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6><strong>Kepada:</strong></h6>
                    <p class="mb-0">{{ $invoice->customer->name ?? $invoice->member->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Dibuat oleh:</strong></h6>
                    <p class="mb-0">{{ $invoice->user->name }}</p>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="table-responsive mb-4">
                <table class="table table-binvoiceed">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Ket</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->item as $i => $item)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-end">{{ number_format($item->amount,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY --}}
            <div class="row">
                <div class="col-md-6">
                    {{-- <p class="mb-1"><strong>Catatan:</strong></p>
                    <p class="text-muted">Terima kasih telah berbelanja 🙏</p> --}}
                </div>

                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">{{ number_format($invoice->subtotal,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon</th>
                            <td class="text-end">{{ number_format($invoice->discount,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Lain</th>
                            <td class="text-end">{{ number_format($invoice->expenses,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Pajak</th>
                            <td class="text-end">{{ number_format($invoice->taxes,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <th>Grand Total</th>
                            <td class="text-end"><strong>{{ number_format($invoice->grand_total,0,',','.') }}</strong></td>
                        </tr>
                        {{-- <tr>
                            <th>Dibayar</th>
                            <td class="text-end">{{ number_format($invoice->paid,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa</th>
                            <td class="text-end">{{ number_format($invoice->left,0,',','.') }}</td>
                        </tr> --}}
                    </table>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
