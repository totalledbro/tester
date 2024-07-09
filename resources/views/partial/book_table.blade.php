{{-- book_table.blade.php --}}
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
                    <button class="edit-btn" onclick="openEditForm({{ $book->id }})"><ion-icon name="create-outline"></ion-icon></button>
                    <form class="delete-form" action="{{ route('deletebook', $book->id) }}" method="POST" onsubmit="return confirmDelete(this, '{{ ucwords($book->title) }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn"><ion-icon name="trash-outline"></ion-icon></button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div id="editFormsContainer">
    @foreach ($books as $book)
    <div class="form-popup" id="editForm{{ $book->id }}">
        <div class="form-box edit">
            <span class="close-btn material-symbols-rounded" onclick="closeEditForm({{ $book->id }})">close</span>
            <div class="form-content">
                <h2>Edit buku</h2>
                <form method="POST" action="{{ route('updatebook', $book->id) }}" enctype="multipart/form-data">
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
                                <option value="{{ $category->id }}" {{ $category->id == $book->category_id ? 'selected' : '' }}>{{ ucwords($category->name) }}</option>
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
</div>

<script>
    function confirmDelete(form, bookTitle) {
        const message = `Hapus ${bookTitle}?`;
        if (confirm(message)) {
            form.submit();
        }
        return false; // Prevent the default form submission
    }

    function openEditForm(id) {
    document.getElementById(`editForm${id}`).style.display = "block";
}

function closeEditForm(id) {
    document.getElementById(`editForm${id}`).style.display = "none";
}
</script>