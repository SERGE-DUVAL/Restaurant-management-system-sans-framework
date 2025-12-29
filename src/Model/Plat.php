<?php
namespace src\Model;

class Plat
{
    private $id;
    private $nom;
    private $description;
    private $prix;
    private $image;
    private $categorieId;
    private $disponible;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id,
        $nom,
        $description,
        $prix,
        $image,
        $categorieId,
        $disponible,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->image = $image;
        $this->categorieId = $categorieId;
        $this->disponible = $disponible;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getPrix() { return $this->prix; }
    public function getImage() { return $this->image; }
    public function getCategorieId() { return $this->categorieId; }
    public function isDisponible() { return $this->disponible; }
}
