@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>{{ ucwords($category->name) }}</h1>
    <h2>Daftar Buku dalam Kategori</h2>
</div>

<div class="fade-in">
    <hr>

    <!-- Search Box -->
    <div class="search-box">
        <input type="text" id="search-input" placeholder="Cari buku..." autocomplete="off">
    </div>
    <div id="book-list" class="book-list" style="display: none;"></div>
    
</div>
<!-- Modal Form for Loan -->
<div id="loanModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="loanForm" method="POST">
            @csrf
            <input type="hidden" id="user_id" name="user_id" value="{{ Auth::check() ? Auth::user()->id : '' }}">
            <input type="hidden" id="book_id" name="book_id">
            <div class="cover" id="book-cover"></div>
            <div id="loan-details">
                <p><strong>Judul:</strong> <span id="book-title"></span></p>
                <p><strong>Penulis:</strong> <span id="book-author"></span></p>
                <p><strong>Tahun:</strong> <span id="book-year"></span></p>
                <p><strong>Kategori:</strong> <span id="book-category"></span></p>
                <p><strong>Tanggal Sekarang:</strong> <span id="today-date"></span></p>
                <p><strong>Tanggal Batas:</strong> <span id="date-limit"></span></p>
            </div>
            <button type="submit" id="pinjam-button">Submit</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let books = @json($books);

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
                            <p><strong>Penulis</strong> ${capitalizeWords(book.author)}</p>
                            <p><strong>Tahun</strong> ${book.year}</p>
                        </div>
                        <div class="book-action">
                            @auth
                                <a href="#" class="pinjam-button" data-book='${JSON.stringify(book)}'>Pinjam</a>
                            @endauth
                        </div>
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

    // Show modal when "Pinjam" button is clicked
    $(document).on('click', '.pinjam-button', function(e) {
        e.preventDefault();
        let bookData = $(this).data('book');
        let bookCoverUrl = `{{ asset('storage/cover') }}/${bookData.pdf_url.split('/').pop().replace('.pdf', '.png')}`;
        let todayDate = new Date().toLocaleDateString('id-ID', {
            year: 'numeric', month: 'long', day: 'numeric'
        });
        let limitDate = new Date();
        limitDate.setDate(limitDate.getDate() + 7);
        let limitDateString = limitDate.toLocaleDateString('id-ID', {
            year: 'numeric', month: 'long', day: 'numeric'
        });

        $('#book-cover').html(`<img src="${bookCoverUrl}" alt="Book Cover">`);
        $('#today-date').text(todayDate);
        $('#date-limit').text(limitDateString);

        $('#book_id').val(bookData.id);
        $('#book-title').text(bookData.title);
        $('#book-author').text(bookData.author);
        $('#book-year').text(bookData.year);
        $('#book-category').text(bookData.category.name);

        $('#loanModal').css('display', 'block');
    });

    // Close modal when close button is clicked
    $(document).on('click', '.close', function() {
        $('#loanModal').css('display', 'none');
        $('#book-cover').empty();
        $('#today-date').empty();
        $('#date-limit').empty();
        $('#book-title').empty();
        $('#book-author').empty();
        $('#book-year').empty();
        $('#book-category').empty();
    });

    // Submit the form when it's submitted
    $('#loanForm').on('submit', function(e) {
        e.preventDefault();

        let bookData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            book_id: $('#book_id').val(),
            user_id: $('#user_id').val()
        };

        $.ajax({
            type: 'POST',
            url: '{{ route("addloan") }}',
            data: bookData,
            success: function(response) {
                displayMessage('Loan request submitted successfully!', 'success');
            },
            error: function(xhr, status, error) {
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred while submitting the loan request.';
                displayMessage(errorMessage, 'error');
            }
        });

        $('#loanModal').css('display', 'none');
    });

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

    // Function to display messages
    function displayMessage(message, type) {
        $('#messages').html(`<div class="${type}">${message}</div>`);
        setTimeout(function() {
            $('#messages').empty();
        }, 5000);
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                observer.unobserve(entry.target);
            }
        });
    });

    const elements = document.querySelectorAll('.fade-in');
    elements.forEach(el => observer.observe(el));

});
</script>
@endsection

</script>
@endsection

<style>
@keyframes grow {
    from {
        transform: scale(0.5);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
/* Welcome Section */
.welcome-section {
    text-align: center;
    margin-top: 20px;
    animation: grow 1s ease-out;
    will-change: transform, opacity;
}

.welcome-section h1, .welcome-section h2 {
    margin-bottom: 10px;
    color: white;
}      
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
    color: black;
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

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    text-align: left;
}

.modal-content .cover {
    text-align: center; /* Center the content horizontally */
    margin-bottom: 20px;
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

.book-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

#loan-details {
    text-align: left;
    width: 100%;
}

#loanForm {
    text-align: center;
}

#loanForm button {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
}

#loanForm button:hover {
    background-color: #0056b3;
}

.success-message {
    color: green;
    text-align: center;
}

.error-message {
    color: red;
    text-align: center;
}

.fade-in {
    opacity: 0;
    transform: translateY(50px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in.show {
    opacity: 1;
    transform: translateY(0);
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
