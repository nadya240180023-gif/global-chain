@extends('adminlte::page')

@section('title', 'Data Supplier')

@section('content_header')
<h1>Data Supplier</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Supplier
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Supplier</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th width="170">Aksi</th>
                </tr>
            </thead>

            <tbody>

            @forelse($suppliers as $supplier)

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $supplier->kode_supplier }}</td>
                    <td>{{ $supplier->nama_supplier }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->telepon }}</td>
                    <td>{{ $supplier->status }}</td>

                    <td>
                        <a href="{{ route('suppliers.edit',$supplier->id) }}" class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="{{ route('suppliers.destroy',$supplier->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus supplier ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="text-center">
                        Belum ada data supplier
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@stop