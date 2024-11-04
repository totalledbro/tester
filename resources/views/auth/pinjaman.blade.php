@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Pinjaman Buku</h1>
    <h2>Buku yang Telah Dipinjam</h2>
</div>

<div class="fade-in">
    <hr>
    <div class="limit">
        <p><strong>Sisa Limit Pinjaman Anda:</strong> {{ $loanLimit }}</p>
        <p><strong>Buku yang Saat Ini Dipinjam:</strong> <span id="current-loans-count">{{ $loans->count() }}</span></p>
    </div>

    <!-- Search Box -->
    <div class="search-box">
        <input type="text" id="search-input" placeholder="Cari buku..." autocomplete="off">
    </div>
    <div id="book-list" class="book-list" style="display: none;"></div>
    <!-- Button to Open Riwayat Modal -->
    <button id="riwayatButton" class="riwayat-btn">Riwayat Pinjaman</button>
</div>

<!-- Confirmation Modal -->
<div id="confirmReturnModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Apakah Anda yakin ingin mengembalikan buku ini?</p>
        <button id="confirmReturnBtn">Ya, Kembalikan</button>
        <button id="cancelReturnBtn">Batal</button>
    </div>
</div>

<!-- Riwayat Modal -->
<div id="riwayatModal" class="modal">
    <div class="modal-content">
        <span class="close-riwayat">&times;</span>
        <h2>Riwayat Pinjaman</h2>
        <table class="riwayat-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun</th>
                    <th>Tanggal Kembali</th>
                </tr>
            </thead>
            <tbody id="riwayat-tbody">
                <!-- Data will be loaded here by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var bacaBookUrl = "{{ route('baca', ['id' => 'PLACEHOLDER']) }}";

    $(document).ready(function() {
        let loans = @json($loans); // Initially fetched loans

        function displayBooks(loans) {
            $('#book-list').empty().show();
            let nonReturnedLoans = loans.filter(loan => loan.return_date === null);
            $('#current-loans-count').text(nonReturnedLoans.length);
            if (nonReturnedLoans.length === 0) {
                $('#book-list').append('<p style="color: red; text-align: center;">Tidak ada buku yang ditemukan.</p>');
            } else {
                let list = '<ul>';
                nonReturnedLoans.forEach(function(loan) {
                    let book = loan.book;
                    let bookCoverUrl = `{{ asset('storage/cover') }}/${book.pdf_url.split('/').pop().replace('.pdf', '.png')}`;
                    let bacaUrl = bacaBookUrl.replace('PLACEHOLDER', loan.id);
                    let limitDate = new Date(loan.limit_date);
                    let today = new Date();
                    
                    // Only compare dates, ignoring the time
                    limitDate.setHours(0, 0, 0, 0);
                    today.setHours(0, 0, 0, 0);

                    let timeDiff = limitDate.getTime() - today.getTime();
                    let daysLeft = Math.ceil(timeDiff / (1000 * 3600 * 24));

                    let limitDateString = limitDate.toLocaleDateString('id-ID', {
                        year: 'numeric', month: 'long', day: 'numeric'
                    });
                    
                    // Add "(1 hari lagi)" for books expiring in 1 day
                    if (daysLeft === 1) {
                        limitDateString += " (1 hari lagi)";
                    }
                    let itemClass = daysLeft <= 1 ? 'book-item urgent' : 'book-item';
                    list += `
                        <li class="${itemClass}" data-loan-id="${loan.id}">
                            <div class="cover">
                                <img data-src="${bookCoverUrl}" alt="Book Cover" class="lazy-load">
                            </div>
                            <div class="book-details">
                                <h3>${capitalizeWords(book.title)}</h3>
                                <p><strong>Penulis:</strong> ${capitalizeWords(book.author)}</p>
                                <p><strong>Tahun:</strong> ${book.year}</p>
                                <p><strong>Batas Pinjam:</strong> ${limitDateString}</p>
                            </div>
                            <div class="book-actions">
                                <a href="${bacaUrl}" class="read-book">Baca</a>
                                <button class="return-book">Kembalikan</button>
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

        // Handle Riwayat Modal
        $('#riwayatButton').on('click', function() {
            $.ajax({
                url: '/returned-books',
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    let returnedBooks = response.returnedBooks;
                    let tbody = $('.riwayat-table tbody');
                    tbody.empty();
                    if (returnedBooks.length === 0) {
                        tbody.append('<tr><td colspan="4" style="text-align:center;">No returned books found.</td></tr>');
                    } else {
                        console.log(response.returnedBooks);

                    returnedBooks.forEach(loan => {
                        if (loan.book) {
                            let title = loan.book.title.replace(/\b\w/g, char => char.toUpperCase());
                            let author = loan.book.author.replace(/\b\w/g, char => char.toUpperCase());
                            let returnDate = new Date(loan.return_date).toLocaleDateString('id-ID', {
                                year: 'numeric', month: 'long', day: 'numeric'
                            });
                            tbody.append(`
                                <tr>
                                    <td>${title}</td>
                                    <td>${author}</td>
                                    <td>${loan.book.year}</td>
                                    <td>${returnDate}</td>
                                </tr>
                            `);
                        } else {
                            console.warn(`Loan ID ${loan.id} has no book data.`);
                        }
                    });
                    }
                    $('#riwayatModal').css('display', 'block');
                },
                error: function(xhr) {
                    console.error("Failed to fetch returned books:", xhr);
                }
            });
        });

    $('.close-riwayat').on('click', function() {
        $('#riwayatModal').css('display', 'none');
    });

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
    margin-top: 5px;
    animation: grow 1s ease-out;
    will-change: transform, opacity;
}

.welcome-section h1, .welcome-section h2 {
    margin-bottom: 10px;
    color: white;
}    
/* Limit section */
.limit {
    text-align: center;
    color: white;
}

/* Search box styles */
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

/* Book list styles */
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

.book-item.urgent {
    background-color: rgba(253, 170, 201, 0.9);
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
    text-align: left;
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

.book-actions {
    display: flex;
    flex-direction: column;
    margin-left: auto;
    gap: 10px;
}

.read-book {
    background-color: #007bff; /* Current "Kembalikan Buku" button color */
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    text-align: center;
}

.return-book {
    background-color: #28a745; /* New color for "Kembalikan Buku" button */
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    text-align: center;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    text-align: center; /* Center-align text in the modal */
}

.close, .close-riwayat {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus, .close-riwayat:hover, .close-riwayat:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

#confirmReturnBtn, #cancelReturnBtn {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    margin: 5px;
    cursor: pointer;
}

#confirmReturnBtn:hover, #cancelReturnBtn:hover {
    background-color: #0056b3;
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
.riwayat-btn {
    background-color: #17a2b8;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    margin-top: 20px;
    cursor: pointer;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.riwayat-btn:hover {
    background-color: #138496;
}
.riwayat-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.riwayat-table th, .riwayat-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.riwayat-table th {
    background-color: #f8f9fa;
    font-weight: bold;
}
/* Responsive styles */
@media (max-width: 768px) {
    .cover {
        width: 30%;
        margin-right: 10px;
    }

    .book-details {
        margin-left: 10px;
    }

    .book-actions {
        margin-left: 0;
        margin-top: 10px;
    }

    .search-box input[type="text"] {
        width: 90%;
    }

    .modal-content {
        width: 90%;
    }
}

@media (max-width: 768px) {
    .cover {
        width: 50%;
        margin-right: 10px;
    }

    .book-details, .book-details h3{
        margin: 0 0 10px;
        text-align: center;
    }

    .book-item {
        flex-direction: column;
        align-items: center;
    }

    .book-actions {
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
    }
}
</style>
