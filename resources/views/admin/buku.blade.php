@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Data Buku</h1>
    <div class="content">
        <div class="header">
            <button class="add-btn" onclick="openForm()">Tambah Buku</button>
        </div>

        <div class="form-popup" id="bookForm">
            <div class="form-box add">
                <span class="close-btn material-symbols-rounded" onclick="closeForm()">close</span>
                <div class="form-content">
                    <h2>Tambah buku</h2>
                    <form id="add-form" method="POST" action="{{ route('addbook') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="title" id="title" required>
                            <label>Judul</label>
                        </div>
                        <div class="input-field">
                            <input type="text" name="author" id="author" required>
                            <label>Penulis</label>
                        </div>
                        <div class="input-field">
                            <input type="text" name="year" id="year" required oninput="restrictToNumbers(this)">
                            <label>Tahun</label>
                        </div>
                        <div class="input-field">
                            <select name="category_id" id="category_id" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories->sortBy('name') as $category)
                                    <option value="{{ $category->id }}">{{ ucwords($category->name) }}</option>
                                @endforeach
                            </select>
                            <label>Kategori</label>
                        </div>
                        <div class="input-field">
                            <input type="file" name="pdf" id="pdf" accept=".pdf" required>
                            <label>Upload PDF</label>
                        </div>
                        <button type="submit" class="button">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
        <input type="text" id="search-input" placeholder="Cari buku..." oninput="filterBooks()">
        <h2>Daftar Buku</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 200px;">Judul</th>
                        <th style="width: 150px;">Penulis</th>
                        <th style="width: 100px;">Tahun</th>
                        <th style="width: 100px;">Stok</th>
                        <th style="width: 150px;">Kategori</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="book-list">
                    @foreach ($books as $book)
                    <tr class="book-entry" data-title="{{ strtolower($book->title) }}" data-author="{{ strtolower($book->author) }}" data-year="{{ $book->year }}" data-stock="{{ $book->stock }}" data-category="{{ strtolower($book->category ? $book->category->name : 'N/A') }}">
                        <td>{{ ucwords($book->title) }}</td>
                        <td>{{ ucwords($book->author) }}</td>
                        <td>{{ $book->year }}</td>
                        <td>{{ $book->stock }}</td>
                        <td>{{ $book->category ? ucwords($book->category->name) : 'N/A' }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="edit-btn" onclick="openEditForm({{ $book->id }})">Edit</button>
                                <form action="{{ route('deletebook', $book->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <ion-icon name="trash-bin-outline"></ion-icon> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <div class="form-popup" id="editForm{{$book->id}}">
                        <div class="form-box edit">
                            <span class="close-btn material-symbols-rounded" onclick="closeEditForm({{ $book->id }})">close</span>
                            <div class="form-content">
                                <h2>Edit buku</h2>
                                <form id="edit-form-{{ $book->id }}" method="POST" action="{{ route('updatebook', $book->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="input-field">
                                        <input type="text" name="title" id="title{{ $book->id }}" value="{{ $book->title }}">
                                        <label>Judul</label>
                                    </div>
                                    <div class="input-field">
                                        <input type="text" name="author" id="author{{ $book->id }}" value="{{ $book->author }}">
                                        <label>Penulis</label>
                                    </div>
                                    <div class="input-field">
                                        <input type="text" name="year" id="tahun{{ $book->id }}" value="{{ $book->year }}">
                                        <label>Tahun</label>
                                    </div>
                                    <div class="input-field">
                                        <select name="category_id" id="category_id{{ $book->id }}">
                                            <option value="" disabled selected>Pilih Kategori</option>
                                            @foreach($categories->sortBy('name') as $category)
                                                <option value="{{ $category->id }}" {{ $category->id == $book->category_id ? 'selected' : '' }}>{{  ucwords($category->name) }}</option>
                                            @endforeach
                                        </select>
                                        <label>Kategori</label>
                                    </div>
                                    <div class="input-field">
                                        <input type="file" name="pdf" id="pdf{{ $book->id }}" accept=".pdf">
                                        <label>Upload PDF</label>
                                    </div>
                                    <button type="submit" class="button">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-controls">
            <form method="GET" action="{{ url('buku') }}">
                <label for="perPage">Tampilkan:</label>
                <select name="perPage" id="perPage" onchange="this.form.submit()">
                    <option value="10"{{ $perPage == 10 ? ' selected' : '' }}>10</option>
                    <option value="20"{{ $perPage == 20 ? ' selected' : '' }}>20</option>
                </select>
            </form>
            <div class="pagination-info">
                Halaman {{ $books->currentPage() }} dari {{ $books->lastPage() }}
            </div>
            <div class="pagination-links">
            @if ($books->onFirstPage())
                <span class="disabled"><ion-icon name="chevron-back-outline"></ion-icon></span>
            @else
                <a href="{{ $books->previousPageUrl() }}"><ion-icon name="chevron-back-outline"></ion-icon></a>
            @endif

            @php
                $current = $books->currentPage();
                $last = $books->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
                @endphp

            @if ($start > 1)
                <a href="{{ $books->url(1) }}">1</a>
                @if ($start > 2)
                    <span>...</span>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="current">{{ $page }}</span>
                @else
                    <a href="{{ $books->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($end < $last)
                @if ($end < $last - 1)
                    <span>...</span>
                @endif
                <a href="{{ $books->url($last) }}">{{ $last }}</a>
            @endif

            @if ($books->hasMorePages())
                <a href="{{ $books->nextPageUrl() }}"><ion-icon name="chevron-forward-outline"></ion-icon></a>
            @else
                <span class="disabled"><ion-icon name="chevron-forward-outline"></ion-icon></span>
            @endif
            </div>
        </div>
    </div>
</div>

<div id="overlay" onclick="closeAllForms()"></div>

<script>
function openForm() {
    document.getElementById("bookForm").classList.add("active");
    document.getElementById("overlay").style.display = "block"; 
}

function closeForm() {
    document.getElementById("bookForm").classList.remove("active");
    document.getElementById("overlay").style.display = "none"; 
}

function openEditForm(id) {
    document.getElementById("editForm" + id).classList.add("active");
    document.getElementById("overlay").style.display = "block";
}

function closeEditForm(id) {
    document.getElementById("editForm" + id).classList.remove("active");
    document.getElementById("overlay").style.display = "none"; 
}

function closeAllForms() {
    document.querySelectorAll('.form-popup.active').forEach((form) => {
        form.classList.remove('active');
    });
    document.getElementById("overlay").style.display = "none";
}

function filterBooks() {
    let input = document.getElementById('search-input').value.toLowerCase();
    let books = document.querySelectorAll('.book-entry');
    books.forEach((book) => {
        let title = book.dataset.title;
        let author = book.dataset.author;
        let year = book.dataset.year;
        let stock = book.dataset.stock;
        let category = book.dataset.category;
        if (title.includes(input) || author.includes(input) || year.includes(input) || stock.includes(input) || category.includes(input)) {
            book.style.display = "";
        } else {
            book.style.display = "none";
        }
    });
}

function restrictToNumbers(input) {
    input.value = input.value.replace(/\D/g, '');
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

.header {
    width: 100%;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-bottom: 20px;
    gap: 20px; /* Add some gap between button and input */
}

#search-input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    margin-bottom: 20px;
}

.add-btn {
    padding: 10px 20px;
    background-color: var(--blue);
    color: var(--white);
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-btn:hover {
    background-color: #1e1c59;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
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
    background-color: #e0e0e0;
}

.missing-data {
    color: red;
}

.form-popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9;
    width: 90%; 
    max-width: 500px; 
    background-color: white;
    border: 1px solid #ddd;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
    overflow: hidden; 
}

.form-popup.active {
    display: block;
}

.form-content {
    padding: 20px;
}

.h2 {
    margin-bottom: 20px;
}

.input-field {
    position: relative;
    margin-bottom: 20px;
}

.input-field input,
.input-field select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    outline: none;
}

.input-field label {
    position: absolute;
    top: -5px;
    left: 10px;
    background: white;
    padding: 0 5px;
    color: #aaa;
    font-size: 12px;
}

.button {
    padding: 10px 20px;
    background-color: var(--blue);
    color: var(--white);
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.button:hover {
    background-color: #1e1c59;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 8;
}

.action-buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.edit-btn, .delete-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.edit-btn {
    background-color: #4CAF50;
    color: white;
}

.edit-btn:hover {
    background-color: #45a049;
}

.delete-btn {
    background-color: #f44336;
    color: white;
}

.delete-btn:hover {
    background-color: #d32f2f;
}

.delete-form {
    display: inline-block;
    margin: 0;
}

.close-btn {
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    color: #aaa;
}

.close-btn:hover {
    color: #000;
}

.book-entry {
    /* Removed animation for simplicity */
}

.pagination-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.pagination-controls form {
    display: flex;
    align-items: center;
}

.pagination-controls label {
    margin-right: 10px;
}

.pagination-controls select {
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.pagination-info {
    margin-left: 20px;
    margin-right: 20px;
}

.pagination-links {
    display: flex;
    align-items: center;
}

.pagination-links a, .pagination-links span {
    margin: 0 5px;
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
}

.pagination-links a:hover {
    background-color: #f0f0f0;
}

.pagination-links .current {
    background-color: #007bff;
    color: white;
    border: 1px solid #007bff;
}

.pagination-links .disabled {
    color: #ccc;
    pointer-events: none;
}
</style>
