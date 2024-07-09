@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Data Kategori</h1>
    <div class="content">

        <!-- Display Success and Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="category">
            <button class="add-btn" onClick="openForm()">Tambah Kategori</button>
        </div>
        <input type="text" id="search-input" placeholder="Cari kategori..." oninput="filterCategories()">
        <div class="form-popup" id="categoryForm">
            <div class="form-box add">
                <span class="close-btn material-symbols-rounded" onClick="closeForm()" style="display: none;">close</span>
                <div class="form-content">
                    <h2>Tambah Kategori</h2>
                    <form id="add-form" method="POST" action="{{ route('addcategory') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="name" id="categoryName" required>
                            <label>Nama Kategori</label>
                        </div>
                        <div class="input-field">
                            <input type="file" name="image" id="categoryImage" accept="image/*">
                            <label>Gambar Kategori</label>
                        </div>
                        <button type="submit" class="button">Tambah</button>
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
                        <th>Gambar</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="category-list">
                    @foreach ($categories as $category)
                    <tr class="category-entry" data-name="{{ strtolower($category->name) }}">
                        <td>{{ ucwords($category->name) }}</td>
                        <td>
                            @if($category->image_url)
                                <div class="image-container">
                                    <img src="{{ Storage::url($category->image_url) }}" alt="{{ $category->name }}" class="category-image">
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="edit-btn" onClick="openEditForm({{ $category->id }})">
                                    <ion-icon name="create-outline"></ion-icon> 
                                </button>
                                <form action="{{ route('deletecategory', $category->id) }}" method="POST" class="delete-form" onsubmit="return checkCategoryUsage(event, {{ $category->id }}, '{{ ucwords($category->name) }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <ion-icon name="trash-bin-outline"></ion-icon> 
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <div class="form-popup" id="editForm{{$category->id}}">
                        <div class="form-box edit">
                            <span class="close-btn material-symbols-rounded" onClick="closeEditForm({{ $category->id }})">close</span>
                            <div class="form-content">
                                <h2>Edit Kategori</h2>
                                <form id="edit-form-{{ $category->id }}" method="POST" action="{{ route('updatecategory', $category->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="input-field">
                                        <input type="text" name="name" id="categoryName{{ $category->id }}" value="{{ $category->name }}" required>
                                        <label>Nama Kategori</label>
                                    </div>
                                    <div class="input-field">
                                        <input type="file" name="image" id="categoryImage{{ $category->id }}" accept="image/*">
                                        <label>Gambar Kategori</label>
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
        <div class="overlay" id="overlay" onClick="closeForm()"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function checkCategoryUsage(event, categoryId, categoryName) {
        event.preventDefault(); // Prevent the default form submission

        try {
            const response = await fetch(`/category/${categoryId}/check`);
            const data = await response.json();

            if (!data.canDelete) {
                alert('Ada buku yang menggunakan kategori ini.');
                return false;
            }

            if (confirm(`Hapus ${categoryName}?`)) {
                // Submit the form programmatically
                document.querySelector(form[action$="/category/${categoryId}"]).submit();
            } else {
                return false;
            }
        } catch (error) {
            console.error('Error checking category usage:', error);
            return false;
        }
    }

    function filterCategories() {
        const keyword = document.getElementById('search-input').value.toLowerCase();
        const rows = document.getElementById('category-list').getElementsByTagName('tr');
        let found = false;

        for (let i = 0; i < rows.length; i++) {
            const name = rows[i].getAttribute('data-name');
            if (name.includes(keyword)) {
                rows[i].style.display = "";
                found = true;
            } else {
                rows[i].style.display = "none";
            }
        }

        if (!found) {
            document.getElementById('category-list').style.display = "none";
        } else {
            document.getElementById('category-list').style.display = "";
        }
    }

    function openForm() {
        document.getElementById("categoryForm").classList.add("active");
        document.getElementById("overlay").style.display = "block";
    }

    function closeForm() {
        document.getElementById("categoryForm").classList.remove("active");
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
    width: 100%;
    margin-bottom: 20px;
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
    width: 80%; /* Reduced width */
    max-width: 400px; /* Reduced max-width */
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
    gap: 5px; /* Adjust gap as needed */
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
    background-color: #da190b;
}

.image-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.category-image {
    max-width: 50px;
    max-height: 50px;
    border-radius: 50%;
}

.form-box.add, .form-box.edit {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.form-box .close-btn {
    align-self: flex-end;
    cursor: pointer;
    margin-top: 10px;
    margin-right: 10px;
}

.form-content h2 {
    margin-bottom: 20px; /* Added space below heading */
}

/* Fade-in effect */
/* .category-entry {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.category-entry.show {
    opacity: 1;
} */
</style>

