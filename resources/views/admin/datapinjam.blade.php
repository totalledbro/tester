@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>History Peminjaman</h1>
    <div class="content">
        <input type="text" id="search-input" placeholder="Cari data peminjaman..." oninput="filterLoans()">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 200px;">Nama</th>
                        <th style="width: 300px;">Buku</th>
                        <th style="width: 150px;">Tanggal Pinjam</th>
                        <th style="width: 150px;">Tanggal Batas</th>
                        <th style="width: 150px;">Tanggal Kembali</th>
                    </tr>
                </thead>
                <tbody id="loan-list">
                    @foreach ($loans as $loan)
                    <tr data-name="{{ strtolower($loan->user->first_name . ' ' . $loan->user->last_name) }}" data-book="{{ strtolower($loan->book->title) }}">
                        <td>{{ ucwords($loan->user->first_name) . ' ' . ucwords($loan->user->last_name) }}</td>
                        <td>{{ ucwords($loan->book->title) }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('j F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->limit_date)->translatedFormat('j F Y') }}</td>
                        <td>{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->translatedFormat('j F Y') : 'Buku belum dikembalikan' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterLoans() {
        const keyword = document.getElementById('search-input').value.toLowerCase();
        const rows = document.getElementById('loan-list').getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const name = rows[i].getAttribute('data-name');
            const book = rows[i].getAttribute('data-book');
            if (name.includes(keyword) || book.includes(keyword)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
</script>
@endsection

<style>
.content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
}

#search-input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    margin-bottom: 20px;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th, .table td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background-color: #f2f2f2;
}

.table tbody tr:hover {
    background-color: #e0e0e0; /* Highlight color */
}
</style>
