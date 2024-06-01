@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Data Buku</h1>
    <div class="content">
        <div class="category">
            <button class="add-btn" onClick="openForm()">Tambah Buku</button>
            <input type="text" id="search-input" placeholder="Cari buku..." oninput="filterBooks()">
        </div>
        
        <div class="form-popup" id="bookForm">
            <div class="form-box add">
                <span class="close-btn material-symbols-rounded" onClick="closeForm()">close</span>
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
                    <tr data-title="{{ strtolower($book->title) }}" data-author="{{ strtolower($book->author) }}" data-year="{{ $book->year }}" data-stock="{{ $book->stock }}" data-category="{{ strtolower($book->category ? $book->category->name : 'N/A') }}">
                        <td>{{ ucwords($book->title) }}</td>
                        <td>{{ ucwords($book->author) }}</td>
                        <td>{{ $book->year }}</td>
                        <td>{{ $book->stock }}</td>
                        <td>{{ $book->category ? ucwords($book->category->name) : 'N/A' }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="edit-btn" onClick="openEditForm({{ $book->id }})">Edit</button>
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
                            <span class="close-btn material-symbols-rounded" onClick="closeEditForm({{ $book->id }})">close</span>
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
    </div>
    <div class="overlay" id="overlay" onClick="closeForm()"></div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Adding event listener to the overlay to close any form when clicked
        document.getElementById("overlay").addEventListener("click", closeAllForms);
    });

    function openForm() {
        document.getElementById("bookForm").classList.add("active");
        document.getElementById("overlay").style.display = "block"; // Show overlay
    }

    function closeForm() {
        document.getElementById("bookForm").classList.remove("active");
        document.getElementById("overlay").style.display = "none"; // Hide overlay
    }

    function openEditForm(editFormId) {
        // Show the edit form with the corresponding ID
        document.getElementById('editForm' +editFormId).classList.add("active");
        document.getElementById("overlay").style.display = "block"; // Show overlay
    }
    function closeEditForm(editFormId) {
    // Close the form with the specified ID
    document.getElementById('editForm' + editFormId).classList.remove("active");
    document.getElementById("overlay").style.display = "none"; // Hide overlay
}
    function restrictToNumbers(input) {
        // Remove non-numeric characters from the input value
        input.value = input.value.replace(/\D/g, '');
        if (input.value.length > 4) {
            input.value = input.value.slice(0, 4);
        }
    }

    // Filter books based on search input
    function filterBooks() {
        let keyword = document.getElementById('search-input').value.toLowerCase();
        let bookList = document.getElementById('book-list');
        let rows = bookList.getElementsByTagName('tr');
        let found = false; // Variable to track if any book is found

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
                found = true; // Book found, set found to true
            } else {
                rows[i].style.display = "none";
            }
        }

        // If no book is found, hide the entire table
        if (!found) {
            bookList.style.display = "none";
        } else {
            bookList.style.display = ""; // Show the table if books are found
        }
    }

    // Function to close all forms and overlay
    function closeAllForms() {
        // Close the add form
        closeForm();

        // Close all edit forms
        let editForms = document.querySelectorAll('.form-popup.active');
        editForms.forEach(form => {
            form.classList.remove('active');
        });
    }
</script>

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
    width: 200px; /* Adjust the width as needed */
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
    width: 90%; /* Changed to 90% for better mobile responsiveness */
    max-width: 500px; /* Set a max-width */
    background-color: white;
    border: 1px solid #ddd;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
    overflow: hidden; /* Ensure the content fits within the popup */
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
    gap: 10px; /* Adjust gap as needed */
}

.edit-btn, .delete-btn {
    padding: 8px 12px; /* Increased padding */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.edit-btn {
    background-color: #4CAF50; /* Green */
    color: white;
}

.edit-btn:hover {
    background-color: #45a049;
}

.delete-btn {
    background-color: #f44336; /* Red */
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
</style>
