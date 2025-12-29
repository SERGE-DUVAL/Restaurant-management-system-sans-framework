<?php
namespace src\Model;

class CommandePlat
{
    private $id;
    private $commandeId;
    private $platId;
    private $quantite;
    private $prixUnitaire;

    public function __construct($id, $commandeId, $platId, $quantite, $prixUnitaire)
    {
        $this->id = $id;
        $this->commandeId = $commandeId;
        $this->platId = $platId;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
    }

    public function getSousTotal()
    {
        return $this->quantite * $this->prixUnitaire;
    }
}
