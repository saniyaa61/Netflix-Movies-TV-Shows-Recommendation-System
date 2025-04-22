<!-- my_watchlist.php (additional application view) -->
<?php
// Starting the session to access user data (username) across pages
session_start();

// Checking if the user is logged in; if not, redirecting to login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Sending a redirect header to the browser
    exit;  // Stopping the script to ensure no further code runs
}

// Loading users from "users.json" in the "data" folder into an array
// file_get_contents() reads the file, json_decode() converts it to a PHP array with "true" for associative format
$usersData = json_decode(file_get_contents('data/users.json'), true);

// Initializing variables for the current user and their watchlist
// $currentUser will hold the user’s data; $userWatchlist will hold their watchlist items
$currentUser = null;
$userWatchlist = [];
// Looping through $usersData to find the current user based on their username
foreach ($usersData as $u) {
    if ($u['username'] === $_SESSION['username']) {  // Matching session username
        $currentUser = $u;  // Storing the user’s full data
        $userWatchlist = $u['watchlist'] ?? [];  // Getting watchlist, defaults to empty array if not set
        break;  // Stopping loop once found
    }
}

// If user not found, redirecting to login page (shouldn't happen, but just in case)
if (!$currentUser) {
    header("Location: login.php");
    exit;
}

// Handling "Remove from Watchlist" action
// Checking if the request is POST and the "remove_from_watchlist" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_watchlist'])) {
    // Getting the item name to remove from the form, or empty string if not set
    $itemName = $_POST['item_name'] ?? '';
    // Checking if the item exists and is in the user’s watchlist
    if ($itemName && in_array($itemName, $userWatchlist)) {
        // Removing the item from the user's watchlist
        // array_filter() keeps items where the function returns true; "use ($itemName)" accesses the variable
        $userWatchlist = array_filter($userWatchlist, function($item) use ($itemName) {
            return $item !== $itemName;  // Keeping all items except the one to remove
        });
        // Reindexing the array after filtering
        $userWatchlist = array_values($userWatchlist);
        // Updating the user's watchlist in users.json
        foreach ($usersData as &$u) {  // &$u allows modifying the original array
            if ($u['username'] === $_SESSION['username']) {
                $u['watchlist'] = $userWatchlist;  // Updating the watchlist
                break;
            }
        }
        // Saving the updated $usersData back to "users.json"
        // JSON_PRETTY_PRINT formats the JSON with indentation for readability
        file_put_contents('data/users.json', json_encode($usersData, JSON_PRETTY_PRINT));
    }
    // Redirecting to my_watchlist.php to refresh the page and prevent form resubmission
    header("Location: my_watchlist.php");
    exit;
}

// Mapping movie/TV show names to image filenames (same as index.php)
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
    /* Aligning body styling with the rest of the site */
    body {
        background-color: #fff; /* White background */
        color: #000; /* Black text */
    }

    /* Watchlist section */
    .watchlist-section {
        max-width: 1200px; /* Matching other pages */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding padding on all sides */
    }

    /* Watchlist header */
    .watchlist-header {
        margin-bottom: 30px;  /* Adding space below the header */
    }
    .watchlist-header h2 {
        font-weight: 700;  /* Bold heading */
        font-size: 1.8rem; /* Match profile.php for consistency */
        margin: 0;  /* Removing default margins */
    }

    /* Watchlist items */
    .watchlist-item {
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 10px;  /* Rounded corners */
        padding: 15px;  /* Inner padding */
        margin-bottom: 20px;  /* Spacing between items */
        display: flex;  /* Arranging poster, details, and button horizontally */
        align-items: center;  /* Vertically centering content */
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
    .item-details a {
        text-decoration: none;  /* Removing underline from link */
        color: inherit;  /* Inherits text color (black) */
    }
    .remove-btn {
        border: 1px solid black;  /* Black border */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 8px 16px;  /* Padding for size */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none;  /* Removing underline */
        transition: background-color 0.3s;  /* Smooth hover effect */
    }
    .remove-btn:hover {
        background-color: #f0f0f0;  /* Light gray on hover */
    }

    /* Responsive design */
    @media (max-width: 576px) {
        .watchlist-section {
            padding: 15px;  /* Reduced padding */
        }
        .watchlist-header h2 {
            font-size: 1.5rem;  /* Smaller heading */
        }
        .watchlist-item {
            flex-direction: column;  /* Stacking items vertically */
            align-items: flex-start;  /* Aligning to left */
            padding: 10px;  /* Reduced padding */
        }
        .poster-img {
            width: 80px;  /* Smaller width */
            height: 120px;  /* Smaller height */
            margin-right: 0;  /* No right margin */
            margin-bottom: 10px;  /* Spacing below image */
        }
        .item-details h5 {
            font-size: 1.1rem;  /* Smaller title */
        }
        .remove-btn {
            padding: 6px 12px;  /* Smaller padding */
            font-size: 0.9rem;  /* Smaller text */
        }
    }
</style>

<!-- Watchlist Content -->
<div class="watchlist-section">  <!-- Main container -->
    <!-- Watchlist Header -->
    <div class="watchlist-header">
        <h2>My Watchlist</h2>
    </div>

    <!-- Watchlist Items -->
    <?php if (empty($userWatchlist)): ?>  <!-- Checking if the watchlist is empty -->
        <p>Your watchlist is empty. Add some movies or TV shows from the homepage!</p>
    	<!-- Message with suggestion to add items -->
    <?php else: ?>  <!-- If there are items, display them -->
        <?php foreach ($userWatchlist as $itemName): ?>  <!-- Looping through watchlist items -->
            <div class="watchlist-item">
                <?php if (isset($imageMap[$itemName])): ?>  <!-- Checking if the item has a poster -->
                    <img src="images/<?php echo htmlspecialchars($imageMap[$itemName]); ?>" alt="<?php echo htmlspecialchars($itemName); ?>" class="poster-img">
                	<!-- Displaying poster; htmlspecialchars() for security -->
                <?php endif; ?>
                <div class="item-details">
                    <h5>
                        <a href="details.php?name=<?php echo urlencode($itemName); ?>">
                            <?php echo htmlspecialchars($itemName); ?>
                        </a>
                    </h5>
                	<!-- Title as a link to details.php; urlencode() for URL safety -->
                </div>
                <form method="POST" style="display: inline;">  <!-- Inline form for remove button -->
                    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($itemName); ?>">
                    <!-- Hidden input sends the item name -->
                    <button type="submit" name="remove_from_watchlist" class="remove-btn">Remove</button>
                	<!-- Button to remove the item -->
                </form>
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