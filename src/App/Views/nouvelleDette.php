<?php
session_start();

// Initialisez le panier si ce n'est pas déjà fait
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Incluez votre connexion à la base de données ici
require __DIR__ . '/../../../config/config.php';

// Récupérez les articles disponibles (vous devez déjà avoir cette partie dans votre configuration)
$articles = []; // À remplacer par votre propre méthode pour récupérer les articles depuis la base de données

// Traitez le formulaire lorsque l'utilisateur ajoute un article au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    $articleId = $_POST['article_id'];
    $quantite = $_POST['quantite'];

    // Recherchez l'article correspondant (vous devez déjà avoir $articles disponible)
    $article = null;
    foreach ($articles as $art) {
        if ($art->id == $articleId) {
            $article = $art;
            break;
        }
    }

    if ($article) {
        $libelle = $article->libelle;
        $prixUnitaire = $article->prix_unitaire;
        $montant = $prixUnitaire * $quantite;

        // Ajoutez l'article au panier
        $_SESSION['panier'][] = [
            'id' => $articleId,
            'libelle' => $libelle,
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'montant' => $montant
        ];
    }
}

// Traitez le formulaire lorsque l'utilisateur valide le panier (enregistre la dette)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validerPanier'])) {
    try {
        // Démarrez une transaction
        $pdo->beginTransaction();

        // Récupérez l'ID du client à partir de la session
        $client_id = $_SESSION['client_id'] ?? null;

        // Vérifiez que l'ID du client est disponible
        if (!$client_id) {
            throw new Exception('ID du client non disponible.');
        }

        // Date de la dette
        $date_dette = date('Y-m-d');

        // Montant initial et autres valeurs initiales
        $montant_initial = 0;
        $montant_restant = 0;
        $montant_verser = 0;

        // Insérez la dette
        $stmt = $pdo->prepare("INSERT INTO dettes (client_id, date_creation, montant_initial, montant_restant, montant_verser) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$client_id, $date_dette, $montant_initial, $montant_restant, $montant_verser]);
        $dette_id = $pdo->lastInsertId();

        // Insérez les articles de la dette
        foreach ($_SESSION['panier'] as $item) {
            $article_id = $item['id'];
            $quantite = $item['quantite'];
            $prix_unitaire = $item['prix_unitaire'];
            $montant = $item['montant'];

            // Insérez chaque article de la dette dans la table correspondante
            $stmt = $pdo->prepare("INSERT INTO details_dette (dette_id, article_id, quantite, prix_unitaire, montant) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$dette_id, $article_id, $quantite, $prix_unitaire, $montant]);

            // Calculer le montant total de la dette
            $montant_total += $montant;
        }

        // Commit de la transaction si tout s'est bien passé
        $pdo->commit();

        // Réinitialisez le panier après avoir enregistré la dette avec succès
        $_SESSION['panier'] = [];

        // Message de succès à afficher
        $_SESSION['message'] = 'La dette a été enregistrée avec succès !';
    } catch (PDOException $e) {
        // En cas d'erreur, rollback de la transaction
        $pdo->rollBack();

        // Message d'erreur à afficher (à des fins de débogage)
        $_SESSION['message'] = 'Erreur lors de l\'enregistrement de la dette : ' . $e->getMessage();
    } catch (Exception $e) {
        // Autre erreur logique (par exemple, ID du client non disponible)
        $_SESSION['message'] = 'Erreur lors de l\'enregistrement de la dette : ' . $e->getMessage();
    }

    // Redirection vers la même page pour afficher le message de succès ou d'erreur
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérez les articles du panier
$panier = $_SESSION['panier'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Dette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Nouvelle Dette</h1>

        <?php if (isset($_SESSION['message'])) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['message']); ?></span>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Informations du client</h2>
                <?php if (!empty($client) && is_array($client)) : ?>
                    <div class="grid grid-cols-3 gap-4">
                        <p><span class="font-medium">Nom:</span> <?php echo htmlspecialchars($client['nom'] ?? ''); ?></p>
                        <p><span class="font-medium">Prénom:</span> <?php echo htmlspecialchars($client['prenom'] ?? ''); ?></p>
                        <p><span class="font-medium">Téléphone:</span> <?php echo htmlspecialchars($client['telephone'] ?? ''); ?></p>
                    </div>
                <?php else : ?>
                    <p class="text-gray-600">Aucune information sur le client disponible.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Sélectionner un article</h2>
                <form action="/ajouter" method="post">
                    <div class="mb-4">
                        <label for="articleSelect" class="block text-sm font-medium text-gray-700 mb-2">Article</label>
                        <select id="articleSelect" name="article_id" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500">
                            <?php foreach ($articles as $article) : ?>
                                <option value="<?php echo htmlspecialchars($article->id); ?>"><?php echo htmlspecialchars($article->libelle); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="quantite" class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                        <input type="number" id="quantite" name="quantite" required class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <button type="submit" name="ajouter_panier" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Ajouter au Panier</button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Panier</h2>
                <?php if (!empty($panier)) : ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($panier as $item) : ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['libelle']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['quantite']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars(number_format($item['montant'], 2)); ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p class="text-gray-600">Le panier est vide.</p>
                <?php endif; ?>

                <form action="/ajouter" method="post" class="mt-6">
                    <button type="submit" name="validerPanier" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Valider la dette</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
