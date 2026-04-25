@extends('template.master')

@section('transaksi_jasa_active', 'active')
@section('invoice_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Edit Tagihan</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="row mb-3">

                    <div class="col-md-3">
                        <label>Tanggal</label>
                        <input type="date" name="date" value="{{ $invoice->date }}" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>Berlaku sampai</label>
                        <input type="date" name="due_date" value="{{ $invoice->due_date }}" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>Jenis</label>
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" name="type" value="member"
                                {{ $invoice->type == 'member' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary">Anggota</label>

                            <input type="radio" class="btn-check" name="type" value="customer"
                                {{ $invoice->type == 'customer' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary">Umum</label>
                        </div>
                    </div>

                    <div class="col-md-3 member-field">
                        <label>Anggota</label>
                        <select name="member_id" class="form-select select2">
                            <option value="">-- Pilih --</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}"
                                    {{ $invoice->member_id == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
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
                                    {{ $invoice->customer_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Items --}}
                <table class="table" id="items-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Harga</th>
                            <th>Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->item as $i => $item)
                        <tr>
                            <td>
                                <input type="text" name="items[{{ $i }}][name]" value="{{ $item->name }}" class="form-control">
                            </td>
                            <td>
                                <textarea name="items[{{ $i }}][description]" class="form-control">{{ $item->description }}</textarea>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][amount]" value="{{ number_format($item->amount,0,',','.') }}" class="form-control price">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove">X</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" id="add-row" class="btn btn-primary mb-3">+ Tambah</button>

                {{-- Summary --}}
                <div class="row">
                    <div class="col-md-8 offset-md-4">
                        <div class="row">

                            <!-- KOLOM KIRI -->
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Subtotal</label>
                                    <input type="text" name="subtotal" id="subtotal" class="form-control"
                                        value="{{ number_format($invoice->subtotal,0,',','.') }}" readonly>
                                </div>

                                <div class="mb-2">
                                    <label>Diskon</label>
                                    <input type="text" name="discount" id="discount" class="form-control"
                                        value="{{ number_format($invoice->discount,0,',','.') }}">
                                </div>

                                <div class="mb-2">
                                    <label>Biaya Lain-lain</label>
                                    <input type="text" name="expenses" id="expenses" class="form-control"
                                        value="{{ number_format($invoice->expenses,0,',','.') }}">
                                </div>

                                <div class="mb-2">
                                    <label>Pajak</label>
                                    <input type="text" name="taxes" id="taxes" class="form-control"
                                        value="{{ number_format($invoice->taxes,0,',','.') }}">
                                </div>
                            </div>

                            <!-- KOLOM KANAN -->
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Grand Total</label>
                                    <input type="text" name="grand_total" id="grand_total" class="form-control"
                                        value="{{ number_format($invoice->grand_total,0,',','.') }}" readonly>
                                </div>

                                {{-- <div class="mb-2">
                                    <label>DP / Pembayaran</label>
                                    <input type="text" name="paid" id="paid" class="form-control"
                                        value="{{ number_format($invoice->paid,0,',','.') }}">
                                </div>

                                <div class="mb-2">
                                    <label>Sisa</label>
                                    <input type="text" name="left" id="left" class="form-control"
                                        value="{{ number_format($invoice->left,0,',','.') }}" readonly>
                                </div>

                                <div class="mb-2">
                                    <label>Pembayaran lewat</label>
                                    <select name="account_id" class="form-control select2">
                                        <option value="">-- Pilih --</option>
                                        @foreach($payment_gateways as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $invoice->account_id == $p->id ? 'selected' : '' }}>
                                                {{ $p->code }} - {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>

                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-success">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function(){

    let index = {{ count($invoice->item) }};

    function format(n){ return new Intl.NumberFormat('id-ID').format(n||0); }
    function parse(n){ return parseFloat((n||'').replace(/\./g,''))||0; }

    $('#add-row').click(function(){
        $('#items-table tbody').append(`
            <tr>
                <td><input name="items[${index}][name]" class="form-control"></td>
                <td><textarea name="items[${index}][description]" class="form-control"></textarea></td>
                <td><input name="items[${index}][amount]" class="form-control price"></td>
                <td><button type="button" class="btn btn-danger remove">X</button></td>
            </tr>
        `);
        index++;
    });

    $(document).on('click','.remove',function(){
        $(this).closest('tr').remove();
        calculate();
    });

    $(document).on('keyup','.price, #discount, #expenses, #taxes, #paid',function(){
        let v = parse($(this).val());
        $(this).val(format(v));
        calculate();
    });

    function calculate(){
        let subtotal = 0;
        $('.price').each(function(){
            subtotal += parse($(this).val());
        });

        $('#subtotal').val(format(subtotal));

        let grand = subtotal - parse($('#discount').val()) + parse($('#expenses').val()) + parse($('#taxes').val());

        $('#grand_total').val(format(grand));

        let paid = parse($('#paid').val());
        $('#left').val(format(grand - paid));
    }

    calculate();

});
</script>
@endsection
