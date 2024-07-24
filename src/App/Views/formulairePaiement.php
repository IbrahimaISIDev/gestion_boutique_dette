<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payer une Dette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen font-sans">
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-4xl font-bold mb-8 text-center text-indigo-800">
            <i class="fas fa-money-bill-wave mr-2"></i>Payer une Dette
        </h1>

        <?php if (isset($error)) : ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Erreur</p>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($dette)) : ?>
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-indigo-600 text-white py-4 px-6">
                    <h2 class="text-2xl font-semibold">Détails de la dette</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-600">Montant initial:</span>
                        <span class="font-semibold text-indigo-600"><?php echo number_format($dette->montant_initial, 0, ',', ' '); ?> F CFA</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-600">Montant versé:</span>
                        <span class="font-semibold text-green-600"><?php echo number_format($dette->montant_verser, 0, ',', ' '); ?> F CFA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Montant restant:</span>
                        <span class="font-semibold text-red-600"><?php echo number_format($dette->montant_restant, 0, ',', ' '); ?> F CFA</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-indigo-600 text-white py-4 px-6">
                    <h2 class="text-2xl font-semibold">Effectuer un paiement</h2>
                </div>
                <div class="p-6">
                    <form action="/payer-dette" method="post">
                        <input type="hidden" name="idDette" value="<?= htmlspecialchars($dette->getId()) ?>">
                        <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($dette->client_id); ?>">

                        <div class="mb-6">
                            <label for="montant_verser" class="block text-sm font-medium text-gray-700 mb-2">Montant à verser</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">F CFA</span>
                                </div>
                                <input type="number" id="montant_verser" name="montant_verser" step="1" min="0" required class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-16 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition duration-300 flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>Effectuer le paiement
                        </button>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Information</p>
                <p>Aucune dette trouvée pour cet ID.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>