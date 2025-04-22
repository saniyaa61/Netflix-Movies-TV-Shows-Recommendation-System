<?php

class MovieTVShow {
    
    //Data Members
    private $name;
    private $director;
    private $genre;
    private $releaseDate;
    private $pgRating;
    private $description;
    private $recommendationDescription;
    private $recommendationScore;
    private $averageRating;
    private $isMovie;
    private $actors = []; // Array of Actor objects
    private $userReviews = []; // Array of UserReview objects
    private $externalReviews = []; // Array of ExternalReview objects (exactly 3)
    
    // Construtor
    public function __construct($name, $director, $genre, $releaseDate, $pgRating, $description,
        $recommendationDescription, $recommendationScore, $averageRating, $isMovie) {
            $this->name = $name;
            $this->director = $director;
            $this->genre = $genre;
            $this->releaseDate = $releaseDate;
            $this->pgRating = $pgRating;
            $this->description = $description;
            $this->recommendationDescription = $recommendationDescription;
            $this->recommendationScore = $recommendationScore;
            $this->averageRating = $averageRating;
            $this->isMovie = $isMovie;
    }
    
    // Getters and Setters
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getDirector() { return $this->director; }
    public function setDirector($director) { $this->director = $director; }
    
    public function getGenre() { return $this->genre; }
    public function setGenre($genre) { $this->genre = $genre; }
    
    public function getReleaseDate() { return $this->releaseDate; }
    public function setReleaseDate($releaseDate) { $this->releaseDate = $releaseDate; }
    
    public function getPgRating() { return $this->pgRating; }
    public function setPgRating($pgRating) { $this->pgRating = $pgRating; }
    
    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }
    
    public function getRecommendationDescription() { return $this->recommendationDescription; }
    public function setRecommendationDescription($recommendationDescription) { $this->recommendationDescription = $recommendationDescription; }
    
    public function getRecommendationScore() { return $this->recommendationScore; }
    public function setRecommendationScore($recommendationScore) { $this->recommendationScore = $recommendationScore; }
    
    public function getAverageRating() { return $this->averageRating; }
    public function setAverageRating($averageRating) { $this->averageRating = $averageRating; }
    
    public function getIsMovie() { return $this->isMovie; }
    public function setIsMovie($isMovie) { $this->isMovie = $isMovie; }
    
    // Relationship methods
    public function addActor($actor) { $this->actors[] = $actor; }
    public function getActors() { return $this->actors; }
    
    public function addUserReview($userReview) { $this->userReviews[] = $userReview; }
    public function getUserReviews() { return $this->userReviews; }
    
    public function addExternalReview($externalReview) {
        if (count($this->externalReviews) < 3) $this->externalReviews[] = $externalReview;
    }
    public function getExternalReviews() { return $this->externalReviews; }
    
    // JSON serialization
    public function toJSON() {
        return json_encode([
            'name' => $this->name,
            'director' => $this->director,
            'genre' => $this->genre,
            'releaseDate' => $this->releaseDate,
            'pgRating' => $this->pgRating,
            'description' => $this->description,
            'recommendationDescription' => $this->recommendationDescription,
            'recommendationScore' => $this->recommendationScore,
            'averageRating' => $this->averageRating,
            'isMovie' => $this->isMovie
            // Relationships will be handled separately in data files
        ]);
    }
    
    // Display method
    public function printDetails() {
        echo "<h2>{$this->name}</h2>";
        echo "<p>Director: {$this->director}<br>";
        echo "Genre: {$this->genre}<br>";
        echo "Release Date: {$this->releaseDate}<br>";
        echo "PG Rating: {$this->pgRating}<br>";
        echo "Description: {$this->description}<br>";
        echo "Recommendation: {$this->recommendationDescription} (Score: {$this->recommendationScore}/10)<br>";
        echo "Average Rating: {$this->averageRating}</p>";
    }
    
} // End of MovieTVShow class

?>