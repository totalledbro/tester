@extends('layouts.app')

@section('content')
<div class="main">
    <h1>Kategori</h1>
    <div class="content">
        <div class="category">
        <button class="add-btn" onClick="openForm()">Tambah Kategori</button>
        <div class="form-popup" id="categoryForm">
            <span class="close-btn material-symbols-rounded" onClick="closeForm()">close</span>
            <div class="form-box add">
                <div class="form-content" >
                    <h2>Tambah Kategori</h2>
                    <form id="add-form" method="POST" action="{{ route('addcategory') }}">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="name" id="categoryName" required>
                            <label>Nama Kategori</label>
                        </div>
                        <button type="submit" class="tambah">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
        <h2>Daftar Kategori</h2>
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
                        <td>{{ ucwords($category->name) }}</td>
                        <td>
                        <button class="edit-btn" onClick="openEditForm({{ $category->id }})">edit</button>
                            <form action="{{ route('deletecategory', $category->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                    <ion-icon name="trash-bin-outline"></ion-icon>
                                    </button>
                            </form>
                        <td><!-- Add actions here --></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
        <div class="overlay" id="overlay" onClick="closeForm()"></div> <!-- Add overlay element -->
        <div class="form-popup" id="editForm{{$category->id}}">
                            <span class="close-btn material-symbols-rounded" onClick="closeEditForm({{ $category->id }})">close</span>

                                <div class="form-box edit">
                                    <div class="form-content" >
                                        <h2>Edit Kategori</h2>
                                        <form id="edit-form-{{ $category->id }}" method="POST" action="{{ route('updatecategory', $category->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="input-field">
                                                <input type="text" name="name" id="categoryName{{ $category->id }}" value="{{ $category->name }}" required>
                                                <label>Nama Kategori</label>
                                            </div>
                                            <button type="submit" class="update">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
    </div>
</div>
@endsection

<style>
</style>

<script>
        const form = document.getElementById('add-form');
        function openForm() {
            document.getElementById("categoryForm").classList.add("active");
            document.getElementById("overlay").style.display = "block"; // Show overlay
        }

        function closeForm() {
            document.getElementById("categoryForm").classList.remove("active");
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



        form.addEventListener('submit', function(event) {
            // Get the input element by its ID
            const input = document.getElementById('categoryName');

            // Convert the input value to lowercase and update the input value
            input.value = input.value.toLowerCase();
        });
</script>
