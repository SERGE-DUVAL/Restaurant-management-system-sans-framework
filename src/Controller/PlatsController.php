<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Plat;
use PDO;

class PlatsController {

    // Lister tous les plats
    public function index() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM plats");
        return json_encode( $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Ajouter un plat
    public function store($data) {
        $db = Database::getConnection();

        $plat = new Plat(
            null,
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['image'] ?? null,
            $data['categorie_id'],
            $data['disponible'] ?? true,
            date('Y-m-d H:i:s'),
            null
        );

        $stmt = $db->prepare("
            INSERT INTO plats(nom, description, prix, image, categorie_id, disponible, created_at)
            VALUES(:nom, :description, :prix, :image, :categorie_id, :disponible, :created_at)
        ");

        $stmt->execute([
            'nom' => $plat->getNom(),
            'description' => $plat->getDescription(),
            'prix' => $plat->getPrix(),
            'image' => $plat->getImage(),
            'categorie_id' => $plat->getCategorieId(),
            'disponible' => $plat->isDisponible(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return json_encode( ['success' => 'Plat ajouté', 'plat_id' => $db->lastInsertId()]);
    }

    // Modifier un plat
    public function update($id, $data) {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            UPDATE plats SET 
                nom = :nom,
                description = :description,
                prix = :prix,
                image = :image,
                categorie_id = :categorie_id,
                disponible = :disponible,
                updated_at = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            'nom' => $data['nom'],
            'description' => $data['description'],
            'prix' => $data['prix'],
            'image' => $data['image'] ?? null,
            'categorie_id' => $data['categorie_id'],
            'disponible' => $data['disponible'] ?? true,
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id
        ]);

        return json_encode( ['success' => 'Plat modifié']);
    }

    // Supprimer un plat
    public function destroy($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM plats WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return json_encode( ['success' => 'Plat supprimé']);
    }

    // Rechercher un plat par nom
    public function search($keyword) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM plats WHERE nom LIKE :keyword");
        $stmt->execute(['keyword' => "%$keyword%"]);
        return json_encode( $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
