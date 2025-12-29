<?php
namespace src\Model;

class Client
{
    private $id;
    private $nom;
    private $telephone;
    private $email;
    private $createdAt;

    public function __construct($id, $nom, $telephone, $email, $createdAt)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getTelephone() { return $this->telephone; }
    public function getEmail() { return $this->email; }
}
