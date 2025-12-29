<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Plat;
use src\Model\Commande;
use PDO;

class OnlineOrderController {

    // Afficher le menu pour les clients
    public function menu() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, nom, description, prix, image, categorie_id, disponible FROM plats WHERE disponible = 1");
        $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($menu);
    }

    // Ajouter un plat au panier (session ou table temporaire)
    public function addToCart($data) {
        session_start();
        $platId = $data['plat_id'];
        $quantite = $data['quantite'] ?? 1;

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$platId])) {
            $_SESSION['cart'][$platId] += $quantite;
        } else {
            $_SESSION['cart'][$platId] = $quantite;
        }

        echo json_encode(['success' => 'Plat ajouté au panier', 'cart' => $_SESSION['cart']]);
    }

    // Valider la commande et l'enregistrer en base
    public function checkout($data) {
        session_start();
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo json_encode(['error' => 'Panier vide']);
            return;
        }

        $db = Database::getConnection();

        // Créer la commande
        $commande = new Commande(
            null,
            $data['client_id'],
            $data['user_id'] ?? null, // si un employé enregistre la commande
            0,
            'en_attente',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );

        $stmt = $db->prepare("
            INSERT INTO commandes(client_id, user_id, total, statut, created_at, updated_at)
            VALUES (:client_id, :user_id, :total, :statut, :created_at, :updated_at)
        ");
        $stmt->execute([
            'client_id' => $commande->getClientId(),
            'user_id' => $commande->getUserId(),
            'total' => $commande->getTotal(),
            'statut' => $commande->getStatut(),
            'created_at' => $commande->getCreatedAt(),
            'updated_at' => $commande->getUpdatedAt()
        ]);

        $commandeId = $db->lastInsertId();
        $total = 0;

        // Ajouter les plats dans commande_plat
        foreach ($_SESSION['cart'] as $platId => $quantite) {
            $platStmt = $db->prepare("SELECT prix FROM plats WHERE id = :id");
            $platStmt->execute(['id' => $platId]);
            $plat = $platStmt->fetch(PDO::FETCH_ASSOC);

            $prixUnitaire = $plat['prix'];
            $total += $prixUnitaire * $quantite;

            $insertCmdPlat = $db->prepare("
                INSERT INTO commande_plat(commande_id, plat_id, quantite, prix_unitaire)
                VALUES (:commande_id, :plat_id, :quantite, :prix_unitaire)
            ");
            $insertCmdPlat->execute([
                'commande_id' => $commandeId,
                'plat_id' => $platId,
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire
            ]);
        }

        // Mettre à jour le total de la commande
        $updateTotal = $db->prepare("UPDATE commandes SET total = :total WHERE id = :id");
        $updateTotal->execute([
            'total' => $total,
            'id' => $commandeId
        ]);

        // Vider le panier
        unset($_SESSION['cart']);

        echo json_encode(['success' => 'Commande passée avec succès', 'commande_id' => $commandeId, 'total' => $total]);
    }

    // Vérifier le statut d'une commande
    public function status($orderId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, client_id, total, statut, created_at, updated_at FROM commandes WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($commande) {
            echo json_encode($commande);
        } else {
            echo json_encode(['error' => 'Commande introuvable']);
        }
    }
}
