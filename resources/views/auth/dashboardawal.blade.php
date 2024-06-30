@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Selamat Datang dan Selamat Membaca</h1>
    <h2>Mari Jelajahi Dunia Pengetahuan di Perpustakaan Digital</h2>
</div>

<div class="fade-in">
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
</div>

<div class="info-section">
    <div class="map-box fade-in">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d253488.46258826382!2d115.22165987150804!3d-6.919600167820184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2ddaa486f0484a57%3A0x7b1ed6efa51aa6ea!2sPulau%20Kangean!5e0!3m2!1sid!2sid!4v1717815170595!5m2!1sid!2sid" 
            width="100%" 
            height="300" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade" 
            sandbox="allow-scripts allow-same-origin allow-popups allow-forms">
        </iframe>
    </div>
    <div class="about-us fade-in">
        <h3>Tentang Kami</h3>
        <p style="text-align: center;">Perpustakaan Digital Kalinganyar adalah portal pengetahuan untuk semua masyarakat. Kami berkomitmen untuk menyediakan akses mudah dan cepat ke berbagai koleksi buku, jurnal, dan artikel digital yang relevan dan bermanfaat. Kami percaya bahwa pengetahuan adalah kunci kemajuan bangsa, dan dengan adanya perpustakaan digital ini, kami berharap untuk dapat mendukung pendidikan dan pengembangan diri bagi semua kalangan. Mari bergabung dan manfaatkan perpustakaan digital ini untuk masa depan yang lebih baik.</p>
        <p><a href="https://sidesa.kalinganyar.id">Situs Desa Kalinganyar</a></p>
        <div class="contact-info">
            <a href="mailto:desakalinganyarkangean@gmail.com"><ion-icon name="mail-outline"></ion-icon> desakalinganyarkangean@gmail.com</a>
            <a href="https://maps.app.goo.gl/L9QfqxectzywQiGv6"><ion-icon name="location-outline"></ion-icon> JL.Lorong Dalem-Kalianyar Arjasa Sumenep</a>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var bookCards = document.querySelectorAll('.book-card');
        bookCards.forEach(function(card) {
            card.addEventListener('click', function() {
                this.querySelector('.book-info').classList.toggle('show-info');
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
</script>

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
        width: 300px;
        height: 450px;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .book-cover {
        width: 100%;
        height: 100%;
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

    .book-info.show-info {
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
        animation: grow 1s ease-out;
    }

    .about-us {
        flex: 1;
        margin-left: 20px;
    }

    .about-us h3 {
        margin-bottom: 10px;
        color: black;
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
        display: flex;
        flex-direction: column;
        margin-top: 20px;
    }

    .contact-info a {
        display: inline-block;
        margin-bottom: 10px;
        color: #333;
        text-decoration: none;
        font-size: 1rem;
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

    .fade-in {
        opacity: 0;
        transform: translateY(50px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .fade-in.show {
        opacity: 1;
        transform: translateY(0);
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
        .book-container {
            height: auto; /* Ensure container can grow to fit content */
        }

        .book-card {
            width: 200px; /* Smaller width for smaller screens */
            height: 300px; /* Adjust height accordingly */
        }

        .book-info {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .book-info.show-info {
            transform: translateY(0);
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
            height: auto; /* Ensure container can grow to fit content */
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
