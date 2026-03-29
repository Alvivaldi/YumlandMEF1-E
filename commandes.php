<?php
$commandes_donnees = json_decode(file_get_contents('donnees/commandes.json'), true);
$utilisateurs_donnees = json_decode(file_get_contents('donnees/utilisateurs.json'), true);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commandes</title>
    <link rel="stylesheet" href="css/commandes.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />

</head>

<body class="admin-body">
    <?php include 'includes/header.php'; ?>

    <header>

        <h1 class="big-title">Commandes & Livraison</h1>
    </header>

    <main class="restaurateur-main">
        <div class="stats-container">
            <div class="box1-content admin-card">
                <h3>À cuisiner</h3>
                <p class="stat-number">Nombres de commandes...</p>
            </div>
            <div class="box1-content admin-card" style="background-color: #d23508;">
                <h2 class="entree" style="color: white;">🚚 En cours de préparation...</h2>
                <p class="stat-number entree" style="color: white;">3</p>
            </div>
        </div>

        <div class="commandes-grid">
            <section class="column-prepa">
                <h2 class="entree">🔥 À Préparer</h2>

                    <?php foreach ($commandes as $cmd): ?>
                        <?php if ($cmd['statut'] === 'a_preparer'): ?>
                            <div class="box-commande">
                                <div class="commande-header">
                                    <span class="num-commande">#<?php echo $cmd['id_commande']; ?></span>
                                    <span class="heure"><?php echo $cmd['heure']; ?></span>
                                </div>
                                <div class="commande-details">
                                    <ul>
                                        <?php foreach ($cmd['details'] as $article): ?>
                                            <li><?php echo $article['quantite']; ?>x <?php echo $article['produit']; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <button class="btn-action-status">Lancer la préparation</button>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
            </section>

            <section class="column-livraison">
                <h2 class="entree" style="color: #010000;">🚚 En cours de livraison</h2>

                <?php foreach ($commandes as $cmd): ?>
                    <?php if ($cmd['statut'] === 'en_livraison'): ?>
                        <div class="box-commande delivery-mode">
                            <div class="commande-header">
                                <span class="num-commande">#<?php echo $cmd['id_commande']; ?></span>
                                <span class="badge livreur">
                                    Livreur : <?php echo $cmd['livreur_attribue'] ?? 'Non assigné'; ?>
                                </span>
                            </div>
                            <div class="commande-details">
                                <p>Destination : <?php echo $cmd['adresse_livraison']; ?></p>
                                <p>Montant : <?php echo $cmd['prix_total']; ?> €</p>
                            </div>
                            <button class="btn-secondary" disabled>En attente de remise...</button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        </div>
    </main>
    
</body>

</html>