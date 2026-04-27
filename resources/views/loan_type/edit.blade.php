@extends('template.master')

@section('master_active', 'active')
@section('loan_type_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Edit Jenis Pinjaman</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('loan_type.update', $loan_type->id) }}" method="POST" id="form-loan">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $loan_type->name) }}" required autofocus>
                </div>

                {{-- Akun --}}
                <div class="mb-3">
                    <label class="form-label">Akun</label>
                    <select name="account_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($accounts as $a)
                            <option value="{{ $a->id }}"
                                {{ old('account_id', $loan_type->account_id) == $a->id ? 'selected' : '' }}>
                                {{ $a->code }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bunga --}}
                <div class="mb-3">
                    <label class="form-label">Bunga (%)</label>
                    <input type="number" step="0.01" name="interest_rate" class="form-control"
                           value="{{ old('interest_rate', $loan_type->interest_rate) }}" required>
                </div>

                {{-- Tipe Bunga --}}
                <div class="mb-3">
                    <label class="form-label">Tipe Bunga</label>
                    <select name="interest_type" class="form-control" required>
                        <option value="flat"
                            {{ old('interest_type', $loan_type->interest_type) == 'flat' ? 'selected' : '' }}>
                            Flat
                        </option>
                        <option value="effective"
                            {{ old('interest_type', $loan_type->interest_type) == 'effective' ? 'selected' : '' }}>
                            Efektif
                        </option>
                    </select>
                </div>

                {{-- Tenor --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tenor Minimum (bulan)</label>
                        <input type="number" name="tenor_min" class="form-control"
                               value="{{ old('tenor_min', $loan_type->tenor_min) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tenor Maksimum (bulan)</label>
                        <input type="number" name="tenor_max" class="form-control"
                               value="{{ old('tenor_max', $loan_type->tenor_max) }}">
                    </div>
                </div>

                {{-- Plafon --}}
                <div class="mb-3">
                    <label class="form-label">Plafon Maksimum</label>
                    <input type="text" name="max_amount" class="form-control rupiah"
                           value="{{ old('max_amount', number_format($loan_type->max_amount, 0, ',', '.')) }}">
                </div>

                {{-- Jaminan --}}
                <div class="mb-3">
                    <label class="form-label">Butuh Jaminan?</label>
                    <select name="requires_collateral" class="form-control" required>
                        <option value="0"
                            {{ old('requires_collateral', $loan_type->requires_collateral) == '0' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="1"
                            {{ old('requires_collateral', $loan_type->requires_collateral) == '1' ? 'selected' : '' }}>
                            Ya
                        </option>
                    </select>
                </div>

                {{-- Action --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('loan_type.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function(){

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    function parseRupiah(str) {
        return (str || '').replace(/\./g, '');
    }

    // format saat ketik
    $(document).on('keyup', '.rupiah', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });

    // bersihin sebelum submit
    $('#form-loan').on('submit', function(){
        $('.rupiah').each(function(){
            let clean = parseRupiah($(this).val());
            $(this).val(clean);
        });
    });

});
</script>
@endsection
