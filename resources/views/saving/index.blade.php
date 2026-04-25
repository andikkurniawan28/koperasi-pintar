@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('saving_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Simpanan</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('saving.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="savingTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Jenis</th>
                            <th>Anggota</th>
                            <th>Admin</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    $(function() {
        $('#savingTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('saving.index') }}",
            saving: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'date', name: 'date' },
                { data: 'code', name: 'code' },
                { data: 'type', name: 'type' },
                { data: 'member', name: 'member' },
                { data: 'user', name: 'user' },
                // { data: 'total', name: 'total', render: function(data) { return formatRupiah(data); } },
                { data: 'total', name: 'total', render: function(data) { return formatRupiah(data); } },
                // { data: 'via', name: 'via' },
                { data: 'action', name: 'action', savingable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
