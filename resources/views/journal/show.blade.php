@extends('template.invoice')

@section('transaksi_akuntansi_active', 'active')
@section('journal_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Jurnal Umum</strong></h3>
                    {{-- <p class="mb-0">Invoice Jurnal Umum</p> --}}
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $journal->code }}</h5>
                    <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($journal->date)->locale('id')->translatedFormat('d F Y') }}</p>
                    {{-- <p class="mb-0">Status:
                        <span class="badge bg-label-primary">{{ $journal->status }}</span>
                    </p> --}}
                </div>
            </div>

            <hr>

            {{-- CUSTOMER --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    {{-- <h6><strong>Kepada:</strong></h6>
                    <p class="mb-0">{{ $journal->customer->name ?? $journal->member->name }}</p> --}}
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Dibuat oleh:</strong></h6>
                    <p class="mb-0">{{ $journal->user->name }}</p>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="table-responsive mb-4">
                <table class="table table-bjournaled">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Akun</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journal->entry as $i => $item)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $item->account->code }} - {{ $item->account->name }}</td>
                            <td class="text-end">{{ number_format($item->debit,0,',','.') }}</td>
                            <td class="text-end">{{ number_format($item->credit,0,',','.') }}</td>
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
                    {{-- <table class="table">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">{{ number_format($journal->subtotal,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon</th>
                            <td class="text-end">{{ number_format($journal->discount,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Lain</th>
                            <td class="text-end">{{ number_format($journal->expenses,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Pajak</th>
                            <td class="text-end">{{ number_format($journal->taxes,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <th>Grand Total</th>
                            <td class="text-end"><strong>{{ number_format($journal->grand_total,0,',','.') }}</strong></td>
                        </tr>
                        {{-- <tr>
                            <th>Dibayar</th>
                            <td class="text-end">{{ number_format($journal->paid,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa</th>
                            <td class="text-end">{{ number_format($journal->left,0,',','.') }}</td>
                        </tr> --}}
                    </table> --}}
                </div>
            </div>

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('journal.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
