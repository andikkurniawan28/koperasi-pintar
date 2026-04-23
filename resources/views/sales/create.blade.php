@extends('template.master')

@section('transaksi_toko_active', 'active')
@section('sales_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h1 class="h3 mb-3"><strong>Tambah Penjualan</strong></h1>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('sales.store') }}" method="POST">
                    @csrf

                    {{-- Header --}}
                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label>Jenis Transaksi</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type" id="member" value="member" checked>
                                <label class="btn btn-outline-primary" for="member">Anggota</label>

                                <input type="radio" class="btn-check" name="type" id="customer" value="customer">
                                <label class="btn btn-outline-primary" for="customer">Umum</label>
                            </div>
                        </div>

                        <div class="col-md-3 member-field">
                            <label>Anggota</label>
                            <select name="member_id" class="form-select select2">
                                <option value="">-- Pilih --</option>
                                @foreach ($members as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} - {{ $t->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 customer-field">
                            <label>Customer</label>
                            <select name="customer_id" class="form-select select2">
                                <option value="">-- Pilih --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Items --}}
                    <div class="table-responsive">
                    <table class="table table-bsalesed" id="items-table">
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

                                <!-- KOLOM KIRI -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label>Subtotal</label>
                                        <input type="text" name="subtotal" id="subtotal" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                        <label>Diskon</label>
                                        <input type="text" name="discount" id="discount" class="form-control" value="0">
                                    </div>

                                    <div class="mb-2">
                                        <label>Biaya Lain-lain</label>
                                        <input type="text" name="expenses" id="expenses" class="form-control" value="0">
                                    </div>

                                    <div class="mb-2">
                                        <label>Pajak</label>
                                        <input type="text" name="taxes" id="taxes" class="form-control" value="0">
                                    </div>
                                </div>

                                <!-- KOLOM KANAN -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label>Grand Total</label>
                                        <input type="text" name="grand_total" id="grand_total" class="form-control" readonly>
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
                                            <option value="">-- Pilih --</option>
                                            @foreach($payment_gateways as $p)
                                                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
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

    // =========================
    // TYPE (member / customer)
    // =========================
    let selectedType = $('input[name="type"]:checked').val();

    function toggleCustomerMember() {
        if (selectedType === 'member') {
            $('.member-field').show();
            $('.customer-field').hide();
            $('select[name="customer_id"]').val(null).trigger('change');
        } else {
            $('.member-field').hide();
            $('.customer-field').show();
            $('select[name="termin_id"]').val(null).trigger('change');
        }
    }

    // initial load
    toggleCustomerMember();

    // change type
    $('input[name="type"]').on('change', function () {
        selectedType = $(this).val();

        toggleCustomerMember();

        // update semua harga di row
        $('.product').each(function () {
            $(this).trigger('change');
        });
    });

    // =========================
    // HELPER
    // =========================
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    function parseRupiah(str) {
        return parseFloat((str || '').replace(/\./g, '')) || 0;
    }

    let products = @json($products);

    // =========================
    // TAMBAH ROW
    // =========================
    $('#add-row').click(function() {
        let row = `
        <tr>
            <td>
                <select name="items[${index}][product_id]" class="form-select select2 product">
                    <option value="">-- Pilih --</option>
                    ${products.map(p =>
                        `<option value="${p.id}"
                            data-price-member="${p.price_for_member}"
                            data-price-customer="${p.price_for_customer}">
                            ${p.barcode} - ${p.name}
                        </option>`
                    ).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][qty]" class="form-control qty" value="1">
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

        let price = (selectedType === 'member')
            ? selected.data('price-member')
            : selected.data('price-customer');

        row.find('.price').val(formatRupiah(price)).trigger('keyup');
    });

    // =========================
    // FORMAT UANG
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

});
</script>
@endsection
