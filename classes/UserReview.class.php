<?php

class UserReview {
    
    // Data memberd
    private $authorName;
    private $title;
    private $content;
    private $score;
    private $movieTVShow; // Reference to MovieTVShow object
    
    // Constructor
    public function __construct($authorName, $title, $content, $score, $movieTVShow) {
        $this->authorName = $authorName;
        $this->title = $title;
        $this->content = $content;
        $this->score = $score;
        $this->movieTVShow = $movieTVShow;
    }
    
    // Getters and Setters
    public function getAuthorName() { return $this->authorName; }
    public function setAuthorName($authorName) { $this->authorName = $authorName; }
    
    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = $title; }
    
    public function getContent() { return $this->content; }
    public function setContent($content) { $this->content = $content; }
    
    public function getScore() { return $this->score; }
    public function setScore($score) { $this->score = $score; }
    
    public function getMovieTVShow() { return $this->movieTVShow; }
    public function setMovieTVShow($movieTVShow) { $this->movieTVShow = $movieTVShow; }
    
    // JSON serialization
    public function toJSON() {
        return json_encode([
            'authorName' => $this->authorName,
            'title' => $this->title,
            'content' => $this->content,
            'score' => $this->score,
            'movieTVShow' => $this->movieTVShow->getName() // Store reference by name
        ]);
    }
    
    // Display method
    public function printReview() {
        echo "<p><strong>{$this->title}</strong> by {$this->authorName}<br>";
        echo "Score: {$this->score}/10<br>";
        echo "{$this->content}</p>";
    }
    
} // End of UserReview class

?>