@extends('template.master')

@section('transaksi_toko_active', 'active')
@section('purchase_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h1 class="h3 mb-3"><strong>Tambah Pembelian</strong></h1>

        <div class="card">
            <div class="card-body">

                <form action="{{ route('purchase.store') }}" method="POST">
                    @csrf

                    {{-- ================= HEADER ================= --}}
                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label>Supplier</label>
                            <select name="supplier_id" class="form-select select2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>


                    {{-- ================= ITEMS ================= --}}
                    <div class="table-responsive">
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th width="120">Qty</th>
                                    <th width="180">Harga</th>
                                    <th width="180">Amount</th>
                                    <th width="80">Hapus</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-primary mb-3 mt-3" id="add-row">
                        + Tambah Item
                    </button>


                    {{-- ================= SUMMARY ================= --}}
                    <div class="row">
                        <div class="col-md-8 offset-md-4">
                            <div class="row">

                                {{-- LEFT --}}
                                <div class="col-md-6">

                                    <div class="mb-2">
                                        <label>Subtotal</label>
                                        <input type="text" name="subtotal" id="subtotal" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Diskon</label>
                                        <input type="text" name="discount" id="discount" class="form-control"
                                            value="0">
                                    </div>

                                    <div class="mb-2">
                                        <label>Biaya Lain-lain</label>
                                        <input type="text" name="expenses" id="expenses" class="form-control"
                                            value="0">
                                    </div>

                                    <div class="mb-2">
                                        <label>Pajak</label>
                                        <input type="text" name="taxes" id="taxes" class="form-control"
                                            value="0">
                                    </div>

                                </div>

                                {{-- RIGHT --}}
                                <div class="col-md-6">

                                    <div class="mb-2">
                                        <label>Grand Total</label>
                                        <input type="text" name="grand_total" id="grand_total" class="form-control"
                                            readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Uang Dibayar</label>
                                        <input type="text" id="paid" class="form-control" value="0">
                                    </div>

                                    <div class="mb-2">
                                        <label>Selisih</label>
                                        <input type="text" id="left" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Pembayaran lewat</label>
                                        <select name="account_id" class="form-control select2" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach ($payment_gateways as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->code }} - {{ $p->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- ================= ACTION ================= --}}
                    <div class="text-end mt-3">
                        <a href="{{ route('purchase.index') }}" class="btn btn-secondary">Batal</a>
                        <button class="btn btn-success">Simpan</button>
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
            let products = @json($products);

            // =========================
            // HELPER
            // =========================
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
                <select name="items[${index}][product_id]" class="form-select select2 product" required>
                    <option value="">-- Pilih --</option>
                    ${products.map(p =>
                        `<option value="${p.id}" data-price="${p.buy_price}">
                                ${p.barcode} - ${p.name}
                            </option>`
                    ).join('')}
                </select>
            </td>

            <td>
                <input type="number" name="items[${index}][qty]" class="form-control qty" value="1" min="1">
            </td>

            <td>
                <input type="text" name="items[${index}][price]" class="form-control price">
            </td>

            <td>
                <input type="text" name="items[${index}][amount]" class="form-control amount" readonly>
            </td>

            <td>
                <button type="button" class="btn btn-danger remove">X</button>
            </td>
        </tr>
        `;

                $('#items-table tbody').append(row);
                $('#items-table tbody tr:last .select2').select2();

                index++;
            });


            // =========================
            // PILIH PRODUK → SET HARGA
            // =========================
            $(document).on('change', '.product', function() {

                let selected = $(this).find(':selected');
                let row = $(this).closest('tr');

                let price = selected.data('price') || 0;

                row.find('.price').val(formatRupiah(price)).trigger('keyup');
            });


            // =========================
            // FORMAT INPUT UANG
            // =========================
            $(document).on('keyup', '.price, #discount, #expenses, #taxes, #paid', function() {

                let value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
            });


            // =========================
            // HITUNG AMOUNT
            // =========================
            $(document).on('keyup change', '.qty, .price', function() {

                let row = $(this).closest('tr');

                let qty = parseFloat(row.find('.qty').val()) || 0;
                let price = parseRupiah(row.find('.price').val());

                let amount = qty * price;

                row.find('.amount').val(formatRupiah(amount));

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
            // TOTAL
            // =========================
            function calculateTotal() {

                let subtotal = 0;

                $('.amount').each(function() {
                    subtotal += parseRupiah($(this).val());
                });

                $('#subtotal').val(formatRupiah(subtotal));

                let discount = parseRupiah($('#discount').val());
                let expenses = parseRupiah($('#expenses').val());
                let taxes = parseRupiah($('#taxes').val());

                let grandTotal = subtotal - discount + expenses + taxes;

                $('#grand_total').val(formatRupiah(grandTotal));

                let paid = parseRupiah($('#paid').val());
                let left = paid - grandTotal;

                $('#left').val(formatRupiah(left));
            }

            $('#discount, #expenses, #taxes, #paid').on('keyup change', calculateTotal);


            // =========================
            // VALIDASI
            // =========================
            $('form').submit(function(e) {

                if (!$('select[name="supplier_id"]').val()) {
                    alert('Supplier wajib dipilih');
                    e.preventDefault();
                }

            });

        });
    </script>
@endsection
