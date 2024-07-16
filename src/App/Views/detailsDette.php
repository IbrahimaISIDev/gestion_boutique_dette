<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la dette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* .navbar {
            background-color: #2d3748;
            color: #ffffff;
        } */

        .navbar a {
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            color: #e2e8f0;
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a202c;
        }

        .card p {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #4a5568;
        }

        .error-message {
            color: #e53e3e;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar py-4">
        <div class="container mx-auto flex justify-between items-center bg-blue-500 py-4">
            <h1 class="text-2xl font-bold text-white">Gestion des Dettes</h1>
            <a href="#" class="text-white">Accueil</a>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Détails de la dette</h1>
        <?php if (isset($_POST['idDette'])) : ?>
            <p class="mb-4">ID de la dette: <span class="font-semibold"><?php echo $_POST['idDette']; ?></span></p>
            <?php if (isset($dette)) : ?>
                <div class="card">
                    <h2>Informations du client</h2>
                    <p><span class="font-semibold">Nom:</span> <?php echo $client->nom; ?></p>
                    <p><span class="font-semibold">Prénom:</span> <?php echo $client->prenom; ?></p>
                    <p><span class="font-semibold">Téléphone:</span> <?php echo $client->telephone; ?></p>
                </div>
                <div class="card">
                    <h2>Informations de la dette</h2>
                    <p><span class="font-semibold">ID du client:</span> <?php echo $dette->client_id; ?></p>
                    <p><span class="font-semibold">Montant initial:</span> <?php echo $dette->montant_initial; ?></p>
                    <p><span class="font-semibold">Montant versé:</span> <?php echo $dette->montant_verser; ?></p>
                    <p><span class="font-semibold">Montant restant:</span> <?php echo $dette->montant_restant; ?></p>
                    <p><span class="font-semibold">Date de création:</span> <?php echo $dette->date_creation; ?></p>
                    <p><span class="font-semibold">Statut:</span> <?php echo $dette->statut; ?></p>
                </div>
            <?php else : ?>
                <p class="error-message">Erreur: Aucune dette trouvée pour l'ID <?php echo $_POST['idDette']; ?></p>
            <?php endif; ?>
        <?php else : ?>
            <p class="error-message">Erreur: ID de dette manquant</p>
        <?php endif; ?>
    </div>
</body>

</html>
