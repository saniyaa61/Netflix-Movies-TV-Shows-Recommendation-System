<?php

class Actor {
    
    // Data Members
    private $firstName;
    private $lastName;
    private $dateOfBirth;
    private $nationality;
    private $isActive;
    
    // Constructor
    public function __construct($firstName, $lastName, $dateOfBirth, $nationality, $isActive) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
        $this->nationality = $nationality;
        $this->isActive = $isActive;
    }
    
    // Getters and Setters
    public function getFirstName() { return $this->firstName; }
    public function setFirstName($firstName) { $this->firstName = $firstName; }
    
    public function getLastName() { return $this->lastName; }
    public function setLastName($lastName) { $this->lastName = $lastName; }
    
    public function getDateOfBirth() { return $this->dateOfBirth; }
    public function setDateOfBirth($dateOfBirth) { $this->dateOfBirth = $dateOfBirth; }
    
    public function getNationality() { return $this->nationality; }
    public function setNationality($nationality) { $this->nationality = $nationality; }
    
    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
    
    // JSON serialization for file storage
    public function toJSON() {
        return json_encode([
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'dateOfBirth' => $this->dateOfBirth,
            'nationality' => $this->nationality,
            'isActive' => $this->isActive
        ]);
    }
    
    // Display method for debugging or rendering
    public function printActorDetails() {
        echo "<p><strong>{$this->firstName} {$this->lastName}</strong><br>";
        echo "DOB: {$this->dateOfBirth}<br>";
        echo "Nationality: {$this->nationality}<br>";
        echo "Active: " . ($this->isActive ? "Yes" : "No") . "</p>";
    }
    
} // End of Actor class

?>