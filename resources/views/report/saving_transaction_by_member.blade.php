@extends('template.master')

@section('laporan_active', 'active')
@section('saving_transaction_by_member_active', 'active')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

```
<h1 class="h3 mb-3"><strong>Laporan Kontribusi Modal & Transaksi Oleh Anggota</strong></h1>

{{-- FILTER --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Dari</label>
                <input type="date" id="date_from" class="form-control" value="{{ date('Y-m-01') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sampai</label>
                <input type="date" id="date_to" class="form-control" value="{{ date('Y-m-t') }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" id="btn-process">
                    Proses
                </button>
            </div>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead id="table-head"></thead>
            <tbody id="table-body"></tbody>
        </table>
    </div>
</div>
```

</div>
@endsection

@section('script')

<script>
$(function(){

    function rupiah(val){
        return new Intl.NumberFormat('id-ID').format(val || 0);
    }

    $('#btn-process').click(function(){

        let btn = $(this);
        btn.prop('disabled', true).text('Loading...');

        $.post("{{ route('saving_transaction_by_member.process') }}", {
            _token: "{{ csrf_token() }}",
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val()
        })
        .done(function(res){

            let cols = res.columns || {};
            let data = res.data || [];

            // =========================
            // HEADER
            // =========================
            let head = `<tr>
                <th>Member</th>`;

            Object.entries(cols).forEach(([id, name]) => {
                head += `<th>${name}</th>`;
            });

            head += `
                <th>Penarikan</th>
                <th>Saldo Simpanan</th>
                <th>Pinjaman</th>
                <th>Angsuran</th>
                <th>Sisa Hutang</th>
                <th>Sales</th>
            </tr>`;

            $('#table-head').html(head);

            // =========================
            // BODY
            // =========================
            let body = '';

            if(data.length === 0){
                body = `<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>`;
            } else {

                data.forEach(row => {

                    body += `<tr>
                        <td>${row.member}</td>`;

                    Object.keys(cols).forEach(id => {
                        body += `<td>Rp ${rupiah(row['saving_'+id])}</td>`;
                    });

                    body += `
                        <td>Rp ${rupiah(row.saving_out)}</td>
                        <td>Rp ${rupiah(row.saving_balance)}</td>
                        <td>Rp ${rupiah(row.loan)}</td>
                        <td>Rp ${rupiah(row.installment)}</td>
                        <td>Rp ${rupiah(row.loan_remaining)}</td>
                        <td>Rp ${rupiah(row.sales)}</td>
                    </tr>`;
                });
            }

            $('#table-body').html(body);

        })
        .fail(function(err){
            console.error(err);
            alert('Terjadi kesalahan saat mengambil data');
        })
        .always(function(){
            btn.prop('disabled', false).text('Proses');
        });

    });

});
</script>

@endsection
