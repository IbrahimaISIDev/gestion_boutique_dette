<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payer une Dette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Payer une Dette</h1>

        <?php if (isset($error)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($dette)) : ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Détails de la dette</h2>
                    <p><span class="font-medium">Montant initial:</span> <?php echo htmlspecialchars($dette->montant_initial); ?> F CFA</p>
                    <p><span class="font-medium">Montant versé:</span> <?php echo htmlspecialchars($dette->montant_verser); ?> F CFA</p>
                    <p><span class="font-medium">Montant restant:</span> <?php echo htmlspecialchars($dette->montant_restant); ?> F CFA</p>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Effectuer un paiement</h2>
                    <form action="/payer-dette" method="post">
                        <input type="hidden" name="idDette" value="<?= htmlspecialchars($dette->getId()) ?>">
                        <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($dette->client_id); ?>">

                        <div class="mb-4">
                            <label for="montant_verser" class="block text-sm font-medium text-gray-700 mb-2">Montant à verser</label>
                            <input type="number" id="montant_verser" name="montant_verser" step="1" min="0" required class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>

                        <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 transition duration-300">Payer</button>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <p class="text-gray-600">Aucune dette trouvée pour cet ID.</p>
        <?php endif; ?>
    </div>
</body>

</html>