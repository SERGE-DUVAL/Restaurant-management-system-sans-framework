<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\User;
use PDO;

class AuthController {

    // Connexion
    public function login($body) {
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($password, $userData['password'])) {
            $user = new User(
                $userData['id'],
                $userData['nom'],
                $userData['prenom'],
                $userData['email'],
                $userData['password'],
                $userData['role'],
                $userData['telephone'],
                $userData['created_at'],
                $userData['updated_at']
            );

            // Génération simple d'un token simulé (pour exemple)
            $token = bin2hex(random_bytes(16));

            // Stocker le token en session ou DB selon ton architecture
            $_SESSION['user'] = [
                'id' => $user->getId(),
                'role' => $user->getRole(),
                'token' => $token,
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'telephone' => $user->getTelephone()
                
            ];

            return json_encode(['success' => 'Connexion réussie', 'token' => $token, 'role' => $user->getRole()]);
        }

        return json_encode(['error' => 'Email ou mot de passe incorrect']);
    }

    // Déconnexion
    public function logout() {
        session_destroy();
        return json_encode(['success' => 'Déconnexion réussie']);
    }

    // Réinitialisation mot de passe (envoi d'un lien ou token)
    public function forgotPassword($body) {
        $email = $body['email'] ?? null;
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Générer un token temporaire pour réinitialisation
            $resetToken = bin2hex(random_bytes(16));
            $stmt = $db->prepare("UPDATE users SET reset_token = :token WHERE id = :id");
            $stmt->execute([
                'token' => $resetToken,
                'id' => $user['id']
            ]);

            // Ici, envoie du mail contenant le lien de réinitialisation
            // ex: https://tonapp.com/reset-password?token=$resetToken
            return json_encode(['success' => 'Lien de réinitialisation envoyé']);
        }

        return json_encode(['error' => 'Email non trouvé']);
    }

    // Reset mot de passe avec token
    public function resetPassword($body) {
        $token = $body['token'] ?? null;
        $newPassword = $body['newPassword'] ?? null;
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = :token");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password = :password, reset_token = NULL WHERE id = :id");
            $stmt->execute([
                'password' => $hashedPassword,
                'id' => $user['id']
            ]);

            return json_encode(['success' => 'Mot de passe réinitialisé']);
        }

        return json_encode(['error' => 'Token invalide ou expiré']);
    }
}
