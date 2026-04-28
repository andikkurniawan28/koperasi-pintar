@extends('template.master')

@section('home_active', 'active')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

```
<div class="row">

    {{-- HERO --}}
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="d-flex align-items-center row h-100">
                <div class="col-md-8">
                    <div class="card-body">
                        <h4 class="card-title text-primary mb-3">Hallo {{ auth()->user()->name }} 👋</h4>
                        <p class="mb-4 fs-5" id="hero-text">Memuat data koperasi...</p>

                        <a href="{{ route('loan.index') }}" class="btn btn-primary">
                            <i class="bx bx-money"></i> Lihat Pinjaman
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="/fms/public/sneat/assets/img/illustrations/man-with-laptop-light.png" height="150"/>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI RIGHT --}}
    <div class="col-lg-4">
        <div class="row">

            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="text-muted">Total Simpanan</span>
                        <h3 class="mt-2 mb-1" id="total_savings">Rp 0</h3>
                        <small class="text-success">Dana anggota terkumpul</small>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="text-muted">Pinjaman Berjalan</span>
                        <h3 class="mt-2 mb-1" id="outstanding_loan">Rp 0</h3>
                        <small class="text-muted">Total outstanding</small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ROW 2 --}}
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <span class="text-muted">Cash Masuk</span>
                <h4 class="mt-2" id="cash_in">Rp 0</h4>
                <small class="text-success">Bulan ini</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <span class="text-muted">Cash Keluar</span>
                <h4 class="mt-2" id="cash_out">Rp 0</h4>
                <small class="text-danger">Bulan ini</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <span class="text-muted">Pinjaman Cair</span>
                <h4 class="mt-2" id="loan_disbursed">Rp 0</h4>
                <small class="text-muted">Bulan ini</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <span class="text-muted">Angsuran Masuk</span>
                <h4 class="mt-2" id="installments">Rp 0</h4>
                <small class="text-primary">Pembayaran anggota</small>
            </div>
        </div>
    </div>

    {{-- ROW 3 --}}
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">Ringkasan Keuangan</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span>Cash Masuk</span>
                    <strong id="summary_cash_in">Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Cash Keluar</span>
                    <strong id="summary_cash_out">Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Net Cash</span>
                    <strong id="net_cash">Rp 0</strong>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">Kesehatan Pinjaman</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span>Total Pinjaman</span>
                    <strong id="total_loan">Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Kredit Macet (NPL)</span>
                    <strong id="bad_loan">Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Rasio NPL</span>
                    <strong id="npl_ratio">0%</strong>
                </div>

            </div>
        </div>
    </div>

</div>
```

</div>
@endsection

@section('script')

<script>
$(function () {

    function rupiah(val) {
        return new Intl.NumberFormat('id-ID').format(val || 0);
    }

    $.get("{{ route('dashboard') }}")
    .done(function(res) {

        let s = res.summary || {};

        $('#hero-text').html(`
            Cash masuk bulan ini <b>Rp ${rupiah(s.cash_in)}</b><br>
            Pinjaman cair <b>Rp ${rupiah(s.loan_disbursed)}</b>
        `);

        $('#total_savings').text('Rp ' + rupiah(s.total_savings));
        $('#outstanding_loan').text('Rp ' + rupiah(s.outstanding_loan));

        $('#cash_in').text('Rp ' + rupiah(s.cash_in));
        $('#cash_out').text('Rp ' + rupiah(s.cash_out));
        $('#loan_disbursed').text('Rp ' + rupiah(s.loan_disbursed));
        $('#installments').text('Rp ' + rupiah(s.installments));

        $('#summary_cash_in').text('Rp ' + rupiah(s.cash_in));
        $('#summary_cash_out').text('Rp ' + rupiah(s.cash_out));
        $('#net_cash').text('Rp ' + rupiah(s.net_cash));

        $('#total_loan').text('Rp ' + rupiah(s.outstanding_loan));
        $('#bad_loan').text('Rp ' + rupiah(s.bad_loan));

        let npl = 0;
        if (s.outstanding_loan > 0) {
            npl = (s.bad_loan / s.outstanding_loan * 100).toFixed(2);
        }
        $('#npl_ratio').text(npl + '%');

    });

});
</script>

@endsection
