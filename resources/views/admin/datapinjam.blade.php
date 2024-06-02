@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>History Peminjaman</h1>
    <div class="content">
        <input type="text" id="search-input" placeholder="Cari data peminjaman..." oninput="filterLoans()">
        <div class="table-responsive">
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
                <tbody id="loan-list">
                    @foreach ($loans as $loan)
                    <tr class="loan-entry" data-name="{{ strtolower($loan->user->first_name . ' ' . $loan->user->last_name) }}" data-book="{{ strtolower($loan->book->title) }}">
                        <td>{{ ucwords($loan->user->first_name) . ' ' . ucwords($loan->user->last_name) }}</td>
                        <td>{{ ucwords($loan->book->title) }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('j F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->limit_date)->translatedFormat('j F Y') }}</td>
                        <td>{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->translatedFormat('j F Y') : 'Buku belum dikembalikan' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button id="prev-page" disabled>&laquo; Previous</button>
            <button id="next-page">Next &raquo;</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const entries = document.querySelectorAll('.loan-entry');
        let currentIndex = 0;
        const entriesPerPage = 10;

        const prevPageButton = document.getElementById('prev-page');
        const nextPageButton = document.getElementById('next-page');

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
    });

    function filterLoans() {
        const keyword = document.getElementById('search-input').value.toLowerCase();
        const rows = document.getElementById('loan-list').getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const name = rows[i].getAttribute('data-name');
            const book = rows[i].getAttribute('data-book');
            rows[i].style.display = (name.includes(keyword) || book.includes(keyword)) ? '' : 'none';
        }

        // Reset pagination after filtering
        const visibleRows = Array.from(rows).filter(row => row.style.display === '');
        const entriesPerPage = 10;
        let currentPage = 0;

        const showVisibleEntries = () => {
            visibleRows.forEach((row, index) => {
                row.style.display = (index >= currentPage * entriesPerPage && index < (currentPage + 1) * entriesPerPage) ? '' : 'none';
            });

            document.getElementById('prev-page').disabled = currentPage === 0;
            document.getElementById('next-page').disabled = currentPage >= Math.ceil(visibleRows.length / entriesPerPage) - 1;

            // Add the show class with a delay to trigger the fade-in effect
            setTimeout(() => {
                visibleRows.forEach((entry, index) => {
                    if (index >= currentPage * entriesPerPage && index < (currentPage + 1) * entriesPerPage) {
                        entry.classList.add('show');
                    } else {
                        entry.classList.remove('show');
                    }
                });
            }, 100); // 100ms delay to ensure the elements are in the DOM
        };

        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 0) {
                currentPage--;
                showVisibleEntries();
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < Math.ceil(visibleRows.length / entriesPerPage) - 1) {
                currentPage++;
                showVisibleEntries();
            }
        });

        showVisibleEntries();
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

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.pagination button {
    padding: 10px 20px;
    border: 1px solid #ddd;
    background-color: #f2f2f2;
    cursor: pointer;
    margin: 0 5px;
}

.pagination button:disabled {
    cursor: not-allowed;
    background-color: #e0e0e0;
}

/* Add the fade-in effect */
.loan-entry {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.loan-entry.show {
    opacity: 1;
    transform: translateY(0);
}
</style>
