@extends('template.master')

@section('transaksi_toko_active', 'active')
@section('stock_adjustment_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h1 class="h3 mb-3"><strong>Edit Stok Opname</strong></h1>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('stock_adjustment.update', $stock_adjustment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Header --}}
                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="date" name="date" value="{{ $stock_adjustment->date }}" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label>Jenis Transaksi</label>
                            <div class="btn-group w-100">
                                <input type="radio" class="btn-check" name="type" id="member" value="member"
                                    {{ $stock_adjustment->type == 'member' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="member">Anggota</label>

                                <input type="radio" class="btn-check" name="type" id="customer" value="customer"
                                    {{ $stock_adjustment->type == 'customer' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="customer">Umum</label>
                            </div>
                        </div>

                        <div class="col-md-3 member-field">
                            <label>Anggota</label>
                            <select name="member_id" class="form-select select2">
                                <option value="">-- Pilih --</option>
                                @foreach ($members as $t)
                                    <option value="{{ $t->id }}" {{ $stock_adjustment->member_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} - {{ $t->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 customer-field">
                            <label>Customer</label>
                            <select name="customer_id" class="form-select select2">
                                <option value="">-- Pilih --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}"
                                        {{ $stock_adjustment->customer_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Items --}}
                    <div class="table-responsive">
                        <table class="table" id="items-table">
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

                    {{-- Summary --}}
                    <div class="row">
                        <div class="col-md-8 offset-md-4">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label>Subtotal</label>
                                        <input type="text" name="subtotal" id="subtotal"
                                            value="{{ number_format($stock_adjustment->subtotal) }}" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Diskon</label>
                                        <input type="text" name="discount" id="discount"
                                            value="{{ number_format($stock_adjustment->discount) }}" class="form-control">
                                    </div>

                                    <div class="mb-2">
                                        <label>Biaya Lain-lain</label>
                                        <input type="text" name="expenses" id="expenses"
                                            value="{{ number_format($stock_adjustment->expenses) }}" class="form-control">
                                    </div>

                                    <div class="mb-2">
                                        <label>Pajak</label>
                                        <input type="text" name="taxes" id="taxes"
                                            value="{{ number_format($stock_adjustment->taxes) }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label>Grand Total</label>
                                        <input type="text" name="grand_total" id="grand_total"
                                            value="{{ number_format($stock_adjustment->grand_total) }}" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Uang Diterima</label>
                                        <input type="text" id="paid" class="form-control" value="0">
                                    </div>

                                    <div class="mb-2">
                                        <label>Kembalian</label>
                                        <input type="text" id="left" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Pembayaran lewat</label>
                                        <select name="account_id" class="form-control select2">
                                            @foreach ($payment_gateways as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ $stock_adjustment->account_id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->code }} - {{ $p->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('stock_adjustment.index') }}" class="btn btn-secondary">Batal</a>
                        <button class="btn btn-success">Update</button>
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

            let selectedType = $('input[name="type"]:checked').val();

            function toggleCustomerMember() {
                if (selectedType === 'member') {
                    $('.member-field').show();
                    $('.customer-field').hide();
                    $('select[name="customer_id"]').val(null).trigger('change');
                } else {
                    $('.member-field').hide();
                    $('.customer-field').show();
                    $('select[name="member_id"]').val(null).trigger('change');
                }
            }

            toggleCustomerMember();

            $('input[name="type"]').on('change', function() {
                selectedType = $(this).val();
                toggleCustomerMember();
                $('.product').trigger('change');
            });

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka || 0);
            }

            function parseRupiah(str) {
                return parseFloat((str || '').replace(/\./g, '')) || 0;
            }

            let products = @json($products);
            let existingItems = @json($stock_adjustment->product);

            function addRow(item = null) {

                let row = `
        <tr>
            <td>
                <select name="items[${index}][product_id]" class="form-select select2 product">
                    <option value="">-- Pilih --</option>
                    ${products.map(p =>
                        `<option value="${p.id}"
                                data-price-member="${p.price_for_member}"
                                data-price-customer="${p.price_for_customer}"
                                ${item && p.id == item.product_id ? 'selected' : ''}>
                                ${p.barcode} - ${p.name}
                            </option>`
                    ).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][qty]" class="form-control qty"
                    value="${item ? item.qty : 1}">
            </td>
            <td>
                <input type="text" name="items[${index}][price]" class="form-control price"
                    value="${item ? formatRupiah(item.price) : ''}">
            </td>
            <td>
                <input type="text" name="items[${index}][amount]" class="form-control amount"
                    value="${item ? formatRupiah(item.amount) : ''}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger remove">X</button>
            </td>
        </tr>
        `;

                $('#items-table tbody').append(row);
                $('#items-table tbody tr:last .select2').select2();

                index++;
            }

            // load existing
            existingItems.forEach(item => addRow(item));

            $('#add-row').click(() => addRow());

            $(document).on('change', '.product', function() {
                let selected = $(this).find(':selected');
                let row = $(this).closest('tr');

                let price = (selectedType === 'member') ?
                    selected.data('price-member') :
                    selected.data('price-customer');

                row.find('.price').val(formatRupiah(price)).trigger('keyup');
            });

            $(document).on('keyup change', '.qty, .price', function() {
                let row = $(this).closest('tr');

                let qty = parseFloat(row.find('.qty').val()) || 0;
                let price = parseRupiah(row.find('.price').val());

                let amount = qty * price;

                row.find('.amount').val(formatRupiah(amount));
                calculateTotal();
            });

            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                calculateTotal();
            });

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

            calculateTotal();

        });
    </script>
@endsection
