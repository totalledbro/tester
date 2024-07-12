@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Kategori Buku</h1>
    <h2>Jelajahi Buku Berdasarkan Kategori</h2>
</div>
<div class="fade-in">
    <hr>
    <div id="category-list" class="category-list"></div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let categories = @json($categories); // Initially fetched categories

    // Function to display categories
    function displayCategories(categories) {
        $('#category-list').empty();
        if (categories.length === 0) {
            $('#category-list').append('<p style="color: red; text-align: center;">Tidak ada kategori yang ditemukan.</p>');
        } else {
            let list = '<div class="category-cards">';
            categories.forEach(function(category) {
                list += `
                    <div class="category-card">
                        <div class="category-background lazy-load" data-src="{{ asset('storage') }}/${category.image_url}"></div>
                        <div class="category-content">
                            <h3>${capitalizeWords(category.name)}</h3>
                            <a href="{{ url('/kategori') }}/${category.slug}" class="view-category-button">
                                <ion-icon name="book-outline"></ion-icon> Lihat Koleksi
                            </a>
                        </div>
                    </div>
                `;
            });
            list += '</div>';
            $('#category-list').append(list);
        }
        lazyLoadImages(); // Call lazy loading function
    }

    // Function to capitalize words
    function capitalizeWords(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

    // Function to lazy load images
    function lazyLoadImages() {
        const lazyImages = document.querySelectorAll('.lazy-load');

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.backgroundImage = `url(${img.getAttribute('data-src')})`;
                    img.classList.remove('lazy-load');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => observer.observe(img));
    }

    // Initial display of categories
    displayCategories(categories);

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
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
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

.category-list {
    padding: 20px;
}

.category-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.category-card {
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid #ccc;
    border-radius: 5px;
    width: calc(33.333% - 20px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
    height: 250px;
    overflow: hidden;
}

.category-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: transform 0.3s ease; /* Smooth zoom-in */
}

.category-card:hover .category-background {
    transform: scale(1.1); /* Zoom in on hover */
}

.category-content {
    position: relative;
    z-index: 2; /* Place content above the background */
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.35);
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
}

.category-card h3 {
    margin: 0 0 10px;
    text-transform: uppercase;
    color: white;
}

.view-category-button {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.view-category-button ion-icon {
    margin-right: 8px;
    font-size: 18px;
}

.view-category-button:hover {
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

/* Responsive Styles */
@media (max-width: 768px) {
    .category-card {
        width: calc(50% - 20px);
        height: 200px;
    }

    .category-content {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .category-card {
        width: 100%;
        height: 150px;
    }

    .category-content {
        padding: 10px;
    }

    .view-category-button {
        padding: 8px 16px;
        font-size: 12px;
    }
}
</style>
