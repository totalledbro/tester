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
<div id="book-list" class="book-list"></div>
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
                    let list = '<ul>';
                    data.forEach(function(book) {
                        list += `
                            <li class="book-item">
                                <h3>${capitalizeWords(book.title)}</h3>
                                <p><strong>Author:</strong> ${capitalizeWords(book.author)}</p>
                                <p><strong>Year:</strong> ${book.year}</p>
                            </li>
                        `;
                    });
                    list += '</ul>';
                    $('#book-list').append(list);
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
        padding: 20px;
    }

    .book-list ul {
        list-style-type: none;
        padding: 0;
    }

    .book-item {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 20px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .book-item h3 {
        margin: 0 0 10px;
    }

    .book-item p {
        margin: 5px 0;
    }

    .book-item p strong {
        color: #333;
    }
</style>
