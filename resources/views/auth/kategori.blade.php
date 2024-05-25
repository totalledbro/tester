@extends('layouts.lay')

@section('content')
<div class="welcome-section">
    <h1>Kategori Buku</h1>
    <h2>Jelajahi Buku Berdasarkan Kategori</h2>
</div>

<hr>

<!-- Messages Section -->
<div id="messages"></div>

<div id="category-list" class="category-list"></div>
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
                        <h3>${capitalizeWords(category.name)}</h3>
                        <p>${category.description}</p>
                        <a href="{{ url('/kategori') }}/${category.slug}" class="view-category-button">
                            <ion-icon name="book-outline"></ion-icon> Lihat Buku
                        </a>
                    </div>
                `;
            });
            list += '</div>';
            $('#category-list').append(list);
        }
    }

    // Function to capitalize words
    function capitalizeWords(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

    // Initial display of categories
    displayCategories(categories);
});
</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
@endsection

<style>
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
    padding: 20px;
    width: calc(33.333% - 20px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.category-card h3 {
    margin: 0 0 10px;
    text-transform: uppercase;
    color: black;
}

.category-card p {
    margin: 5px 0;
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

@media (max-width: 768px) {
    .category-card {
        width: calc(50% - 20px);
    }
}

@media (max-width: 480px) {
    .category-card {
        width: 100%;
    }
}
</style>
