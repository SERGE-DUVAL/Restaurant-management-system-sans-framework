<?php
namespace src\Model;

class Commande
{
    private $id;
    private $clientId;
    private $userId;
    private $total;
    private $statut;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id,
        $clientId,
        $userId,
        $total,
        $statut,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->userId = $userId;
        $this->total = $total;
        $this->statut = $statut;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getClientId() { return $this->clientId; }
    public function getUserId() { return $this->userId; }
    public function getTotal() { return $this->total; }
    public function getStatut() { return $this->statut; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
}
