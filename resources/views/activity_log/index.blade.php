@extends('template.master')

@section('configuration_active', 'active')
@section('activity_log_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Daftar Log</strong></h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="activity_logTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Aktivitas</th>
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
        $('#activity_logTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('activity_log.index') }}",
            order: [[0, 'desc']], // ✅ fix

            columns: [
                { data: 'id', name: 'id' },
                { data: 'created_at', name: 'created_at' },
                { data: 'user', name: 'user' },
                { data: 'description', name: 'description' },
            ]
        });
    });
</script>
@endsection
