@extends('template.master')

@section('master_active', 'active')
@section('account_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Tambah Akun</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('account.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="number" name="code" class="form-control" value="{{ old('code') }}" autofocus required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sub</label>
                    <select name="sub" class="form-control">
                        <option value="Aset Lancar">Aset Lancar</option>
                        <option value="Aset Tetap">Aset Tetap</option>
                        <option value="Jangka Pendek">Jangka Pendek</option>
                        <option value="Simpanan">Simpanan</option>
                        <option value="Modal">Modal</option>
                        <option value="Toko Anggota">Toko Anggota</option>
                        <option value="Toko Umum">Toko Umum</option>
                        <option value="Simpan Pinjam Anggota">Simpan Pinjam Anggota</option>
                        <option value="Simpan Pinjam Umum">Simpan Pinjam Umum</option>
                        <option value="Jasa Anggota">Jasa Anggota</option>
                        <option value="Jasa Umum">Jasa Umum</option>
                        <option value="Lain-lain">Lain-lain</option>
                        <option value="Toko">Toko</option>
                        <option value="Operasional">Operasional</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('account.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
