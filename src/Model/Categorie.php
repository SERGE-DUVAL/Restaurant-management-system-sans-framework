<?php
namespace src\Model;

class Categorie
{
    private $id;
    private $nom;
    private $description;
    private $createdAt;
    private $updatedAt;

    public function __construct($id, $nom, $description, $createdAt, $updatedAt)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
}
