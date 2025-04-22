<!-- details.php -->
<?php
// Starting the session to track user data (username) across pages
session_start();
// Including the MovieTVShow and Actor classes files to create movie/TV show and actor objects
require_once 'classes/MovieTVShow.class.php';
require_once 'classes/Actor.class.php';

// Loading movie/TV show data from "movies_tvshows.json" in the "data" folder into a string
$jsonData = file_get_contents('data/movies_tvshows.json');
// file_get_contents() reads the entire file as text
$moviesTVShowsData = json_decode($jsonData, true);

// Loading actor data from "actors.json" and decodes it into an array
$actorsData = json_decode(file_get_contents('data/actors.json'), true);
$actors = [];  // Creating an empty array to store Actor objects
// Looping through each actor’s data in $actorsData
foreach ($actorsData as $data) {
    // Creating a new Actor object with properties from $data and adds it to $actors
    $actors[] = new Actor(
        $data['firstName'],
        $data['lastName'],
        $data['dateOfBirth'],
        $data['nationality'],
        $data['isActive']
    );
}

// Loading external reviews from "external_reviews.json" into an array
$externalReviewsData = json_decode(file_get_contents('data/external_reviews.json'), true);

// Loading user reviews from "user_reviews.json" into an array
$userReviewsData = json_decode(file_get_contents('data/user_reviews.json'), true);

// Loading users from "users.json" to get the current user's watchlist
$usersData = json_decode(file_get_contents('data/users.json'), true);
$currentUser = null;
$userWatchlist = [];

// Checking if the user is logged in by looking for a username in the session
if (isset($_SESSION['username'])) {
    // Looping through users to find the one matching the session username
    foreach ($usersData as $u) {
        if ($u['username'] === $_SESSION['username']) {
            $currentUser = $u;  // Storing the user’s data
            $userWatchlist = $u['watchlist'] ?? [];  // Getting their watchlist, or empty array if none
            break;  // Stopping the loop once found
        }
    }
}

// Creating MovieTVShow objects and assigning actors
$moviesTVShows = [];
// Looping through movie/TV show data, with $index as position and $data as details
foreach ($moviesTVShowsData as $index => $data) {
    // Creating a new MovieTVShow object with all its properties
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
    $moviesTVShows[] = $movieTVShow;
}

// Getting the name of the movie/TV show from the URL
// urldecode() converts URL-encoded characters back to normal (%20 to space)
$name = isset($_GET['name']) ? urldecode($_GET['name']) : null;

// Finding the selected movie/TV show
$selectedItem = null;
foreach ($moviesTVShows as $item) {
    if ($item->getName() === $name) {
        $selectedItem = $item;  // Storing the matching object
        break;  // Stopping the loop once found
    }
}

// If no matching item is found, shows an error and exits
if (!$selectedItem) {
    ob_start();  // Starting buffering to capture the error message
    echo "<div class='text-center'><h5>Item not found.</h5><p>Please go back to the homepage and select a valid movie or TV show.</p><a href='index.php' class='custom-btn'>Back to Homepage</a></div>";
    $content = ob_get_clean();  // Capturing the buffered output
    require_once 'master.php';  // Loading the master template with the error content
    exit;  // Stopping the script here
}

// Handling "Add to Watchlist" action
// Checking if the request is POST and the "add_to_watchlist" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_watchlist'])) {
    // Redirecting to login page if user isn’t logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    // Getting the item name from the form, or empty string if not set
    $itemName = $_POST['item_name'] ?? '';
    // If the item exists and isn’t already in the watchlist, adds it
    if ($itemName && !in_array($itemName, $userWatchlist)) {
        // Adding the item to the user's watchlist
        $userWatchlist[] = $itemName;
        // Updating the user's watchlist in users.json
        foreach ($usersData as &$u) {
            if ($u['username'] === $_SESSION['username']) {
                $u['watchlist'] = $userWatchlist;
                break;
            }
        }
        // Saving the updated $usersData back to "users.json"
        // JSON_PRETTY_PRINT makes the file human-readable with nice formatting
        file_put_contents('data/users.json', json_encode($usersData, JSON_PRETTY_PRINT));
    }
    // Redirecting to prevent form resubmission
    header("Location: details.php?name=" . urlencode($itemName));
    exit;
}

// Handling "Add Review" form submission
$reviewErrors = [];    // Array to store validation errors
$successMessage = '';  // Message for successful review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    // Redirecting to login page if user isn’t logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    // Getting form data, trimming whitespace; defaults to empty string if not set
    $reviewTitle = trim($_POST['review_title'] ?? '');
    $reviewContent = trim($_POST['review_content'] ?? '');
    $reviewScore = trim($_POST['review_score'] ?? '');
    $authorName = trim($_POST['author_name'] ?? '');

    // Validating the form inputs
    if (empty($authorName)) {
        $reviewErrors[] = "Author Name is required.";
    }
    if (empty($reviewTitle)) {
        $reviewErrors[] = "Review Title is required.";
    }
    if (empty($reviewContent)) {
        $reviewErrors[] = "Review Content is required.";
    }
    if (empty($reviewScore)) {
        $reviewErrors[] = "Rating is required.";
    } elseif (!is_numeric($reviewScore) || $reviewScore < 1 || $reviewScore > 5) {
        $reviewErrors[] = "Rating must be a number between 1 and 5.";
    }

    // If no errors, adding the review to user_reviews.json
    if (empty($reviewErrors)) {
        $newReview = [
            // Creating a new review array with form data and username
            'username' => $_SESSION['username'],
            'authorName' => $authorName,
            'content' => $reviewContent,
            'score' => (float)$reviewScore,  // Converting to float for consistency
            'title' => $reviewTitle,
            'movieTVShow' => $selectedItem->getName()
        ];
        // Adding the review to $userReviewsData
        $userReviewsData[] = $newReview;
        // Saving updated reviews to "user_reviews.json"
        file_put_contents('data/user_reviews.json', json_encode($userReviewsData, JSON_PRETTY_PRINT));
        $successMessage = "Review added successfully!";
        // Redirecting to prevent form resubmission
        header("Location: details.php?name=" . urlencode($selectedItem->getName()));
        exit;
    }
}

// Filtering external reviews for the selected movie/TV show
$externalReviews = array_filter($externalReviewsData, function($review) use ($name) {
    return $review['movieTVShow'] === $name;
});

// Filtering user reviews for the selected movie/TV show
$userReviews = array_filter($userReviewsData, function($review) use ($name) {
    return $review['movieTVShow'] === $name;
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

// Mapping movie/TV show names to trailer filenames
$trailerMap = [
    'Damsel' => 'Damsel trailer.mp4',
    'Uglies' => 'Uglies trailer.mp4',
    'The Six Triple Eight' => 'The Six Triple Eight trailer.mp4',
    'Queen of Tears' => 'Queen of Tears trailer.mp4',
    'King the Land' => 'King the Land trailer.mp4',
    'Doctor Slump' => 'Doctor Slump trailer.mp4'
];

// Placeholder mapping for duration (since it's not in the data)
$durationMap = [
    'Damsel' => '1h 50m',
    'Uglies' => '1h 40m',
    'The Six Triple Eight' => '2h 10m',
    'Queen of Tears' => '16 episodes',
    'King the Land' => '16 episodes',
    'Doctor Slump' => '16 episodes'
];

// Starting buffering to capture the HTML content for master.php
ob_start(); // Start output buffering
?>
<style>
    /* Aligning body styling to index.php for consistency */
    body {
        background-color: #fff; /* White background */
        color: #000; /* Black text */
    }

    /* Poster and Trailer Section */
    .media-section {
        max-width: 1200px;  /* Limiting width for readability */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding space around content */
    }
    .poster-img {
        width: 100%;  /* Filling its container */
        height: 400px; /* Fixing height to match trailer */
        object-fit: contain;  /* Keeping aspect ratio */
    }
    .trailer-video {
        width: 100%;  /* Filling its container */
        height: 400px; /* Matching poster height */
        border-radius: 5px;  /* Rounded corners */
    }

    /* Buttons Section */
    .action-buttons {
        display: flex;  /* Arranging buttons horizontally */
        justify-content: center;  /* Centering the buttons under the poster */
        align-items: center;  /* Vertically aligning */
        gap: 15px;  /* Spacing between buttons */
        margin-top: 10px; /* Adding spacing between the poster and buttons */
    }
    .add-to-watchlist {
        display: flex;  /* Aligning icon and text */
        align-items: center;  /* Centering vertically */
        gap: 5px;  /* Spacing between icon and text */
        border: 1px solid black;
        background-color: white;
        color: black;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s, transform 0.1s;  /* Smooth hover effects */
    }
    .add-to-watchlist:hover:not(:disabled) {
        background-color: #f0f0f0;  /* Light gray on hover */
        transform: scale(1.05);  /* Slightly enlarging */
    }
    .add-to-watchlist:disabled {
        background-color: #e0e0e0;  /* Gray when disabled */
        cursor: not-allowed;  /* Shows it’s unclickable */
    }
    .rating-icon {
        font-size: 1.5rem;  /* Icon size */
        color: black;
        text-decoration: none;
        transition: color 0.3s, transform 0.1s;  /* Smooth hover */
    }
    .rating-icon:hover {
        color: #555;  /* Darker gray on hover */
        transform: scale(1.2);  /* Slightly enlarging */
    }
    .rating-icon.active {
        color: red !important; /* Red when active */
    }

    /* Details Section */
    .details-section {
        padding: 10px 0; /* Vertical padding */
    }
    .details-section .row {
        margin-bottom: 10px;  /* Spacing between rows */
        justify-content: center; /* Centering the row content */
    }
    .details-section .col-md-6 {
        padding-right: 20px; /* Reducing padding for better centering */
        padding-left: 20px; /* Adding padding to the left for symmetry */
    }
    .details-section p {
        margin: 0;  /* Removing default paragraph margins */
    }
    .details-section strong {
        font-weight: 700;  /* Bold labels */
    }

    /* Reviews Section */
    .reviews-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .reviews-section h3 {
        font-weight: 700;
        margin-bottom: 10px;
    }
    .review-box {
        border: 1px solid #ddd;  /* Light gray border */
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .review-box:not(:last-child) {
        border-bottom: 1px solid #ddd; /* Separator between reviews */
        margin-bottom: 20px;
        padding-bottom: 20px;
    }
    .review-source, .review-user {
        font-style: italic;
        color: #555;  /* Gray text */
        margin-bottom: 5px;
    }
    .review-source a {
        text-decoration: none; /* Removing underline from sourceName hyperlink */
        color: #555; /* Matching the color of the review-source class */
    }
    .review-source a:hover {
        color: #333; /* Slightly darker on hover for visual feedback */
    }
    .review-score, .review-rating {
        font-weight: 600;
        color: #333;
    }
    .add-review-btn {
        border: 1px solid black;
        color: black;
        background-color: white;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s;
    }
    .add-review-btn:hover {
        background-color: #f0f0f0;
    }

    /* Review Form */
    .review-form {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        display: none; /* Hidden by default */
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;  /* Full width */
        font-weight: 700;
        margin-bottom: 5px;
    }
    .form-group input[type="text"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: "Comic Sans MS", "Comic Sans", cursive;  /* Matching site font */
        font-size: 1rem;
    }
    .form-group textarea {
        height: 100px;
        resize: vertical;  /* Allowing vertical resizing */
    }
    .submit-review-btn, .cancel-review-btn {
        border: 1px solid black;
        color: black;
        background-color: white;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s;
        margin-right: 10px;
    }
    .submit-review-btn:hover, .cancel-review-btn:hover {
        background-color: #f0f0f0;
    }

    /* Success and Error Messages */
    .success-message, .error-messages {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .success-message {
        background-color: #d4edda;  /* Light green */
        color: #155724;  /* Dark green text */
    }
    .error-messages {
        background-color: #f8d7da;  /* Light red */
        color: #721c24;  /* Dark red text */
    }
    .error-messages ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .error-messages li {
        margin-bottom: 5px;
    }
    .success-message i, .error-messages i {
        font-size: 1.2rem;
    }

    /* Custom Button Style for Back to Homepage */
    .custom-btn {
        border: 1px solid black;
        color: black;
        background-color: white;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s;
    }
    .custom-btn:hover {
        background-color: #f0f0f0;
    }

    /* Responsive Design for small screens */
    @media (max-width: 576px) {
        .media-section, .reviews-section {
            padding: 15px;
        }
        .poster-img, .trailer-video {
            height: 300px;  /* Smaller height */
        }
        .reviews-section h3 {
            font-size: 1.3rem;
        }
        .review-box {
            padding: 10px;
        }
        .review-form {
            padding: 10px;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            font-size: 0.9rem;
        }
        .submit-review-btn, .cancel-review-btn {
            padding: 6px 12px;
            font-size: 0.9rem;
        }
    }
</style>

<!-- Poster, Trailer, Buttons, and Details Section -->
<div class="media-section">
    <div class="row">  <!-- Bootstrap grid row -->
        <div class="col-md-4 text-center">  <!-- 4 columns on medium+ screens -->
            <img src="images/<?php echo htmlspecialchars($imageMap[$selectedItem->getName()]); ?>" class="poster-img" alt="<?php echo htmlspecialchars($selectedItem->getName()); ?>">
            <!-- Displaying the poster; htmlspecialchars() ensures safety -->
            <!-- Buttons below the poster -->
            <div class="action-buttons">
                <form method="POST" style="display: inline;">  <!-- Inline form for watchlist -->
                    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($selectedItem->getName()); ?>">
                    <button type="submit" name="add_to_watchlist" class="add-to-watchlist" <?php echo in_array($selectedItem->getName(), $userWatchlist) ? 'disabled' : ''; ?>>
                        <i class="bi bi-plus-circle"></i> 
                        <?php echo in_array($selectedItem->getName(), $userWatchlist) ? 'Added to Watchlist' : 'Add to Watchlist'; ?>
                    	<!-- Changing button text based on watchlist status -->
                    </button>
                </form>
                <a href="#" class="rating-icon thumbs-up" title="Thumbs Up"><i class="bi bi-hand-thumbs-up"></i></a>
                <a href="#" class="rating-icon thumbs-down" title="Thumbs Down"><i class="bi bi-hand-thumbs-down"></i></a>
            	<!-- Thumbs up/down icons; functionality added via JS -->
            </div>
        </div>
        <div class="col-md-8">  <!-- 8 columns for trailer and details -->
            <video class="trailer-video" controls>  <!-- Video player with controls -->
                <source src="trailers/<?php echo htmlspecialchars($trailerMap[$selectedItem->getName()]); ?>" type="video/mp4">
                <p>Your browser does not support the video tag.</p>
            </video>
            <!-- Details below the trailer -->
            <div class="details-section">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Cast:</strong> 
                            <?php 
                            $actorsList = array_map(function($actor) {
                                return htmlspecialchars($actor->getFirstName() . ' ' . $actor->getLastName());
                            }, $selectedItem->getActors());
                            echo implode(', ', $actorsList);
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($durationMap[$selectedItem->getName()]); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Director:</strong> <?php echo htmlspecialchars($selectedItem->getDirector()); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Release Date:</strong> <?php echo htmlspecialchars($selectedItem->getReleaseDate()); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Genre:</strong> <?php echo htmlspecialchars($selectedItem->getGenre()); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($selectedItem->getPGRating()); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Section -->
<div class="reviews-section">
    <!-- First Row: Editorial Review -->
    <div class="row">
        <div class="col-12">
            <h3>Editorial Review</h3>
            <div class="review-box">
                <p><?php echo htmlspecialchars($selectedItem->getRecommendationDescription()); ?></p>
                <p><strong>Score:</strong> <?php echo htmlspecialchars($selectedItem->getRecommendationScore()); ?>/10</p>
            </div>
        </div>
    </div>
    <!-- Second Row: External Review and User Reviews -->
    <div class="row">
        <div class="col-md-6">
            <h3>External Reviews</h3>
            <?php foreach ($externalReviews as $review): ?>
                <div class="review-box">
                    <p class="review-source">
                        <a href="<?php echo htmlspecialchars($review['sourceLocation']); ?>" target="_blank">
                            <?php echo htmlspecialchars($review['sourceName']); ?>
                        </a>
                    </p>
                    <p><?php echo htmlspecialchars($review['description']); ?></p>
                    <p class="review-score"><strong>Score:</strong> <?php echo htmlspecialchars($review['rating']); ?>/10</p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-6">
            <h3>User Reviews</h3>
            <?php foreach ($userReviews as $review): ?>
                <div class="review-box">
                    <p class="review-user"><?php echo htmlspecialchars($review['authorName']); ?></p>
                    <p><?php echo htmlspecialchars($review['content']); ?></p>
                    <p class="review-rating"><strong>Rating:</strong> <?php echo htmlspecialchars($review['score']); ?>/5 stars</p>
                </div>
            <?php endforeach; ?>

            <!-- Adding Review Button and Form -->
            <?php if (!isset($_SESSION['username'])): ?>
                <a href="login.php" class="add-review-btn">Add a Review (Login Required)</a>
            <?php else: ?>
                <a href="#" class="add-review-btn" id="show-review-form">Add a Review</a>
                <?php if (!empty($successMessage)): ?>
                    <div class="success-message">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo htmlspecialchars($successMessage); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($reviewErrors)): ?>
                    <div class="error-messages">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <ul>
                            <?php foreach ($reviewErrors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="POST" class="review-form" id="review-form">
                    <div class="form-group">
                        <label for="author_name">Author Name</label>
                        <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($_POST['author_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="review_title">Review Title</label>
                        <input type="text" id="review_title" name="review_title" value="<?php echo htmlspecialchars($_POST['review_title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="review_content">Review Content</label>
                        <textarea id="review_content" name="review_content" required><?php echo htmlspecialchars($_POST['review_content'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="review_score">Rating (1-5)</label>
                        <select id="review_score" name="review_score" required>
                            <option value="">Select a rating</option>
                            <option value="1" <?php echo (isset($_POST['review_score']) && $_POST['review_score'] == '1') ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?php echo (isset($_POST['review_score']) && $_POST['review_score'] == '2') ? 'selected' : ''; ?>>2</option>
                            <option value="3" <?php echo (isset($_POST['review_score']) && $_POST['review_score'] == '3') ? 'selected' : ''; ?>>3</option>
                            <option value="4" <?php echo (isset($_POST['review_score']) && $_POST['review_score'] == '4') ? 'selected' : ''; ?>>4</option>
                            <option value="5" <?php echo (isset($_POST['review_score']) && $_POST['review_score'] == '5') ? 'selected' : ''; ?>>5</option>
                        </select>
                    </div>
                    <button type="submit" name="submit_review" class="submit-review-btn">Submit Review</button>
                    <button type="button" class="cancel-review-btn" id="cancel-review-form">Cancel</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Back to Homepage Button -->
<div class="text-center mb-4">
    <a href="index.php" class="custom-btn">Back to Homepage</a>
</div>

<!-- JavaScript to Toggle the Review Form and Handle Thumbs Up/Down -->
<script>
    document.addEventListener('DOMContentLoaded', function() {  // Running when page is fully loaded
        // Review Form Toggle
        const showReviewFormBtn = document.getElementById('show-review-form');
        const reviewForm = document.getElementById('review-form');
        const cancelReviewFormBtn = document.getElementById('cancel-review-form');

        if (showReviewFormBtn && reviewForm && cancelReviewFormBtn) {
            // Showing the form when "Add a Review" is clicked
            showReviewFormBtn.addEventListener('click', function(e) {
                e.preventDefault();  // Preventing link default behavior
                reviewForm.style.display = 'block';
                showReviewFormBtn.style.display = 'none';
            });

            // Hiding the form when "Cancel" is clicked
            cancelReviewFormBtn.addEventListener('click', function() {
                reviewForm.style.display = 'none';
                showReviewFormBtn.style.display = 'inline-block';
                // Resetting the form
                reviewForm.reset();  // Clearing form fields
            });
        }

        // Thumbs Up/Down Functionality
        const thumbsUpBtn = document.querySelector('.thumbs-up');
        const thumbsDownBtn = document.querySelector('.thumbs-down');

        thumbsUpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Toggle active state for thumbs up
            if (thumbsUpBtn.classList.contains('active')) {
                // If already active, reverting to default
                thumbsUpBtn.classList.remove('active');
                thumbsUpBtn.querySelector('i').classList.remove('bi-hand-thumbs-up-fill');
                thumbsUpBtn.querySelector('i').classList.add('bi-hand-thumbs-up');
            } else {
                // Activating thumbs up and deactivating thumbs down
                thumbsUpBtn.classList.add('active');
                thumbsUpBtn.querySelector('i').classList.remove('bi-hand-thumbs-up');
                thumbsUpBtn.querySelector('i').classList.add('bi-hand-thumbs-up-fill');

                thumbsDownBtn.classList.remove('active');
                thumbsDownBtn.querySelector('i').classList.remove('bi-hand-thumbs-down-fill');
                thumbsDownBtn.querySelector('i').classList.add('bi-hand-thumbs-down');
            }
        });

        thumbsDownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Toggle active state for thumbs down
            if (thumbsDownBtn.classList.contains('active')) {
                // If already active, reverting to default
                thumbsDownBtn.classList.remove('active');
                thumbsDownBtn.querySelector('i').classList.remove('bi-hand-thumbs-down-fill');
                thumbsDownBtn.querySelector('i').classList.add('bi-hand-thumbs-down');
            } else {
                // Activating thumbs down and deactivating thumbs up
                thumbsDownBtn.classList.add('active');
                thumbsDownBtn.querySelector('i').classList.remove('bi-hand-thumbs-down');
                thumbsDownBtn.querySelector('i').classList.add('bi-hand-thumbs-down-fill');

                thumbsUpBtn.classList.remove('active');
                thumbsUpBtn.querySelector('i').classList.remove('bi-hand-thumbs-up-fill');
                thumbsUpBtn.querySelector('i').classList.add('bi-hand-thumbs-up');
            }
        });
    });
</script>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->