@extends('template.invoice')

@section('transaksi_simpan_pinjam_active', 'active')
@section('saving_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="mb-1"><strong>Setoran Simpanan</strong></h3>
                    {{-- <p class="mb-0">Bukti Pembayaran</p> --}}
                </div>
                <div class="text-end">
                    <h5 class="mb-1">#{{ $saving->code }}</h5>
                    <p class="mb-0">Tanggal: {{ $saving->date }}</p>
                </div>
            </div>

            <hr>

            {{-- INFO --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6><strong>Dari:</strong></h6>
                    <p class="mb-0">{{ $saving->member->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6><strong>Diterima oleh:</strong></h6>
                    <p class="mb-0">{{ $saving->user->name }}</p>
                </div>
            </div>

            {{-- DETAIL --}}
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Jenis</th>
                        <td>{{ $saving->savingType->name }}</td>
                    </tr>
                    <tr>
                        <th>Total Setoran</th>
                        <td><strong>{{ number_format($saving->total,0,',','.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Setoran Lewat</th>
                        <td>{{ $saving->account->code }}-{{ $saving->account->name }}</td>
                    </tr>
                </table>
            </div>

            {{-- TERBILANG --}}
            {{-- <div class="mb-4">
                <p class="mb-1"><strong>Terbilang:</strong></p>
                <p class="text-muted" id="terbilang-text"></p>
            </div> --}}

            {{-- FOOTER --}}
            <div class="row mt-5">
                <div class="col-md-6">
                    <p class="text-muted">Terima kasih atas setoran Anda.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>{{ $saving->date }}</p>
                    <br><br>
                    <p><strong>{{ $saving->user->name }}</strong></p>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print
                </button>
                <a href="{{ route('saving.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/terbilang@1.0.0/terbilang.min.js"></script>
@endsection
