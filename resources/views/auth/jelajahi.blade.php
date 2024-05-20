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
                        let bookCoverUrl = `{{ asset('storage/cover') }}/${book.pdf_url.split('/').pop().replace('.pdf', '.png')}`;
                        list += `
                            <li class="book-item">
                                <div class="cover">
                                    <img src="${bookCoverUrl}" alt="Book Cover">
                                </div>
                                <div class="book-details">
                                    <h3>${capitalizeWords(book.title)}</h3>
                                    <p><strong>Author:</strong> ${capitalizeWords(book.author)}</p>
                                    <p><strong>Year:</strong> ${book.year}</p>
                                </div>
                                ${@json(auth()->check()) ? `
                                <div class="book-action">
                                    <a href="#" class="pinjam-button">Pinjam</a>
                                </div>` : ''}
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
        padding: 20px;
    }

    .book-list ul {
        list-style-type: none;
        padding: 0;
    }

    .book-item {
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
    }

    .cover {
        width: 20%;
        max-width: 200px;
        height: auto;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .cover img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        margin-right: 20px;
    }

    .book-details {
        flex: 1;
        text-align: left;
        margin-left: 20px;
    }

    .book-details h3 {
        margin: 0 0 10px;
        text-transform: uppercase;
        text-align: left;
    }

    .book-details p {
        margin: 5px 0;
    }

    .book-details p strong {
        color: #333;
    }

    .book-action {
        margin-left: auto;
    }

    .pinjam-button {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
    }

    .pinjam-button:hover {
        background-color: #0056b3;
    }

    @media (max-width: 600px) {
        .book-item {
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cover {
            width: 100%;
            max-width: 150px;
            height: auto;
            object-fit: cover;
            transition: transform 0.3s ease;
            margin-bottom: 10px;
        }

        .book-details {
            width: 100%;
            text-align: center;
            margin-left: 0;
        }

        .book-details h3 {
            text-align: center;
        }

        .book-action {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }

        .pinjam-button {
            width: 100%;
        }
    }
</style>
