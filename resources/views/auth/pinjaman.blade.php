@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Pinjaman Buku</h1>
    <h2>Buku yang Telah Dipinjam</h2>
</div>

<hr>

<!-- Search Box -->
<div class="search-box">
    <input type="text" id="search-input" placeholder="Cari buku..." autocomplete="off">
</div>
<div id="book-list" class="book-list" style="display: none;"></div>

<!-- Confirmation Modal -->
<div id="confirmReturnModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Apakah Anda yakin ingin mengembalikan buku ini?</p>
        <button id="confirmReturnBtn">Ya, Kembalikan</button>
        <button id="cancelReturnBtn">Batal</button>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let loans = @json($loans); // Initially fetched loans
    let books = loans.map(loan => loan.book);

    function displayBooks(loans) {
    $('#book-list').empty().show();
    let nonReturnedLoans = loans.filter(loan => loan.return_date === null);
    if (nonReturnedLoans.length === 0) {
        $('#book-list').append('<p style="color: red; text-align: center;">Tidak ada buku yang ditemukan.</p>');
    } else {
        let list = '<ul>';
        nonReturnedLoans.forEach(function(loan) {
            let book = loan.book;
            let bookCoverUrl = `{{ asset('storage/cover') }}/${book.pdf_url.split('/').pop().replace('.pdf', '.png')}`;
            list += `
                <li class="book-item" data-loan-id="${loan.id}">
                    <div class="cover">
                        <img data-src="${bookCoverUrl}" alt="Book Cover" class="lazy-load">
                    </div>
                    <div class="book-details">
                        <h3>${capitalizeWords(book.title)}</h3>
                        <p><strong>Author:</strong> ${capitalizeWords(book.author)}</p>
                        <p><strong>Year:</strong> ${book.year}</p>
                    </div>
                    <div class="book-action">
                        <button class="return-book">Kembalikan Buku</button>
                    </div>
                </li>
            `;
        });
        list += '</ul>';
        $('#book-list').append(list);
        lazyLoadImages();
    }
}


    function capitalizeWords(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

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

    $('#book-list').hide();

    $('#search-input').on('input', function() {
        let keyword = $(this).val().toLowerCase();
        if (keyword.length > 0) {
            let filteredLoans = loans.filter(loan => 
                loan.book.title.toLowerCase().includes(keyword) ||
                loan.book.author.toLowerCase().includes(keyword)
            );
            displayBooks(filteredLoans);
        } else {
            $('#book-list').hide();
        }
    });

    if (loans.length > 0) {
        displayBooks(loans);
    }

    $('#book-list').on('click', '.return-book', function() {
        let loanId = $(this).closest('.book-item').data('loan-id');
        
        // Show the confirmation modal
        $('#confirmReturnModal').css('display', 'block');

        // Handle "Confirm" button click
        $('#confirmReturnBtn').on('click', function() {
            $.ajax({
                url: `/return-book/${loanId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    location.reload(); // Reload the page to update the list
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });

            // Close the modal after the action is confirmed
            $('#confirmReturnModal').css('display', 'none');
        });

        // Handle "Cancel" button click
        $('#cancelReturnBtn, .close').on('click', function() {
            // Close the modal without taking any action
            $('#confirmReturnModal').css('display', 'none');
        });
    });
});
</script>
@endsection

<style>
.return-book {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
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
}

.book-details p {
    margin: 5px 0;
}

.book-details p strong {
    color: #333;
}

.book-action {
    margin-left: auto; /* Aligns the button to the right */
}

/* Modal Styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
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

.modal-content button {
    margin: 5px;
}

</style>
