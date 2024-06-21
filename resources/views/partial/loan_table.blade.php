{{-- loan_table.blade.php --}}
@foreach ($loans as $loan)
<tr class="loan-entry" data-name="{{ strtolower($loan->user->first_name ?? 'tidak ada data') }} {{ strtolower($loan->user->last_name ?? 'tidak ada data') }}" data-book="{{ strtolower($loan->book->title ?? 'tidak ada data') }}">
    <td>{{ $loan->user ? ucwords($loan->user->first_name) . ' ' . ucwords($loan->user->last_name) : 'Tidak ada data' }}</td>
    <td>{{ $loan->book ? ucwords($loan->book->title) : 'Tidak ada data' }}</td>
    <td>{{ $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date)->locale('id')->isoFormat('D MMMM YYYY') : 'Tidak ada data' }}</td>
    <td>{{ $loan->limit_date ? \Carbon\Carbon::parse($loan->limit_date)->locale('id')->isoFormat('D MMMM YYYY') : 'Tidak ada data' }}</td>
    <td>{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->locale('id')->isoFormat('D MMMM YYYY') : 'Buku belum dikembalikan' }}</td>
</tr>
@endforeach
