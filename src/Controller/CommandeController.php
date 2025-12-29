<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Commande;
use PDO;

class CommandeController {

    // Lister toutes les commandes
    public function index() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, client_id, user_id, total, statut, created_at, updated_at FROM commandes");
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($commandes);
    }

    // Ajouter une commande
    public function store($data) {
        $db = Database::getConnection();

        $commande = new Commande(
            null,
            $data['client_id'],
            $data['user_id'],
            $data['total'] ?? 0,
            $data['statut'] ?? 'en_attente',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );

        $stmt = $db->prepare("
            INSERT INTO commandes (client_id, user_id, total, statut, created_at, updated_at)
            VALUES (:client_id, :user_id, :total, :statut, :created_at, :updated_at)
        ");

        $stmt->execute([
            'client_id' => $commande->getClientId(),
            'user_id' => $commande->getUserId(),
            'total' => $commande->getTotal(),
            'statut' => $commande->getStatut(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $commandeId = $db->lastInsertId();
        echo json_encode(['success' => 'Commande créée', 'commande_id' => $commandeId]);
    }

    // Modifier une commande
    public function update($id, $data) {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            UPDATE commandes SET client_id=:client_id, user_id=:user_id, total=:total, statut=:statut, updated_at=:updated_at
            WHERE id=:id
        ");

        $stmt->execute([
            'id' => $id,
            'client_id' => $data['client_id'],
            'user_id' => $data['user_id'],
            'total' => $data['total'] ?? 0,
            'statut' => $data['statut'] ?? 'en_attente',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => 'Commande mise à jour']);
    }

    // Supprimer une commande
    public function destroy($id) {
        $db = Database::getConnection();

        // Supprimer les plats liés à cette commande
        $stmt = $db->prepare("DELETE FROM commande_plat WHERE commande_id = :id");
        $stmt->execute(['id' => $id]);

        // Supprimer la commande
        $stmt = $db->prepare("DELETE FROM commandes WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => 'Commande supprimée']);
    }

    // Ajouter un plat à la commande
    public function addDish($commandeId, $data) {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            INSERT INTO commande_plat (commande_id, plat_id, quantite, prix_unitaire)
            VALUES (:commande_id, :plat_id, :quantite, :prix_unitaire)
        ");

        $stmt->execute([
            'commande_id' => $commandeId,
            'plat_id' => $data['plat_id'],
            'quantite' => $data['quantite'] ?? 1,
            'prix_unitaire' => $data['prix_unitaire']
        ]);

        echo json_encode(['success' => 'Plat ajouté à la commande']);
    }

    // Supprimer un plat de la commande
    public function removeDish($commandeId, $data) {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            DELETE FROM commande_plat WHERE commande_id=:commande_id AND plat_id=:plat_id
        ");

        $stmt->execute([
            'commande_id' => $commandeId,
            'plat_id' => $data['plat_id']
        ]);

        echo json_encode(['success' => 'Plat retiré de la commande']);
    }

    // Historique des commandes
    public function history() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT c.id, c.client_id, c.user_id, c.total, c.statut, c.created_at, c.updated_at,
                   GROUP_CONCAT(cp.plat_id, ':', cp.quantite) as plats
            FROM commandes c
            LEFT JOIN commande_plat cp ON c.id = cp.commande_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");

        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($commandes);
    }
}
