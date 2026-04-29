@extends('template.master')

@section('laporan_active', 'active')
@section('saving_transaction_by_member_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h1 class="h3 mb-3">
            <strong>Laporan Kontribusi Anggota</strong>
        </h1>

        {{-- FILTER --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari</label>
                        <input type="date" id="date_from" class="form-control" value="{{ date('Y-m-01') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sampai</label>
                        <input type="date" id="date_to" class="form-control" value="{{ date('Y-m-t') }}">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="btn-process">
                            Proses
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE MODAL --}}
        <div class="card mb-3">
            <div class="card-header">
                <strong>Kontribusi Simpanan</strong>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead id="table-head-modal"></thead>
                    <tbody id="table-body-modal"></tbody>
                </table>
            </div>
        </div>

        {{-- TABLE TRANSAKSI --}}
        <div class="card">
            <div class="card-header">
                <strong>Kontribusi Transaksi</strong>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead id="table-head-transaksi"></thead>
                    <tbody id="table-body-transaksi"></tbody>
                </table>
            </div>
        </div>

    </div>
@endsection


@section('script')
    <script>
        $(function() {

            const rupiah = (val) => new Intl.NumberFormat('id-ID').format(val || 0);

            // =========================
            // TABLE MODAL
            // =========================
            const renderTableModal = (savingTypes, data) => {

                let head = `<tr><th>Anggota</th>`;
                savingTypes.forEach(s => head += `<th>${s.name}</th>`);
                head += `<th>Total Modal</th><th>%</th></tr>`;

                $('#table-head-modal').html(head);

                let grandTotal = 0;

                data.forEach(row => {
                    let total = 0;
                    savingTypes.forEach(s => total += row[s.name] || 0);
                    row.total_modal = total;
                    grandTotal += total;
                });

                // sort by modal
                data.sort((a, b) => b.total_modal - a.total_modal);

                let body = '';

                if (data.length === 0) {
                    body =
                        `<tr><td colspan="${savingTypes.length + 3}" class="text-center">Tidak ada data</td></tr>`;
                } else {

                    data.forEach(row => {

                        let persen = grandTotal ? (row.total_modal / grandTotal * 100) : 0;

                        body += `<tr>
                    <td>${row.member}</td>`;

                        savingTypes.forEach(s => {
                            body += `<td>Rp ${rupiah(row[s.name])}</td>`;
                        });

                        body += `
                    <td class="fw-bold">Rp ${rupiah(row.total_modal)}</td>
                    <td>${persen.toFixed(2)}%</td>
                </tr>`;
                    });

                    body += `
                <tr class="table-dark">
                    <td colspan="${savingTypes.length + 1}"><strong>TOTAL</strong></td>
                    <td><strong>Rp ${rupiah(grandTotal)}</strong></td>
                    <td>100%</td>
                </tr>
            `;
                }

                $('#table-body-modal').html(body);
            };


            // =========================
            // TABLE TRANSAKSI
            // =========================
            const renderTableTransaksi = (data) => {

                let head = `
            <tr>
                <th>Anggota</th>
                <th>Belanja</th>
                <th>Pinjaman</th>
                <th>Jasa</th>
                <th>Total Transaksi</th>
                <th>%</th>
            </tr>`;

                $('#table-head-transaksi').html(head);

                let grandTotal = 0;

                data.forEach(row => {
                    let total =
                        (row.Belanja || 0) +
                        (row.Pinjaman || 0) +
                        (row.Jasa || 0);

                    row.total_transaksi = total;
                    grandTotal += total;
                });

                // sort by transaksi (utama koperasi)
                data.sort((a, b) => b.total_transaksi - a.total_transaksi);

                let body = '';

                if (data.length === 0) {
                    body = `<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>`;
                } else {

                    data.forEach(row => {

                        let persen = grandTotal ? (row.total_transaksi / grandTotal * 100) : 0;

                        body += `<tr>
                    <td>${row.member}</td>
                    <td>Rp ${rupiah(row.Belanja)}</td>
                    <td>Rp ${rupiah(row.Pinjaman)}</td>
                    <td>Rp ${rupiah(row.Jasa)}</td>
                    <td class="fw-bold">Rp ${rupiah(row.total_transaksi)}</td>
                    <td>${persen.toFixed(2)}%</td>
                </tr>`;
                    });

                    body += `
                <tr class="table-dark">
                    <td colspan="4"><strong>TOTAL</strong></td>
                    <td><strong>Rp ${rupiah(grandTotal)}</strong></td>
                    <td>100%</td>
                </tr>
            `;
                }

                $('#table-body-transaksi').html(body);
            };


            // =========================
            // AJAX
            // =========================
            $('#btn-process').click(function() {

                let btn = $(this);
                btn.prop('disabled', true).text('Loading...');

                $.post("{{ route('saving_transaction_by_member.process') }}", {
                        _token: "{{ csrf_token() }}",
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val()
                    })
                    .done(function(res) {

                        let savingTypes = res.savingTypes || [];
                        let data = res.data || [];

                        renderTableModal(savingTypes, data);
                        renderTableTransaksi(data);

                    })
                    .fail(function(err) {
                        console.error(err);
                        alert('Terjadi kesalahan saat mengambil data');
                    })
                    .always(function() {
                        btn.prop('disabled', false).text('Proses');
                    });

            });

        });
    </script>
@endsection
