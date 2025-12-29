<?php
namespace src\Model;

class User
{
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $passwordHash;
    private $role;
    private $telephone;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id,
        $nom,
        $prenom,
        $email,
        $passwordHash,
        $role,
        $telephone,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->telephone = $telephone;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getPasswordHash() { return $this->passwordHash; }
    public function getRole() { return $this->role; }
    public function getTelephone() { return $this->telephone; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
}
