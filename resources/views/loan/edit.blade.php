@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('loan_active', 'active')

@section('content')
<div class="container-xxl container-p-y">
    <h1 class="h3 mb-3"><strong>Edit Pinjaman</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('loan.update', $loan->id) }}" method="POST" id="form-loan">
                @csrf
                @method('PUT')

                {{-- Tanggal --}}
                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control"
                        value="{{ old('date', $loan->date) }}">
                </div>

                {{-- Jenis Pinjaman --}}
                <div class="mb-3">
                    <label>Jenis Pinjaman</label>
                    <select name="loan_type_id" id="loan_type" class="form-select select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($types as $o)
                            <option value="{{ $o->id }}"
                                data-rate="{{ $o->interest_rate }}"
                                data-type="{{ $o->interest_type }}"
                                {{ old('loan_type_id', $loan->loan_type_id) == $o->id ? 'selected' : '' }}>
                                {{ $o->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Anggota --}}
                <div class="mb-3">
                    <label>Anggota</label>
                    <select name="member_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach($members as $a)
                            <option value="{{ $a->id }}"
                                {{ old('member_id', $loan->member_id) == $a->id ? 'selected' : '' }}>
                                {{ $a->code }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nominal --}}
                <div class="mb-3">
                    <label>Nominal Pinjaman</label>
                    <input type="text" name="principal" id="principal" class="form-control"
                        value="{{ old('principal', number_format($loan->principal, 0, ',', '.')) }}">
                </div>

                {{-- Tenor --}}
                <div class="mb-3">
                    <label>Tenor (bulan)</label>
                    <input type="number" name="tenor" id="tenor" class="form-control"
                        value="{{ old('tenor', $loan->tenor) }}">
                </div>

                {{-- Preview --}}
                <div class="mb-3">
                    <label>Estimasi Cicilan / Bulan</label>
                    <input type="text" id="installment_preview" class="form-control"
                        value="{{ number_format($loan->installment, 0, ',', '.') }}" readonly>
                </div>

                {{-- Akun --}}
                <div class="mb-3">
                    <label>Pencairan Lewat</label>
                    <select name="account_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}"
                                {{ old('account_id', $loan->account_id) == $a->id ? 'selected' : '' }}>
                                {{ $a->code }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-end">
                    <a href="{{ route('loan.index') }}" class="btn btn-secondary">Batal</a>
                    <button class="btn btn-success">Update</button>
                </div>
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

// format input
$(document).on('keyup', '#principal', function() {
    let value = parseRupiah($(this).val());
    $(this).val(formatRupiah(value));
});

// hitung ulang cicilan
function hitungSimulasi() {
    let principal = parseRupiah($('#principal').val());
    let tenor = parseInt($('#tenor').val()) || 1;

    let selected = $('#loan_type option:selected');
    let rate = parseFloat(selected.data('rate')) || 0;
    let type = selected.data('type');

    if (!principal || !tenor) return;

    let total_interest = 0;

    if (type === 'flat') {
        total_interest = (principal * rate / 100) * tenor;
    } else {
        total_interest = (principal * rate / 100) * (tenor / 2);
    }

    let total = principal + total_interest;
    let installment = total / tenor;

    $('#installment_preview').val(formatRupiah(installment));
}

$(document).on('keyup change', '#principal, #tenor, #loan_type', hitungSimulasi);

// bersihin sebelum submit
$('#form-loan').on('submit', function(){
    let clean = parseRupiah($('#principal').val());
    $('#principal').val(clean);
});
</script>
@endsection
