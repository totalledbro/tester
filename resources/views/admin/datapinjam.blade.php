@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>History Peminjaman</h1>
    <div class="content">
        <button id="print-button" onclick="openPrintModal()">Print</button>
        <input type="text" id="search-input" placeholder="Cari data peminjaman..." oninput="filterLoans()">
        <div class="table-responsive">
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
                    @foreach ($loans as $loan)
                        <tr class="loan-entry" data-name="{{ strtolower($loan->user->first_name . ' ' . $loan->user->last_name) ?? 'Tidak ada data' }}" data-book="{{ strtolower($loan->book->title ?? 'Tidak ada data') }}" data-loan-date="{{ $loan->loan_date }}">
                            <td>
                                @if ($loan->user && $loan->user->first_name && $loan->user->last_name)
                                    {{ ucwords($loan->user->first_name) . ' ' . ucwords($loan->user->last_name) }}
                                @else
                                    <span class="missing-data">Tidak ada data</span>
                                @endif
                            </td>
                            <td>
                                @if ($loan->book && $loan->book->title)
                                    {{ ucwords($loan->book->title) }}
                                @else
                                    <span class="missing-data">Tidak ada data</span>
                                @endif
                            </td>
                            <td>
                                @if ($loan->loan_date)
                                    {{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('j F Y') }}
                                @else
                                    <span class="missing-data">Tidak ada data</span>
                                @endif
                            </td>
                            <td>
                                @if ($loan->limit_date)
                                    {{ \Carbon\Carbon::parse($loan->limit_date)->translatedFormat('j F Y') }}
                                @else
                                    <span class="missing-data">Tidak ada data</span>
                                @endif
                            </td>
                            <td>
                                @if ($loan->return_date)
                                    {{ \Carbon\Carbon::parse($loan->return_date)->translatedFormat('j F Y') }}
                                @else
                                    Buku belum dikembalikan
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-controls">
            <form method="GET" action="{{ url('datapinjam') }}">
                <label for="perPage">Tampilkan:</label>
                <select name="perPage" id="perPage" onchange="this.form.submit()">
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
                <a href="{{ $loans->previousPageUrl() }}"><ion-icon name="chevron-back-outline"></ion-icon></a>
            @endif

            @php
                $current = $loans->currentPage();
                $last = $loans->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp

            @if ($start > 1)
                <a href="{{ $loans->url(1) }}">1</a>
                @if ($start > 2)
                    <span>...</span>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="current">{{ $page }}</span>
                @else
                    <a href="{{ $loans->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($end < $last)
                @if ($end < $last - 1)
                    <span>...</span>
                @endif
                <a href="{{ $loans->url($last) }}">{{ $last }}</a>
            @endif

            @if ($loans->hasMorePages())
                <a href="{{ $loans->nextPageUrl() }}"><ion-icon name="chevron-forward-outline"></ion-icon></a>
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
function filterLoans() {
    const keyword = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('.loan-entry');

    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const book = row.getAttribute('data-book');
        if (name.includes(keyword) || book.includes(keyword)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openPrintModal() {
    document.getElementById('printModal').style.display = 'block';
}

function closePrintModal() {
    document.getElementById('printModal').style.display = 'none';
}

function printTable() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (startDate && endDate && startDate > endDate) {
        document.getElementById('dateError').style.display = 'block';
        return;
    } else {
        document.getElementById('dateError').style.display = 'none';
    }

    const params = new URLSearchParams();
    if (startDate) params.append('startDate', startDate);
    if (endDate) params.append('endDate', endDate);

    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenElement) {
        console.error('CSRF token not found');
        return;
    }
    const csrfToken = csrfTokenElement.getAttribute('content');

    fetch(`/print?${params.toString()}`, {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(loans => {
        console.log('Loans fetched:', loans);

        // Sort loans based on loan date
        loans.sort((a, b) => new Date(a.loan_date) - new Date(b.loan_date));

        const filteredRows = loans.map(loan => {
            const userFirstName = loan.user ? loan.user.first_name.toLowerCase() : 'tidak ada data';
            const userLastName = loan.user ? loan.user.last_name.toLowerCase() : 'tidak ada data';
            const bookTitle = loan.book ? loan.book.title.toLowerCase() : 'tidak ada data';
            const loanDate = loan.loan_date || '';
            const limitDate = loan.limit_date || '';
            const returnDate = loan.return_date || 'Buku belum dikembalikan';

            return `
                <tr class="loan-entry" data-name="${userFirstName} ${userLastName}" data-book="${bookTitle}" data-loan-date="${loanDate}">
                    <td>${loan.user ? ucwords(loan.user.first_name) + ' ' + ucwords(loan.user.last_name) : '<span class="missing-data">Tidak ada data</span>'}</td>
                    <td>${loan.book ? ucwords(loan.book.title) : '<span class="missing-data">Tidak ada data</span>'}</td>
                    <td>${loanDate ? new Date(loanDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '<span class="missing-data">Tidak ada data</span>'}</td>
                    <td>${limitDate ? new Date(limitDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '<span class="missing-data">Tidak ada data</span>'}</td>
                    <td>${loan.return_date ? new Date(returnDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Buku belum dikembalikan'}</td>
                </tr>
            `;
        }).join('');

        const printWindow = window.open('', '_blank');
        const today = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        let periodText = startDate && endDate ? ` Data Peminjaman Buku Tanggal ${new Date(startDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })} Hingga ${new Date(endDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`
            : startDate ? `Tanggal ${new Date(startDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`
            : 'Semua Periode';

        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Data Peminjaman</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                        }
                        h1 {
                            text-align: center;
                            font-size: 24px;
                        }
                        h2 {
                            text-align: center;
                            font-size: 18px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid black;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f2f2f2;
                            text-align: center;
                        }
                        tr {
                            page-break-inside: avoid;
                        }
                        .missing-data {
                            color: red;
                        }
                        .header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-bottom: 20px;
                        }
                        .header .title {
                            font-size: 32px;
                            text-align: center;
                            width: 100%;
                        }
                        .header .date {
                            font-size: 14px;
                            text-align: right;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="title">Perpustakaan Digital Kalinganyar</div>
                    </div>
                    <h2>${periodText}</h2>
                    <div class="date">Dicetak Pada ${today}</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Batas</th>
                                <th>Tanggal Kembali</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filteredRows}
                        </tbody>
                    </table>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        printWindow.onafterprint = function() {
            window.location.reload();
        };
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function ucwords(str) {
    return str.replace(/^(.)|\s+(.)/g, function (letter) {
        return letter.toUpperCase();
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
    background-color: #e0e0e0; /* Highlight color */
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
