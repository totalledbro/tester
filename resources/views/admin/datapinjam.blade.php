@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>History Peminjaman</h1>
    <div class="content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Buku</th>
                        <th>
                            <a href="#" class="sort-link" data-sort="loan_date">
                                Tanggal Pinjam
                                @if($sortColumn == 'loan_date')
                                    <ion-icon name="caret-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-outline"></ion-icon>
                                @else
                                    <ion-icon name="caret-down-outline" style="display: none;"></ion-icon>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" class="sort-link" data-sort="limit_date">
                                Tanggal Batas
                                @if($sortColumn == 'limit_date')
                                    <ion-icon name="caret-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-outline"></ion-icon>
                                @else
                                    <ion-icon name="caret-down-outline" style="display: none;"></ion-icon>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" class="sort-link" data-sort="return_date">
                                Tanggal Kembali
                                @if($sortColumn == 'return_date')
                                    <ion-icon name="caret-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-outline"></ion-icon>
                                @else
                                    <ion-icon name="caret-down-outline" style="display: none;"></ion-icon>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody id="loan-table-body">
                    @foreach ($loans as $loan)
                    <tr>
                        <td>{{ ucfirst($loan->user->first_name) . ' ' . ucfirst($loan->user->last_name) }}</td>
                        <td>{{ ucfirst($loan->book->title) }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->limit_date)->format('d-m-Y') }}</td>
                        <td>{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d-m-Y') : 'Buku belum dikembalikan' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Document ready - jQuery version:', $.fn.jquery);

    if (typeof $ !== 'undefined') {
        console.log('jQuery loaded');
    } else {
        console.log('jQuery not loaded');
    }

    $('.sort-link').on('click', function(e) {
        e.preventDefault();
        
        let sortColumn = $(this).data('sort');
        let currentDirection = $(this).find('ion-icon').attr('name');
        let sortDirection = currentDirection.includes('up') ? 'desc' : 'asc';
        
        console.log('Sort link clicked');
        console.log('sortColumn:', sortColumn);
        console.log('currentDirection:', currentDirection);
        console.log('sortDirection:', sortDirection);

        $.ajax({
            url: "{{ route('datapinjam') }}",
            type: "GET",
            data: {
                sort: sortColumn,
                direction: sortDirection
            },
            success: function(response) {
                console.log('AJAX request successful');
                $('#loan-table-body').html(response.html);
                
                // Hide all icons
                $('.sort-link ion-icon').hide();
                
                // Show and set the current sorted column icon
                $(`.sort-link[data-sort="${sortColumn}"] ion-icon`).attr('name', sortDirection === 'asc' ? 'caret-up-outline' : 'caret-down-outline').show();
            },
            error: function(xhr, status, error) {
                console.log('AJAX request failed:', error);
                alert('Failed to fetch sorted data.');
            }
        });
    });
});
</script>
@endsection

<style>
.content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 8px;
    border: 1px solid #000; /* Solid border */
}

.table th {
    background-color: #f2f2f2;
    cursor: pointer;
}

.table th a {
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
}

.table th ion-icon {
    margin-left: 5px;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
}
</style>
