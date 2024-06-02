@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Data Buku</h1>
    <div class="content">
        <div class="category">
            <button class="add-btn" onclick="openForm()">Tambah Buku</button>
            <input type="text" id="search-input" placeholder="Cari buku..." oninput="filterBooks()">
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

        <div class="pagination">
            <button id="prev-page" disabled>&laquo; Previous</button>
            <button id="next-page">Next &raquo;</button>
        </div>
    </div>
    <div class="overlay" id="overlay" onclick="closeAllForms()"></div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const entries = document.querySelectorAll('.book-entry');
    let currentIndex = 0;
    const entriesPerPage = 10;

    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');
    const overlay = document.getElementById('overlay');

    if (!prevPageButton || !nextPageButton || !overlay) {
        console.error('Pagination buttons or overlay not found');
        return;
    }

    const showEntries = () => {
        for (let i = 0; i < entries.length; i++) {
            entries[i].style.display = (i >= currentIndex && i < currentIndex + entriesPerPage) ? '' : 'none';
        }
        prevPageButton.disabled = currentIndex === 0;
        nextPageButton.disabled = currentIndex + entriesPerPage >= entries.length;

        // Add the show class with a delay to trigger the fade-in effect
        setTimeout(() => {
            entries.forEach((entry, index) => {
                if (index >= currentIndex && index < currentIndex + entriesPerPage) {
                    entry.classList.add('show');
                } else {
                    entry.classList.remove('show');
                }
            });
        }, 100); // 100ms delay to ensure the elements are in the DOM
    };

    prevPageButton.addEventListener('click', () => {
        currentIndex = Math.max(currentIndex - entriesPerPage, 0);
        showEntries();
    });

    nextPageButton.addEventListener('click', () => {
        currentIndex = Math.min(currentIndex + entriesPerPage, entries.length - entriesPerPage);
        showEntries();
    });

    showEntries();

    overlay.addEventListener("click", closeAllForms);
});

function openForm() {
    document.getElementById("bookForm").classList.add("active");
    document.getElementById("overlay").style.display = "block"; 
}

function closeForm() {
    document.getElementById("bookForm").classList.remove("active");
    document.getElementById("overlay").style.display = "none"; 
}

function openEditForm(editFormId) {
    document.getElementById('editForm' + editFormId).classList.add("active");
    document.getElementById("overlay").style.display = "block"; 
}

function closeEditForm(editFormId) {
    document.getElementById('editForm' + editFormId).classList.remove("active");
    document.getElementById("overlay").style.display = "none"; 
}

function restrictToNumbers(input) {
    input.value = input.value.replace(/\D/g, '');
    if (input.value.length > 4) {
        input.value = input.value.slice(0, 4);
    }
}

function filterBooks() {
    let keyword = document.getElementById('search-input').value.toLowerCase();
    let bookList = document.getElementById('book-list');
    let rows = bookList.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        let title = rows[i].getAttribute('data-title');
        let author = rows[i].getAttribute('data-author');
        let year = rows[i].getAttribute('data-year');
        let stock = rows[i].getAttribute('data-stock');
        let category = rows[i].getAttribute('data-category');

        if (
            title.includes(keyword) ||
            author.includes(keyword) ||
            year.includes(keyword) ||
            stock.includes(keyword) ||
            category.includes(keyword)
        ) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

function closeAllForms() {
    closeForm();
    let editForms = document.querySelectorAll('.form-popup.active');
    editForms.forEach(form => {
        form.classList.remove('active');
    });
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

.category {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

#search-input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 200px; 
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 8px;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f2f2f2;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
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
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.book-entry.show {
    opacity: 1;
    transform: translateY(0);
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.pagination button {
    padding: 10px 20px;
    margin: 0 5px;
    background-color: var(--blue);
    color: var(--white);
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.pagination button:disabled {
    background-color: #ddd;
    cursor: not-allowed;
}

.pagination button:hover:not(:disabled) {
    background-color: #1e1c59;
}
</style>
