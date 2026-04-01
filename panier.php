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

    <div style="max-width: 900px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); font-family: 'Montserrat', sans-serif;">
        
        <?php if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])): ?>
            <p style="text-align: center; font-size: 1.2em; padding: 40px;">Votre panier est actuellement vide.</p>
            <div style="text-align: center;">
                <a href="carte.php" style="padding: 10px 20px; background-color: #fca311; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Retour au menu</a>
            </div>
            
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #ddd;">
                        <th style="padding: 15px;">Produit</th>
                        <th style="padding: 15px;">Prix Unitaire</th>
                        <th style="padding: 15px;">Quantité</th>
                        <th style="padding: 15px;">Sous-total</th>
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
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px; display: flex; align-items: center;">
                                <img src="<?= htmlspecialchars($plat_trouve['image']) ?>" alt="image plat" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                                <strong><?= htmlspecialchars($plat_trouve['nom']) ?></strong>
                            </td>
                            <td style="padding: 15px;"><?= number_format($plat_trouve['prix'], 2) ?> €</td>
                            <td style="padding: 15px; font-weight: bold;"><?= $quantite ?></td>
                            <td style="padding: 15px; color: #fca311; font-weight: bold;"><?= number_format($sous_total, 2) ?> €</td>
                        </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </tbody>
            </table>

            <div style="text-align: right; margin-top: 20px; font-size: 1.5em; padding: 20px; background: #fff8eb; border-radius: 8px;">
                <strong>Total à payer : <?= number_format($total, 2) ?> €</strong>
            </div>

            <form action="preparer_paiement.php" method="POST" style="margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px;">
                <h3 style="margin-bottom: 15px;">Quand souhaitez-vous votre commande ?</h3>
                
                <div style="margin-bottom: 10px;">
                    <label style="cursor: pointer; font-size: 1.1em;">
                        <input type="radio" name="timing" value="immediat" checked>
                        Préparation immédiate (Au plus vite)
                    </label>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="cursor: pointer; font-size: 1.1em;">
                        <input type="radio" name="timing" value="plus_tard">
                        Pour plus tard :
                    </label>
                    <input type="datetime-local" name="date_livraison" style="margin-left: 10px; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" style="background-color: #27ae60; color: white; padding: 15px 40px; font-size: 1.2em; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.3s;">
                        <i class="fa-solid fa-credit-card"></i> Payer avec CYBank
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>