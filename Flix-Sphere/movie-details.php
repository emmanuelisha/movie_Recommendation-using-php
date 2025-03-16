<?php
// movie-details.php

include('config.php');

if (isset($_GET['id'])) {
    $movieId = $_GET['id'];
    $movieUrl = TMDB_BASE_URL . 'movie/' . $movieId . '?api_key=' . TMDB_API_KEY;
    $response = file_get_contents($movieUrl);
    $movie = json_decode($response, true);
    
    // Check if movie is available on Nkiri.com and FZStudios
    $nkiriAvailability = checkMovieAvailability('nkiri.com', $movie['title']);
    $fzstudiosAvailability = checkMovieAvailability('fzstudios.com', $movie['title']);
}

function checkMovieAvailability($site, $movieTitle) {
    $searchUrl = 'https://' . $site . '/search?q=' . urlencode($movieTitle);
    
    // Use cURL to fetch the page content
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($ch);
    curl_close($ch);

    // Check if the movie title appears in the page content
    if (strpos($content, $movieTitle) !== false) {
        // Parse for download link (this will vary depending on the website structure)
        if ($site == 'nkiri.com') {
            preg_match('/<a href="(https:\/\/nkiri\.com\/download\/[^\"]+)"/', $content, $matches);
            return $matches[1] ?? null;  // Return the first match (download link)
        }
        if ($site == 'fzstudios.com') {
            preg_match('/<a href="(https:\/\/fzstudios\.com\/download\/[^\"]+)"/', $content, $matches);
            return $matches[1] ?? null;  // Return the first match (download link)
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['title']; ?> - Movie Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #FF7E5F, #FEB47B); /* Gradient background */
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding-top: 50px;
        }
        h1 {
            text-align: center;
            color: #fff;
            font-size: 36px;
            margin-bottom: 30px;
        }
        .movie-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .movie-poster img {
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .movie-card {
            width: 60%;
            background: rgba(255, 255, 255, 0.85);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            color: #343a40;
        }
        .movie-card h3 {
            font-size: 26px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .movie-card p {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }
        .movie-card h4 {
            font-size: 18px;
            color: #28a745;
            margin-top: 10px;
        }
        .movie-card .btn {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 18px;
            margin-right: 15px;
            display: inline-block;
            margin-top: 20px;
        }
        .movie-card .btn-download {
            background-color: #28a745;
        }
        .btn-back {
            padding: 12px 20px;
            background-color: #6c757d;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
        }
        .movie-icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?php echo $movie['title']; ?></h1>
    
    <div class="movie-details">
        <div class="movie-poster">
            <img src="https://image.tmdb.org/t/p/w500/<?php echo $movie['poster_path']; ?>" alt="<?php echo $movie['title']; ?>">
        </div>
        <div class="movie-card">
            <h3>Overview</h3>
            <p><?php echo $movie['overview']; ?></p>
            <h4>Release Date: <?php echo $movie['release_date']; ?></h4>
            <h4>Rating: <?php echo $movie['vote_average']; ?> / 10</h4>

            <!-- Show download links if available -->
            <?php if ($nkiriAvailability): ?>
                <a href="<?php echo $nkiriAvailability; ?>" class="btn btn-download" target="_blank"><i class="fas fa-download movie-icon"></i> Download from Nkiri</a>
            <?php else: ?>
                <p style="color: #ff6f61;">Movie not found on Nkiri.com.</p>
            <?php endif; ?>

            <?php if ($fzstudiosAvailability): ?>
                <a href="<?php echo $fzstudiosAvailability; ?>" class="btn btn-download" target="_blank"><i class="fas fa-download movie-icon"></i> Download from FZ Studios</a>
            <?php else: ?>
                <p style="color: #ff6f61;">Movie not found on FZ Studios.</p>
            <?php endif; ?>
        </div>
    </div>

    <a href="index.php" class="btn btn-back"><i class="fas fa-arrow-left movie-icon"></i> Back to Search</a>
</div>

</body>
</html>
