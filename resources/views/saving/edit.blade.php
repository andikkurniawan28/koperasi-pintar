@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('saving_active', 'active')

@section('content')
<div class="container-xxl container-p-y">
    <h1 class="h3 mb-3"><strong>Edit Simpanan</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('saving.update', $saving->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control"
                        value="{{ $saving->date }}">
                </div>

                <div class="mb-3">
                    <label>Jenis Simpanan</label>
                    <select name="saving_type_id" class="form-select select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($types as $o)
                            <option value="{{ $o->id }}"
                                {{ $saving->saving_type_id == $o->id ? 'selected' : '' }}>
                                {{ $o->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Bisa ditarik kapan?</label>
                    <input type="date" name="withdraw_allowed_at" class="form-control" value="{{ $saving->withdraw_allowed_at }}">
                </div>

                <div class="mb-3">
                    <label>Anggota</label>
                    <select name="member_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach($members as $a)
                            <option value="{{ $a->id }}"
                                {{ $saving->member_id == $a->id ? 'selected' : '' }}>
                                {{ $a->code }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Total Bayar</label>
                    <input type="text" name="total" id="total" class="form-control"
                        value="{{ number_format($saving->total, 0, ',', '.') }}">
                </div>

                <div class="mb-3">
                    <label>Setoran lewat</label>
                    <select name="account_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}"
                                {{ $saving->account_id == $a->id ? 'selected' : '' }}>
                                {{ $a->code }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-end">
                    <a href="{{ route('saving.index') }}" class="btn btn-secondary">Batal</a>
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

$(document).on('keyup', '#total', function() {
    let value = parseRupiah($(this).val());
    $(this).val(formatRupiah(value));
});
</script>
@endsection
