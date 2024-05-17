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
