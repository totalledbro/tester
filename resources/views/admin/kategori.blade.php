@extends('layouts.app')

@section('content')
<div class="main">
    <h1>All Categories</h1>
    <div class="content">
   
        <h2>Daftar Produk</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Aksi</th> <!-- Empty header cell for buttons -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td><!-- Add actions here --></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
     </div>
</div>
@endsection
<style>
    /* Style for the responsive table */
    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
    }

    /* Style for the table itself */
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    .table th {
        background-color: #f2f2f2;
        text-align: left;
    }

    /* Add additional styling as needed */
</style>
