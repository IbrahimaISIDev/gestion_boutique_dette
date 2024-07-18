<!-- listePaiements.php -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Paiements</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
    <!-- Navbar (à réutiliser si nécessaire) -->
    <?php include 'navbar.php'; ?>

    <!-- Main content -->
    <div class="container mx-auto px-4 py-8">
        <?php if (isset($error)) : ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php else : ?>
            <h2>Liste des Paiements pour la Dette ID <?= $dette->id ?></h2>

            <p>Détails de la Dette :</p>
            <ul>
                <li>Dette ID: <?= $dette->id ?></li>
                <li>Montant Total: <?= number_format($dette->montant_total, 2) ?> F CFA</li>
                <li>Montant Restant: <?= number_format($dette->montant_restant, 2) ?> F CFA</li>
                <!-- Ajoutez d'autres détails de la dette selon vos besoins -->
            </ul>

            <h3>Liste des Paiements :</h3>

            <?php if (empty($paiements)) : ?>
                <p>Aucun paiement effectué pour cette dette.</p>
            <?php else : ?>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">ID Paiement</th>
                                <th class="py-3 px-6 text-left">Montant Versé</th>
                                <th class="py-3 px-6 text-left">Date de Paiement</th>
                                <!-- Ajoutez plus de colonnes si nécessaire -->
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            <?php foreach ($paiements as $paiement) : ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100 transition duration-300">
                                    <td class="py-3 px-6 text-left"><?= $paiement->id ?></td>
                                    <td class="py-3 px-6 text-left"><?= number_format($paiement->montant, 2) ?> F CFA</td>
                                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($paiement->date_paiement) ?></td>
                                    <!-- Ajoutez plus de cellules pour afficher d'autres détails du paiement -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>

</html>
