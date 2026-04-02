<?php
session_start();
include 'includes/fonctions.php';

// Vérifier qu'on sait quelle commande on note
if (!isset($_GET['id']) && !isset($_POST['id_commande'])) {
    header('Location: profil.php');
    exit;
}
$id_commande = $_GET['id'] ?? $_POST['id_commande'];

// TRAITEMENT : Quand le client clique sur "Soumettre"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commandes = lireJSON('donnees/commandes.json');
    
    foreach ($commandes as $index => $cmd) {
        if ($cmd['id_commande'] === $id_commande) {
            // On sauvegarde la notation Livraison
            $commandes[$index]['note_livraison'] = (int)$_POST['note_livraison'];
            $commandes[$index]['comment_livraison'] = htmlspecialchars($_POST['comment_livraison']);
            
            // On sauvegarde la notation Plats
            $commandes[$index]['note_plats'] = (int)$_POST['note_plats'];
            $commandes[$index]['comment_plats'] = htmlspecialchars($_POST['comment_plats']);
            
            // On fait une moyenne globale pour l'afficher sur la page profil !
            $commandes[$index]['note'] = ($commandes[$index]['note_livraison'] + $commandes[$index]['note_plats']) / 2;
            break;
        }
    }
    
    ecrireJSON('donnees/commandes.json', $commandes);
    header('Location: profil.php?success=notation');
    exit;
}
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notation</title>
    <link rel="stylesheet" href="css/notation.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
    
    <style>
        /* Un petit ajout CSS pour transformer tes radios en boutons clairs sans casser ton design */
        .choix-note label { margin-right: 15px; font-size: 1.2em; cursor: pointer; }
        .choix-note input { transform: scale(1.5); margin-right: 5px; cursor: pointer; }
    </style>
</head>

<body>
   <?php include 'includes/header.php'; ?>
   
    <section>
        <div class="tete-notation">
            <h1>NOTATION</h1>
            <h2>Donnez nous votre avis !</h2>
            <p>Commande N° <strong><?= htmlspecialchars($id_commande) ?></strong></p>
        </div>
    </section>

    <section class="notation">
        <form action="notation.php" method="POST">
            <input type="hidden" name="id_commande" value="<?= htmlspecialchars($id_commande) ?>">

            <h3>LIVRAISON</h3>
            <h4>Comment évalueriez-vous la qualité de notre service de livraison ?</h4>
            <div class="notation-container">
                <div class="notation-item">
                    <h3>Note:</h3>
                    <div class="choix-note">
                        <label><input type="radio" name="note_livraison" value="1" required> 1 ★</label>
                        <label><input type="radio" name="note_livraison" value="2"> 2 ★</label>
                        <label><input type="radio" name="note_livraison" value="3"> 3 ★</label>
                        <label><input type="radio" name="note_livraison" value="4"> 4 ★</label>
                        <label><input type="radio" name="note_livraison" value="5"> 5 ★</label>
                    </div>
                </div>
                <div class="notation-item">
                    <h5>Commentaire:</h5>
                    <textarea name="comment_livraison" placeholder="Laissez votre commentaire ici..."></textarea>
                </div>
            </div>

            <p>_____________________________________________________</p>

            <h3>QUALITÉ DES PLATS</h3>
            <h4>Comment évalueriez-vous la qualité de nos plats ?</h4>
            <div class="notation-container">
                <div class="notation-item">
                    <h3>Note:</h3>
                    <div class="choix-note">
                        <label><input type="radio" name="note_plats" value="1" required> 1 ★</label>
                        <label><input type="radio" name="note_plats" value="2"> 2 ★</label>
                        <label><input type="radio" name="note_plats" value="3"> 3 ★</label>
                        <label><input type="radio" name="note_plats" value="4"> 4 ★</label>
                        <label><input type="radio" name="note_plats" value="5"> 5 ★</label>
                    </div>
                </div>
                <div class="notation-item">
                    <h5>Commentaire:</h5>
                    <textarea name="comment_plats" placeholder="Laissez votre commentaire ici..."></textarea>
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="submit-btn" style="padding: 15px 40px; font-size: 1.2em; cursor: pointer;">Soumettre mon avis global</button>
            </div>

        </form>
        </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>