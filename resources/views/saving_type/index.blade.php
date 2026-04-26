@extends('template.master')

@section('master_active', 'active')
@section('saving_type_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Jenis Simpanan</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('saving_type.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="saving_typeTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Akun</th>
                            <th>Dapat Ditarik</th>
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
    $(function() {
        $('#saving_typeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('saving_type.index') }}",
            order: [[0, 'asc']],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'account', name: 'account' },
                { data: 'is_withdrawable', name: 'is_withdrawable' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
