@extends('template.master')

@section('transaksi_toko_active', 'active')
@section('stock_adjustment_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h1 class="h3 mb-3"><strong>Tambah Stock Opname</strong></h1>

        <div class="card">
            <div class="card-body">

                <form action="{{ route('stock_adjustment.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label>Tipe</label>
                            <select name="inOut" class="form-control">
                                <option value="in">Masuk (Stock Bertambah)</option>
                                <option value="out">Keluar (Stock Berkurang)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Produk</label>
                            <select name="product_id" class="form-control select2" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->barcode }} - {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label>Qty</label>
                            <input type="number" id="qty" name="qty" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-3">
                            <label>Harga Beli</label>
                            <input type="text" id="buy_price" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Total</label>
                            <input type="text" name="total" id="total" class="form-control" readonly>
                        </div>

                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('stock_adjustment.index') }}" class="btn btn-secondary">Batal</a>
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

            $('.select2').select2();

            let products = @json($products);

            function format(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }

            function parse(num) {
                return parseFloat((num || '').replace(/\./g, '')) || 0;
            }

            // pilih produk → ambil buy_price
            $('select[name="product_id"]').on('change', function() {

                let id = $(this).val();
                let product = products.find(p => p.id == id);

                let price = product ? product.buy_price : 0;

                $('#buy_price').val(format(price));

                calculate();
            });

            // qty berubah
            $('#qty').on('keyup change', function() {
                calculate();
            });

            function calculate() {

                let qty = parseFloat($('#qty').val()) || 0;
                let price = parse($('#buy_price').val());

                let total = qty * price;

                $('#total').val(format(total));
            }

        });
    </script>
@endsection
