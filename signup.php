<!-- signup.php -->
<?php
// Starting the session to manage user data across pages
session_start();

// Including the User class file to create user objects
require_once 'classes/User.class.php';

// Checking if the user is already logged in; if so, redirect to index.php
if (isset($_SESSION['username'])) {
    header("Location: index.php");  // Sending a redirect header to the browser
    exit;  // Stopping the script to ensure no further code runs
}

// Initializing variables to hold form data and validate errors
$username = $email = $password = $confirmPassword = '';
$errors = [];

// Handling form submission when the request method is POST (form submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Getting form data, trimming whitespace where applicable; defaults to empty string if not set
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';  // Password (not trimmed, as spaces might be intentional)
    $confirmPassword = $_POST['confirmPassword'] ?? '';  // Confirming password

    // Loading existing users from users.json into an array
    // file_get_contents() reads the file, json_decode() converts JSON to a PHP array with "true" for associative format
    $usersData = json_decode(file_get_contents('data/users.json'), true);

    // Validating the form inputs
    if (empty($username)) {
        $errors[] = "Username is required.";  // Checking if username is empty
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";  // Ensurinng minimum length
    } elseif (array_search($username, array_column($usersData, 'username')) !== false) {
        $errors[] = "Username already exists.";  // Checking for duplicate username
        // array_column() extracts usernames, array_search() returns index or false
    }

    if (empty($email)) {
        $errors[] = "Email is required.";  // Checking if email is empty
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";  // Validating email format (e.g., has @ and domain)
    }

    if (empty($password)) {
        $errors[] = "Password is required.";  // Checking if password is empty
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";  // Ensuring minimum length
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";  // Ensuring password and confirmation match
    }

    // If no validation errors, creating a new User object and saving to users.json
    if (empty($errors)) {
        // Creating a new User object with provided and default values
        $newUser = new User(
            '', // firstName (empty)
            '', // lastName (empty)
            '', // dateOfBirth (empty)
            '', // country (empty)
            $email,  // Email from form
            $username,  // Username from form
            password_hash($password, PASSWORD_DEFAULT), // Hashing the password for security
            '', // preferredMovie (empty)
            '', // preferredTVShow (empty)
            []  // watchlist (empty array)
        );

        // Adding the new user to $usersData
        // $newUser->toJSON() returns JSON string; json_decode() converts it to an array
        $usersData[] = json_decode($newUser->toJSON(), true);

        // Saving the updated $usersData back to "users.json"
        // JSON_PRETTY_PRINT formats the JSON with indentation for readability.
        file_put_contents('data/users.json', json_encode($usersData, JSON_PRETTY_PRINT));

        // Redirecting to login page with success message
        header("Location: login.php?signup=success");
        exit;
    }
}

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

    /* Form section */
    .signup-section {
        max-width: 500px;  /* Limiting width for a compact form */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding padding */
    }
    .signup-section h2 {
        font-weight: 700;  /* Bold heading */
        text-align: center;  /* Centering the title */
        margin-bottom: 20px;  /* Spacing below heading */
    }
    .form-group {
        margin-bottom: 15px;  /* Spacing between form fields */
    }
    .form-group label {
        display: block;  /* Full width for labels */
        font-weight: 600;  /* Slightly bold labels */
        margin-bottom: 5px;  /* Spacing below labels */
    }
    .form-group input {
        width: 100%;  /* Full width inputs */
        padding: 8px;  /* Inner padding */
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 5px;  /* Rounded corners */
        font-family: "Comic Sans MS", "Comic Sans", cursive;  /* Matching site font */
        font-size: 1rem;  /* Standard text size */
    }
    .error-messages {
        color: red;  /* Red text for errors */
        margin-bottom: 15px;  /* Spacing below error list */
    }
    .error-messages li {
        margin-bottom: 5px;  /* Spacing between error items */
    }
    .signup-btn {
        display: block;  /* Full width button */
        width: 100%;  /* Ensuring full width */
        border: 1px solid black;  /* Black border */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 10px;  /* Padding for size */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none;  /* Removing underline */
        font-weight: 500;  /* Medium-bold text */
        text-align: center;  /* Centering text */
        transition: background-color 0.3s;  /* Smooth hover effect */
    }
    .signup-btn:hover {
        background-color: #f0f0f0;  /* Light gray on hover */
    }
    .login-link {
        text-align: center;  /* Centering the link */
        margin-top: 15px;  /* Spacing above link */
    }
    .login-link a {
        color: black;  /* Black text */
        text-decoration: none;  /* No underline */
    }
    .login-link a:hover {
        text-decoration: underline;  /* Underline on hover */
    }
</style>

<!-- Signup Content -->
<div class="signup-section">  <!-- Main container -->
    <h2>Sign Up</h2>  <!-- Page title -->
    <?php if (!empty($errors)): ?>  <!-- Checking if there are validation errors -->
        <ul class="error-messages">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            	<!-- Displaying each error safely -->
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="signup.php">  <!-- Form submits to itself -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        	<!-- Text input with previous value preserved; required attribute enforces input -->
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        	<!-- Email input with previous value; type="email" adds basic validation -->
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
       		<!-- Password input; no value preserved for security -->
        </div>
        <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
        	<!-- Confirmation password input -->
        </div>
        <button type="submit" class="signup-btn">Sign Up</button>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Log In</a></p>
    	<!-- Link to login page -->
    </div>
</div>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->