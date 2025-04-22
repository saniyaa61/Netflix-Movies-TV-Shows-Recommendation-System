<?php

class User {
    
    // Data members
    private $firstName;
    private $lastName;
    private $dateOfBirth;
    private $country;
    private $email;
    private $username;
    private $password;
    private $preferredMovie;
    private $preferredTVShow;
    private $watchlist;
    
    // Constructor
    public function __construct($firstName, $lastName, $dateOfBirth, $country, $email, $username, $password, $preferredMovie, $preferredTVShow, $watchlist = []) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
        $this->country = $country;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password; // Can be plain text or hashed
        $this->preferredMovie = $preferredMovie;
        $this->preferredTVShow = $preferredTVShow;
        $this->watchlist = $watchlist; // Initialize watchlist (default to empty array)
    }
    
    // Getters and Setters
    public function getFirstName() { return $this->firstName; }
    public function setFirstName($firstName) { $this->firstName = $firstName; }
    
    public function getLastName() { return $this->lastName; }
    public function setLastName($lastName) { $this->lastName = $lastName; }
    
    public function getDateOfBirth() { return $this->dateOfBirth; }
    public function setDateOfBirth($dateOfBirth) { $this->dateOfBirth = $dateOfBirth; }
    
    public function getCountry() { return $this->country; }
    public function setCountry($country) { $this->country = $country; }
    
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    
    public function getUsername() { return $this->username; }
    public function setUsername($username) { $this->username = $username; }
    
    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }
    
    public function getPreferredMovie() { return $this->preferredMovie; }
    public function setPreferredMovie($preferredMovie) { $this->preferredMovie = $preferredMovie; }
    
    public function getPreferredTVShow() { return $this->preferredTVShow; }
    public function setPreferredTVShow($preferredTVShow) { $this->preferredTVShow = $preferredTVShow; }
    
    public function getWatchlist() { return $this->watchlist; }
    public function setWatchlist($watchlist) { $this->watchlist = $watchlist; }
    
    // JSON serialization
    public function toJSON() {
        return json_encode([
            'username' => $this->username, // Match the order in users.json
            'email' => $this->email,
            'password' => $this->password,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'dateOfBirth' => $this->dateOfBirth,
            'country' => $this->country,
            'preferredMovie' => $this->preferredMovie,
            'preferredTVShow' => $this->preferredTVShow,
            'watchlist' => $this->watchlist
        ]);
    }
    
    // Verify password (supports both plain text and hashed passwords)
    public function verifyPassword($password) {
        // Check if the stored password is hashed (starts with "$2y$" and is long)
        $isHashed = (strlen($this->password) > 20 && strpos($this->password, '$2y$') === 0);
        // strlen() gets the length, strpos() finds where "$2y$" appears (0 means starting position)
        
        if ($isHashed) {
            // If hashed, use password_verify
            return password_verify($password, $this->password);
        } else {
            // If plain text, compare directly
            return $this->password === $password;  // "===" checks if they’re exactly the same
        }
    }
    
} // End of User class

?>