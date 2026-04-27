@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('installment_active', 'active')

@section('content')
<div class="container-xxl container-p-y">
    <h1 class="h3 mb-3"><strong>Tambah Pelunasan Pinjaman</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('installment.store') }}" method="POST">
                @csrf

                {{-- Tanggal --}}
                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                {{-- Loan --}}
                <div class="mb-3">
                    <label>Pilih Pinjaman</label>
                    <select name="loan_id" id="loan_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach($loans as $l)
                            <option value="{{ $l->id }}"
                                data-member="{{ $l->member->name }}"
                                data-remaining="{{ $l->remaining_balance }}"
                                data-installment="{{ $l->installment }}">
                                {{ $l->code }} - {{ $l->member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info --}}
                <div class="mb-3">
                    <label>Anggota</label>
                    <input type="text" id="member" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Sisa Hutang</label>
                    <input type="text" id="remaining" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Cicilan / Bulan</label>
                    <input type="text" id="installment" class="form-control" readonly>
                </div>

                {{-- Denda --}}
                <div class="mb-3">
                    <label>Denda</label>
                    <input type="text" name="penalty" id="penalty" class="form-control" value="0">
                </div>

                {{-- Akun --}}
                <div class="mb-3">
                    <label>Pembayaran Lewat</label>
                    <select name="account_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka || 0);
}

function parseRupiah(str) {
    return parseFloat((str || '').replace(/\./g, '')) || 0;
}

// isi otomatis saat pilih loan
$('#loan_id').on('change', function() {
    let selected = $(this).find(':selected');

    let member = selected.data('member') || '';
    let remaining = selected.data('remaining') || 0;
    let installment = selected.data('installment') || 0;

    $('#member').val(member);
    $('#remaining').val(formatRupiah(remaining));
    $('#installment').val(formatRupiah(installment));
});

// format denda
$(document).on('keyup', '#penalty', function() {
    let value = parseRupiah($(this).val());
    $(this).val(formatRupiah(value));
});
</script>
@endsection
