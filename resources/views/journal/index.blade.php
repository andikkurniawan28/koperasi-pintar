@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('journal_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Jurnal Umum</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('journal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="journalTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Admin</th>
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
        $('#journalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('journal.index') }}",
            order: [[0, 'desc']], // ✅ fix

            columns: [
                { data: 'id', name: 'id' },
                { data: 'date', name: 'date' },
                { data: 'code', name: 'code' },
                { data: 'user', name: 'user' },
                { data: 'action', name: 'action', orderable: false, searchable: false } // ✅ fix
            ]
        });
    });
</script>
@endsection
