@extends('template.invoice')

@section('transaksi_simpan_pinjam_active', 'active')
@section('loan_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Pinjaman</strong></h3>
                    <p class="mb-0">Bukti Pencairan</p>
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $loan->code }}</h5>
                    <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($loan->date)->locale('id')->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            <hr>

            {{-- INFO --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6><strong>Anggota:</strong></h6>
                    <p class="mb-0">{{ $loan->member->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Diproses oleh:</strong></h6>
                    <p class="mb-0">{{ $loan->user->name }}</p>
                </div>
            </div>

            {{-- DETAIL --}}
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Jenis Pinjaman</th>
                        <td>{{ $loan->loanType->name }}</td>
                    </tr>
                    <tr>
                        <th>Nominal Pinjaman</th>
                        <td><strong>{{ number_format($loan->principal,0,',','.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Bunga</th>
                        <td>{{ $loan->interest_rate }}% ({{ ucfirst($loan->loanType->interest_type) }})</td>
                    </tr>
                    <tr>
                        <th>Tenor</th>
                        <td>{{ $loan->tenor }} bulan</td>
                    </tr>
                    <tr>
                        <th>Total Bunga</th>
                        <td>{{ number_format($loan->total_interest,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Kewajiban</th>
                        <td><strong>{{ number_format($loan->total_amount,0,',','.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Cicilan / Bulan</th>
                        <td>{{ number_format($loan->installment,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <th>Pencairan Lewat</th>
                        <td>{{ $loan->account->code }} - {{ $loan->account->name }}</td>
                    </tr>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="row mt-5">
                <div class="col-md-6">
                    <p class="text-muted">Terima kasih. Harap melakukan pembayaran sesuai jadwal.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>{{ \Carbon\Carbon::parse($loan->date)->locale('id')->translatedFormat('d F Y') }}</p>
                    <br><br>
                    <p><strong>{{ $loan->user->name }}</strong></p>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('loan.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
