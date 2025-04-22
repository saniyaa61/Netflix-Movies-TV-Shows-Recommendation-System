# NextN - Movies & TV Shows Recommendation Website

## Overview
NextN is a web application designed for movie and TV show enthusiasts to discover, explore, and manage their entertainment preferences. Built with PHP and styled with Bootstrap, the website offers a user-friendly interface with the following features:

- **Homepage (index.php):** Browse a curated list of movies and TV shows with posters, cast details, release years, and ratings. Toggle between movies and TV shows for personalized browsing.

- **Streaming Service (streaming_service.php):** Learn about Netflix, including its history, key features (vast content library, offline viewing, multiple profiles), and subscription plans.

- **Recommended (recommended.php):** View the top 3 recommended movies or TV shows based on average ratings, with a dropdown to switch between categories.

- **My Watchlist (my_watchlist.php):** Manage a personal watchlist (requires login) to save movies and TV shows for later viewing, with options to remove items.

- **Details Page (details.php):** Access detailed information about a selected movie or TV show, including posters, trailers, cast, director, genre, duration, and reviews (editorial, external, and user-submitted). Users can add items to their watchlist, rate with thumbs up/down, and submit reviews (login required).

- **User Authentication (signup.php, login.php, logout.php):** Sign up for a new account, log in to access personalized features, or log out securely. User data is stored in a JSON file.

- **User Profile (profile.php):** Update personal details (first name, last name, date of birth, country, preferred movie/TV show) and manage submitted reviews with edit and delete options.

- **Data-Driven Content:** Utilizes JSON files (movies_tvshows.json, actors.json, external_reviews.json, user_reviews.json, users.json) to store and display dynamic content.

The website uses a consistent design with a Comic Sans MS font, Bootstrap for responsive layouts, and custom CSS for styling buttons, cards, and forms. It provides a seamless experience for discovering entertainment options and managing user preferences.

## Prerequisites
To run the NextN website locally, you need the following:
  - `XAMPP:` A free and open-source tool that provides an Apache web server and PHP support.
  - `A web browser` (e.g., Chrome, Firefox, Edge).
  - `Basic knowledge`  of navigating file directories and web browsers.

### Installation and Running Instructions
Follow these steps to set up and run the NextN website on your local machine:

#### Install XAMPP:
Download XAMPP from https://www.apachefriends.org/ for your operating system (Windows, macOS, or Linux).

Install XAMPP by following the installer’s instructions. Ensure you select the Apache and PHP components during installation.

#### Enable the Apache Server:
Open the XAMPP Control Panel (usually found in the XAMPP installation directory or via a desktop shortcut).

Start the Apache module by clicking the "Start" button next to it. Ensure the status turns green, indicating the server is running.

#### Set Up the Project:
Clone or download the NextN project repository from GitHub to your local machine.

Locate the XAMPP installation directory (e.g., C:\xampp on Windows).

#### Navigate to the htdocs folder (e.g., C:\xampp\htdocs).
Copy the entire NextN project folder (containing index.php, master.php, details.php, etc., along with classes, data, images, and trailers folders) into the htdocs folder. For example, place it in C:\xampp\htdocs\nextn.

#### Access the Website:
Open a web browser (e.g., Chrome, Firefox).
In the address bar, type localhost/nextn (replace nextn with the name of your project folder if different) and press Enter.

The NextN homepage (index.php) should load, displaying the list of movies and TV shows.

Navigate through the website using the navigation bar (Streaming Service, Recommended, My Watchlist) or sign up/log in to access personalized features.

## Troubleshooting:
If localhost doesn’t load, ensure the Apache server is running in the XAMPP Control Panel.

Verify that the project folder is correctly placed in htdocs and that all files (including data/*.json, images/*, and trailers/*) are present.

Check file permissions if you encounter access issues, especially on macOS or Linux.

## Notes
The website stores user data, reviews, and watchlists in JSON files (data/users.json, data/user_reviews.json). Ensure the data folder is writable by the server (Apache) to allow updates.

The classes folder contains PHP classes (User.class.php, MovieTVShow.class.php, Actor.class.php) used for data modeling.

Media files (posters in images/ and trailers in trailers/) are referenced in the code; ensure they are not deleted or moved.

The website is designed to be responsive, but testing on various screen sizes is recommended for the best experience.

# Enjoy exploring movies and TV shows with NextN!