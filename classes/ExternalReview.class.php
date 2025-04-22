<?php

class ExternalReview {
    
    // Data members
    private $sourceName;
    private $sourceLocation;
    private $isOnline;
    private $rating;
    private $description;
    private $movieTVShow; // Reference to MovieTVShow object
    
    // Constructor
    public function __construct($sourceName, $sourceLocation, $isOnline, $rating, $description, $movieTVShow) {
        $this->sourceName = $sourceName;
        $this->sourceLocation = $sourceLocation;
        $this->isOnline = $isOnline;
        $this->rating = $rating;
        $this->description = $description;
        $this->movieTVShow = $movieTVShow;
    }
    
    // Getters and Setters
    public function getSourceName() { return $this->sourceName; }
    public function setSourceName($sourceName) { $this->sourceName = $sourceName; }
    
    public function getSourceLocation() { return $this->sourceLocation; }
    public function setSourceLocation($sourceLocation) { $this->sourceLocation = $sourceLocation; }
    
    public function getIsOnline() { return $this->isOnline; }
    public function setIsOnline($isOnline) { $this->isOnline = $isOnline; }
    
    public function getRating() { return $this->rating; }
    public function setRating($rating) { $this->rating = $rating; }
    
    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }
    
    public function getMovieTVShow() { return $this->movieTVShow; }
    public function setMovieTVShow($movieTVShow) { $this->movieTVShow = $movieTVShow; }
    
    // JSON serialization
    public function toJSON() {
        return json_encode([
            'sourceName' => $this->sourceName,
            'sourceLocation' => $this->sourceLocation,
            'isOnline' => $this->isOnline,
            'rating' => $this->rating,
            'description' => $this->description,
            'movieTVShow' => $this->movieTVShow->getName() // Store reference by name
        ]);
    }
    
    // Display method
    public function printReview() {
        echo "<p><strong>{$this->sourceName}</strong><br>";
        echo "Rating: {$this->rating}/10<br>";
        echo "Description: {$this->description}<br>";
        echo "Source: <a href='{$this->sourceLocation}'>{$this->sourceLocation}</a></p>";
    }
    
} // End of ExternalReview class

?>