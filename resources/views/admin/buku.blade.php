@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Data Buku</h1>
    <div class="content">
        <div class="header">
            <button class="add-btn" onclick="openForm()">Tambah Buku</button>
            <form method="GET" action="{{ route('buku') }}" id="search-form">
                <input type="text" name="search" id="search-input" placeholder="Cari buku..." value="{{ $search ?? '' }}">
            </form>
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
        <div class="table-responsive" id="table-container">
            {!! isset($html) ? $html : '' !!}
            <div id="loading-spinner" style="display: none;">
                <div class="spinner"></div>
                Loading...
            </div>
        </div>

        <div class="pagination-controls">
            <form method="GET" action="{{ route('buku') }}" id="pagination-form">
                <label for="perPage">Tampilkan:</label>
                <select name="perPage" id="perPage" onchange="submitPaginationForm()">
                    <option value="10"{{ $perPage == 10 ? ' selected' : '' }}>10</option>
                    <option value="20"{{ $perPage == 20 ? ' selected' : '' }}>20</option>
                </select>
                <input type="hidden" name="search" value="{{ $search }}">
            </form>
            <div class="pagination-info">
                Halaman {{ $books->currentPage() }} dari {{ $books->lastPage() }}
            </div>
            <div class="pagination-links">
                @if ($books->onFirstPage())
                    <span class="disabled"><ion-icon name="chevron-back-outline"></ion-icon></span>
                @else
                    <a href="{{ $books->appends(['search' => $search, 'perPage' => $perPage])->previousPageUrl() }}" onclick="fetchPage(event, '{{ $books->appends(['search' => $search, 'perPage' => $perPage])->previousPageUrl() }}')"><ion-icon name="chevron-back-outline"></ion-icon></a>
                @endif

                @php
                    $current = $books->currentPage();
                    $last = $books->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                @if ($start > 1)
                    <a href="{{ $books->appends(['search' => $search, 'perPage' => $perPage])->url(1) }}" onclick="fetchPage(event, '{{ $books->appends(['search' => $search, 'perPage' => $perPage])->url(1) }}')">1</a>
                    @if ($start > 2)
                        <span>...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <span class="active">{{ $i }}</span>
                    @else
                        <a href="{{ $books->appends(['search' => $search, 'perPage' => $perPage])->url($i) }}" onclick="fetchPage(event, '{{ $books->appends(['search' => $search, 'perPage' => $perPage])->url($i) }}')">{{ $i }}</a>
                    @endif
                @endfor

                @if ($end < $last)
                    @if ($end < $last - 1)
                        <span>...</span>
                    @endif
                    <a href="{{ $books->appends(['search' => $search, 'perPage' => $perPage])->url($last) }}" onclick="fetchPage(event, '{{ $books->appends(['search' => $search, 'perPage' => $perPage])->url($last) }}')">{{ $last }}</a>
                @endif

                @if ($books->hasMorePages())
                    <a href="{{ $books->appends(['search' => $search, 'perPage' => $perPage])->nextPageUrl() }}" onclick="fetchPage(event, '{{ $books->appends(['search' => $search, 'perPage' => $perPage])->nextPageUrl() }}')"><ion-icon name="chevron-forward-outline"></ion-icon></a>
                @else
                    <span class="disabled"><ion-icon name="chevron-forward-outline"></ion-icon></span>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openForm() {
    document.getElementById("bookForm").style.display = "block";
}

function closeForm() {
    document.getElementById("bookForm").style.display = "none";
}

function openEditForm(id) {
    document.getElementById(`editForm${id}`).style.display = "block";
}

function closeEditForm(id) {
    document.getElementById(`editForm${id}`).style.display = "none";
}

document.getElementById('search-input').addEventListener('input', function() {
    submitSearchForm();
});

function restrictToNumbers(input) {
    input.value = input.value.replace(/\D/g, '');
}

function showLoadingSpinner() {
    document.getElementById('loading-spinner').style.display = 'flex';
}

function hideLoadingSpinner() {
    document.getElementById('loading-spinner').style.display = 'none';
}

function submitSearchForm() {
    let form = document.getElementById('search-form');
    let formData = new FormData(form);
    let searchQuery = new URLSearchParams(formData).toString();

    showLoadingSpinner();

    fetch(`{{ route('buku') }}?${searchQuery}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('table-container').innerHTML = data.html;
        updatePaginationLinks(data, searchQuery);
        hideLoadingSpinner();
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoadingSpinner();
    });
}

function fetchPage(event, url, searchQuery = '') {
    event.preventDefault();
    showLoadingSpinner();

    fetch(`${url}&${searchQuery}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('table-container').innerHTML = data.html;
        updatePaginationLinks(data, searchQuery);
        hideLoadingSpinner();
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoadingSpinner();
    });
}

function updatePaginationLinks(data, searchQuery) {
    document.querySelector('.pagination-info').innerText = `Halaman ${data.currentPage} dari ${data.lastPage}`;
    
    let paginationLinks = '';
    if (data.currentPage > 1) {
        paginationLinks += `<a href="${data.url + '&page=' + (data.currentPage - 1)}" onclick="fetchPage(event, '${data.url}&page=${data.currentPage - 1}', '${searchQuery}')"><ion-icon name="chevron-back-outline"></ion-icon></a>`;
    } else {
        paginationLinks += `<span class="disabled"><ion-icon name="chevron-back-outline"></ion-icon></span>`;
    }
    
    if (data.currentPage < data.lastPage) {
        paginationLinks += `<a href="${data.url + '&page=' + (data.currentPage + 1)}" onclick="fetchPage(event, '${data.url}&page=${data.currentPage + 1}', '${searchQuery}')"><ion-icon name="chevron-forward-outline"></ion-icon></a>`;
    } else {
        paginationLinks += `<span class="disabled"><ion-icon name="chevron-forward-outline"></ion-icon></span>`;
    }
    
    document.querySelector('.pagination-links').innerHTML = paginationLinks;
}

function submitPaginationForm() {
    let form = document.getElementById('pagination-form');
    form.submit();
}
</script>
@endsection

<style>
/* Add your styles here */
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
    position: relative; /* Added for spinner positioning */
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th, .table td {
    padding: 8px;
    border: 1px solid #ddd;

}

.table th {
    background-color: #f2f2f2;
    text-align: center;
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

#loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    font-size: 18px;
    color: #007bff;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.spinner {
    border: 4px solid #f3f3f3; 
    border-top: 4px solid #3498db; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
