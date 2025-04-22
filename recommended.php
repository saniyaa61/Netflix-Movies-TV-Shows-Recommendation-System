<!-- recommended.php -->
<?php
// Starting the session to maintain user data (ogin status) across pages
session_start();
// Loading data from movies_tvshows.json
// file_get_contents() reads the entire file as text, containing movie/TV show data
$jsonData = file_get_contents('data/movies_tvshows.json');

// Converting the JSON string into a PHP array for processing
// json_decode() with "true" makes it an associative array (key => value) instead of an objec
$moviesTVShowsData = json_decode($jsonData, true);

// Creating an array to store movies and TV shows
$moviesTVShows = [];
foreach ($moviesTVShowsData as $data) {
    $moviesTVShows[] = [
        'name' => $data['name'],
        'averageRating' => $data['averageRating'],
        'isMovie' => $data['isMovie']
    ];
}

// Getting the filter type from the URL (Movies or TV Shows)
// $_GET['type'] retrieves the "type" parameter; defaults to "Movies" if not set
$filterType = isset($_GET['type']) ? $_GET['type'] : 'Movies';

// Filtering the data based on the selected type
// array_filter() keeps items where the function returns true
// "use ($filterType)" lets the function access the $filterType variable
$filteredItems = array_filter($moviesTVShows, function($item) use ($filterType) {
    // If $filterType is "Movies," keep items where isMovie is true
    // If $filterType is "TV Shows," keep items where isMovie is false
    return ($filterType === 'Movies' && $item['isMovie']) || ($filterType === 'TV Shows' && !$item['isMovie']);
});

// Sorting by averageRating in descending order (highest first)
// usort() uses a custom comparison function; <=> (spaceship operator) returns -1, 0, or 1
usort($filteredItems, function($a, $b) {
    return $b['averageRating'] <=> $a['averageRating'];  // Higher rating comes first
});

// Taking the top 3 items from the sorted array
// array_slice() extracts a portion; 0 is the start, 3 is the length
$topItems = array_slice($filteredItems, 0, 3);

// Mapping movie/TV show names to image filenames (same as in details.php)
$imageMap = [
    'Damsel' => 'damsel.jpg',
    'Uglies' => 'uglies.jpg',
    'The Six Triple Eight' => '638.jpg',
    'Queen of Tears' => 'qot.jpg',
    'King the Land' => 'ktl.jpg',
    'Doctor Slump' => 'docslump.jpg'
];

// Starting output buffering to capture the HTML/CSS for the page
// This content will be stored in $content and passed to master.php
ob_start(); // Start output buffering
?>
<style>
    /* Aligning body styling with the rest of the site */
    body {
        background-color: #fff; /* White background to match other pages */
        color: #000; /* Black text */
    }

    /* Section styling */
    .recommended-section {
        max-width: 1200px;  /* Limits width for readability */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding padding on all sides */
    }

    /* Header with title and dropdown */
    .recommended-header {
        display: flex;  /* Uses flexbox to arrange title and dropdown horizontally */
        justify-content: space-between;  /* Pushing title to left, dropdown to right */
        align-items: center;  /* Vertically centering the items */
        margin-bottom: 30px;  /* Adding space below the header */
    }
    .recommended-header h2 {
        font-weight: 700;  /* Bold heading */
        font-size: 1.8rem;  /* Larger font size for emphasis */
        margin: 0;  /* Removing default margins */
    }
    .filter-dropdown .dropdown-toggle {
        border: 1px solid black;  /* Black border */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 8px 16px;  /* Padding for size */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none;  /* Removing underline */
        font-weight: 500;  /* Medium-bold text */
        transition: background-color 0.3s;  /* Smooth hover effect */
    }
    .filter-dropdown .dropdown-toggle:hover {
        background-color: #f0f0f0;  /* Light gray on hover */
    }
    .filter-dropdown .dropdown-menu {
        border-radius: 10px;  /* Rounded corners */
        border: 1px solid #ddd;  /* Light gray border */
    }
    .filter-dropdown .dropdown-item {
        color: black;  /* Black text */
        padding: 8px 16px;  /* Padding for spacing */
    }
    .filter-dropdown .dropdown-item:hover {
        background-color: #f0f0f0;  /* Light gray on hover */
    }

    /* Recommended items */
    .recommended-item {
        display: flex;  /* Arranging rank, image, and details horizontally */
        align-items: center;  /* Vertically centering content */
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 10px;  /* Rounded corners */
        padding: 15px;  /* Inner padding */
        margin-bottom: 20px;  /* Spacing between items */
        transition: transform 0.3s;  /* Smooth scaling on hover */
    }
    .recommended-item:hover {
        transform: scale(1.02);  /* Slightly enlarges on hover (2%) */
    }
    .rank {
        font-size: 1.5rem;  /* Larger font for rank number */
        font-weight: 700;  /* Bold */
        margin-right: 20px;  /* Spacing between rank and image */
    }
    .poster-img {
        width: 100px;  /* Fixing width for posters */
        height: 150px;  /* Fixing height */
        object-fit: cover;  /* Cropping image to fit, maintaining aspect ratio */
        border-radius: 5px;  /* Slightly rounded corners */
        margin-right: 20px;  /* Spacing between image and details */
    }
    .item-details {
        flex-grow: 1;  /* Taking up remaining space in the flex container */
    }
    .item-details h5 {
        font-weight: 700;  /* Bold title */
        margin: 0 0 10px 0;  /* No top margin, 10px bottom margin */
    }
    .item-details p {
        margin: 0;  /* No margins */
        font-weight: 600;  /* Slightly bold rating */
    }
    .item-details a {
        text-decoration: none;  /* Removing underline from link */
        color: inherit;  /* Inheriting text color (black) */
    }
</style>

<!-- Recommended Content -->
<div class="recommended-section">  <!-- Main container -->
    <!-- Header with Title and Dropdown -->
    <div class="recommended-header">
        <h2>Top 3 Recommended <?php echo htmlspecialchars($filterType); ?> to Watch</h2>
        <!-- Displays "Movies" or "TV Shows" based on filter; htmlspecialchars() ensures safety -->
        <div class="filter-dropdown">
            <div class="dropdown">  <!-- Bootstrap dropdown container -->
                <button class="dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($filterType); ?>
                	<!-- Showing current filter type as button text -->
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="recommended.php?type=Movies">Movies</a></li>
                    <li><a class="dropdown-item" href="recommended.php?type=TV Shows">TV Shows</a></li>
                	<!-- Linking to switch filter type -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Recommended Items -->
    <?php if (empty($topItems)): ?>  <!-- Checking if there are no items to display -->
        <p>No <?php echo htmlspecialchars($filterType); ?> found.</p>
    	<!-- Showing a message if no movies or TV shows match the filter -->
    <?php else: ?>  <!-- If there are items, display them -->
        <?php foreach ($topItems as $index => $item): ?>  <!-- Looping through top 3 items -->
            <div class="recommended-item">
                <span class="rank"><?php echo ($index + 1) . '.'; ?></span>
                <!-- Displaying rank (1, 2, 3) with a dot -->
                <img src="images/<?php echo htmlspecialchars($imageMap[$item['name']]); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="poster-img">
                <!-- Showing poster image; htmlspecialchars() for security -->
                <div class="item-details">
                    <h5><a href="details.php?name=<?php echo urlencode($item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?></a></h5>
                    <!-- Title as a link to details.php; urlencode() for URL safety -->
                    <p>Average Rating: <?php echo htmlspecialchars($item['averageRating']); ?>/10</p>
                	<!-- Displaying rating out of 10 -->
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->