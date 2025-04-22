<!-- streaming_service.php -->
<?php
// Starting the session to maintain user data (login status) across pages
session_start();

// Starting output buffering to capture all HTML/CSS output into a variable ($content).
// This allows us to build the page content and pass it to master.php for rendering
ob_start(); // Start output buffering
?>
<style>
    /* Aligning body styling with the rest of the site */
    body {
        background-color: #fff; /* White background to match other pages */
        color: #000; /* Black text */
    }

    /* Section styling */
    .streaming-section {
        max-width: 1200px;  /* Capping the width at 1200px to keep content readable on wide screens */
        margin: 0 auto;  /* Centering the section horizontally by setting left/right margins to auto */
        padding: 20px;  /* Adding 20px padding on all sides for breathing room */
    }

    /* Header section with logo and title aligned horizontally */
    .header-section {
        text-align: center;  /* Centering all text and elements inside this section */
        margin-bottom: 40px;  /* Adding 40px space below for separation from the next section */
    }
    .header-content {
        display: flex;  /* Using flexbox to arrange logo and title horizontally */
        align-items: center;  /* Vertically centering the logo and title */
        justify-content: center;  /* Horizontally centering the content within the section */
        gap: 25px; /* Increasing space to accommodate larger elements */
        flex-wrap: wrap; /* Allowing wrapping on smaller screens */
    }
    .netflix-logo {
        width: 180px;  /* Setting logo width to 180px for a balanced size */
        height: auto;  /* Keeping the logo’s aspect ratio by auto-adjusting height */
    }
    .title-container {
        display: flex;  /* Using flexbox to stack title and tagline vertically */
        flex-direction: column;  /* Arranging items in a column (top to bottom) */
        align-items: center; /* Centering the title and tagline */
    }
    .header-section h1 {
        margin: 0; /* Removing default margin to align properly with logo */
        font-size: 3.5rem; /* Setting font size for a much bigger title */
    }
    .header-section .tagline {
        margin-top: 8px; /* Adding 8px space above the tagline for separation from the title */
        font-size: 1.4rem; /* Slightly larger font size for visibility */
    }

    /* About section */
    .about-section {
        margin-bottom: 40px;  /* Adding 40px space below to separate from the next section */
    }
    .about-section h2 {
        font-weight: 700;  /* Making the heading bold (700 is a heavy weight) */
        margin-bottom: 20px;  /* Adding 20px space below the heading */
    }
    .about-section p {
        font-size: 1.1rem;  /* Slightly larger text for readability */
        line-height: 1.6;  /* Increasing line spacing to 1.6 times the font size for better legibility */
    }

    /* Features section */
    .features-section {
        margin-bottom: 40px;  /* Spacing below the section */
    }
    .features-section h2 {
        font-weight: 700;  /* Bold heading */
        margin-bottom: 20px;  /* Spacing below the heading */
        text-align: center;  /* Centering the heading */
    }
    .feature-card {
        border: 1px solid #ddd;  /* Light gray border for a subtle outline */
        border-radius: 10px;  /* Rounded corners for a softer look */
        padding: 20px;  /* Adding padding inside the card */
        text-align: center;  /* Centering text and icons */
        transition: transform 0.3s;  /* Smooth scaling effect on hover over 0.3 seconds */
    }
    .feature-card:hover {
        transform: scale(1.05);  /* Enlarging the card by 5% when hovered over */
    }
    .feature-card i {
        font-size: 2.5rem;  /* Setting icon size to 2.5 times the base size */
        color: #000;  /* Black icons */
        margin-bottom: 15px;  /* Spacing below the icon */
    }
    .feature-card h5 {
        font-weight: 700;  /* Bold title */
        margin-bottom: 10px;  /* Space below the title */
    }

    /* Subscription plans section */
    .plans-section {
        margin-bottom: 40px;  /* Spacing below the section */
    }
    .plans-section h2 {
        font-weight: 700;  /* Bold heading */
        margin-bottom: 20px;  /* Spacing below the heading */
        text-align: center;  /* Centering the heading */
    }
    .plan-card {
        border: 1px solid #ddd;  /* Light gray border */
        border-radius: 10px;  /* Rounded corners */
        padding: 20px;  /* Inner padding */
        text-align: center;  /* Centering content */
        transition: transform 0.3s;  /* Smooth scaling on hover */
    }
    .plan-card:hover {
        transform: scale(1.05);  /* Enlarges by 5% on hover */
    }
    .plan-card h5 {
        font-weight: 700;  /* Bold plan name */
        margin-bottom: 10px;  /* Spacing below the name */
    }
    .plan-card .price {
        font-size: 1.5rem;  /* Larger font for price */
        font-weight: 600;  /* Slightly bold (600 is medium-bold) */
        color: #000;  /* Black text */
        margin-bottom: 10px;  /* Spacing below the price */
    }

    /* Call to action (CTA) button */
    .cta-btn {
        border: 1px solid black;  /* Black border */
        color: black;  /* Black text */
        background-color: white;  /* White background */
        padding: 10px 20px;  /* 10px top/bottom, 20px left/right padding */
        border-radius: 10px;  /* Rounded corners */
        text-decoration: none;  /* Removing link underline */
        font-weight: 500;  /* Medium-bold text */
        transition: background-color 0.3s;  /* Smooth background change on hover */
    }
    .cta-btn:hover {
        background-color: #f0f0f0;  /* Light gray background on hover */
    }
</style>

<!-- Streaming Service Content -->
<div class="streaming-section">  <!-- Main container for all content -->
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">  <!-- Flex container for logo and title -->
            <img src="images/netflix_logo.png" alt="Netflix Logo" class="netflix-logo">
            <div class="title-container">  <!-- Container for title and tagline -->
                <h1>Streaming Service: Netflix</h1>
                <p class="tagline">Your go-to platform for movies, TV shows, and more!</p>
            </div>
        </div>
    </div>

    <!-- About Netflix Section -->
    <div class="about-section">
        <h2>About Netflix</h2>
        <p>
            Netflix, founded in 1997 by Reed Hastings and Marc Randolph, started as a DVD rental service before 
            pivoting to streaming in 2007. Today, it’s a global leader in entertainment, offering a vast library of 
            movies, TV shows, documentaries, and original content. With a mission to "entertain the world," Netflix 
            operates in over 190 countries, serving millions of subscribers with personalized recommendations and 
            high-quality content.
        </p>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <h2>Key Features of Netflix</h2>
        <div class="row">  <!-- Bootstrap grid row for 3 columns -->
            <div class="col-md-4 mb-4">  <!-- 4 columns on medium+ screens, 4 units bottom margin -->
                <div class="feature-card">
                    <i class="bi bi-film"></i>  <!-- Bootstrap Icon for film -->
                    <h5>Vast Content Library</h5>
                    <p>Access thousands of movies, TV shows, and Netflix Originals across various genres.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="bi bi-download"></i>  <!-- Icon for downloading -->
                    <h5>Offline Viewing</h5>
                    <p>Download select titles to watch offline, perfect for travel or low-connectivity areas.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="bi bi-person-circle"></i>  <!-- Icon for profiles -->
                    <h5>Multiple Profiles</h5>
                    <p>Create up to 5 profiles per account for personalized recommendations for each user.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Plans Section -->
    <div class="plans-section">
        <h2>Subscription Plans</h2>
        <div class="row">  <!-- Bootstrap grid row -->
            <div class="col-md-4 mb-4">
                <div class="plan-card">
                    <h5>Standard with Ads</h5>
                    <p class="price">$6.99/month</p>
                    <p>Stream on 2 devices in HD with limited ads.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="plan-card">
                    <h5>Standard</h5>
                    <p class="price">$15.49/month</p>
                    <p>Stream on 2 devices in HD, ad-free, with downloads.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="plan-card">
                    <h5>Premium</h5>
                    <p class="price">$22.99/month</p>
                    <p>Stream on 4 devices in Ultra HD, ad-free, with downloads.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center">  <!-- Centers the button -->
        <a href="https://www.netflix.com" target="_blank" class="cta-btn">Visit Netflix</a>
    	<!-- Linking to Netflix’s website, opens in new tab with target="_blank" -->
    </div>
</div>
<?php
$content = ob_get_clean(); // Capturing all buffered content into $content

// Including the master.php file to render the full page
// "require_once" ensures it’s loaded once and stops if missing
require_once 'master.php';
?>  <!-- Ending the PHP block -->