@extends('template.master')

@section('master_active', 'active')
@section('loan_type_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Jenis Pinjaman</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('loan_type.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="loan_typeTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Akun</th>
                            <th>Bunga</th>
                            <th>Tenor</th>
                            <th>Plafon</th>
                            <th>Butuh Jaminan</th>
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
        $('#loan_typeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('loan_type.index') }}",
            order: [[0, 'asc']],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'account', name: 'account' },
                { data: 'interest', name: 'interest' },
                { data: 'tenor', name: 'tenor' },
                { data: 'max_amount', name: 'max_amount', render: function(data) { return formatRupiah(data); } },
                { data: 'collateral', name: 'collateral' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
