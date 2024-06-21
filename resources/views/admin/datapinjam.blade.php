@extends('layouts.app')

@section('content')
<div class="main active">
    <!-- Spinner -->
    <div id="loading-spinner" class="spinner" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    
    <h1>History Peminjaman</h1>
    <div class="content">
        <button id="print-button" onclick="openPrintModal()">Print</button>
        
        <form method="GET" action="{{ url('datapinjam') }}" id="search-form">
            <input type="text" name="search" id="search-input" placeholder="Cari data peminjaman..." value="{{ request('search') }}" autocomplete="off">
        </form>
        
        <div class="table-responsive" id="table-container">
            <table class="table" id="loan-table">
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
                    @include('partial.loan_table', ['loans' => $loans])
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-controls">
            <form method="GET" action="{{ url('datapinjam') }}" id="pagination-form">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <label for="perPage">Tampilkan:</label>
                <select name="perPage" id="perPage" onchange="submitPaginationForm()">
                    <option value="10"{{ $perPage == 10 ? ' selected' : '' }}>10</option>
                    <option value="20"{{ $perPage == 20 ? ' selected' : '' }}>20</option>
                </select>
            </form>
            <div class="pagination-info">
                Halaman {{ $loans->currentPage() }} dari {{ $loans->lastPage() }}
            </div>
            <div class="pagination-links">
                @if ($loans->onFirstPage())
                    <span class="disabled"><ion-icon name="chevron-back-outline"></ion-icon></span>
                @else
                    <a href="{{ $loans->previousPageUrl() }}" onclick="fetchPage(event, '{{ $loans->previousPageUrl() }}')"><ion-icon name="chevron-back-outline"></ion-icon></a>
                @endif

                @php
                    $current = $loans->currentPage();
                    $last = $loans->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                @if ($start > 1)
                    <a href="{{ $loans->url(1) }}" onclick="fetchPage(event, '{{ $loans->url(1) }}')">1</a>
                    @if ($start > 2)
                        <span>...</span>
                    @endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $loans->url($page) }}" onclick="fetchPage(event, '{{ $loans->url($page) }}')">{{ $page }}</a>
                    @endif
                @endfor

                @if ($end < $last)
                    @if ($end < $last - 1)
                        <span>...</span>
                    @endif
                    <a href="{{ $loans->url($last) }}" onclick="fetchPage(event, '{{ $loans->url($last) }}')">{{ $last }}</a>
                @endif

                @if ($loans->hasMorePages())
                    <a href="{{ $loans->nextPageUrl() }}" onclick="fetchPage(event, '{{ $loans->nextPageUrl() }}')"><ion-icon name="chevron-forward-outline"></ion-icon></a>
                @else
                    <span class="disabled"><ion-icon name="chevron-forward-outline"></ion-icon></span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div id="printModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePrintModal()">&times;</span>
        <h2>Pilih Periode Waktu untuk Print</h2>
        <form id="printForm">
            <label for="startDate">Mulai Tanggal:</label>
            <input type="date" id="startDate" name="startDate">
            <label for="endDate">Sampai Tanggal:</label>
            <input type="date" id="endDate" name="endDate">
            <button type="button" onclick="printTable()">Print</button>
            <p id="dateError" style="color: red; display: none;">"Mulai Tanggal" harus lebih awal dari "Sampai Tanggal".</p>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function submitPaginationForm() {
    let form = document.getElementById('pagination-form');
    let formData = new FormData(form);
    let paginationQuery = new URLSearchParams(formData).toString();

    showLoadingSpinner();

    try {
        let response = await fetch(`{{ url('datapinjam') }}?${paginationQuery}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        let data = await response.json();
        updateTableAndPagination(data);
    } catch (error) {
        console.error('Error:', error);
    } finally {
        hideLoadingSpinner();
    }
}

async function fetchPage(event, url) {
    event.preventDefault();

    showLoadingSpinner();

    try {
        let response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        let data = await response.json();
        updateTableAndPagination(data);
    } catch (error) {
        console.error('Error:', error);
    } finally {
        hideLoadingSpinner();
    }
}

function updateTableAndPagination(data) {
    document.getElementById('loan-list').innerHTML = data.html;
    updatePaginationLinks(data);
}

function updatePaginationLinks(data) {
    document.querySelector('.pagination-info').innerText = `Halaman ${data.currentPage} dari ${data.lastPage}`;

    let paginationLinks = '';
    let baseUrl = data.url;
    let searchQuery = document.getElementById('search-input').value;
    let perPage = document.getElementById('perPage').value;

    if (data.currentPage > 1) {
        paginationLinks += `<a href="${baseUrl}?page=${data.currentPage - 1}&search=${searchQuery}&perPage=${perPage}" onclick="fetchPage(event, '${baseUrl}?page=${data.currentPage - 1}&search=${searchQuery}&perPage=${perPage}')"><ion-icon name="chevron-back-outline"></ion-icon></a>`;
    } else {
        paginationLinks += `<span class="disabled"><ion-icon name="chevron-back-outline"></ion-icon></span>`;
    }

    for (let i = 1; i <= data.lastPage; i++) {
        if (i === data.currentPage) {
            paginationLinks += `<span class="current">${i}</span>`;
        } else {
            paginationLinks += `<a href="${baseUrl}?page=${i}&search=${searchQuery}&perPage=${perPage}" onclick="fetchPage(event, '${baseUrl}?page=${i}&search=${searchQuery}&perPage=${perPage}')">${i}</a>`;
        }
    }

    if (data.currentPage < data.lastPage) {
        paginationLinks += `<a href="${baseUrl}?page=${data.currentPage + 1}&search=${searchQuery}&perPage=${perPage}" onclick="fetchPage(event, '${baseUrl}?page=${data.currentPage + 1}&search=${searchQuery}&perPage=${perPage}')"><ion-icon name="chevron-forward-outline"></ion-icon></a>`;
    } else {
        paginationLinks += `<span class="disabled"><ion-icon name="chevron-forward-outline"></ion-icon></span>`;
    }

    document.querySelector('.pagination-links').innerHTML = paginationLinks;
}

function showLoadingSpinner() {
    let spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'flex';
    }
}

function hideLoadingSpinner() {
    let spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
}

document.getElementById('search-input').addEventListener('input', async function () {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(async () => {
        let query = this.value;
        let form = document.getElementById('search-form');
        let formData = new FormData(form);
        let searchQuery = new URLSearchParams(formData).toString();

        showLoadingSpinner();

        try {
            let response = await fetch(`{{ url('datapinjam') }}?${searchQuery}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            let data = await response.json();
            updateTableAndPagination(data);
        } catch (error) {
            console.error('Error:', error);
        } finally {
            hideLoadingSpinner();
        }
    }, 500);
});

function openPrintModal() {
    document.getElementById('printModal').style.display = 'block';
}

function closePrintModal() {
    document.getElementById('printModal').style.display = 'none';
}

async function printTable() {
    let startDate = document.getElementById('startDate').value;
    let endDate = document.getElementById('endDate').value;

    if (startDate > endDate) {
        document.getElementById('dateError').style.display = 'block';
        return;
    }

    document.getElementById('dateError').style.display = 'none';

    let printUrl = '{{ url("printPinjam") }}?startDate=' + startDate + '&endDate=' + endDate;
    let printWindow = window.open(printUrl, '_blank');
    printWindow.print();
}
</script>
@endsection


<style>
/* Spinner Styles */
.spinner {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1050;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}

/* Existing Styles */
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

#print-button {
    padding: 10px 20px;
    border: none;
    background-color: #4CAF50;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 20px;
}

#print-button:hover {
    background-color: #45a049;
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
    background-color: #e0e0e0;
}

.missing-data {
    color: red;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
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
