<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la dette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Gestion des Dettes</h1>
            <a href="#" class="text-white hover:text-blue-200 transition duration-300">
                <i class="fas fa-home mr-2"></i>Accueil
            </a>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Détails de la dette
        </h1>
        <?php if (isset($_POST['idDette'])) : ?>
            <p class="mb-6 text-lg">ID de la dette: <span class="font-semibold text-blue-600"><?php echo $_POST['idDette']; ?></span></p>
            <?php if (isset($dette)) : ?>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="card p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">
                            <i class="fas fa-user mr-2"></i>Informations du client
                        </h2>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Nom:</span> <?php echo $client->nom; ?></p>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Prénom:</span> <?php echo $client->prenom; ?></p>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Email:</span> <?php echo $client->email; ?></p>
                        <p><span class="font-semibold text-gray-600">Téléphone:</span> <?php echo $client->telephone; ?></p>
                    </div>
                    <div class="card p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">
                            <i class="fas fa-money-bill-wave mr-2"></i>Informations de la dette
                        </h2>
                        <p class="mb-2"><span class="font-semibold text-gray-600">ID du client:</span> <?php echo $dette->client_id; ?></p>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Montant initial:</span> <?php echo number_format($dette->montant_initial, 0, ',', ' '); ?> F CFA</p>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Montant versé:</span> <?php echo number_format($dette->montant_verser, 0, ',', ' '); ?> F CFA</p>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Montant restant:</span> <?php echo number_format($dette->montant_restant, 0, ',', ' '); ?> F CFA</p>
                        <p class="mb-2"><span class="font-semibold text-gray-600">Date de création:</span> <?php echo date('d/m/Y', strtotime($dette->date_creation)); ?></p>
                        <p><span class="font-semibold text-gray-600">Statut:</span>
                            <span class="px-2 py-1 rounded <?php echo $dette->statut === 'Payé' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'; ?>">
                                <?php echo $dette->statut; ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="mt-8 card p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">
                        <i class="fas fa-shopping-cart mr-2"></i>Liste des articles de la dette
                    </h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($articles as $article) : ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-300">
                                <p class="mb-2"><span class="font-semibold text-gray-600">Article:</span> <?php echo $article->libelle; ?></p>
                                <p class="mb-2"><span class="font-semibold text-gray-600">Quantité:</span> <?php echo $article->quantite; ?></p>
                                <p class="mb-2"><span class="font-semibold text-gray-600">Prix unitaire:</span> <?php echo number_format($article->prix_unitaire, 0, ',', ' '); ?> F CFA</p>
                                <p><span class="font-semibold text-gray-600">Montant:</span> <?php echo number_format($article->montant, 0, ',', ' '); ?> F CFA</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                <p class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Erreur: Aucune dette trouvée pour l'ID <?php echo $_POST['idDette']; ?>
                </p>
            <?php endif; ?>
        <?php else : ?>
            <p class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i>Erreur: ID de dette manquant
            </p>
        <?php endif; ?>
    </div>
</body>

</html>