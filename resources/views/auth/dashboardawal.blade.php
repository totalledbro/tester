@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Selamat Datang dan Selamat Membaca</h1>
    <h2>Mari Jelajahi Dunia Pengetahuan di Perpustakaan Digital</h2>
</div>

<hr>

<h3>Koleksi Terbaru</h3>

<div class="book-container">
    @foreach($books as $book)
        <div class="book-card">
            <img src="{{ asset('storage/cover/' . pathinfo($book->pdf_url, PATHINFO_FILENAME) . '.png') }}" alt="Book Cover" class="book-cover">
            <div class="book-info">
                <h4 class="book-title">{{ ucwords($book->title) }}</h4>
                <p class="book-author">{{ ucwords($book->author) }}</p>
                <p class="book-year">{{ $book->year }}</p>
            </div>
        </div>
    @endforeach
</div>

<hr>

<div class="info-section">
    <div class="map-box">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15844.740299399678!2d115.29940899999998!3d-6.86841345!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f30!3m3!1m2!1s0x2ddabd69c53ff505%3A0x4e6a833e66168e8c!2sKalinganyar%2C%20Kalianyar%2C%20Arjasa%2C%20Kabupaten%20Sumenep%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1717473694329!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <div class="about-us">
        <h3>Tentang Kami</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla.</p>
        <p><a href="#">Situs Desa Kalinganyar</a></p>
        <div class="contact-info">
            <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
            <a href="#"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="#"><ion-icon name="logo-whatsapp"></ion-icon></a>
            <a href="tel:+62123456789"><ion-icon name="call"></ion-icon> +62123456789</a>
        </div>
    </div>
</div>
@endsection

<style>
    /* Welcome Section */
    .welcome-section {
        text-align: center;
        margin-top: 20px;
    }

    .welcome-section h1, .welcome-section h2 {
        margin-bottom: 10px;
    }

    hr {
        margin: 20px 0;
        border: 0;
        border-top: 1px solid #ddd;
    }

    /* Book Section */
    .book-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
        justify-content: center;
        align-items: center;
    }

    .book-card {
        position: relative;
        width: calc(100% - 40px); /* Adjust the width for smaller screens */
        max-width: 300px;
        height: auto; /* Allow the height to adjust according to content */
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px; /* Add some space at the bottom */
    }

    .book-cover {
        width: 100%;
        height: auto; /* Allow the height to adjust according to content */
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .book-card:hover .book-cover {
        transform: scale(1.1);
    }

    .book-info {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .book-card:hover .book-info {
        transform: translateY(0);
    }

    .book-title, .book-author, .book-year {
        margin: 5px 0;
        text-align: center;
    }

    /* Info Section */
    .info-section {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        padding: 20px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        max-width: 1200px;
        text-align: left;
    }

    .about-us {
        flex: 1;
        margin-left: 20px;
    }

    .about-us h3 {
        margin-bottom: 10px;
        color:black;
    }

    .about-us p {
        margin-bottom: 20px;
    }

    .about-us a {
        color: #007bff;
        text-decoration: none;
    }

    .about-us a:hover {
        text-decoration: underline;
    }

    .contact-info {
        margin-top: 20px;
    }

    .contact-info a {
        display: inline-block;
        margin-right: 10px;
        color: #333;
        text-decoration: none;
        font-size: 1.5rem;
    }

    .contact-info a:hover {
        color: #007bff;
    }

    .map-box {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .info-section {
            flex-direction: column;
            align-items: center;
        }

        .map-box, .about-us {
            width: 100%;
            margin: 0;
        }

        .map-box {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 576px) {
        .book-card {
            width: 100%;
            height: auto;
        }

        .book-info {
            position: relative;
            transform: none;
            padding: 5px;
        }
    }

    @media (max-width: 320px) {
        body {
            padding-top: 60px;
        }

        .welcome-section {
            padding: 0 10px;
        }

        .welcome-section h1, .welcome-section h2 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .book-container {
            flex-direction: column;
            align-items: center;
            padding: 0 10px;
        }

        .book-card {
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .book-cover {
            height: auto;
        }

        .book-info {
            position: relative;
            transform: none;
            padding: 5px;
        }

        .info-section {
            flex-direction: column;
            padding: 10px;
        }

        .about-us, .map-box {
            width: 100%;
            margin-right: 0;
            margin-bottom: 20px;
        }

        .map-box {
            width: 100%;
        }
    }
</style>
