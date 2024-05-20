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
@endsection

@section('styles')
<style>
    /* General Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Open Sans", sans-serif;
    }

    body {
        width: 100%;
        background: url(/img/login-bg.png) center/cover no-repeat fixed;
        padding-top: 80px;
    }

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

    h3 {
        text-align: center;
        margin-bottom: 20px;
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
        width: 100%;
        max-width: 300px;
        height: 450px;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

    .book-card:hover .book-info {
        transform: translateY(0);
    }

    .book-title, .book-author, .book-year {
        margin: 5px 0;
        text-align: center;
    }

    /* Responsive Styles */
    @media (max-width: 600px) {
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
        
        h3 {
            font-size: 1rem;
            margin-bottom: 10px;
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
    }
</style>
@endsection
