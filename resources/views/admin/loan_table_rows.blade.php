@foreach ($loans as $loan)
<tr>
    <td>{{ ucfirst($loan->user->first_name) . ' ' . ucfirst($loan->user->last_name) }}</td>
    <td>{{ ucfirst($loan->book->title) }}</td>
    <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d-m-Y') }}</td>
    <td>{{ \Carbon\Carbon::parse($loan->limit_date)->format('d-m-Y') }}</td>
    <td>{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d-m-Y') : 'Buku belum dikembalikan' }}</td>
</tr>
@endforeach
