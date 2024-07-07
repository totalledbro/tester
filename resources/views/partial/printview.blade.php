<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Loans</title>
    <style>
        /* Add your custom styles here */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f2f2f2;
            text-align: center;
        }
        h1, h3 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Data Peminjaman Perpustakaan Digital Kalinganyar</h1>
    @if($startDate && $endDate)
        @if(\Carbon\Carbon::parse($startDate)->eq(\Carbon\Carbon::parse($endDate)))
            <h3>Periode tanggal {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</h3>
        @else
            <h3>Periode tanggal {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} hingga {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</h3>
        @endif
    @endif
    <h4>Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h4>
    <table class="table" id="loan-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Batas</th>
                <th>Tanggal Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $index => $loan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ ucwords($loan->user->first_name) . ' ' . ucwords($loan->user->last_name) }}</td>
                    <td>{{ ucwords($loan->book->title) }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->limit_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->return_date)->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
