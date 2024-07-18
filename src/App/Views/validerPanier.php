<?php

session_start();
require __DIR__ . '/../../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_panier'])) {
    $panier = $_SESSION['panier'] ?? [];

    if (!empty($panier)) {
        try {
            // Connexion à la base de données
            $pdo = new PDO($dsn, $username, $password, $options);

            // Démarrer la transaction
            $pdo->beginTransaction();

            // Insertion de la dette
            $stmt = $pdo->prepare("INSERT INTO dettes (client_id, date_dette) VALUES (:client_id, NOW())");
            $stmt->execute(['client_id' => $client['id']]);
            $detteId = $pdo->lastInsertId();

            // Insertion des articles dans la table de relation dette_articles
            $stmt = $pdo->prepare("INSERT INTO dette_articles (dette_id, article_id, quantite, prix_unitaire) VALUES (:dette_id, :article_id, :quantite, :prix_unitaire)");

            foreach ($panier as $item) {
                $stmt->execute([
                    'dette_id' => $detteId,
                    'article_id' => $item['id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire']
                ]);
            }

            // Commit de la transaction
            $pdo->commit();

            // Détruire la session et réinitialiser le panier
            unset($_SESSION['panier']);

            // Afficher un message de succès
            $_SESSION['message'] = 'La dette a été validée avec succès.';

            // Redirection pour rester sur la même page
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } catch (Exception $e) {
            // Rollback en cas d'erreur
            $pdo->rollBack();
            $_SESSION['message'] = 'Erreur lors de la validation de la dette : ' . $e->getMessage();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    } else {
        $_SESSION['message'] = 'Votre panier est vide.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    $_SESSION['message'] = 'Requête invalide.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
