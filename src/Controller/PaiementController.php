<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Paiement;
use PDO;
use Dompdf\Dompdf;
use Dompdf\Options;

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
    public function generateInvoice($commandeId)
{
    $db = Database::getConnection();

    // Paiement
    $stmt = $db->prepare("SELECT * FROM paiements WHERE commande_id = :id");
    $stmt->execute(['id' => $commandeId]);
    $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paiement) {
        http_response_code(404);
        echo json_encode(['message' => 'Paiement introuvable']);
        return;
    }

    // Commande
    $cmdStmt = $db->prepare("SELECT * FROM commandes WHERE id = :id");
    $cmdStmt->execute(['id' => $commandeId]);
    $commande = $cmdStmt->fetch(PDO::FETCH_ASSOC);

    // Plats
    $platsStmt = $db->prepare("
        SELECT p.nom, cp.quantite, cp.prix_unitaire
        FROM commande_plat cp
        JOIN plats p ON cp.plat_id = p.id
        WHERE cp.commande_id = :id
    ");
    $platsStmt->execute(['id' => $commandeId]);
    $plats = $platsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul total
    $total = 0;
    foreach ($plats as $p) {
        $total += $p['quantite'] * $p['prix_unitaire'];
    }

    // HTML facture
    $html = "
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        .total { font-weight: bold; }
    </style>

    <h1>FACTURE</h1>

    <p>
        <strong>Commande #:</strong> {$commande['id']}<br>
        <strong>Date:</strong> {$commande['created_at']}<br>
        <strong>Moyen de paiement:</strong> {$paiement['mode_paiement']}
    </p>

    <table>
        <thead>
            <tr>
                <th>Plat</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>";

    foreach ($plats as $plat) {
        $ligneTotal = $plat['quantite'] * $plat['prix_unitaire'];
        $html .= "
            <tr>
                <td>{$plat['nom']}</td>
                <td>{$plat['quantite']}</td>
                <td>{$plat['prix_unitaire']} FCFA</td>
                <td>{$ligneTotal} FCFA</td>
            </tr>";
    }

    $html .= "
        <tr class='total'>
            <td colspan='3'>TOTAL À PAYER</td>
            <td>{$total} FCFA</td>
        </tr>
        </tbody>
    </table>";

    // Dompdf
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Afficher dans le navigateur (imprimable)
    $dompdf->stream("facture_commande_{$commandeId}.pdf", [
        "Attachment" => false // true = téléchargement
    ]);
}
}
