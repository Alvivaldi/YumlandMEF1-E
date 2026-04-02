<?php
session_start();
include 'includes/fonctions.php';

$plats = lireJSON('donnees/plats.json');
$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/carte.css" /> 
    <link rel="stylesheet" type="text/css" href="css/panier.css" /> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="header-carte">
        <h1 class="titre-carte">Mon Panier</h1>
    </div>

    <div class="panier-container">
        
        <?php if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])): ?>
            <p class="panier-vide">Votre panier est actuellement vide.</p>
            <div class="text-center">
                <a href="carte.php" class="btn-retour">Retour au menu</a>
            </div>
            
        <?php else: ?>
            <table class="table-panier">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($_SESSION['panier'] as $id_plat => $quantite): 
                        
                        $plat_trouve = null;
                        foreach ($plats as $p) {
                            if ($p['id'] == $id_plat) {
                                $plat_trouve = $p;
                                break;
                            }
                        }

                        if ($plat_trouve):
                            $sous_total = $plat_trouve['prix'] * $quantite;
                            $total += $sous_total; 
                    ?>
                        <tr>
                            <td class="plat-info">
                                <img src="<?= htmlspecialchars($plat_trouve['image']) ?>" alt="image plat" class="plat-img">
                                <strong><?= htmlspecialchars($plat_trouve['nom']) ?></strong>
                            </td>
                            <td><?= number_format($plat_trouve['prix'], 2) ?> €</td>
                            <td class="quantite-text"><?= $quantite ?></td>
                            <td class="sous-total-text"><?= number_format($sous_total, 2) ?> €</td>
                        </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </tbody>
            </table>

            <div class="total-container">
                <strong>Total à payer : <?= number_format($total, 2) ?> €</strong>
            </div>

            <form action="preparer_paiement.php" method="POST" class="form-paiement">
                <h3>Quand souhaitez-vous votre commande ?</h3>
                
                <div class="timing-option">
                    <label class="timing-label">
                        <input type="radio" name="timing" value="immediat" checked>
                        Préparation immédiate (Au plus vite)
                    </label>
                </div>
                
                <div class="timing-option">
                    <label class="timing-label">
                        <input type="radio" name="timing" value="plus_tard">
                        Pour plus tard :
                    </label>
                    <input type="datetime-local" name="date_livraison" class="date-livraison-input">
                </div>

                <div class="text-center" style="margin-top: 30px;">
                    <button type="submit" class="btn-payer">
                        <i class="fa-solid fa-credit-card"></i> Payer avec CYBank
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>