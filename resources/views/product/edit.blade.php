@extends('template.master')

@section('master_active', 'active')
@section('product_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Edit Produk</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('product.update', $product->id) }}" method="POST" id="form-product">
                @csrf
                @method('PUT')

                {{-- Barcode --}}
                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" name="barcode" class="form-control"
                        value="{{ old('barcode', $product->barcode) }}" required autofocus>
                </div>

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="text" name="buy_price" class="form-control rupiah"
                        value="{{ old('buy_price', number_format($product->buy_price,0,',','.')) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga Jual ke Anggota</label>
                    <input type="text" name="price_for_member" class="form-control rupiah"
                        value="{{ old('price_for_member', number_format($product->price_for_member,0,',','.')) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga Jual ke Umum</label>
                    <input type="text" name="price_for_customer" class="form-control rupiah"
                        value="{{ old('price_for_customer', number_format($product->price_for_customer,0,',','.')) }}">
                </div>

                {{-- Minimum Stok --}}
                <div class="mb-3">
                    <label class="form-label">Minimum Stok</label>
                    <input type="number" name="minimum_alert" class="form-control"
                        value="{{ old('minimum_alert', $product->minimum_alert) }}" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('product.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function(){

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    function parseRupiah(str) {
        return (str || '').replace(/\./g, '');
    }

    // format saat ketik
    $(document).on('keyup', '.rupiah', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });

    // bersihin sebelum submit
    $('#form-product').on('submit', function(){
        $('.rupiah').each(function(){
            let clean = parseRupiah($(this).val());
            $(this).val(clean);
        });
    });

});
</script>
@endsection
