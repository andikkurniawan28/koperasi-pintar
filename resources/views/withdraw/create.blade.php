@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('withdraw_active', 'active')

@section('content')
    <div class="container-xxl container-p-y">
    <h1 class="h3 mb-3"><strong>Tambah Penarikan Simpanan</strong></h1>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('withdraw.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label>Jenis Penarikan Simpanan</label>
                        <select name="saving_type_id" class="form-select select2" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($types as $o)
                                <option value="{{ $o->id }}">
                                    {{ $o->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="mb-3">
                        <label>Bisa ditarik kapan?</label>
                        <input type="date" name="withdraw_allowed_at" class="form-control" value="">
                    </div> --}}

                    <div class="mb-3">
                        <label>Anggota</label>
                        <select name="member_id" class="form-control select2" required>
                            <option value="">-- Pilih --</option>
                            @foreach($members as $a)
                            <option value="{{ $a->id }}">{{ $a->code }}-{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Total Bayar</label>
                        <input type="text" name="total" id="total" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Ditarik dari</label>
                        <select name="account_id" class="form-control select2" required>
                            <option value="">-- Pilih --</option>
                            @foreach($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->code }}-{{ $a->name }}</option>
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

        $(document).on('keyup', '#total', function() {
            let value = parseRupiah($(this).val());
            $(this).val(formatRupiah(value));
        });
    </script>
@endsection
