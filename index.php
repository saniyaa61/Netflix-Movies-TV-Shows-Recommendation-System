<!-- index.php -->
<?php
// Starting the session to store and access data (like a logged-in username) across pages
session_start();
// Including the MovieTVShow and Actor classes files to create movie/TVshow and actor objects
require_once 'classes/MovieTVShow.class.php';
require_once 'classes/Actor.class.php';

// Loading data from "movies_tvshows.json" into a string
$jsonData = file_get_contents('data/movies_tvshows.json');
// file_get_contents() reads the entire file as text. This JSON file holds movie/TV show data
$moviesTVShowsData = json_decode($jsonData, true);

// Loading actors from "actors.json" nd decoding it into an array
$actorsData = json_decode(file_get_contents('data/actors.json'), true);
$actors = [];  // Creating an empty array to store Actor objects
// Looping through each actor’s data in the $actorsData array
foreach ($actorsData as $data) {
    $actors[] = new Actor(
        $data['firstName'],
        $data['lastName'],
        $data['dateOfBirth'],
        $data['nationality'],
        $data['isActive']
    );
}

// Creating MovieTVShow objects and assigning actors
$moviesTVShows = [];
foreach ($moviesTVShowsData as $index => $data) {
    $movieTVShow = new MovieTVShow(
        $data['name'],
        $data['director'],
        $data['genre'],
        $data['releaseDate'],
        $data['pgRating'],
        $data['description'],
        $data['recommendationDescription'],
        $data['recommendationScore'],
        $data['averageRating'],
        $data['isMovie']
    );
    // Calculates the starting index for actors (2 actors per movie/TV show)
    // $index * 2 means: movie 0 gets actors 0 and 1, movie 1 gets actors 2 and 3, etc
    $actorIndex = $index * 2;
    
    // Checking if an actor exists at $actorIndex; if so, adds them to the movie/TV show
    // isset() prevents errors if there aren’t enough actors
    if (isset($actors[$actorIndex])) $movieTVShow->addActor($actors[$actorIndex]);
    // Checking for the next actor and adds them if they exist
    if (isset($actors[$actorIndex + 1])) $movieTVShow->addActor($actors[$actorIndex + 1]);
    // Adding the fully built MovieTVShow object to the $moviesTVShows array
    $moviesTVShows[] = $movieTVShow;
}

// Determining which content to show based on user selection (default to movies)
$showMovies = true; // Default to movies
// Checking if the URL has a "type" parameter (index.php?type=tvshows) and if it’s "tvshows"
// $_GET holds URL parameters; if set to "tvshows," switch to showing TV shows
if (isset($_GET['type']) && $_GET['type'] === 'tvshows') {
    $showMovies = false;  // Switching to TV shows
}

// Filtering movies or TV shows
// array_filter() keeps items where the function returns true
$displayItems = array_filter($moviesTVShows, function($item) use ($showMovies) {
    // If $showMovies is true, return true for movies (getIsMovie() is true)
    // If $showMovies is false, return true for TV shows (!getIsMovie() is true)
    return $showMovies ? $item->getIsMovie() : !$item->getIsMovie();
});

// Mapping movie/TV show names to image filenames
$imageMap = [
    'Damsel' => 'damsel.jpg',
    'Uglies' => 'uglies.jpg',
    'The Six Triple Eight' => '638.jpg',
    'Queen of Tears' => 'qot.jpg',
    'King the Land' => 'ktl.jpg',
    'Doctor Slump' => 'docslump.jpg'
];

// Starts output buffering, which captures all HTML/PHP output into a buffer instead of sending it to the browser
// This lets us store the content in a variable ($content) to pass to master.php
ob_start(); // Start output buffering
?>
<style>
    /* Custom styling for posters */
    .poster-img {
        width: 100%;  /* Making the image fill its container’s width (the card). */
        object-fit: contain; /* Maintaining aspect ratio, no stretching */
        background-color: #f8f9fa; /* Light background for consistency */
    }

    /* Custom toggle button styling */
    .toggle-btn {
        border: 1px solid black;  /* Adding a 1px black border. */
        color: black;  /* Black text. */
        background-color: white;  /* White background. */
        text-decoration: none; /* Removing the underline from links */
    }
    .toggle-btn:hover {
        background-color: #f0f0f0; /* Light gray on hover */
    }
    .toggle-btn.active {
        background-color: #e0e0e0; /* Slightly darker when active */
        border: 1px solid black;  /* Keeping the border for consistency. */
    }

    /* Bold titles */
    .card-title {
        font-weight: 700; /* Bolder titles */
    }

    /* Custom styling for View Details button */
    .details-btn {
        border: 1px solid black;  /* Black border. */
        color: black;  /* Black text. */
        background-color: white;  /* White background. */
        padding: 8px 16px;  /* 8px top/bottom, 16px left/right padding. */
        border-radius: 10px;  /* Rounded corners. */
        text-decoration: none; /* Removing link underline */
    }
    .details-btn:hover {
        background-color: #f0f0f0; /* Light gray on hover */
    }
</style>

<div class="text-center mb-4">  <!-- Centers content horizontally (text-center) with 4 units of bottom margin (mb-4) -->
    <!-- Toggle Buttons -->
    <div class="btn-group" role="group">  <!-- Bootstrap’s btn-group groups buttons together visually -->
    	<!-- Movies button: links to index.php?type=movies -->
        <a href="index.php?type=movies" class="btn toggle-btn <?php echo $showMovies ? 'active' : ''; ?>">Movies</a>
        <!-- "btn" is Bootstrap’s button style, "toggle-btn" is custom styling, and "active" is added if $showMovies is true -->
        <!-- TV Shows button: links to index.php?type=tvshows -->
        <a href="index.php?type=tvshows" class="btn toggle-btn <?php echo !$showMovies ? 'active' : ''; ?>">TV Shows</a>
    	<!-- "active" is added if $showMovies is false (showing TV shows) -->
    </div>
</div>

<div class="row">  <!-- Bootstrap’s row class starts a grid layout -->
    <?php foreach ($displayItems as $item): ?>  <!-- Loops through filtered movies or TV shows; $item is a MovieTVShow object -->
        <div class="col-md-4 mb-4">  <!-- Column: 4 units wide on medium+ screens (1/3 of 12-column grid), 4 units bottom margin -->
            <div class="card h-100">  <!-- Bootstrap card with full height (h-100) to align cards evenly -->
                <!-- Poster -->
                <img src="images/<?php echo htmlspecialchars($imageMap[$item->getName()]); ?>" class="card-img-top poster-img" alt="<?php echo htmlspecialchars($item->getName()); ?>">
                <!-- Displaying the poster image from the "images" folder using $imageMap; htmlspecialchars() escapes special characters for security -->
                <!-- "card-img-top" places the image at the top of the card, "poster-img" adds custom styling -->
                <div class="card-body text-center">  <!-- Card body with centered text -->
                    <!-- Title -->
                    <h5 class="card-title"><?php echo htmlspecialchars($item->getName()); ?></h5>
                    <!-- Displaying the movie/TV show name in a bold heading (h5); htmlspecialchars() for security -->
                    <!-- Cast -->
                    <p class="card-text">
                        <strong>Cast:</strong> 
                        <?php 
                        // Creating an array of actor names from the $item’s actors
                        // array_map() applies a function to each actor, combining first and last names
                        $actorsList = array_map(function($actor) {
                            return htmlspecialchars($actor->getFirstName() . ' ' . $actor->getLastName());
                        }, $item->getActors());
                        // Joining the names with ", " (e.g., "John Doe, Jane Smith") and outputs them.
                        echo implode(', ', $actorsList);
                        ?>
                    </p>
                    <!-- Release Year -->
                    <p class="card-text">
                        <strong>Release Year:</strong> 
                        <?php echo htmlspecialchars(date('Y', strtotime($item->getReleaseDate()))); ?>
                    	<!-- Extracting the year (e.g., "2023") from the release date using strtotime() and date() -->
                    </p>
                    <!-- Rating -->
                    <p class="card-text">
                        <strong>Rating:</strong> 
                        <?php echo htmlspecialchars($item->getAverageRating()); ?>/10
                    	<!-- Displaying the average rating out of 10 (e.g., "7.8/10") -->
                    </p>
                    <!-- View Details Button -->
                    <a href="details.php?name=<?php echo urlencode($item->getName()); ?>" class="details-btn">View Details</a>
                	<!-- Linking to details.php with the movie/TV show name as a URL parameter; urlencode() ensures special characters are safe in the URL -->
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->