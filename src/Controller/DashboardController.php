<?php
namespace src\Controller;

use src\Config\Database;
use PDO;

class DashboardController {

    // Récupère les infos du dashboard
    public function index() {
        $db = Database::getConnection();

        // Total des ventes du jour
        $stmt = $db->prepare("SELECT SUM(total) as total_ventes_journalieres 
                              FROM commandes 
                              WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $totalVentesJour = $stmt->fetch(PDO::FETCH_ASSOC)['total_ventes_journalieres'] ?? 0;

        // Total des ventes du mois
        $stmt = $db->prepare("SELECT SUM(total) as total_ventes_mensuelles 
                              FROM commandes 
                              WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $totalVentesMois = $stmt->fetch(PDO::FETCH_ASSOC)['total_ventes_mensuelles'] ?? 0;

        // Nombre de commandes
        $stmt = $db->query("SELECT COUNT(*) as nombre_commandes FROM commandes");
        $nombreCommandes = $stmt->fetch(PDO::FETCH_ASSOC)['nombre_commandes'] ?? 0;

        // Plats les plus vendus
        $stmt = $db->query("
            SELECT p.nom, SUM(cp.quantite) as total_vendu
            FROM plats p
            INNER JOIN commande_plat cp ON p.id = cp.plat_id
            GROUP BY p.id
            ORDER BY total_vendu DESC
            LIMIT 5
        ");
        $platsPopulaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'total_ventes_journalieres' => $totalVentesJour,
            'total_ventes_mensuelles' => $totalVentesMois,
            'nombre_commandes' => $nombreCommandes,
            'plats_les_plus_vendus' => $platsPopulaires
        ]);
    }

    // Exemple : données pour graphique des ventes par jour du mois
    public function salesChart() {
        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT DATE(created_at) as date, SUM(total) as total_ventes
            FROM commandes
            WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)
        ");

        $chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($chartData);
    }
}
