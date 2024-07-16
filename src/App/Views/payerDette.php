<!-- /views/payerDette.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Payer Dette</title>
</head>
<body>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($dettes) && count($dettes) > 0): ?>
        <h1>Payer Dette pour la Dette ID: <?php echo $dettes[0]->getId(); ?></h1>
        <form action="/payer-dette" method="post">
            <input type="hidden" name="dette_id" value="<?php echo $dettes[0]->getId(); ?>">
            <label for="montant_verser">Montant à Verser:</label>
            <input type="text" id="montant_verser" name="montant_verser">
            <button type="submit">Payer</button>
        </form>
    <?php endif; ?>
</body>
</html>
