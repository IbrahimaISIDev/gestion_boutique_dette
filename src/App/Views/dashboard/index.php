<?php

$pdo = require __DIR__ . '/../../../../config/config.php';

use Src\Core\Validator;
use Src\App\Model\ClientModel;
use Src\Core\Database\MysqlDatabase;

$db = new MysqlDatabase($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['form_data'] = $_POST;
    $validator = new Validator();
    $rules = [
        'nom' => ['required' => true],
        'prenom' => ['required' => true],
        'email' => ['email' => true],
        'telephone' => ['required' => true],
        'photo' => ['file' => true]
    ];

    if ($validator->validate($_POST, $rules)) {
        $clientModel = new ClientModel($db);
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $photoUrl = '';

        if ($clientModel->telephoneExists($telephone)) {
            $errors['telephone'][] = "Ce numéro de téléphone est déjà utilisé.";
            error_log("Numéro de téléphone déjà utilisé : $telephone");
        } else {
            if ($clientModel->ajouterClient($nom, $prenom, $email, $telephone, $photoUrl)) {
                unset($_SESSION['form_data']);
                echo "Client ajouté avec succès !";
                error_log("Client ajouté avec succès : $telephone");
            } else {
                $errors['general'][] = "Erreur lors de l'ajout du client.";
                error_log("Erreur lors de l'ajout du client.");
            }
        }
    } else {
        $errors = $validator->getErrors();
        error_log("Erreurs de validation : " . json_encode($errors));
    }
}

$client = null;
$totauxDette = null;
$dettes = [];

if (isset($_POST['action']) && $_POST['action'] === 'searchClient') {
    $telephone = $_POST['telephone'] ?? null;

    if ($telephone) {
        $clientModel = new ClientModel($db);
        $client = $clientModel->getClientByTelephone($telephone);

        if ($client) {
            $dettes = $clientModel->getDetteByClientId($client['id']);
            $totalDette = 0;
            $totalVerse = 0;
            foreach ($dettes as $dette) {
                $totalDette += $dette['montant_initial'];
                $totalVerse += $dette['montant_verser'];
            }
            $totalRestant = $totalDette - $totalVerse;
            $totauxDette = [
                'montant_initial' => $totalDette,
                'montant_verser' => $totalVerse,
                'montant_restant' => $totalRestant,
            ];
        } else {
            $errors['telephone'][] = "Aucun client trouvé avec ce numéro de téléphone.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients et Dettes</title>
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

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="file"] {
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus,
        input[type="file"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-2xl font-bold">Gestion des Clients et Dettes</div>
            <div class="flex space-x-4">
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"><i class="fas fa-home mr-2"></i>Accueil</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"><i class="fas fa-users mr-2"></i>Clients</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"><i class="fas fa-file-invoice-dollar mr-2"></i>Dettes</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"><i class="fas fa-user-circle mr-2"></i>Profil</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Nouveau Client Section -->
            <div class="card p-6">
                <h2 class="text-2xl font-bold mb-6 text-blue-800"><i class="fas fa-user-plus mr-2"></i>Nouveau Client</h2>
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                    <div class="mb-4">
                        <label for="nom" class="block text-sm font-medium text-gray-700"><i class="fas fa-user mr-2"></i>Nom</label>
                        <input type="text" id="nom" name="nom" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" value="<?php echo isset($_SESSION['form_data']['nom']) ? htmlspecialchars($_SESSION['form_data']['nom']) : ''; ?>">
                        <?php if (isset($errors['nom'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['nom'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="prenom" class="block text-sm font-medium text-gray-700"><i class="fas fa-user mr-2"></i>Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" value="<?php echo isset($_SESSION['form_data']['prenom']) ? htmlspecialchars($_SESSION['form_data']['prenom']) : ''; ?>">
                        <?php if (isset($errors['prenom'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['prenom'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700"><i class="fas fa-envelope mr-2"></i>Email</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                        <?php if (isset($errors['email'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['email'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="telephone" class="block text-sm font-medium text-gray-700"><i class="fas fa-phone mr-2"></i>Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" value="<?php echo isset($_SESSION['form_data']['telephone']) ? htmlspecialchars($_SESSION['form_data']['telephone']) : ''; ?>">
                        <?php if (isset($errors['telephone'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['telephone'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="photo" class="block text-sm font-medium text-gray-700"><i class="fas fa-camera mr-2"></i>Photo</label>
                        <input type="file" id="photo" name="photo" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <img id="preview-img" src="" alt="Image Preview" class="mt-2 w-48 h-48 object-cover rounded-md">
                        <?php if (isset($errors['photo'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['photo'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="btn bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700"><i class="fas fa-save mr-2"></i>Enregistrer</button>
                    </div>
                </form>
            </div>

            <!-- Suivi Dette Section -->
            <div class="card p-6">
                <h2 class="text-2xl font-bold mb-6 text-blue-800"><i class="fas fa-search-dollar mr-2"></i>Suivi de Dette</h2>
                <form class="mb-4 flex gap-2" method="POST" action="/recherche">
                    <input type="hidden" name="action" value="searchClient">
                    <input type="text" name="telephone" placeholder="Entrez le numéro de téléphone du client" class="flex-grow p-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="btn bg-blue-500 text-white p-2 rounded-r-md hover:bg-blue-600"><i class="fas fa-search mr-2"></i>Rechercher</button>
                </form>

                <?php if (isset($client)) : ?>
                    <div class="flex justify-center mb-4">
                        <div class="w-32 h-32 bg-gray-300 rounded-md overflow-hidden">
                            <?php if (!empty($client['photo_url'])) : ?>
                                <img src="<?= htmlspecialchars($client['photo_url']) ?>" class="w-full h-full object-cover" alt="Photo du client">
                            <?php else : ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user-circle text-6xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="client-nom" class="block text-sm font-medium text-gray-700"><i class="fas fa-user mr-2"></i>Nom :</label>
                            <input type="text" id="client-nom" value="<?= htmlspecialchars($client['nom']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                        <div>
                            <label for="client-prenom" class="block text-sm font-medium text-gray-700"><i class="fas fa-user mr-2"></i>Prénom :</label>
                            <input type="text" id="client-prenom" value="<?= htmlspecialchars($client['prenom']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                        <div>
                            <label for="client-telephone" class="block text-sm font-medium text-gray-700"><i class="fas fa-phone mr-2"></i>Téléphone :</label>
                            <input type="text" id="client-telephone" value="<?= htmlspecialchars($client['telephone']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                    </div>

                    <div class="flex justify-center gap-4 mt-6">
                        <button class="btn bg-green-500 text-white p-3 rounded-md hover:bg-green-600" onclick="window.location.href='/suivi-dette?client_id=<?= $client['id'] ?>'"><i class="fas fa-chart-line mr-2"></i>Suivi Dette</button>
                        <button class="btn bg-blue-500 text-white p-3 rounded-md hover:bg-blue-600" onclick="window.location.href='/nouvelle-dette?client_id=<?= $client['id'] ?>'"><i class="fas fa-plus-circle mr-2"></i>Nouvelle Dette</button>
                    </div>

                    <?php if (isset($totauxDette)) : ?>
                        <h2 class="text-xl font-bold mt-8 mb-4 text-blue-800"><i class="fas fa-file-invoice-dollar mr-2"></i>Détails de la dette</h2>
                        <div class="space-y-4 bg-white p-4 rounded-lg shadow-md">
                            <div>
                                <label for="total-dette" class="block text-sm font-medium text-gray-700"><i class="fas fa-money-bill-wave mr-2"></i>Somme Totale Dette :</label>
                                <input type="text" id="montant_initial" value="<?= htmlspecialchars(number_format($totauxDette['montant_initial'], 2, ',', ' ')) ?> F CFA" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                            </div>
                            <div>
                                <label for="montant-versee" class="block text-sm font-medium text-gray-700"><i class="fas fa-hand-holding-usd mr-2"></i>Montant Versé :</label>
                                <input type="text" id="montant-verser" value="<?= htmlspecialchars(number_format($totauxDette['montant_verser'], 2, ',', ' ')) ?> F CFA" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                            </div>
                            <div>
                                <label for="montant-restant" class="block text-sm font-medium text-gray-700"><i class="fas fa-balance-scale mr-2"></i>Montant Restant :</label>
                                <input type="text" id="montant_restant" value="<?= htmlspecialchars(number_format($totauxDette['montant_restant'], 2, ',', ' ')) ?> F CFA" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                            </div>
                        </div>
                    <?php else : ?>
                        <p class="text-center text-gray-600 mt-4">Aucune dette trouvée pour ce client.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-800 to-blue-600 text-white text-center py-4 mt-8">
        <p>&copy; <?= date('Y-m-d') ?> Boutique Diallo. Tous droits réservés.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Preview uploaded image
        const photoInput = document.getElementById('photo');
        const previewImg = document.getElementById('preview-img');

        photoInput.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener('load', function() {
                    previewImg.setAttribute('src', this.result);
                });

                reader.readAsDataURL(file);
            } else {
                previewImg.setAttribute('src', '');
            }
        });
    </script>
</body>

</html>