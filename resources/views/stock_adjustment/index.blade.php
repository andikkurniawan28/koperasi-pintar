@extends('template.master')

@section('transaksi_toko_active', 'active')
@section('stock_adjustment_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Stok Opname</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('stock_adjustment.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="stock_adjustmentTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Produk</th>
                            <th>Admin</th>
                            <th>Qty</th>
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
        $('#stock_adjustmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stock_adjustment.index') }}",
            stock_adjustment: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'date', name: 'date' },
                { data: 'code', name: 'code' },
                { data: 'inOut', name: 'inOut' },
                { data: 'description', name: 'description' },
                { data: 'product', name: 'product' },
                { data: 'user', name: 'user' },
                { data: 'qty', name: 'qty' },
                { data: 'total', name: 'total', render: function(data) { return formatRupiah(data); } },
                { data: 'action', name: 'action', stock_adjustmentable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
