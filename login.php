<!-- login.php -->
<?php
// Starting the session o manage user data across pages
session_start();

// Including the User class file to create user objects for verification
require_once 'classes/User.class.php';

// Checking if the user is already logged in; if so, redirect to index.php
if (isset($_SESSION['username'])) {
    header("Location: index.php");  // Sending a redirect header to the browser
    exit;  // Stopping the script to ensure no further code runs
}

// Initializing variables to hold form data and validate errors
$username = $password = '';
$errors = [];

// Checking for signup success message in the URL (e.g., login.php?signup=success)
$signupSuccess = isset($_GET['signup']) && $_GET['signup'] === 'success';

// Handling form submission when the request method is POST (form submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Getting form data; trimming username whitespace, keeping password as-is
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';  // Password (not trimmed, spaces might be intentional)

    // Loading existing users from users.json
    // file_get_contents() reads the file, json_decode() converts JSON to a PHP array with "true" for associative format
    $usersData = json_decode(file_get_contents('data/users.json'), true);

    // Finding the user and creating a User object
    $user = null;
    // Looping through $usersData to find the user by username
    foreach ($usersData as $u) {
        if ($u['username'] === $username) {
            // Creating a User object with all stored data if username matches
            $user = new User(
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

    // Validating the form inputs and user credentials
    if (empty($username)) {
        $errors[] = "Username is required.";  // Checking if username is empty
    }
    if (empty($password)) {
        $errors[] = "Password is required.";  // Checking if password is empty
    }
    if (!$user) {
        $errors[] = "Invalid username or password.";  // No user found with that username
    } elseif (!$user->verifyPassword($password)) {
        $errors[] = "Invalid username or password.";  // Password doesn’t match hash
    }

    // If no validation errors, setting the session and redirecting to index.php
    if (empty($errors)) {
        $_SESSION['username'] = $username;  // Storing username in session for authentication
        header("Location: index.php");  // Redirecting to homepage
        exit;  // Stopping script execution
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
    .login-section {
        max-width: 500px;  /* Limiting width for a compact form, matches signup.php */
        margin: 0 auto;  /* Centering horizontally */
        padding: 20px;  /* Adding padding */
    }
    .login-section h2 {
        font-weight: 700;  /* Bold heading */
        text-align: center;  /* Centering the title */
        margin-bottom: 20px;  /* Spacing below heading */
    }
    .success-message {
        color: green;  /* Green text for success */
        text-align: center;  /* Centering the message */
        margin-bottom: 15px;  /* Spacing below message */
    }
    .error-messages {
        color: red;  /* Red text for errors */
        margin-bottom: 15px;  /* Spacing below error list */
    }
    .error-messages li {
        margin-bottom: 5px;  /* Spacing between error items */
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
    .login-btn {
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
    .login-btn:hover {
        background-color: #f0f0f0;  /* Light gray on hover */
    }
    .signup-link {
        text-align: center;  /* Centering the link */
        margin-top: 15px;  /* Spacing above link */
    }
    .signup-link a {
        color: black;  /* Black text */
        text-decoration: none;  /* No underline */
    }
    .signup-link a:hover {
        text-decoration: underline;  /* Underline on hover */
    }
</style>

<!-- Login Content -->
<div class="login-section">  <!-- Main container -->
    <h2>Log In</h2>  <!-- Page title -->
    <?php if ($signupSuccess): ?>  <!-- Checking for signup success flag -->
        <p class="success-message">Signup successful! Please log in.</p>
    	<!-- Displaying green success message -->
    <?php endif; ?>
    <?php if (!empty($errors)): ?>  <!-- Checking if there are validation errors -->
        <ul class="error-messages">
            <?php foreach ($errors as $error): ?>  <!-- Looping through errors -->
                <li><?php echo htmlspecialchars($error); ?></li>
            	<!-- Displaying each error safely -->
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="login.php">  <!-- Form submits to itself -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        	<!-- Text input with previous value preserved; required attribute enforcing input -->
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        	<!-- Password input; no value preserved for security -->
        </div>
        <button type="submit" class="login-btn">Log In</button>  <!-- Submit button -->
    </form>
    <div class="signup-link">
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    	<!-- Link to signup page -->
    </div>
</div>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->