<?php
// Générer la facture et obtenir le nom du fichier
$factureFileName = $this->genererFacture($dette, $montantVerse);
?>

<!-- Structure HTML avec Tailwind CSS pour l'affichage de la facture -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Facture</h1>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Détails de la Facture</h2>
                <p><span class="font-medium">ID Facture:</span> <?php echo htmlspecialchars($factureId); ?></p>
                <p><span class="font-medium">Date:</span> <?php echo htmlspecialchars($date); ?></p>
                <p><span class="font-medium">Montant Versé:</span> <?php echo htmlspecialchars($montantVerse); ?> F CFA</p>
                <p><span class="font-medium">Montant Restant:</span> <?php echo htmlspecialchars($dette->montant_restant); ?> F CFA</p>
                <p><span class="font-medium">Client:</span> <?php echo htmlspecialchars("{$client->nom} {$client->prenom}"); ?></p>
            </div>
        </div>

        <div class="flex justify-center">
            <!-- Lien pour télécharger la facture -->
            <a href="/factures/<?php echo htmlspecialchars($factureFileName); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">Télécharger la facture</a>
        </div>
    </div>
</body>
</html>
