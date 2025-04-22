<!-- search.php (additional application view) --> 
<?php
// Starting the session o maintain user data (login status) across pages
session_start();

// Initializing search error message and results
$searchErrorMessage = '';  // Storing error text if no results are found
$searchResults = [];  // Array to hold matching MovieTVShow objects
$searchQuery = trim($_GET['query'] ?? '');  // Getting the search term from the URL, trimming whitespaces; defaults to empty string if not set

// Processing the search query if it exists
// empty() checks for '', null, or false; ensures we only search with a valid input
if (!empty($searchQuery)) {
    // Loading movies and TV shows from movies_tvshows.json
    // file_get_contents() reads the file, json_decode() converts JSON to a PHP array with "true" for associative format
    $moviesTVShowsData = json_decode(file_get_contents('data/movies_tvshows.json'), true);

    // Including the MovieTVShow and Actor classes to create movie/TV show objects and actor objects for cast info
    require_once 'classes/MovieTVShow.class.php';
    require_once 'classes/Actor.class.php';

    // Loading actors from "actors.json" and decodes it into an array
    $actorsData = json_decode(file_get_contents('data/actors.json'), true);
    $actors = [];  // Empty array to store Actor objects
    // Looping through $actorsData to create Actor objects
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
    // Looping through $moviesTVShowsData to build objects, with $index for actor assignment
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
        // Assigning actors (2 per movie/TV show, based on index)
        $actorIndex = $index * 2;
        if (isset($actors[$actorIndex])) $movieTVShow->addActor($actors[$actorIndex]);
        if (isset($actors[$actorIndex + 1])) $movieTVShow->addActor($actors[$actorIndex + 1]);
        $moviesTVShows[] = $movieTVShow;  // Adding object to array
    }

    // Searching for matching movies or TV shows (case-insensitive partial match)
    foreach ($moviesTVShows as $item) {
        // stripos() checks for a case-insensitive partial match in the name
        // !== false means a match was found (returns position, or false if not)
        if (stripos($item->getName(), $searchQuery) !== false) {
            $searchResults[] = $item;  // Adding matching item to results
        }
    }

    // If no matches are found, setting an error message
    if (empty($searchResults)) {
        $searchErrorMessage = "No results found for '" . htmlspecialchars($searchQuery) . "'.";
        // htmlspecialchars() ensures the query is safely displayed in HTML.
    }
}

// Mapping movie/TV show names to image filenames (for search results)
$imageMap = [
    'Damsel' => 'damsel.jpg',
    'Uglies' => 'uglies.jpg',
    'The Six Triple Eight' => '638.jpg',
    'Queen of Tears' => 'qot.jpg',
    'King the Land' => 'ktl.jpg',
    'Doctor Slump' => 'docslump.jpg'
];

// Starting output buffering to capture the HTML/CSS content for the page
// This will be stored in $content and passed to master.php
ob_start(); // Start output buffering
?>
<style>
    /* Search Results Styling (similar to index.php) */
    .search-results {
        max-width: 1200px;  /* Caps width for consistency */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding padding */
    }
    .search-results h3 {
        font-weight: 700;  /* Bold heading */
        margin-bottom: 20px;  /* Spacing below heading */
    }
    .poster-img {
        width: 100%;  /* Filling card width */
        object-fit: contain; /* Maintaining aspect ratio, no stretching */
        background-color: #f8f9fa; /* Light background for consistency */
    }
    .card-title {
        font-weight: 700; /* Bolder titles */
    }
    .details-btn {
        border: 1px solid black;  /* Black border */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 8px 16px;  /* Padding for size */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none; /* Removing underline */
    }
    .details-btn:hover {
        background-color: #f0f0f0; /* Light gray on hover */
    }

    /* Search Error Message */
    .search-error-message {
        padding: 10px;  /* Inner padding */
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 10px;  /* Rounded corners */
        margin-bottom: 20px;  /* Spacing below */
        display: flex;  /* Aligning icon and text horizontally */
        align-items: center;  /* Vertically centering content */
        gap: 10px;  /* Spacing between icon and text */
        background-color: #f8d7da;  /* Light red background */
        color: #721c24;  /* Dark red text */
    }
    .search-error-message i {
        font-size: 1.2rem;  /* Slightly larger icon */
    }
</style>

<!-- Search Results Section -->
<div class="search-results">  <!-- Main container -->
    <?php if (!empty($searchErrorMessage)): ?>  <!-- Checking if there’s an error message -->
        <div class="search-error-message">
            <i class="bi bi-exclamation-circle-fill"></i>  <!-- Bootstrap Icon for warning -->
            <span><?php echo htmlspecialchars($searchErrorMessage); ?></span>
        	<!-- Displaying the error message safely -->
        </div>
    <?php endif; ?>

    <?php if (!empty($searchResults)): ?>  <!-- If there are search results -->
        <h3>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h3>
        <!-- Heading with the search query -->
        <div class="row">  <!-- Bootstrap grid row -->
            <?php foreach ($searchResults as $item): ?>  <!-- Looping through results -->
                <div class="col-md-4 mb-4">  <!-- 4 columns on medium+ screens, 4 units bottom margin -->
                    <div class="card h-100">  <!-- Bootstrap card, full height -->
                        <!-- Poster -->
                        <img src="images/<?php echo htmlspecialchars($imageMap[$item->getName()]); ?>" class="card-img-top poster-img" alt="<?php echo htmlspecialchars($item->getName()); ?>">
                        <div class="card-body text-center">  <!-- Card content, centered -->
                            <!-- Title -->
                            <h5 class="card-title"><?php echo htmlspecialchars($item->getName()); ?></h5>
                            <!-- Cast -->
                            <p class="card-text">
                                <strong>Cast:</strong> 
                                <?php 
                                // Mapping actors to full names and joins with commas
                                $actorsList = array_map(function($actor) {
                                    return htmlspecialchars($actor->getFirstName() . ' ' . $actor->getLastName());
                                }, $item->getActors());
                                echo implode(', ', $actorsList);
                                ?>
                            </p>
                            <!-- Release Year -->
                            <p class="card-text">
                                <strong>Release Year:</strong> 
                                <?php 
                                // Extracting year from release date using strtotime() and date()
                                echo htmlspecialchars(date('Y', strtotime($item->getReleaseDate()))); ?>
                            </p>
                            <!-- Rating -->
                            <p class="card-text">
                                <strong>Rating:</strong> 
                                <?php echo htmlspecialchars($item->getAverageRating()); ?>/10
                            </p>
                            <!-- View Details Button -->
                            <a href="details.php?name=<?php echo urlencode($item->getName()); ?>" class="details-btn">View Details</a>
                        	<!-- Linking to details.php; urlencode() for URL safety -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php if (empty($searchErrorMessage)): ?>  <!-- If no query was entered -->
            <p>Please enter a search query to find movies or TV shows.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->