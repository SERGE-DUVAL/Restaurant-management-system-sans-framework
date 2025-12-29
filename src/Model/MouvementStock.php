<?php
namespace src\Model;

class MouvementStock
{
    private $id;
    private $stockId;
    private $type;
    private $quantite;
    private $description;
    private $createdAt;

    public function __construct(
        $id,
        $stockId,
        $type,
        $quantite,
        $description,
        $createdAt
    ) {
        $this->id = $id;
        $this->stockId = $stockId;
        $this->type = $type;
        $this->quantite = $quantite;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }
}
