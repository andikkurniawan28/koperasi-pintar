@extends('template.master')

@section('laporan_active', 'active')
@section('balance_sheet_active', 'active')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h1 class="h3 mb-3"><strong>Neraca</strong></h1>

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


        <div class="row">

            {{-- ================= ASET ================= --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header"><strong>Aset</strong></div>

                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Akun</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>

                            <tbody id="aset-body"></tbody>

                            <tfoot>
                                <tr>
                                    <th>Total Aset</th>
                                    <th class="text-end" id="total_aset"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><strong>Total Aktiva</strong></div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th>Total Aktiva</th>
                                <th class="text-end" id="total_aktiva"></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>


            {{-- ================= PASIVA ================= --}}
            <div class="col-md-6">

                {{-- KEWAJIBAN --}}
                <div class="card mb-3">
                    <div class="card-header"><strong>Kewajiban</strong></div>

                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Akun</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>

                            <tbody id="kewajiban-body"></tbody>

                            <tfoot>
                                <tr>
                                    <th>Total Kewajiban</th>
                                    <th class="text-end" id="total_kewajiban"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>


                {{-- EKUITAS --}}
                <div class="card mb-3">
                    <div class="card-header"><strong>Modal</strong></div>

                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Akun</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>

                            <tbody id="ekuitas-body"></tbody>

                            <tfoot>
                                <tr>
                                    <th>Total Modal</th>
                                    <th class="text-end" id="total_ekuitas"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>


                {{-- TOTAL PASIVA --}}
                <div class="card mb-3">
                    <div class="card-header"><strong>Total Pasiva</strong></div>

                    <div class="card-body">
                        <table class="table table-sm table-bordered">

                            <tr>
                                <th>Laba Berjalan</th>
                                <th class="text-end" id="total_laba"></th>
                            </tr>

                            <tr>
                                <th>Total Pasiva</th>
                                <th class="text-end" id="total_pasiva"></th>
                            </tr>

                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection


@section('script')

    <script>
        $('#btn-process').click(function() {

            $.post("{{ route('balance_sheet.process') }}", {

                    _token: "{{ csrf_token() }}",
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val()

                })

                .done(function(res) {

                    renderTable('aset-body', res.aset)
                    renderTable('kewajiban-body', res.kewajiban)
                    renderTable('ekuitas-body', res.ekuitas)

                    // TOTAL
                    $('#total_aset').html(format(res.total_aset))
                    $('#total_aktiva').html(format(res.total_aset))
                    $('#total_kewajiban').html(format(res.total_kewajiban))
                    $('#total_ekuitas').html(format(res.total_ekuitas))

                    // LABA BERJALAN
                    $('#total_laba').html(format(res.laba))
                    $('#laba_berjalan').html(format(res.laba))

                    // TOTAL PASIVA
                    $('#total_pasiva').html(format(res.total_pasiva))

                    // 🔥 OPTIONAL: BALANCE CHECK
                    if (res.total_aset !== res.total_pasiva) {
                        $('#total_pasiva').addClass('text-danger fw-bold')
                    }

                })

                .fail(function(xhr) {
                    console.log(xhr.responseText)
                    alert('Terjadi error')
                })

        })


        function renderTable(target, data) {

            let html = ''

            data.forEach(function(row) {

                html += `
        <tr>
            <td>${row.code} - ${row.name}</td>
            <td class="text-end">${format(row.balance)}</td>
        </tr>
        `
            })

            // 🔥 Tambahkan laba ke ekuitas
            if (target === 'ekuitas-body') {
                html += `
        <tr class="table-warning">
            <td><strong>Laba Berjalan</strong></td>
            <td class="text-end fw-bold" id="laba_berjalan"></td>
        </tr>
        `
            }

            $('#' + target).html(html)
        }


        function format(num) {
            let n = Number(num) || 0;
            return new Intl.NumberFormat('id-ID').format(n)
        }
    </script>

@endsection
