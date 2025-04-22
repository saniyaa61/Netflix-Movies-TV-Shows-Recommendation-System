<!-- logout.php -->
<?php
// Starting the session to access session variables
session_start();

// Destroying the session

// session_unset() removes all data stored in $_SESSION (username), but keeps the session active
session_unset();

// session_destroy() ends the session entirely, invalidating it and removing the session file on the server
session_destroy();

// Redirecting to homepage
header("Location: index.php");  // header() sends a "Location" header to the browser, instructing it to navigate to index.php

// Stops script execution after the redirect
// exit ensures no further code runs, preventing unintended output or actions post-redirect
exit;
?>