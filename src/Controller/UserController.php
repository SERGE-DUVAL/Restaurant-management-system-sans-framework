<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\User;
use PDO;

class UserController {

    // Lister tous les utilisateurs
    public function index() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, nom, prenom, email, role, telephone, created_at, updated_at FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }

    // Ajouter un nouvel utilisateur
    public function store($data) {
        $db = Database::getConnection();

        // Vérifier si l'email existe déjà
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Email déjà utilisé']);
            return;
        }

        $user = new User(
            null,
            $data['nom'],
            $data['prenom'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
            $data['telephone'],
            null,
            null
        );

        $stmt = $db->prepare("
            INSERT INTO users (nom, prenom, email, password, role, telephone)
            VALUES (:nom, :prenom, :email, :password, :role, :telephone)
        ");

        $stmt->execute([
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'password' => $user->getPasswordHash(),
            'role' => $user->getRole(),
            'telephone' => $user->getTelephone()
        ]);

        echo json_encode(['success' => 'Utilisateur créé']);
    }

    // Mettre à jour un utilisateur
    public function update($id, $data) {
        $db = Database::getConnection();

        $stmt = $db->prepare("UPDATE users SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone, updated_at=NOW() WHERE id=:id");
        $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'id' => $id
        ]);

        echo json_encode(['success' => 'Utilisateur mis à jour']);
    }

    // Supprimer un utilisateur
    public function destroy($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id=:id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['success' => 'Utilisateur supprimé']);
    }

    // Rechercher des utilisateurs par nom, prenom ou email
    public function search($params) {
        $db = Database::getConnection();
        $query = "SELECT id, nom, prenom, email, role, telephone FROM users WHERE 1=1";
        $bindings = [];

        if (!empty($params['nom'])) {
            $query .= " AND nom LIKE :nom";
            $bindings['nom'] = "%{$params['nom']}%";
        }
        if (!empty($params['prenom'])) {
            $query .= " AND prenom LIKE :prenom";
            $bindings['prenom'] = "%{$params['prenom']}%";
        }
        if (!empty($params['email'])) {
            $query .= " AND email LIKE :email";
            $bindings['email'] = "%{$params['email']}%";
        }

        $stmt = $db->prepare($query);
        $stmt->execute($bindings);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($users);
    }

    // Mettre à jour le rôle d’un utilisateur
    public function updateRole($id, $data) {
        if (empty($data['role'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Role manquant']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET role=:role, updated_at=NOW() WHERE id=:id");
        $stmt->execute([
            'role' => $data['role'],
            'id' => $id
        ]);

        echo json_encode(['success' => 'Rôle mis à jour']);
    }
}
