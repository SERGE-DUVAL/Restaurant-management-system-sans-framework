<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Stock;
use PDO;

class StockController {

    // Lister tous les stocks
    public function index() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM stocks");
        return json_encode( $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Ajouter un produit au stock
    public function store($data) {
        $db = Database::getConnection();

        $stock = new Stock(
            null,
            $data['plat_id'],
            $data['quantite'],
            $data['seuil_alerte'],
            date('Y-m-d H:i:s'),
            null
        );

        $stmt = $db->prepare("
            INSERT INTO stocks(plat_id, quantite, seuil_alerte, created_at)
            VALUES(:plat_id, :quantite, :seuil_alerte, :created_at)
        ");

        $stmt->execute([
            'plat_id' => $stock->getPlatId(),
            'quantite' => $stock->getQuantite(),
            'seuil_alerte' => $stock->getSeuilAlerte(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return json_encode( ['success' => 'Produit ajouté au stock', 'stock_id' => $db->lastInsertId()]);
    }

    // Modifier un stock
    public function update($id, $data) {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            UPDATE stocks SET 
                quantite = :quantite,
                seuil_alerte = :seuil_alerte,
                updated_at = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            'quantite' => $data['quantite'],
            'seuil_alerte' => $data['seuil_alerte'],
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id
        ]);

        return json_encode( ['success' => 'Stock mis à jour']);
    }

    // Supprimer un stock
    public function destroy($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM stocks WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return json_encode( ['success' => 'Stock supprimé']);
    }

    // Historique des mouvements de stock
    public function movements() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM mouvements_stock ORDER BY created_at DESC");
        return json_encode( $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
