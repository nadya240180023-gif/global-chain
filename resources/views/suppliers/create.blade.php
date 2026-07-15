@extends('adminlte::page')

@section('title', 'Tambah Supplier')

@section('content_header')
<h1>Tambah Supplier</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Form Tambah Supplier</h3>
    </div>

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <label>Kode Supplier</label>
                <input type="text"
                       name="kode_supplier"
                       class="form-control"
                       value="{{ old('kode_supplier') }}"
                       required>
            </div>

            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text"
                       name="nama_supplier"
                       class="form-control"
                       value="{{ old('nama_supplier') }}"
                       required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label>Telepon</label>
                <input type="text"
                       name="telepon"
                       class="form-control"
                       value="{{ old('telepon') }}"
                       required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat"
                          class="form-control"
                          rows="3">{{ old('alamat') }}</textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>

            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

    </form>

</div>

@stop