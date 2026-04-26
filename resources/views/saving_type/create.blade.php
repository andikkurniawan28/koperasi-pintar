@extends('template.master')

@section('master_active', 'active')
@section('saving_type_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3"><strong>Tambah Jenis Simpanan</strong></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('saving_type.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" autofocus required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Akun</label>
                    <select name="account_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bisa ditarik ?</label>
                        <select name="account_id" class="form-control select2" required>
                            <option value="">-- Pilih --</option>
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('saving_type.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
