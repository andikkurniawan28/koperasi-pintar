@extends('template.master')

@section('transaksi_toko_active', 'active')
@section('sales_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Penjualan</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="salesTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Anggota</th>
                            <th>Customer</th>
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
        $('#salesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sales.index') }}",
            sales: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'date', name: 'date' },
                { data: 'code', name: 'code' },
                { data: 'member', name: 'member' },
                { data: 'customer', name: 'customer' },
                { data: 'user', name: 'user' },
                { data: 'grand_total', name: 'grand_total', render: function(data) { return formatRupiah(data); } },
                { data: 'action', name: 'action', salesable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
