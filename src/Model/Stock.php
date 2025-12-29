<?php
namespace src\Model;

class Stock
{
    private $id;
    private $platId;
    private $quantite;
    private $seuilAlerte;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id,
        $platId,
        $quantite,
        $seuilAlerte,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->platId = $platId;
        $this->quantite = $quantite;
        $this->seuilAlerte = $seuilAlerte;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function isEnRupture()
    {
        return $this->quantite <= $this->seuilAlerte;
    }
    public function getId() { return $this->id; }
    public function getPlatId() { return $this->platId; }
    public function getQuantite() { return $this->quantite; }
    public function getSeuilAlerte() { return $this->seuilAlerte; }
    public function getCreatedAt() { return $this->createdAt; }
}
