<?php
// index.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('config.php');

// Default movie list (if no filter applied)
$movies = [];

$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['searchQuery'];
    $searchUrl = TMDB_BASE_URL . 'search/movie?api_key=' . TMDB_API_KEY . '&query=' . urlencode($searchQuery);
    $response = @file_get_contents($searchUrl);  // Suppress warnings if URL fails
    if ($response === FALSE) {
        echo "Error fetching search results.";
        die();
    }
    $movies = json_decode($response, true)['results'];
}

// Genre-based filtering
if (isset($_GET['genre'])) {
    $selectedGenre = $_GET['genre'];
    $genreUrl = TMDB_BASE_URL . 'discover/movie?api_key=' . TMDB_API_KEY . '&with_genres=' . $selectedGenre;
    $response = @file_get_contents($genreUrl);  // Suppress warnings if URL fails
    if ($response === FALSE) {
        echo "Error fetching genre results.";
        die();
    }
    $movies = json_decode($response, true)['results'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlixSphere - Movie Finder</title>
    <link rel="icon" href="logo.jpg" type="image/jpeg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #2980b9, #8e44ad);
            margin: 0;
            padding: 0;
            color: #fff;
        }

        header {
            text-align: left;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        header h1 {
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
        }

        .logo {
            width: 60px;
            height: auto;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-right .user-profile, .header-right .theme-toggle, .header-right .notification {
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
        }

        .nav-bar {
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            padding: 20px;
            align-items: center;
            gap: 20px;
        }

        /* Genre Dropdown */
        .genre-dropdown {
            display: inline-block;
        }

        .genre-dropdown select {
            background-color: #16a085;
            color: white;
            padding: 10px 40px 10px 20px; /* Added padding for the arrow */
            border-radius: 8px;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            font-family: inherit;
            width: 180px;
            transition: background-color 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
        }

        .genre-dropdown select:hover {
            background-color: #1abc9c;
        }

        .genre-dropdown::after {
            content: '\f0d7';  /* FontAwesome down arrow */
            font-family: 'FontAwesome';
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: white;
        }

        .search-container {
            display: flex;
            gap: 20px;
        }

        .search-container input {
            padding: 10px;
            width: 300px;
            border: 2px solid #fff;
            border-radius: 8px;
            background: transparent;
            color: #fff;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-container input:focus {
            border-color: #16a085;
        }

        .search-container button {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }

        .search-container button:hover {
            background-color: #16a085;
        }

        .movies-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 30px 20px;
            gap: 20px;
        }

        .movie-card {
            background-color: #fff;
            color: #333;
            margin: 15px;
            width: 250px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .movie-card img {
            width: 100%;
            height: 350px;
            object-fit: cover;
        }

        .movie-card-content {
            padding: 15px;
        }

        .movie-card h3 {
            font-size: 1.2rem;
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .movie-card p {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .movie-card a {
            text-decoration: none;
            color: #2980b9;
            font-size: 1rem;
            display: inline-block;
            margin-top: 10px;
            font-weight: bold;
        }

        .movie-card a:hover {
            color: #16a085;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header>
        <!-- Logo -->
        <img src="logo.jpg" alt="FlixSphere Logo" class="logo">
        <h1>FlixSphere</h1>

        <!-- Right Section (Profile, Theme Toggle, Notifications) -->
        <div class="header-right">
            <div class="user-profile">
                <i class="fas fa-user-circle"></i> Profile
            </div>
            <div class="theme-toggle">
                <i class="fas fa-sun"></i> <!-- Or moon icon for dark mode -->
            </div>
            <div class="notification">
                <i class="fas fa-bell"></i> <!-- Notification icon -->
            </div>
        </div>
    </header>

    <!-- Navigation Bar with Genre Dropdown and Search Bar -->
    <section class="nav-bar">
        <!-- Genre Dropdown -->
        <div class="genre-dropdown">
            <select name="genre" onchange="window.location.href='?genre=' + this.value;">
                <option value="">Select Genre</option>
                <option value="28">Action</option>
                <option value="12">Adventure</option>
                <option value="35">Comedy</option>
                <option value="18">Drama</option>
                <option value="10751">Family</option>
                <option value="14">Fantasy</option>
                <option value="27">Horror</option>
                <option value="10402">Music</option>
                <option value="878">Science Fiction</option>
                <option value="10749">Romance</option>
                <option value="53">Thriller</option>
            </select>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="POST" style="display: flex;">
                <input type="text" name="searchQuery" placeholder="Search for movies..." required>
                <button type="submit" name="search"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </section>

    <!-- Movie Results Section -->
    <section class="movies-container">
        <?php if (isset($movies) && count($movies) > 0) : ?>
            <?php foreach ($movies as $movie) : ?>
                <div class="movie-card">
                    <img src="https://image.tmdb.org/t/p/w500/<?php echo $movie['poster_path']; ?>" alt="Movie Poster">
                    <div class="movie-card-content">
                        <h3><?php echo $movie['title']; ?></h3>
                        <p><?php echo substr($movie['overview'], 0, 100) . '...'; ?></p>
                        <a href="https://www.themoviedb.org/movie/<?php echo $movie['id']; ?>" target="_blank">Learn more</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No movies found. Try a different search or genre.</p>
        <?php endif; ?>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 FlixSphere. All Rights Reserved.</p>
    </footer>

</body>
</html>
