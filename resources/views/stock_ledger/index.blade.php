@extends('template.master')

@section('laporan_active', 'active')
@section('stock_ledger_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h1 class="h3 mb-3"><strong>Kartu Stok</strong></h1>

    <div class="card mb-3">
        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="form-label">Dari</label>
                    <input
                        type="date"
                        id="date_from"
                        class="form-control"
                        value="{{ date('Y-m-01') }}"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Sampai</label>
                    <input
                        type="date"
                        id="date_to"
                        class="form-control"
                        value="{{ date('Y-m-t') }}"
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Produk</label>
                    <select id="product_id" class="form-control select2">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->barcode }} - {{ $product->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="btn-process">
                        Proses
                    </button>
                </div>

            </div>

        </div>
    </div>


    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="ledger-table">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            {{-- <th>Kode</th> --}}
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection


@section('script')

<script>

$('#btn-process').click(function(){

    let date_from = $('#date_from').val();
    let date_to   = $('#date_to').val();
    let product   = $('#product_id').val();

    if(!date_from || !date_to || !product)
    {
        alert('Lengkapi filter terlebih dahulu');
        return;
    }

    $.ajax({

        url: "{{ route('stock_ledger.process') }}",
        method: "POST",
        data: {
            _token : "{{ csrf_token() }}",
            date_from : date_from,
            date_to : date_to,
            product_id : product
        },

        success:function(response){

            let html = '';

            response.data.forEach(function(row){

                html += `
                <tr>
                    <td>${row.date}</td>
                    <td>${row.description}</td>
                    <td class="text-end">${row.in}</td>
                    <td class="text-end">${row.out}</td>
                    <td class="text-end">${row.balance}</td>
                </tr>
                `;

            });

            $('#ledger-table tbody').html(html);

        }

    });

});


function format(angka)
{
    return new Intl.NumberFormat('id-ID').format(angka || 0);
}

</script>

@endsection
