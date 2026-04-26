@extends('template.master')

@section('master_active', 'active')
@section('saving_type_active', 'active')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="h3 mb-3">
        <strong>Edit Jenis Simpanan</strong>
    </h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('saving_type.update', $saving_type->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $saving_type->name) }}"
                        autofocus
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Akun</label>
                    <select name="account_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($accounts as $a)
                            <option
                                value="{{ $a->id }}"
                                {{ old('account_id', $saving_type->account_id) == $a->id ? 'selected' : '' }}
                            >
                                {{ $a->code }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bisa ditarik ?</label>
                    <select name="is_withdrawable" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        <option
                            value="0"
                            {{ old('is_withdrawable', $saving_type->is_withdrawable) == 0 ? 'selected' : '' }}
                        >
                            Tidak
                        </option>
                        <option
                            value="1"
                            {{ old('is_withdrawable', $saving_type->is_withdrawable) == 1 ? 'selected' : '' }}
                        >
                            Ya
                        </option>
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('saving_type.index') }}" class="btn btn-secondary me-2">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
