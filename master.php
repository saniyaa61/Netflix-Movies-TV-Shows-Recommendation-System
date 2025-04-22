<!-- master.php -->
<?php
// Removing session_start() since it will be called by the including pages

// Determining the current page for active navigation
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextN - Movies & TV Shows Recommendation</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/symbol.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Applying Comic Sans MS to all text */
        body {
            font-family: "Comic Sans MS", cursive, sans-serif;  /* Setting the font to Comic Sans MS */
            min-height: 100vh; /* Ensuring body takes full viewport height */
            display: flex;  /* Using flexbox, a layout system, to arrange child elements (header, main, footer) in a flexible way. */
            flex-direction: column;  /* Stacking the header, main, and footer vertically (top to bottom), like a column. */
        }

        /* Logo styling */
        .logo img {
            height: 40px; /* Setting the logo’s height to 40 pixels. */
        }

        /* Header layout */
        .header-container {
            display: flex;  /* Using flexbox to arrange items (logo, nav, search) horizontally by default. */
            align-items: center;  /* Vertically centering all items in the container (e.g., logo and buttons line up). */
            justify-content: space-between; /* Spreading items across the container: logo/nav on the left, search/signup on the right. */
        }

        /* Navigation buttons container */
        .nav-container {
            display: flex;  /* Arranging navigation buttons horizontally. */
            align-items: center;  /* Vertically centering the buttons so they align with the logo. */
            margin-left: 20px; /* Adjusting space between logo and buttons */
        }

        /* Custom button styling */
        .nav-btn {
            background-color: white;  /* Setting the button background to white. */
            color: black;  /* Setting the text color to black. */
            border: 1px solid black;  /* Adding a 1-pixel solid black border around the button. */
            padding: 8px 16px;  /* Adding padding inside the button: 8px top/bottom, 16px left/right, making it comfy to click. */
            margin-right: 10px;  /* Adding 10px of space to the right of each button, separating them from each other. */
            border-radius: 10px; /* Slightly sharper rounded edges */
        }
        .nav-btn:hover {
            background-color: #f0f0f0; /* Light gray on hover */
        }

        /* Search icon and bar */
        .search-container {
            position: relative;  /* Setting positioning context for child elements (though not heavily used here). */
            display: flex;  /* Arranging the search icon and bar horizontally. */
            align-items: center;  /* Vertically centering the icon and bar. */
        }
        .search-icon {
            font-size: 20px;  /* Setting the icon size to 20 pixels. */
            cursor: pointer; /* Changing the mouse cursor to a hand, indicating it’s clickable. */
            color: black;  /* Making the icon black. */
            margin-right: 10px;  /* Adding 10px space between the icon and the search bar. */
        }
        .search-bar {
            display: none; /* Hiding the search bar by default; it appears when the icon is clicked (via JavaScript). */
            width: 200px;  /* Setting the width to 200 pixels. */
            border: 1px solid black;  /* Adding a 1px black border. */
            border-radius: 5px;  /* Rounded corners by 5px. */
            padding: 5px;   /* Adding 5px padding inside for text spacing. */
            font-family: "Comic Sans MS", "Comic Sans", cursive;  /* Matching with site’s font */
        }
        .search-bar.active { /* Applies when the "active" class is added (via JavaScript) */
            display: inline-block; /* Making the search bar visible */
        }

        /* SignUp/SignIn button */
        .signup-btn {
            background-color: white;  /* White background. */
            color: black;  /* Black text. */
            border: 1px solid black;  /* 1px black border. */
            border-radius: 10px;  /* 1px black border. */
            padding: 8px 16px;  /* Padding for size and comfort. */
        }
        .signup-btn:hover {
            background-color: #f0f0f0;  /* Light gray background. */
        }

        /* Styles for the Account dropdown with user icon */
        .account-btn {
            background-color: transparent; /* No background, so it blends with the header. */
            color: black;  /* Black text/icon. */
            border: none; /* No border, unlike other buttons, for a cleaner look. */
            padding: 8px 16px;  /* Padding for size. */
            text-decoration: none; /* Removing the underline from the link. */
            font-weight: 500;  /* Making text slightly bold (500 is medium-bold). */
            transition: background-color 0.3s;  /* Smoothly changing background over 0.3 seconds on hover. */
            display: flex;  /* Arranging icon and username horizontally. */
            align-items: center;  /* Vertically centering icon and text. */
            gap: 5px; /* Spacing between icon and username */
        }
        .account-btn:hover {
            background-color: #f0f0f0; /* Light gray on hover */
        }
        .account-btn i {
            font-size: 20px; /* Setting icon size to 20px. */
        }
        .dropdown-menu {
            border-radius: 10px;  /* Rounded corners. */
            border: 1px solid black;  /* Black border. */
        }
        .dropdown-item {
            color: black;  /* Black text. */
            padding: 8px 16px;  /* Padding for spacing. */
        }
        .dropdown-item:hover {
            background-color: #f0f0f0;  /* Light gray background. */
        }

        /* Center the content area */
        main {
            flex: 1; /* Making main grow to fill available space between header and footer. */
            display: flex;  /* Using flexbox to center content. */
            justify-content: center; /* Centering content horizontally */
            align-items: center; /* Centering content vertically */
            padding: 20px 0; /* Adding some padding for spacing */
        }

        /* Footer styling */
        footer {
            margin-top: auto; /* Pushing footer to the bottom of the page*/
        }
        .socials-container {
            display: flex;  /* Arranging icons horizontally. */
            align-items: center;  /* Vertically centering icons and "Socials:" text. */
            gap: 10px;  /* 10px spacing between items (text and icons). */
        }
        .social-icon {
            font-size: 20px; /* Icon size. */
            text-decoration: none; /* Removing link underlines. */
            color: #000;  /* Black color */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-light py-3">  <!-- Starting the header with a light gray background (bg-light) and 3 units of vertical padding (py-3) from Bootstrap -->
        <div class="container">  <!-- Bootstrap’s container class setting a fixed width that adjusts for different screen sizes, centering the content -->
            <div class="header-container">
                <!-- Left Side: Logo and Navigation -->
                <div class="d-flex align-items-center">  <!-- Bootstrap’s d-flex making this a flexbox container, align-items-center centers items vertically -->
                    <!-- Logo linked to Homepage-->
                    <a href="index.php" class="logo">  <!-- Linking to index.php, with "logo" class for styling -->
                        <img src="images/logo.png" alt="NextN Logo">
                    </a>
                    <!-- Navigation -->
                    <nav class="nav-container">
                        <a href="streaming_service.php" class="btn nav-btn">Streaming Service</a>
                        <a href="recommended.php" class="btn nav-btn">Recommended</a>
                        <a href="my_watchlist.php" class="btn nav-btn">My Watchlist</a>
                    </nav>
                </div>
                <!-- Right Side: Search and SignUp/SignIn -->
                <div class="d-flex align-items-center gap-2">  <!-- Flexbox container with 2 units of gap between items (Bootstrap’s gap-2) -->
                    <!-- Search Icon and Form -->
                    <div class="search-container">
                        <i class="bi bi-search search-icon"></i>  <!-- Bootstrap Icon for search, styled with search-icon class -->
                        <form method="GET" action="search.php">  <!-- Form submits search query to search.php using GET (data goes in URL) -->
                            <input type="text" class="search-bar" name="query" placeholder="Search..." aria-label="Search">
                        </form>
                    </div>
                    <!-- SignUp/SignIn or Account -->
                    <?php if (isset($_SESSION['username'])): ?>  <!-- PHP check: if a session variable "username" exists (user is logged in), show this -->
                        <!-- Show Account dropdown with user icon and username when logged in -->
                        <div class="dropdown">  <!-- Bootstrap’s dropdown container -->
                            <a class="account-btn dropdown-toggle" href="#" role="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>  <!-- User icon from Bootstrap Icons -->
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>  <!-- Displays the username from the session, htmlspecialchars() prevents XSS attacks by escaping special characters -->
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="accountDropdown">  <!-- Dropdown menu linking to the button -->
                                <li><a class="dropdown-item" href="profile.php">Your Profile</a></li>  <!-- Menu item linking to profile -->
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>  <!-- Menu item linking to log out -->
                            </ul>
                        </div>
                    <?php else: ?>  <!-- If no username in session (not logged in), show this instead -->
                        <!-- Show SignUp/SignIn button linking to login.php when not logged in -->
                        <a href="login.php" class="btn signup-btn">SignUp/SignIn</a>
                    <?php endif; ?>  <!-- Ending the if/else block -->
                </div>
            </div>
        </div>
    </header>

    <!-- Content Area (Centered) -->
    <main>
        <div class="container text-center">
            <?php
            // This is where the page-specific content will go
            if (isset($content)) {
                echo $content; // Printing the content passed from the including page
            } else {
                echo "<p>Content Area - Varies by Page</p>";
            }
            ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-3">  <!-- Footer with light background and padding -->
        <div class="container">  <!-- Bootstrap container -->
            <div class="row">  <!-- Bootstrap row for a grid layout -->
                <!-- Socials -->
                <div class="col-md-6">  <!-- Taking half the width on medium+ screens (md = medium) -->
                    <div class="socials-container">
                        <span>Socials:</span>
                        <a href="https://facebook.com" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="https://instagram.com" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="https://x.com" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://youtube.com" class="social-icon"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <!-- Help Centre -->
                <div class="col-md-6 text-end">
                    <a href="help_centre.php" class="text-decoration-none">Help Centre</a>
                </div>
            </div>
            <div class="row mt-3">
           		<!-- Legal links -->
                <div class="col-md-6">
                    <a href="privacy_policy.php" class="text-decoration-none me-2">Privacy Policy</a> |
                    <a href="terms_of_use.php" class="text-decoration-none mx-2">Terms of Use</a> | 
                    <a href="contact_us.php" class="text-decoration-none ms-2">Contact Us</a>
                </div>
                <!-- Copyright notice -->
                <div class="col-md-6 text-end">
                    <p>© 2025 NextN, Inc.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS for search bar toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {  // Waiting until the HTML is fully loaded before running this code
            const searchIcon = document.querySelector('.search-icon');  // Finding the search icon element by its class
            const searchBar = document.querySelector('.search-bar');  // Finding the search bar element by its class

            // Adding a click event listener to the search icon
            searchIcon.addEventListener('click', function() {
                searchBar.classList.toggle('active');  // Toggles the "active" class on the search bar, showing/hiding it
                if (searchBar.classList.contains('active')) {  // If the search bar is now visible
                    searchBar.focus();  // Moving the cursor to the search bar so user can type right away
                } 
            });

            // Adding a keypress (Enter button) event listener to the search bar
            searchBar.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {  // If the pressed key is Enter
                    e.preventDefault();  // Stoping the default form submission behavior (e.g., page refresh)
                    searchBar.closest('form').submit();  // Finding the parent <form> and submitting it to search.php
                }
            });
        });
    </script>  <!-- Ending the JavaScript block -->
</body>  <!-- Ending the <body> section -->
</html>  <!-- Ending the HTML document -->