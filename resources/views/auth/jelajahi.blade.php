@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Jelajahi Koleksi</h1>
    <h2>Temukan Buku Dari Koleksi</h2>
</div>

<hr>

<!-- Search Box -->
<div class="search-box">
    <input type="text" id="search-input" placeholder="Cari buku..." autocomplete="off">
</div>

<!-- List of Books -->
<div class="book-list" id="book-list">
    @foreach($books as $book)
        <div class="book-item">
            <div class="cover-container">
                <img src="{{ asset('storage/cover/' . pathinfo($book->pdf_url, PATHINFO_FILENAME) . '.png') }}" alt="Cover of {{ $book->title }}" class="book-cover">
            </div>
            <div class="book-info">
                <h3>{{ ucwords($book->title) }}</h3>
                <p>Penulis: {{ ucwords($book->author) }}</p>
                <p>Tahun: {{ $book->year }}</p>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function capitalizeWords(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

    $('#search-input').on('input', function() {
        let keyword = $(this).val();
        $.ajax({
            url: '{{ route("search") }}',
            type: 'GET',
            data: { keyword: keyword },
            success: function(data) {
                $('#book-list').empty();
                if (data.length === 0) {
                    $('#book-list').append('<p>Tidak ada buku yang ditemukan.</p>');
                } else {
                    data.forEach(function(book) {
                        $('#book-list').append(
                            `<div class="book-item">
                                <div class="cover-container">
                                    <img src="/storage/cover/${book.pdf_url.split('/').pop().replace('.pdf', '.png')}" alt="Cover of ${capitalizeWords(book.title)}" class="book-cover">
                                </div>
                                <div class="book-info">
                                    <h3>${capitalizeWords(book.title)}</h3>
                                    <p>Penulis: ${capitalizeWords(book.author)}</p>
                                    <p>Tahun: ${book.year}</p>
                                </div>
                            </div>`
                        );
                    });
                }
            }
        });
    });
});
</script>
@endsection

<style>
    .search-box {
        text-align: center;
        margin: 20px 0;
    }

    .search-box input[type="text"] {
        width: 100%;
        max-width: 600px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .book-list {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        padding: 20px;
    }

    .book-item {
        display: flex;
        flex-direction: column; /* Align items vertically */
        align-items: center;
        width: 100%;
        max-width: 600px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        text-align: left;
    }

    .cover-container {
        width: 100%; /* Ensure the container takes up the full width */
        display: flex;
        justify-content: center; /* Center the image horizontally */
        margin-bottom: 10px; /* Space between image and book info */
    }

    .book-cover {
        max-width: 100px;
        height: auto;
    }

    .book-info {
        width: 100%; /* Ensure book info takes up the full width */
    }

    .book-info h3 {
        margin: 0;
        text-align: center;
    }

    .book-info p {
        margin: 5px 0;
        text-align: center;
    }
</style>
