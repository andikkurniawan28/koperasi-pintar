@extends('template.master')

@section('master_active', 'active')
@section('account_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Edit Akun</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('account.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="number"
                           name="code"
                           class="form-control"
                           value="{{ old('code', $account->code) }}"
                           autofocus
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sub</label>
                    <select name="sub" class="form-control">
                        <option value="Aset Lancar" {{ old('sub', $account->sub) == 'Aset Lancar' ? 'selected' : '' }}>Aset Lancar</option>
                        <option value="Aset Tetap" {{ old('sub', $account->sub) == 'Aset Tetap' ? 'selected' : '' }}>Aset Tetap</option>
                        <option value="Jangka Pendek" {{ old('sub', $account->sub) == 'Jangka Pendek' ? 'selected' : '' }}>Jangka Pendek</option>
                        <option value="Simpanan" {{ old('sub', $account->sub) == 'Simpanan' ? 'selected' : '' }}>Simpanan</option>
                        <option value="Modal" {{ old('sub', $account->sub) == 'Modal' ? 'selected' : '' }}>Modal</option>
                        <option value="Toko Anggota" {{ old('sub', $account->sub) == 'Toko Anggota' ? 'selected' : '' }}>Toko Anggota</option>
                        <option value="Toko Umum" {{ old('sub', $account->sub) == 'Toko Umum' ? 'selected' : '' }}>Toko Umum</option>
                        <option value="Simpan Pinjam Anggota" {{ old('sub', $account->sub) == 'Simpan Pinjam Anggota' ? 'selected' : '' }}>Simpan Pinjam Anggota</option>
                        <option value="Simpan Pinjam Umum" {{ old('sub', $account->sub) == 'Simpan Pinjam Umum' ? 'selected' : '' }}>Simpan Pinjam Umum</option>
                        <option value="Jasa Anggota" {{ old('sub', $account->sub) == 'Jasa Anggota' ? 'selected' : '' }}>Jasa Anggota</option>
                        <option value="Jasa Umum" {{ old('sub', $account->sub) == 'Jasa Umum' ? 'selected' : '' }}>Jasa Umum</option>
                        <option value="Lain-lain" {{ old('sub', $account->sub) == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                        <option value="Toko" {{ old('sub', $account->sub) == 'Toko' ? 'selected' : '' }}>Toko</option>
                        <option value="Operasional" {{ old('sub', $account->sub) == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $account->name) }}"
                           required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('account.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
