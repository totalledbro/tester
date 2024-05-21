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
<div id="book-list" class="book-list" style="display: none;"></div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let books = @json($books); // Initially fetched books

    // Function to display books
    function displayBooks(books) {
        $('#book-list').empty().show();
        if (books.length === 0) {
            $('#book-list').append('<p style="color: red; text-align: center;">Tidak ada buku yang ditemukan.</p>');
        } else {
            let list = '<ul>';
            books.forEach(function(book) {
                let bookCoverUrl = `{{ asset('storage/cover') }}/${book.pdf_url.split('/').pop().replace('.pdf', '.png')}`;
                list += `
                    <li class="book-item">
                        <div class="cover">
                            <img data-src="${bookCoverUrl}" alt="Book Cover" class="lazy-load">
                        </div>
                        <div class="book-details">
                            <h3>${capitalizeWords(book.title)}</h3>
                            <p><strong>Author:</strong> ${capitalizeWords(book.author)}</p>
                            <p><strong>Year:</strong> ${book.year}</p>
                        </div>
                        @auth
                        <div class="book-action">
                            <a href="#" class="pinjam-button">Pinjam</a>
                        </div>
                        @endauth
                    </li>
                `;
            });
            list += '</ul>';
            $('#book-list').append(list);
            lazyLoadImages();
        }
    }

    // Function to capitalize words
    function capitalizeWords(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

    // Function to lazy load images
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        const config = {
            rootMargin: '0px 0px 50px 0px',
            threshold: 0.01
        };

        let observer;
        if ('IntersectionObserver' in window) {
            observer = new IntersectionObserver(onIntersection, config);
            images.forEach(image => {
                if (image.dataset.src) {
                    observer.observe(image);
                }
            });
        } else {
            images.forEach(image => {
                loadImage(image);
            });
        }

        function onIntersection(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadImage(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }

        function loadImage(image) {
            image.src = image.dataset.src;
            image.removeAttribute('data-src');
        }
    }

    // Initially hide the book list
    $('#book-list').hide();

    // Search input event handler
    $('#search-input').on('input', function() {
        let keyword = $(this).val().toLowerCase();
        if (keyword.length > 0) {
            let filteredBooks = books.filter(book => 
                book.title.toLowerCase().includes(keyword) ||
                book.author.toLowerCase().includes(keyword)
            );
            displayBooks(filteredBooks);
        } else {
            $('#book-list').hide();
        }
    });

    // Initial display (if needed)
    if (books.length > 0) {
        displayBooks(books);
    }
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
    color: black;
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
