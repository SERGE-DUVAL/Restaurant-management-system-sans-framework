<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Paiement;
use PDO;

class PaiementController {

    // Enregistrer un paiement
    public function store($data) {
        $db = Database::getConnection();

        $paiement = new Paiement(
            null,
            $data['commande_id'],
            $data['mode_paiement'],
            $data['montant'],
            $data['statut'] ?? 'valide',
            date('Y-m-d H:i:s')
        );

        $stmt = $db->prepare("
            INSERT INTO paiements(commande_id, mode_paiement, montant, statut, created_at)
            VALUES(:commande_id, :mode_paiement, :montant, :statut, :created_at)
        ");

        $stmt->execute([
            'commande_id' => $paiement->getCommandeId(),
            'mode_paiement' => $paiement->getModePaiement(),
            'montant' => $paiement->getMontant(),
            'statut' => $paiement->getStatut(),
            'created_at' => $paiement->getCreatedAt()
        ]);

        return json_encode( ['success' => 'Paiement enregistré', 'paiement_id' => $db->lastInsertId()]);
    }

    // Afficher un paiement par commande
    public function show($commandeId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, commande_id, mode_paiement, montant, statut, created_at FROM paiements WHERE commande_id = :commande_id");
        $stmt->execute(['commande_id' => $commandeId]);
        $paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode( $paiements);
    }

    // Générer une facture (simple JSON)
    public function generateInvoice($commandeId) {
        $db = Database::getConnection();

        // Récupérer le paiement
        $stmt = $db->prepare("SELECT * FROM paiements WHERE commande_id = :commande_id");
        $stmt->execute(['commande_id' => $commandeId]);
        $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$paiement) {
            return json_encode( ['error' => 'Paiement introuvable']);
        }

        // Récupérer la commande et les plats associés
        $cmdStmt = $db->prepare("SELECT * FROM commandes WHERE id = :id");
        $cmdStmt->execute(['id' => $commandeId]);
        $commande = $cmdStmt->fetch(PDO::FETCH_ASSOC);

        $platsStmt = $db->prepare("
            SELECT p.nom, cp.quantite, cp.prix_unitaire
            FROM commande_plat cp
            JOIN plats p ON cp.plat_id = p.id
            WHERE cp.commande_id = :commande_id
        ");
        $platsStmt->execute(['commande_id' => $commandeId]);
        $plats = $platsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Générer la facture
        $invoice = [
            'commande' => $commande,
            'paiement' => $paiement,
            'plats' => $plats
        ];

        return json_encode( $invoice);
    }
}
