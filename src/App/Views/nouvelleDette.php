<?php
session_start();

// Initialisez le panier si ce n'est pas déjà fait
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Incluez votre connexion à la base de données ici
require __DIR__ . '/../../../config/config.php';

// Récupérer les articles depuis la base de données (ajoutez votre propre logique ici)
$stmt = $pdo->query("SELECT * FROM articles");
$articles = $stmt->fetchAll(PDO::FETCH_OBJ);

// Traitez le formulaire lorsque l'utilisateur ajoute un article au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    $articleId = $_POST['article_id'];
    $quantite = $_POST['quantite'];

    // Recherchez l'article correspondant
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_panier'])) {
    try {
        // Démarrez une transaction
        $pdo->beginTransaction();

        // Exemple : Insérer la dette avec toutes les valeurs nécessaires
        $client_id = 1; // Remplacez par l'id du client concerné
        $date_dette = date('Y-m-d'); // Date de la dette
        $montant_initial = 0; // Exemple de valeur initiale, ajustez selon votre logique
        $montant_verser = 0; // Exemple de valeur initiale pour montant_verser

        // Calculer le montant total de la dette
        $montant_total = 0;
        foreach ($_SESSION['panier'] as $item) {
            $montant_total += $item['montant'];
        }

        // Montant restant est le même que le montant initial à ce stade
        $montant_restant = $montant_total;

        // Insérer la dette dans la table dettes
        $stmt = $pdo->prepare("INSERT INTO dettes (client_id, date_creation, montant_initial, montant_restant, montant_verser) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$client_id, $date_dette, $montant_total, $montant_restant, $montant_verser]);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen font-sans">
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-4xl font-bold mb-8 text-center text-indigo-800">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Ajouter une nouvelle dette
        </h1>

        <!-- Affichage des messages de succès ou d'erreur -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="<?php echo strpos($_SESSION['message'], 'Erreur') === false ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700'; ?> p-4 mb-6" role="alert">
                <p class="font-bold"><?php echo strpos($_SESSION['message'], 'Erreur') === false ? 'Succès' : 'Erreur'; ?></p>
                <p><?php echo htmlspecialchars($_SESSION['message']);
                    unset($_SESSION['message']); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-indigo-600 text-white py-4 px-6">
                    <h2 class="text-2xl font-semibold"><i class="fas fa-user mr-2"></i>Informations du client</h2>
                </div>
                <div class="p-6">
                    <?php if (!empty($client) && is_array($client)) : ?>
                        <div class="space-y-3">
                            <p><span class="font-medium text-gray-600">Nom:</span> <?php echo htmlspecialchars($client['nom'] ?? ''); ?></p>
                            <p><span class="font-medium text-gray-600">Prénom:</span> <?php echo htmlspecialchars($client['prenom'] ?? ''); ?></p>
                            <p><span class="font-medium text-gray-600">Téléphone:</span> <?php echo htmlspecialchars($client['telephone'] ?? ''); ?></p>
                        </div>
                    <?php else : ?>
                        <p class="text-gray-600">Aucune information sur le client disponible.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-indigo-600 text-white py-4 px-6">
                    <h2 class="text-2xl font-semibold"><i class="fas fa-shopping-cart mr-2"></i>Ajouter un article</h2>
                </div>
                <div class="p-6">
                    <form action="/ajouter" method="post">
                        <div class="mb-4">
                            <label for="articleSelect" class="block text-sm font-medium text-gray-700 mb-2">Article</label>
                            <select id="articleSelect" name="article_id" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <?php foreach ($articles as $article) : ?>
                                    <option value="<?php echo htmlspecialchars($article->id); ?>"><?php echo htmlspecialchars($article->libelle); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="quantite" class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                            <input type="number" id="quantite" name="quantite" required class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <button type="submit" name="ajouter_panier" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                            <i class="fas fa-plus-circle mr-2"></i>Ajouter au panier
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-indigo-600 text-white py-4 px-6">
                <h2 class="text-2xl font-semibold"><i class="fas fa-shopping-basket mr-2"></i>Panier</h2>
            </div>
            <div class="p-6">
                <?php if (!empty($panier)) : ?>
                    <div class="overflow-x-auto">
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
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo number_format($item['montant'], 0, ',', ' ') . ' F CFA'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <form action="/ajouter" method="post" class="mt-6">
                        <button type="submit" name="valider_panier" class="w-full bg-green-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-600 transition duration-300">
                            <i class="fas fa-check-circle mr-2"></i>Valider le panier
                        </button>
                    </form>
                <?php else : ?>
                    <p class="text-gray-600 text-center py-4">Aucun article ajouté au panier.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>