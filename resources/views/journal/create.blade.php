@extends('template.master')

@section('transaksi_simpan_pinjam_active', 'active')
@section('journal_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h1 class="h3 mb-3">
            <strong>Tambah Jurnal Umum</strong>
        </h1>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('journal.store') }}" method="POST">
                    @csrf

                    {{-- Header --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>

                        <div class="col-md-9">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                    </div>

                    {{-- Detail Journal --}}
                    <div class="table-responsive">
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Akun</th>
                                    <th width="200">Debit</th>
                                    <th width="200">Kredit</th>
                                    <th width="100">Hapus</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-primary mb-3 mt-3" id="add-row">
                        + Tambah Baris
                    </button>

                    {{-- Summary --}}
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="mb-3">
                                <label>Total Debit</label>
                                <input type="text" id="total_debit" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Total Kredit</label>
                                <input type="text" id="total_credit" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Selisih</label>
                                <input type="text" id="balance" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('journal.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button class="btn btn-success">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {

            let index = 0;
            let accounts = @json($accounts);

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka || 0);
            }

            function parseRupiah(str) {
                return parseFloat((str || '').replace(/\./g, '')) || 0;
            }

            // =========================
            // TAMBAH ROW
            // =========================
            $('#add-row').click(function() {

                let row = `
        <tr>
            <td>
                <select
                    name="items[${index}][account_id]"
                    class="form-select select2"
                    required
                >
                    <option value="">-- Pilih Akun --</option>
                    ${accounts.map(a => `
                            <option value="${a.id}">
                                ${a.code} - ${a.name}
                            </option>
                        `).join('')}
                </select>
            </td>

            <td>
                <input
                    type="text"
                    name="items[${index}][debit]"
                    class="form-control debit"
                    value="0"
                >
            </td>

            <td>
                <input
                    type="text"
                    name="items[${index}][credit]"
                    class="form-control credit"
                    value="0"
                >
            </td>

            <td>
                <button
                    type="button"
                    class="btn btn-danger remove"
                >
                    X
                </button>
            </td>
        </tr>
        `;

                $('#items-table tbody').append(row);
                $('#items-table tbody tr:last .select2').select2();

                index++;
            });

            // =========================
            // FORMAT UANG
            // =========================
            $(document).on('keyup', '.debit, .credit', function() {
                let value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
                calculateTotal();
            });

            // =========================
            // HAPUS ROW
            // =========================
            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                calculateTotal();
            });

            // =========================
            // HITUNG TOTAL + VALIDASI
            // =========================
            function calculateTotal() {
                let totalDebit = 0;
                let totalCredit = 0;

                $('.debit').each(function() {
                    totalDebit += parseRupiah($(this).val());
                });

                $('.credit').each(function() {
                    totalCredit += parseRupiah($(this).val());
                });

                let balance = totalDebit - totalCredit;

                $('#total_debit').val(formatRupiah(totalDebit));
                $('#total_credit').val(formatRupiah(totalCredit));
                $('#balance').val(formatRupiah(balance));

                // disable submit jika tidak balance
                if (balance !== 0 || totalDebit === 0 || totalCredit === 0) {
                    $('.btn-success')
                        .prop('disabled', true)
                        .text('Jurnal Tidak Balance');
                } else {
                    $('.btn-success')
                        .prop('disabled', false)
                        .text('Simpan');
                }
            }

            // =========================
            // VALIDASI SAAT SUBMIT
            // =========================
            $('form').on('submit', function(e) {
                let balance = parseRupiah($('#balance').val());
                let totalDebit = parseRupiah($('#total_debit').val());
                let totalCredit = parseRupiah($('#total_credit').val());

                if (balance !== 0 || totalDebit === 0 || totalCredit === 0) {
                    e.preventDefault();
                    alert('Jurnal harus balance sebelum disimpan.');
                    return false;
                }
            });

            // initial state
            calculateTotal();

        });
    </script>
@endsection
