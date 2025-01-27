<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Dettes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

        <!-- Main Content -->
        <div class="w-3/4 p-6">
            <!-- Page de Connexion -->
            <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4 text-center">Connexion</h2>
                <form>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" id="password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="bg-blue-600 text-white p-2 rounded-md">Se connecter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

<!-- 
Je veux entamer un nouveau projet de création d'une application web de gestion des dettes d'une boutique en PHP (Modele MVC) et Tailwind CSS;
 * Pour la premiere partie, je veux uniquement initialiser des données au niveau de ma base de données et les afficher au niveau de mon interface :
 Il s'agira de creer un base de données au niveau de mon interface MySQL et de creer les tables : 
 Lors de la création d'un nouveau client, le mot de passe doit etre crypter !

 -- Active: 1719252693843@@127.0.0.1@3306@gestion_boutique_credit
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    photo VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE dettes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    montant_initial DECIMAL(10, 2) NOT NULL,
    montant_restant DECIMAL(10, 2) NOT NULL,
    montant_verser DECIMAL(10, 2) NOT NULL,
    date_creation DATE NOT NULL,
    statut ENUM('en_cours', 'remboursee') DEFAULT 'en_cours',
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dette_id INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    date_paiement DATE NOT NULL,
    FOREIGN KEY (dette_id) REFERENCES dettes(id)
);

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('boutiquier', 'client') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

Puis de connecter ma base de données à mon interface ! et de pouvoir recuperer les données et les données affichées !

Les roles et les droits  d'acces pour chaque utilisateur :
- Boutiquier : Enregistrer un nouveau client via un formulaire avec ses données et que ces derniers soit enregistrer au niveau de la base de données dés qu'il click sur le bouton "Enregistrer" et lui attribuer une nouvelle dette;
Formulaire Client : Nom, prenom, telephone, email,photo; le mot de passe sera générer au niveau de la base de données (par defaut c'est passer123 pour tous les clients)
Il a également la possibilité de rechercher un client en recherchant son numero et qu'on recupere les données de ce dernier au niveau de la base de données : nom, prenom,email,telephone, montant total de la dette, montant versé, montant restant !

- Client : Peut entrer dans l'application via la page de connexion et voir uniquement que ces dettes (non soldées par défaut) avec ces identifiants 


Pour la gestion des sessions et les droits d'accès, je vais utiliser PHP et la session pour stocker les informations de l'utilisateur connecté et les droits d'accès de l'utilisateur.
Explication de la stratégie de développement:

Je vais commencer par développer l'interface utilisateur (front-end) avec HTML, CSS et Tailwind CSS. Ensuite, je développerai le modele MVC (Modèle, Vue, Contrôleur) avec PHP pour le back-end.

Pour la gestion des clients, je vais utiliser une base de données MySQL pour stocker les données des clients. Je vais écrire des requêtes SQL pour recuperer les données à partir de la base de données MySQL

1. Développer l'interface utilisateur (front-end) avec HTML, CSS et Tailwind CSS:
- Créer une page de connexion
- Créer une page d'accueil avec un menu de navigation
- Créer une page de gestion des clients avec une liste des clients, un formulaire pour ajouter un client;


Je voudrais utiliser la technologie PHP (avec le modele MVC) pour le développement back-end et Tailwind CSS pour le front-end. et MySQL pour la base de données

-->




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

        <!-- Boutons Suivie Dette et Nouvelle Dette -->
        <div class="flex justify-center gap-4 mt-4">
            <button class="bg-green-500 text-white p-2 rounded-md" onclick="window.location.href='/suivi-dette?client_id=<?= $client['id'] ?>'">Suivie Dette</button>
            <button class="bg-blue-500 text-white p-2 rounded-md" onclick="window.location.href='/nouvelle-dette?client_id=<?= $client['id'] ?>'">Nouvelle Dette</button>
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