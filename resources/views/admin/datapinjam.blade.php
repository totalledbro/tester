@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>History Peminjaman</h1>
    <div class="content">
        <input type="text" id="search-input" placeholder="Cari data peminjaman..." oninput="filterLoans()">
        <button id="print-button" onclick="openPrintModal()">Print</button>
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
                    @php
                        $index = 0; // Initialize index for pagination
                    @endphp
                    @foreach ($loans as $loan)
                        @if ($index % 20 == 0 && $index > 0)
                            </tbody>
                        </table>
                        <div class="page-break"></div>
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
                        @endif
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
                        @php
                            $index++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
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
    const rows = document.querySelectorAll('.loan-entry');
    const filteredRows = [];

    let printTitle = '';

    // Validate if endDate is earlier than startDate
    if (startDate && endDate && startDate > endDate) {
        document.getElementById('dateError').style.display = 'block';
        return; // Exit function if validation fails
    } else {
        document.getElementById('dateError').style.display = 'none';
    }

    if (startDate && endDate) {
        printTitle = `Data Peminjaman Buku dari ${formatDate(startDate)} hingga ${formatDate(endDate)}`;
    } else if (startDate) {
        printTitle = `Data Peminjaman Buku Tanggal ${formatDate(startDate)}`;
    }

    // Collect actual DOM elements in filteredRows array
    rows.forEach(row => {
        const loanDate = row.getAttribute('data-loan-date');
        if (!startDate || !endDate || (loanDate >= startDate && loanDate <= endDate)) {
            filteredRows.push(row.outerHTML); // Push the HTML content of the row
        }
    });

    // Format current date to dd MonthName yyyy
    const currentDate = new Date();
    const printDate = `${currentDate.getDate()} ${getMonthName(currentDate.getMonth())} ${currentDate.getFullYear()}`;

    const printContents = `
        <div class="print-header">
            <h2>PERPUSTAKAAN DIGITAL KALINGANYAR</h2>
            <h3>${printTitle}</h3>
            <p>Dicetak pada ${printDate}</p>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 200px;">Nama</th>
                    <th style="width: 300px;">Buku</th>
                    <th style="width: 150px;">Tanggal Pinjam</th>
                    <th style="width: 150px;">Tanggal Batas</th>
                    <th style="width: 150px;">Tanggal Kembali</th>
                </tr>
            </thead>
            <tbody>
                ${filteredRows.join('')}
            </tbody>
        </table>
    `;

    const originalContents = document.body.innerHTML;

    document.body.innerHTML = `
        <html>
            <head>
                <title>Perpustakaan Digital Kalinganyar</title>
                <style>
                    @media print {
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
                        .print-header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        h2, h3 {
                            text-transform: capitalize; /* Capitalize each word */
                        }
                        p {
                            text-align: right;
                            margin-top: 10px;
                            margin-bottom: 0;
                        }
                        .page-break {
                            page-break-before: always;
                        }
                        .table tr {
                            page-break-inside: avoid; /* Avoid splitting rows across pages */
                        }
                    }
                </style>
            </head>
            <body>
                ${printContents}
            </body>
        </html>
    `;

    window.print();

    document.body.innerHTML = originalContents;
    location.reload();
}


function formatDate(date) {
    const [yyyy, mm, dd] = date.split('-');
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${parseInt(dd, 10)} ${months[parseInt(mm, 10) - 1]} ${yyyy}`;
}

function getMonthName(monthIndex) {
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return months[monthIndex];
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
</style>
