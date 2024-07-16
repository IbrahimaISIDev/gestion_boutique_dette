<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Dettes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-blue-500 py-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <a href="/" class="text-white text-xl font-bold">Suivi des Dettes</a>
                <div>
                    <!-- Add your navbar links here -->
                    <a href="#" class="text-white mx-4">Accueil</a>
                    <a href="#" class="text-white mx-4">Profil</a>
                    <a href="#" class="text-white mx-4">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mx-auto px-4 py-8">
        <?php if (isset($client) && $client instanceof \Src\App\Entity\ClientEntity) : ?>
            <h1 class="text-3xl font-bold mb-6 text-center">Suivi des Dettes de <?= htmlspecialchars($client->prenom) ?> <?= htmlspecialchars($client->nom) ?></h1>
            <div class="mb-4 flex justify-between items-center">
                <form action="/filtre" method="POST" class="flex space-x-4">
                    <label class="text-gray-600">Filtrer par date :</label>
                    <input type="date" name="date" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="remboursee">Remboursée</option>
                        <option value="en_cours">En cours</option>
                    </select>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50">
                        Appliquer
                    </button>
                </form>
            </div>

            <div class="-mx-4 overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Date de la Dette</th>
                            <th class="py-3 px-6 text-left">Montant Total</th>
                            <th class="py-3 px-6 text-left">Montant Versé</th>
                            <th class="py-3 px-6 text-left">Montant Restant</th>
                            <th class="py-3 px-6 text-center">Détails</th>
                            <th class="py-3 px-6 text-center">Payer</th>
                            <th class="py-3 px-6 text-center">Liste Paiements</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php foreach ($dettes as $dette) : ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap"><?= htmlspecialchars($dette->getDateDette()) ?></td>
                                <td class="py-3 px-6 text-left"><?= number_format($dette->getMontantTotal(), 2) ?></td>
                                <td class="py-3 px-6 text-left"><?= number_format($dette->getMontantVerser(), 2) ?></td>
                                <td class="py-3 px-6 text-left"><?= number_format($dette->getMontantRestant(), 2) ?></td>
                                <td class="py-3 px-6 text-center">
                                    <form action="/details-dette" method="post" class="inline">
                                        <input type="hidden" name="idDette" value="<?= htmlspecialchars($dette->getId()) ?>">
                                        <button type="submit" class="bg-gray-500 text-white p-2 rounded-md">Détails</button>
                                    </form>

                                </td>
                                <td class="py-3 px-6 text-center">
                                    <form action="/payer-dette" method="post" class="inline">
                                        <input type="hidden" name="idDette" value="<?= htmlspecialchars($dette->getId()) ?>">
                                        <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">Payer</button>
                                    </form>

                                </td>
                                <td class="py-3 px-6 text-center">
                                    <form action="/liste-paiements/<?= $dette->getId() ?>" method="get" class="inline">
                                        <button type="submit" class="bg-green-500 text-white p-2 rounded-md">Paiements</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination controls -->
            <div class="flex justify-between items-center mt-4">
                <!-- Previous page button -->
                <form action="" method="get" class="inline">
                    <input type="hidden" name="client_id" value="<?= $client->getId() ?>">
                    <input type="hidden" name="date" value="<?= $_GET['date'] ?? '' ?>">
                    <input type="hidden" name="status" value="<?= $_GET['status'] ?? '' ?>">
                    <button type="submit" name="page" value="<?= max(1, $pagination['currentPage'] - 1) ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-md">Précédent</button>
                </form>

                <!-- Next page button -->
                <form action="" method="get" class="inline ml-auto">
                    <input type="hidden" name="client_id" value="<?= $client->getId() ?>">
                    <input type="hidden" name="date" value="<?= $_GET['date'] ?? '' ?>">
                    <input type="hidden" name="status" value="<?= $_GET['status'] ?? '' ?>">
                    <button type="submit" name="page" value="<?= min($pagination['totalPages'], $pagination['currentPage'] + 1) ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-md">Suivant</button>
                </form>
            </div>

        <?php else : ?>
            <p class="text-red-500"><?= $error ?? 'Une erreur est survenue' ?></p>
        <?php endif; ?>
    </div>
</body>

</html>