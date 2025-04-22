<!-- profile.php -->
<?php
// Starting the session to access the logged-in user’s data
session_start();

// Checking if the user is logged in; if not, redirecting to login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Sending a redirect header to the browser
    exit;  // Stopping the script to ensure no further code runs
}

// Including the User class file to create and manage user objects
require_once 'classes/User.class.php';

// Loading users from "users.json" into an array
// file_get_contents() reads the file, json_decode() converts JSON to a PHP array with "true" for associative format
$usersData = json_decode(file_get_contents('data/users.json'), true);

// Finding the current user
$currentUser = null;
foreach ($usersData as $u) {
    if ($u['username'] === $_SESSION['username']) {
        // Creating a User object with all stored data if username matches
        $currentUser = new User(
            $u['firstName'],
            $u['lastName'],
            $u['dateOfBirth'],
            $u['country'],
            $u['email'],
            $u['username'],
            $u['password'],
            $u['preferredMovie'],
            $u['preferredTVShow'],
            $u['watchlist']
        );
        break;  // Stopping loop once found
    }
}

// If user not found, redirecting to login (shouldn't happen, but just in case)
if (!$currentUser) {
    header("Location: login.php");
    exit;
}

// Initializing form data variables with current user values
$firstName = $currentUser->getFirstName();
$lastName = $currentUser->getLastName();
$dateOfBirth = $currentUser->getDateOfBirth();
$country = $currentUser->getCountry();
$preferredMovie = $currentUser->getPreferredMovie();
$preferredTVShow = $currentUser->getPreferredTVShow();
$errors = [];  // Array for profile update validation errors
$successMessage = '';  // Success message for profile updates

// Handling "Update Profile" form submission
// Checking for POST request and the "update_profile" button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Getting form data, trimming whitespace from inputs
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $dateOfBirth = trim($_POST['dateOfBirth'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $preferredMovie = trim($_POST['preferredMovie'] ?? '');
    $preferredTVShow = trim($_POST['preferredTVShow'] ?? '');

    // Validating the form inputs
    if (empty($firstName)) {
        $errors[] = "First Name is required.";
    }
    if (empty($lastName)) {
        $errors[] = "Last Name is required.";
    }
    if (empty($dateOfBirth)) {
        $errors[] = "Date of Birth is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
        $errors[] = "Date of Birth must be in YYYY-MM-DD format.";  // Ensuring correct date format
    }
    if (empty($country)) {
        $errors[] = "Country is required.";
    }
    if (empty($preferredMovie)) {
        $errors[] = "Preferred Movie is required.";
    }
    if (empty($preferredTVShow)) {
        $errors[] = "Preferred TV Show is required.";
    }

    // If no errors, updating the user object and saving to users.json
    if (empty($errors)) {
        // Updating $currentUser with new values using setter methods
        $currentUser->setFirstName($firstName);
        $currentUser->setLastName($lastName);
        $currentUser->setDateOfBirth($dateOfBirth);
        $currentUser->setCountry($country);
        $currentUser->setPreferredMovie($preferredMovie);
        $currentUser->setPreferredTVShow($preferredTVShow);

        // Updating the user in $usersData
        foreach ($usersData as &$u) {
            if ($u['username'] === $_SESSION['username']) {
                $u = json_decode($currentUser->toJSON(), true);  // Converting updated object to array
                break;
            }
        }

        // Saving updated $usersData to "users.json"
        // JSON_PRETTY_PRINT formats JSON with indentation
        file_put_contents('data/users.json', json_encode($usersData, JSON_PRETTY_PRINT));

        // Setting success message
        $successMessage = "Profile updated successfully!";
        // Redirecting to prevent form resubmission
        header("Location: profile.php");
        exit;
    }
}

// Loading reviews from "user_reviews.json"
$reviewsFile = 'data/user_reviews.json';
if (!file_exists($reviewsFile)) {
    // If the file doesn't exist, creating it with an empty array
    file_put_contents($reviewsFile, json_encode([]));
}
$reviewsData = json_decode(file_get_contents($reviewsFile), true);

// Filtering reviews to show only the current user’s reviews
// array_filter() keeps items where the function returns true
$userReviews = array_filter($reviewsData, function($review) {
    return $review['username'] === $_SESSION['username'];
});

// Handling "Delete Review" action
// Checking for POST request and "delete_review" button
$deleteSuccessMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    $reviewIndex = $_POST['review_index'] ?? null;  // Getting review index from form
    // Verifying the index exists and the review belongs to the user
    if ($reviewIndex !== null && isset($reviewsData[$reviewIndex]) && $reviewsData[$reviewIndex]['username'] === $_SESSION['username']) {
        // Removing the review from $reviewsData using array_splice()
        array_splice($reviewsData, $reviewIndex, 1);  // Removing 1 item at $reviewIndex
        // Saving the updated reviews to "user_reviews.json"
        file_put_contents('data/user_reviews.json', json_encode($reviewsData, JSON_PRETTY_PRINT));
        $deleteSuccessMessage = "Review deleted successfully!";
        // Redirecting to prevent form resubmission
        header("Location: profile.php");
        exit;
    }
}

// Handling "Edit Review" action
// Checking for POST request and "edit_review" button
$editSuccessMessage = '';
$editErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_review'])) {
    // Getting form data for editing a review
    $reviewIndex = $_POST['review_index'] ?? null;
    $reviewTitle = trim($_POST['review_title'] ?? '');
    $reviewContent = trim($_POST['review_content'] ?? '');
    $reviewScore = trim($_POST['review_score'] ?? '');
    $authorName = trim($_POST['author_name'] ?? '');

    // Validating the edit form inputs
    if (empty($authorName)) {
        $editErrors[] = "Author Name is required.";
    }
    if (empty($reviewTitle)) {
        $editErrors[] = "Review Title is required.";
    }
    if (empty($reviewContent)) {
        $editErrors[] = "Review Content is required.";
    }
    if (empty($reviewScore)) {
        $editErrors[] = "Rating is required.";
    } elseif (!is_numeric($reviewScore) || $reviewScore < 1 || $reviewScore > 5) {
        $editErrors[] = "Rating must be a number between 1 and 5.";
    }

    // If no errors and review is valid, updates the review in $reviewsData
    if (empty($editErrors) && $reviewIndex !== null && isset($reviewsData[$reviewIndex]) && $reviewsData[$reviewIndex]['username'] === $_SESSION['username']) {
        $reviewsData[$reviewIndex]['authorName'] = $authorName;
        $reviewsData[$reviewIndex]['title'] = $reviewTitle;
        $reviewsData[$reviewIndex]['content'] = $reviewContent;
        $reviewsData[$reviewIndex]['score'] = (float)$reviewScore;  // Casting to float for consistency
        file_put_contents('data/user_reviews.json', json_encode($reviewsData, JSON_PRETTY_PRINT));
        $editSuccessMessage = "Review updated successfully!";
        // Redirecting to prevent form resubmission
        header("Location: profile.php");
        exit;
    }
}

// Starts output buffering to capture the HTML/CSS/JavaScript content for the page
// This will be stored in $content and passed to master.php
ob_start(); // Start output buffering
?>
<style>
    /* Aligning body styling with the rest of the site */
    body {
        background-color: #fff; /* White background */
        color: #000; /* Black text */
    }

    /* Profile section */
    .profile-section {
        max-width: 1200px; /* Matching streaming_service.php and recommended.php */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding padding */
    }

    /* Profile header */
    .profile-header {
        margin-bottom: 30px;  /* Spacing below header */
    }
    .profile-header h2 {
        font-weight: 700;  /* Bold heading */
        font-size: 1.8rem; /* Matching recommended.php */
        margin: 0;  /* Removing default margins */
    }

    /* Success and error messages */
    .success-message, .error-messages {
        padding: 10px;  /* Inner padding */
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 10px;  /* Rounded corners */
        margin-bottom: 20px;  /* Spacing below */
        display: flex;  /* Aligning icon and text horizontally */
        align-items: center;  /* Vertically centering content */
        gap: 10px;  /* Spacing between icon and text */
    }
    .success-message {
        background-color: #d4edda;  /* Light green background */
        color: #155724;  /* Dark green text */
    }
    .error-messages {
        background-color: #f8d7da;  /* Light red background */
        color: #721c24;  /* Dark red text */
    }
    .error-messages ul {
        margin: 0;  /* Removing default margins */
        padding: 0;  /* Removing default padding */
        list-style: none;  /* No bullets */
    }
    .error-messages li {
        margin-bottom: 5px;  /* Spacing between error items */
    }
    .success-message i, .error-messages i {
        font-size: 1.2rem;  /* Slightly larger icons */
    }

    /* Form styling */
    .form-group {
        margin-bottom: 20px; /* Consistent spacing */
    }
    .form-group label {
        display: block;  /* Full width labels */
        font-weight: 700; /* Bold labels */
        margin-bottom: 8px;  /* Spacing below labels */
        font-size: 1.1rem; /* Matching streaming_service.php paragraph size */
    }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;  /* Full width */
        padding: 10px;  /* Inner padding */
        border: 1px solid #ddd; /* Matching card borders */
        border-radius: 8px;  /* Slightly rounded corners */
        font-family: "Comic Sans MS", "Comic Sans", cursive;  /* Site font */
        font-size: 1rem;  /* Standard text size */
        transition: border-color 0.3s;  /* Smooth border color change on focus */
    }
    .form-group textarea {
        height: 100px;  /* Fixing height for review content */
        resize: vertical;  /* Allowing vertical resizing only */
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #666;  /* Darker border on focus */
        outline: none;  /* Removing default outline */
    }
    .form-group input[readonly] {
        background-color: #e9ecef; /* Matching previous styling */
        cursor: not-allowed;  /* Indicating non-editable */
    }

    /* Update button */
    .update-btn, .submit-edit-btn, .cancel-edit-btn {
        display: inline-block;  /* Inline for flexibility */
        border: 1px solid black; /* Matching cta-btn */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 10px 20px;  /* Padding for size */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none;  /* No underline */
        font-weight: 500;  /* Medium-bold text */
        text-align: center;  /* Centering text */
        transition: background-color 0.3s;  /* Smooth hover effect */
    }
    .update-btn {
        width: 100%;  /* Full width for profile update */
        margin-bottom: 30px; /* Adding space before reviews section */
    }
    .update-btn:hover, .submit-edit-btn:hover, .cancel-edit-btn:hover {
        background-color: #f0f0f0; /* Matching cta-btn hover */
    }
    .submit-edit-btn, .cancel-edit-btn {
        margin-right: 10px;  /* Spacing between edit form buttons */
        padding: 8px 16px;  /* Slightly smaller padding */
    }

    /* Reviews section */
    .reviews-section {
        margin-top: 40px;  /* Spacing above reviews */
    }
    .reviews-section h3 {
        font-weight: 700;  /* Bold heading */
        font-size: 1.5rem;  /* Slightly smaller than main header */
        margin-bottom: 20px;  /* Spacing below heading */
    }
    .review-item {
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 10px;  /* Rounded corners */
        padding: 15px;  /* Inner padding */
        margin-bottom: 20px;  /* Spacing between items */
    }
    .review-item h5 {
        font-weight: 700;  /* Bold title */
        margin: 0 0 10px 0;  /* No top margin, 10px bottom */
    }
    .review-item p {
        margin: 0 0 5px 0;  /* Minimal spacing between paragraphs */
        font-size: 1rem;  /* Standard text size */
    }
    .review-item a {
        text-decoration: none;  /* No underline on links */
        color: inherit;  /* Inherits text color */
    }
    .review-actions {
        margin-top: 10px;  /* Spacing above buttons */
        display: flex;  /* Horizontal layout */
        gap: 10px;  /* Spacing between buttons */
    }
    .edit-btn, .delete-btn {
        border: 1px solid black;  /* Matching other buttons */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 6px 12px;  /* Smaller padding */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none;  /* No underline */
        font-weight: 500;  /* Medium-bold text */
        transition: background-color 0.3s;  /* Smooth hover effect */
    }
    .edit-btn:hover, .delete-btn:hover {
        background-color: #f0f0f0;  /* Light gray on hover */
    }

    /* Edit Review Form */
    .edit-review-form {
        margin-top: 10px;  /* Space above form */
        padding: 15px;  /* Inner padding */
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 5px;  /* Slightly rounded corners */
        display: none;  /* Hidden by default, shown via JavaScript */
    }

    /* Responsive design */
    @media (max-width: 576px) {
        .profile-section {
            padding: 15px;  /* Reduced padding */
        }
        .profile-header h2 {
            font-size: 1.5rem;  /* Smaller heading */
        }
        .reviews-section h3 {
            font-size: 1.3rem;  /* Smaller reviews heading */
        }
        .form-group label {
            font-size: 1rem;  /* Smaller labels */
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 8px;  /* Reducing padding */
            font-size: 0.9rem;  /* Smaller text */
        }
        .update-btn, .submit-edit-btn, .cancel-edit-btn {
            padding: 8px 16px;  /* Smaller padding */
            font-size: 0.9rem;  /* Smaller text */
        }
        .edit-btn, .delete-btn {
            padding: 4px 8px;  /* Even smaller padding */
            font-size: 0.9rem;  /* Smaller text */
        }
        .review-item {
            padding: 10px;  /* Reduced padding */
        }
        .review-item h5 {
            font-size: 1.1rem;  /* Smaller title */
        }
        .review-item p {
            font-size: 0.9rem;  /* Smaller text */
        }
        .edit-review-form {
            padding: 10px;  /* Reduced padding */
        }
    }
</style>

<!-- Profile Content -->
<div class="profile-section">  <!-- Main container -->
    <!-- Profile Header -->
    <div class="profile-header">
        <h2>User Profile</h2>
    </div>

    <!-- Profile Form -->
    <?php if (!empty($successMessage)): ?>  <!-- Checking for profile update success -->
        <div class="success-message">
            <i class="bi bi-check-circle-fill"></i>  <!-- Success icon -->
            <span><?php echo htmlspecialchars($successMessage); ?></span>
        </div>
    <?php endif; ?> 
    <?php if (!empty($errors)): ?>  <!-- Checking for profile update errors -->
        <div class="error-messages">
            <i class="bi bi-exclamation-circle-fill"></i>  <!-- Error icon -->
            <ul>
                <?php foreach ($errors as $error): ?>  <!-- Loops through errors -->
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="profile.php" name="update_profile">  <!-- Form submits to itself -->
        <div class="row">  <!-- Bootstrap grid row for two columns -->
            <!-- Left Column -->
            <div class="col-md-6">  <!-- 6 columns on medium+ screens -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentUser->getUsername()); ?>" readonly>
                	<!-- Readonly username field -->
                </div>
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required>
                </div>
                <div class="form-group">
                    <label for="dateOfBirth">Date of Birth (YYYY-MM-DD)</label>
                    <input type="text" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($dateOfBirth); ?>" required>
                </div>
                <div class="form-group">
                    <label for="preferredMovie">Preferred Movie</label>
                    <input type="text" id="preferredMovie" name="preferredMovie" value="<?php echo htmlspecialchars($preferredMovie); ?>" required>
                </div>
            </div>
            <!-- Right Column -->
            <div class="col-md-6">  <!-- 6 columns on medium+ screens -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser->getEmail()); ?>" readonly>
                	<!-- Readonly email field -->
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required>
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($country); ?>" required>
                </div>
                <div class="form-group">
                    <label for="preferredTVShow">Preferred TV Show</label>
                    <input type="text" id="preferredTVShow" name="preferredTVShow" value="<?php echo htmlspecialchars($preferredTVShow); ?>" required>
                </div>
            </div>
        </div>
        <button type="submit" class="update-btn" name="update_profile">Update Profile</button>
    </form>

    <!-- Your Reviews Section -->
    <div class="reviews-section">
        <h3>Your Reviews</h3>
        <?php if (!empty($deleteSuccessMessage)): ?>  <!-- Checking for delete success -->
            <div class="success-message">
                <i class="bi bi-check-circle-fill"></i>
                <span><?php echo htmlspecialchars($deleteSuccessMessage); ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($editSuccessMessage)): ?>  <!-- Checking for edit success -->
            <div class="success-message">
                <i class="bi bi-check-circle-fill"></i>
                <span><?php echo htmlspecialchars($editSuccessMessage); ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($editErrors)): ?>  <!-- Checking for edit errors -->
            <div class="error-messages">
                <i class="bi bi-exclamation-circle-fill"></i>
                <ul>
                    <?php foreach ($editErrors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (empty($userReviews)): ?>  <!-- Checking if user has no reviews -->
            <p>You haven't submitted any reviews yet.</p>
        <?php else: ?>
            <?php foreach ($userReviews as $index => $review): ?>  <!-- Looping through reviews -->
                <div class="review-item">
                    <h5>
                        <a href="details.php?name=<?php echo urlencode($review['movieTVShow']); ?>">
                            <?php echo htmlspecialchars($review['movieTVShow']); ?>
                        </a>
                    </h5>  <!-- Title linked to details.php -->
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($review['title']); ?></p>
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['score']); ?>/5</p>
                    <p><strong>Review:</strong> <?php echo htmlspecialchars($review['content']); ?></p>
                    <div class="review-actions">
                        <a href="#" class="edit-btn" data-index="<?php echo $index; ?>">Edit</a>
                        <!-- Edit button with index for JavaScript -->
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                            <input type="hidden" name="review_index" value="<?php echo $index; ?>">
                            <button type="submit" name="delete_review" class="delete-btn">Delete</button>
                        </form>
                    </div>
                    <!-- Edit Review Form -->
                    <form method="POST" class="edit-review-form" id="edit-review-form-<?php echo $index; ?>">
                        <input type="hidden" name="review_index" value="<?php echo $index; ?>">
                        <div class="form-group">
                            <label for="author_name_<?php echo $index; ?>">Author Name</label>
                            <input type="text" id="author_name_<?php echo $index; ?>" name="author_name" value="<?php echo htmlspecialchars($_POST['author_name'] ?? $review['authorName']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="review_title_<?php echo $index; ?>">Review Title</label>
                            <input type="text" id="review_title_<?php echo $index; ?>" name="review_title" value="<?php echo htmlspecialchars($_POST['review_title'] ?? $review['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="review_content_<?php echo $index; ?>">Review Content</label>
                            <textarea id="review_content_<?php echo $index; ?>" name="review_content" required><?php echo htmlspecialchars($_POST['review_content'] ?? $review['content']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="review_score_<?php echo $index; ?>">Rating (1-5)</label>
                            <select id="review_score_<?php echo $index; ?>" name="review_score" required>
                                <option value="">Select a rating</option>
                                <option value="1" <?php echo (($_POST['review_score'] ?? $review['score']) == '1') ? 'selected' : ''; ?>>1</option>
                                <option value="2" <?php echo (($_POST['review_score'] ?? $review['score']) == '2') ? 'selected' : ''; ?>>2</option>
                                <option value="3" <?php echo (($_POST['review_score'] ?? $review['score']) == '3') ? 'selected' : ''; ?>>3</option>
                                <option value="4" <?php echo (($_POST['review_score'] ?? $review['score']) == '4') ? 'selected' : ''; ?>>4</option>
                                <option value="5" <?php echo (($_POST['review_score'] ?? $review['score']) == '5') ? 'selected' : ''; ?>>5</option>
                            </select>
                        </div>
                        <button type="submit" name="edit_review" class="submit-edit-btn">Save Changes</button>
                        <button type="button" class="cancel-edit-btn" data-index="<?php echo $index; ?>">Cancel</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript to Toggle the Edit Review Form -->
<script>
    document.addEventListener('DOMContentLoaded', function() {   // Waiting for the DOM to fully load before running the script
        const editButtons = document.querySelectorAll('.edit-btn');  // Selecting all edit buttons
        const cancelButtons = document.querySelectorAll('.cancel-edit-btn');  // Selecting all cancel buttons

        // Adding click event listeners to edit buttons
        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();  // Prevents default link behavior
                const index = this.getAttribute('data-index');  // Getting review index from data attribute
                const form = document.getElementById('edit-review-form-' + index);  // Finding the edit form
                if (form) {
                    form.style.display = 'block';  // Displaying the form
                    this.style.display = 'none';  // Hiding the edit button
                }
            });
        });

        // Adding click event listeners to cancel buttons
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');  // Getting review index
                const form = document.getElementById('edit-review-form-' + index);  // Finding the form
                const editBtn = document.querySelector(`.edit-btn[data-index="${index}"]`);  // Finding the edit button
                if (form && editBtn) {
                    form.style.display = 'none';  // Hideing the form
                    editBtn.style.display = 'inline-block';  // Displaying the edit button
                    form.reset();  // Resetting the form to original values
                }
            });
        });
    });
</script>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->