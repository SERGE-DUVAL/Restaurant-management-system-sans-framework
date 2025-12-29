<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Categorie;
use PDO;

class CategorieController {

    // Lister toutes les catégories
    public function index() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, nom, description FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
    }

    // Ajouter une catégorie
    public function store($data) {
        if (!isset($data['nom'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nom requis']);
            return;
        }

        $db = Database::getConnection();
        $category = new Categorie(
            null,
            $data['nom'],
            $data['description'] ?? '',
            null,
            null
        );

        $stmt = $db->prepare("INSERT INTO categories (nom, description) VALUES (:nom, :description)");
        $stmt->execute([
            'nom' => $category->getNom(),
            'description' => $category->getDescription()
        ]);

        echo json_encode(['success' => 'Catégorie ajoutée']);
    }

    // Modifier une catégorie
    public function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE categories SET nom = :nom, description = :description WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'] ?? '',
            'description' => $data['description'] ?? ''
        ]);

        echo json_encode(['success' => 'Catégorie mise à jour']);
    }

    // Supprimer une catégorie
    public function destroy($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => 'Catégorie supprimée']);
    }

    // Rechercher une catégorie par nom
    public function search($queryParams) {
        $db = Database::getConnection();
        $term = $queryParams['q'] ?? '';

        $stmt = $db->prepare("SELECT id, nom, description FROM categories WHERE nom LIKE :term");
        $stmt->execute([
            'term' => "%$term%"
        ]);

        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
    }
}
