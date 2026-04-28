@extends('template.master')

@section('configuration_active', 'active')
@section('auto_journal_active', 'active')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h1 class="h3 mb-3"><strong>Auto Jurnal</strong></h1>

        <div class="card">
            <div class="card-body">


                <form action="{{ route('auto_journal.process') }}" method="POST">
                    @csrf
                    {{-- @method('PUT') --}}

                    <div class="table-responsive">
                        <table class="table table-bordered">

                            <tr class="table-primary">
                                <th colspan="2">Pendapatan</th>
                            </tr>

                            <tr>
                                <td width="30%">Pendapatan Toko dari Anggota</td>
                                <td>
                                    <select name="sales_revenue_member_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->sales_revenue_member_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Pendapatan Toko dari Umum</td>
                                <td>
                                    <select name="sales_revenue_customer_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->sales_revenue_customer_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td width="30%">Pendapatan Jasa dari Anggota</td>
                                <td>
                                    <select name="service_revenue_member_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->service_revenue_member_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Pendapatan Jasa dari Umum</td>
                                <td>
                                    <select name="service_revenue_customer_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->service_revenue_customer_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Pendapatan Bunga dari Anggota</td>
                                <td>
                                    <select name="interest_income_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->interest_income_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr class="table-primary">
                                <th colspan="2">Penjualan</th>
                            </tr>

                            <tr>
                                <td>Diskon Penjualan</td>
                                <td>
                                    <select name="sales_discount_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->sales_discount_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Biaya Tambahan</td>
                                <td>
                                    <select name="sales_expense_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->sales_expense_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>PPN Keluaran</td>
                                <td>
                                    <select name="sales_tax_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->sales_tax_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr class="table-primary">
                                <th colspan="2">Pembelian</th>
                            </tr>

                            <tr>
                                <td>Diskon Pembelian</td>
                                <td>
                                    <select name="purchase_discount_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->purchase_discount_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Biaya Pembelian</td>
                                <td>
                                    <select name="purchase_expense_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->purchase_expense_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>PPN Masukan</td>
                                <td>
                                    <select name="purchase_tax_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->purchase_tax_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr class="table-primary">
                                <th colspan="2">Persediaan & HPP</th>
                            </tr>

                            <tr>
                                <td>Akun HPP</td>
                                <td>
                                    <select name="hpp_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->hpp_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Akun Persediaan</td>
                                <td>
                                    <select name="inventory_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->inventory_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Stok Opname +</td>
                                <td>
                                    <select name="stock_adjustment_gain_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->stock_adjustment_gain_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Stok Opname -</td>
                                <td>
                                    <select name="stock_adjustment_loss_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->stock_adjustment_loss_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr class="table-primary">
                                <th colspan="2">Piutang</th>
                            </tr>

                            <tr>
                                <td>Piutang untuk Jasa</td>
                                <td>
                                    <select name="account_receivable_account_id" class="form-control select2">
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a->id }}"
                                                {{ $auto_journal->account_receivable_account_id == $a->id ? 'selected' : '' }}>
                                                {{ $a->code }} - {{ $a->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <button class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection
