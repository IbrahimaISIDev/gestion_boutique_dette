<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Dettes</title>
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
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-white text-2xl font-bold"><i class="fas fa-chart-line mr-2"></i>Suivi des Dettes</a>
                <div class="flex space-x-4">
                    <a href="#" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300"><i class="fas fa-home mr-2"></i>Accueil</a>
                    <a href="#" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300"><i class="fas fa-user mr-2"></i>Profil</a>
                    <a href="#" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mx-auto px-4 py-8">
        <?php if (isset($client) && $client instanceof \Src\App\Entity\ClientEntity) : ?>
            <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">
                <i class="fas fa-user-circle mr-2"></i>Suivi des Dettes de <?= htmlspecialchars($client->prenom) ?> <?= htmlspecialchars($client->nom) ?>
            </h1>

            <!-- Filter form -->
            <div class="mb-6 card p-4">
                <form action="/filtre" method="POST" class="flex flex-wrap items-center space-x-4">
                    <label class="text-gray-700 font-medium"><i class="fas fa-filter mr-2"></i>Filtrer par date :</label>
                    <input type="date" name="date" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="remboursee">Remboursée</option>
                        <option value="en_cours">En cours</option>
                    </select>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-300">
                        <i class="fas fa-search mr-2"></i>Appliquer
                    </button>
                </form>
            </div>

            <!-- Dettes table -->
            <div class="card overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Date de la Dette</th>
                            <th class="py-3 px-6 text-left">Montant Total</th>
                            <th class="py-3 px-6 text-left">Montant Versé</th>
                            <th class="py-3 px-6 text-left">Montant Restant</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        <?php foreach ($dettes as $dette) : ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100 transition duration-300">
                                <td class="py-3 px-6 text-left whitespace-nowrap"><?= htmlspecialchars($dette->getDateDette()) ?></td>
                                <td class="py-3 px-6 text-left"><?= number_format($dette->getMontantTotal(), 0, ',', ' ') ?> F CFA</td>
                                <td class="py-3 px-6 text-left"><?= number_format($dette->getMontantVerser(), 0, ',', ' ') ?> F CFA</td>
                                <td class="py-3 px-6 text-left"><?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> F CFA</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <form action="/details-dette" method="post" class="inline">
                                            <input type="hidden" name="idDette" value="<?= htmlspecialchars($dette->getId()) ?>">
                                            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded transition duration-300">
                                                <i class="fas fa-info-circle mr-2"></i>Détails
                                            </button>
                                        </form>
                                        <a href="/payer-dette?idDette=<?= htmlspecialchars($dette->getId()) ?>" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded transition duration-300">
                                            <i class="fas fa-money-bill-wave mr-2"></i>Payer
                                        </a>
                                        <form action="/liste-paiements/<?= htmlspecialchars($dette->getId()) ?>" method="get" class="inline">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition duration-300">
                                                <i class="fas fa-list-alt mr-2"></i>Paiements
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination controls -->
            <div class="flex justify-center items-center mt-6">
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++) : ?>
                    <form action="" method="get" class="inline">
                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client->id) ?>">
                        <button type="submit" name="page" value="<?= $i ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 mx-1 rounded transition duration-300 <?= $pagination['currentPage'] == $i ? 'bg-blue-500 text-white' : '' ?>">
                            <?= $i ?>
                        </button>
                    </form>
                <?php endfor; ?>
            </div>
        <?php else : ?>
            <p class="text-red-500 text-center text-xl"><i class="fas fa-exclamation-triangle mr-2"></i><?= $error ?? 'Une erreur est survenue' ?></p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-800 to-blue-600 text-white text-center py-4 mt-8">
        <p>&copy; <?= date('Y-m-d') ?> Suivi des Dettes. Tous droits réservés.</p>
    </footer>
</body>

</html>