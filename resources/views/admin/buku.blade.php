@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Data Buku</h1>
    <div class="content">
        <div class="category">
        <button class="add-btn" onClick="openForm()">Tambah Buku</button>
        <div class="form-popup" id="bookForm">
            <span class="close-btn material-symbols-rounded" onClick="closeForm()">close</span>
            <div class="form-box add">
                <div class="form-content" >
                    <h2>Tambah buku</h2>
                    <form id="add-form" method="POST" action="{{ route('addbook') }}">
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
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <label>Kategori</label>
                        </div>
                        <div class="input-field">
                            <input type="text" name="pdf" id="pdf"  required>
                            <label>Upload PDF</label>
                        </div>
                        <button type="submit" class="tambah">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
        <h2>Daftar Buku</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Kategori</th> <!-- Empty header cell for buttons -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                    <tr>
                        <td>{{ ucwords($book->title) }}</td>
                        <td>{{ ucwords($book->author) }}</td>
                        <td>{{ $book->year }}</td>
                        <td>{{ $book->stock }}</td>
                        <td>{{ $book->category ? ucwords($book->category->name) : 'N/A' }}</td>
                        <td>
                        <button class="edit-btn" onClick="openEditForm({{ $book->id }})">edit</button>
                            <form action="{{ route('deletebook', $book->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                    <ion-icon name="trash-bin-outline"></ion-icon>
                                    </button>
                            </form>
                    </tr>

                    <div class="form-popup" id="editForm{{$book->id}}">
            <span class="close-btn material-symbols-rounded" onClick="closeEditForm({{ $book->id }})">close</span>
                <div class="form-box edit">
                    <div class="form-content" >
                        <h2>Edit Buku</h2>
                        <form id="edit-form-{{ $book->id }}" method="POST" action="{{ route('updatebook', $book->id) }}">
                            @csrf
                            @method('POST')
                            <div class="input-field">
                                <input type="text" name="name" id="title{{ $book->id }}" value="{{ $book->title }}" required>
                                <label>Judul</label>
                            </div>
                            <div class="input-field">
                                <input type="text" name="author" id="author{{ $book->id }}" value="{{ $book->author }}" required>
                                <label>Penulis</label>
                            </div>
                            <div class="input-field">
                                <input type="text" name="tahun" id="tahun{{ $book->id }}" value="{{ $book->year }}" required>
                                <label>Tahun</label>
                            </div>
                            <div class="input-field">
                                <select name="category_id" id="category_id{{ $book->id }}" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id == $book->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <label>Kategori</label>
                                /div>
                            <div class="input-field">
                                <input type="file" name="pdf" id="pdf{{ $book->id }}" accept=".pdf" required>
                                <label>Upload PDF</label>
                            </div>
                            <button type="submit" class="update">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="overlay" id="overlay" onClick="closeForm()"></div> <!-- Add overlay element -->
        

</div>
@endsection

<script>
        const form = document.getElementById('add-form');
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
            document.getElementById('editForm' + editFormId).classList.add("active");
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
        }}

</script>
