<?php
namespace src\Model;

class Paiement
{
    private $id;
    private $commandeId;
    private $modePaiement;
    private $montant;
    private $statut;
    private $createdAt;

    public function __construct(
        $id,
        $commandeId,
        $modePaiement,
        $montant,
        $statut,
        $createdAt
    ) {
        $this->id = $id;
        $this->commandeId = $commandeId;
        $this->modePaiement = $modePaiement;
        $this->montant = $montant;
        $this->statut = $statut;
        $this->createdAt = $createdAt;
    }
    public function getId() { return $this->id; }
    public function getCommandeId() { return $this->commandeId; }
    public function getModePaiement() { return $this->modePaiement; }
    public function getMontant() { return $this->montant; }
    public function getStatut() { return $this->statut; }
    public function getCreatedAt() { return $this->createdAt; }
}
