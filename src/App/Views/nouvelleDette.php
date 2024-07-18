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
                                <option value="<?php echo htmlspecialchars($article->id); ?>" <?php if ($selected_article_id == $article->id) echo 'selected'; ?>><?php echo htmlspecialchars($article->libelle); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="prix_unitaire" class="block text-sm font-medium text-gray-700 mb-2">Prix unitaire</label>
                        <input type="text" id="prix_unitaire" name="prix_unitaire" readonly class="w-full px-3 py-2 text-gray-700 border rounded-lg bg-gray-100" value="<?php echo htmlspecialchars($prix_unitaire); ?>">
                    </div>

                    <div class="mb-4">
                        <label for="quantite" class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                        <input type="number" id="quantite" name="quantite" required class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <button type="submit" name="ajouter_panier" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Ajouter</button>
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
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['montant']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p class="text-gray-600">Aucun article ajouté au panier.</p>
                <?php endif; ?>
                <form action="valider_panier.php" method="post">
                    <button type="submit" name="valider_panier" class="mt-4 w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 transition duration-300">Valider</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>