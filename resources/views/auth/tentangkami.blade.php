@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Tentang Kami</h1>
    <h2>Selamat Datang di Perpustakaan Digital Kalinganyar</h2>
</div>
<div class="fade-in">
<hr>
<div class="info-section">
    <div class="about-us fade-in">
        <h3>Tentang Kami</h3>
        <p style="text-align:justify">Perpustakaan Digital Kalinganyar adalah portal pengetahuan untuk semua masyarakat. Kami berkomitmen untuk menyediakan akses mudah dan cepat ke berbagai koleksi buku, jurnal, dan artikel digital yang relevan dan bermanfaat. Kami percaya bahwa pengetahuan adalah kunci kemajuan bangsa, dan dengan adanya perpustakaan digital ini, kami berharap untuk dapat mendukung pendidikan dan pengembangan diri bagi semua kalangan. Mari bergabung dan manfaatkan perpustakaan digital ini untuk masa depan yang lebih baik.</p>
        
        <p style="text-align:justify">Desa kami, Desa Kalinganyar terletak di Kecamatan Arjasa, Kabupaten Sumenep, Provinsi Jawa Timur. Desa kami merupakan desa yang  memiliki lingkungan yang asri dan masyarakat yang ramah. Selain itu, desa kami juga dikelilingi oleh perbukitan dan persawahan yang hijau, menjadikannya tempat yang ideal untuk berwisata dan menikmati keindahan alam.</p>
        
        <p><a href="https://sidesa.kalinganyar.id">Situs Desa Kalinganyar</a></p>
        <div class="contact-info">
            <a href="mailto:desakalinganyarkangean@gmail.com"><ion-icon name="mail-outline"></ion-icon> desakalinganyarkangean@gmail.com</a>
            <a href="https://maps.app.goo.gl/L9QfqxectzywQiGv6"><ion-icon name="location-outline"></ion-icon> JL.Lorong Dalem-Kalianyar Arjasa Sumenep</a>
        </div>
        <div class="map-box fade-in">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63260.869420111665!2d115.3584246!3d-6.9324915!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2ddabd69c53ff509%3A0x99b46bd9d20e02fc!2sKalinganyar%2C%20Kalianyar%2C%20Arjasa%2C%20Kabupaten%20Sumenep%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1720139672257!5m2!1sid!2sid" 
                width="100%" 
                height="300" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade" 
                sandbox="allow-scripts allow-same-origin allow-popups allow-forms">
            </iframe>
        </div>
    </div>
</div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        margin-top: 5px;
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
        margin-right: 20px;
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
</style>
