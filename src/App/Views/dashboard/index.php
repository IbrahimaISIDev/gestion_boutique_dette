<?php

// Inclusion de vos dépendances et initialisations nécessaires
namespace Src\Core;

use Src\Core\Validator;
use Src\App\Model\ClientModel;
use Src\Core\Database\MysqlDatabase;


// Instanciation de MysqlDatabase
$db = new MysqlDatabase();
$errors = [];

// Vérifie si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['form_data'] = $_POST;
    // Validation des données avec la classe Validator
    $validator = new Validator();
    $rules = [
        'nom' => ['required' => true],
        'prenom' => ['required' => true],
        'email' => ['email' => true],
        'telephone' => ['required' => true],
        'photo' => ['file' => true] // Ajoutez une règle appropriée pour la photo
    ];

    if ($validator->validate($_POST, $rules)) {
        // Instanciation de ClientModel en lui fournissant l'instance de MysqlDatabase
        $clientModel = new ClientModel($db);
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $photoUrl = ''; // Ajoutez votre logique pour gérer les fichiers uploadés

        // Vérifier si le numéro de téléphone existe déjà dans la base de données
        if ($clientModel->telephoneExists($telephone)) {
            $errors['telephone'][] = "Ce numéro de téléphone est déjà utilisé.";
            error_log("Numéro de téléphone déjà utilisé : $telephone"); // Message de débogage
        } else {
            if ($clientModel->ajouterClient($nom, $prenom, $email, $telephone, $photoUrl)) {
                unset($_SESSION['form_data']);
                echo "Client ajouté avec succès !";
                error_log("Client ajouté avec succès : $telephone"); // Message de débogage
            } else {
                $errors['general'][] = "Erreur lors de l'ajout du client.";
                error_log("Erreur lors de l'ajout du client."); // Message de débogage
            }
        }
    } else {
        // Récupérer les erreurs de validation
        $errors = $validator->getErrors();
        error_log("Erreurs de validation : " . json_encode($errors)); // Message de débogage
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients et Dettes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-xl font-bold">Gestion des Clients et Dettes</div>
            <div class="flex space-x-4">
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-500">Home</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-500">Clients</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-500">Dettes</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-500">Profil</a>
                <a href="#" class="text-white px-4 py-2 rounded hover:bg-blue-500">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Nouveau Client Section -->
            <div class="bg-blue-300 p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Nouveau Client</h2>
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                    <div class="mb-4">
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" id="nom" name="nom" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" value="<?php echo isset($_SESSION['form_data']['nom']) ? htmlspecialchars($_SESSION['form_data']['nom']) : ''; ?>">
                        <?php if (isset($errors['nom'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['nom'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" value="<?php echo isset($_SESSION['form_data']['prenom']) ? htmlspecialchars($_SESSION['form_data']['prenom']) : ''; ?>">
                        <?php if (isset($errors['prenom'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['prenom'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                        <?php if (isset($errors['email'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['email'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" value="<?php echo isset($_SESSION['form_data']['telephone']) ? htmlspecialchars($_SESSION['form_data']['telephone']) : ''; ?>">
                        <?php if (isset($errors['telephone'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['telephone'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                        <input type="file" id="photo" name="photo" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <img id="preview-img" src="" alt="Image Preview" class="mt-2 w-48 h-48 object-cover rounded-md">
                        <?php if (isset($errors['photo'])) : ?>
                            <p class="text-red-500 text-xs italic"><?= $errors['photo'][0] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="bg-blue-600 text-white p-2 rounded-md">Enregistrer</button>
                    </div>
                </form>
            </div>

            <!-- Suivie Dette Section -->
            <div class="bg-gray-200 p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Suivi de Dette</h2>
                <!-- Formulaire de recherche -->
                <form class="mb-4 flex gap-2" method="POST" action="/recherche">
                    <input type="hidden" name="action" value="searchClient">
                    <input type="text" name="telephone" placeholder="Entrez le numéro de téléphone du client" class="flex-grow p-2 border border-gray-300 rounded-l-md">
                    <button type="submit" class="bg-blue-500 text-white p-2 rounded-r-md">Ok</button>
                </form>

                <?php if (isset($client)) : ?>
                    <div class="flex justify-center mb-4">
                        <div class="w-32 h-32 bg-gray-300 rounded-md">
                            <?php if (!empty($client['photo_url'])) : ?>
                                <img src="<?= htmlspecialchars($client['photo_url']) ?>" class="w-full h-full object-cover rounded-md" alt="Photo du client">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label for="client-nom" class="block text-sm font-medium text-gray-700">Nom :</label>
                            <input type="text" id="client-nom" value="<?= $client['nom'] ?? '' ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
                        </div>
                        <div>
                            <label for="client-prenom" class="block text-sm font-medium text-gray-700">Prénom :</label>
                            <input type="text" id="client-prenom" value="<?= $client['prenom'] ?? '' ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
                        </div>
                        <div>
                            <label for="client-telephone" class="block text-sm font-medium text-gray-700">Téléphone :</label>
                            <input type="text" id="client-telephone" value="<?= $client['telephone'] ?? '' ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
                        </div>
                    </div>
                    <?php if (isset($dette) && !empty($dette)) : ?>
                        <h2 class="text-xl font-bold mt-8">Détails de la dette</h2>
                        <div class="space-y-4">
                            <?php foreach ($dette as $uneDette) : ?>
                                <div class="bg-white p-4 rounded-lg shadow-md">
                                    <div>
                                        <label for="total-dette" class="block text-sm font-medium text-gray-700">Somme Total Dette :</label>
                                        <input type="text" id="montant_initial" value="<?= htmlspecialchars($uneDette['montant_initial']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
                                    </div>
                                    <div>
                                        <label for="montant-versee" class="block text-sm font-medium text-gray-700">Montant Versé :</label>
                                        <input type="text" id="montant-verser" value="<?= htmlspecialchars($uneDette['montant_verser']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
                                    </div>
                                    <div>
                                        <label for="montant-restant" class="block text-sm font-medium text-gray-700">Montant Restant :</label>
                                        <input type="text" id="montant_restant" value="<?= htmlspecialchars($uneDette['montant_restant']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="mt-4">Aucune dette trouvée pour ce client.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white text-center py-4">
        &copy; <?= date('Y-m-d') ?> Boutique Diallo. Tous droits réservés.
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