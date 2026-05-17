<?php
session_start();
include 'includes/fonctions.php';


$valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
$cookie_val  = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
$theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';


if (!isset($_SESSION['user'])) {
    header('Location: formulaire.php');
    exit;
}


if (!isset($_GET['id']) && !isset($_POST['id_commande'])) {
    header('Location: profil.php');
    exit;
}

$id_commande = $_GET['id'] ?? $_POST['id_commande'];
$commandes   = lireJSON('donnees/commandes.json');


$ma_commande    = null;
$index_commande = -1;

foreach ($commandes as $index => $cmd) {
    if ($cmd['id_commande'] == $id_commande && $cmd['id_client'] == $_SESSION['user']['id']) {
        $ma_commande    = $cmd;
        $index_commande = $index;
        break;
    }
}


if (!$ma_commande) {
    die("<h1 style='color:red; text-align:center; margin-top:50px;'>Erreur : Commande introuvable.</h1>
         <p style='text-align:center;'><a href='profil.php'>Retour au profil</a></p>");
}

if (isset($ma_commande['note'])) {
    die("<h1 style='color:red; text-align:center; margin-top:50px;'>Erreur : Vous avez déjà noté cette commande.</h1>
         <p style='text-align:center;'><a href='profil.php'>Retour au profil</a></p>");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commandes[$index_commande]['note_livraison']    = (int) $_POST['note_livraison'];
    $commandes[$index_commande]['comment_livraison'] = htmlspecialchars($_POST['comment_livraison'] ?? '');
    $commandes[$index_commande]['note_plats']        = (int) $_POST['note_plats'];
    $commandes[$index_commande]['comment_plats']     = htmlspecialchars($_POST['comment_plats'] ?? '');
    $commandes[$index_commande]['note']              = ($commandes[$index_commande]['note_livraison'] + $commandes[$index_commande]['note_plats']) / 2;

    ecrireJSON('donnees/commandes.json', $commandes);
    header('Location: profil.php?success=notation');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noter ma commande</title>
    <link rel="stylesheet" id="dynamic-theme" href="<?php echo htmlspecialchars($theme_actif); ?>">
    <link rel="stylesheet" href="css/notation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="notation-container">
        <h1><i class="fa-solid fa-star"></i> Noter la commande #<?= htmlspecialchars($id_commande) ?></h1>

        <form method="POST" action="notation.php">
            <input type="hidden" name="id_commande" value="<?= htmlspecialchars($id_commande) ?>">

            <div class="section-notation">
                <h3>🚚 La Livraison</h3>
                <div class="form-group">
                    <label>Note (sur 5) :</label>
                    <input type="number" name="note_livraison" min="1" max="5" required placeholder="Ex: 4">
                </div>
                <div class="form-group">
                    <label>Commentaire pour le livreur :</label>
                    <textarea name="comment_livraison" rows="3"
                        placeholder="Livreur ponctuel et souriant..."></textarea>
                </div>
            </div>

            <div class="section-notation">
                <h3>🍔 Les Plats</h3>
                <div class="form-group">
                    <label>Note (sur 5) :</label>
                    <input type="number" name="note_plats" min="1" max="5" required placeholder="Ex: 5">
                </div>
                <div class="form-group">
                    <label>Commentaire sur le repas :</label>
                    <textarea name="comment_plats" rows="3" placeholder="C'était délicieux !"></textarea>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-check"></i> Valider mes notes
            </button>
        </form>
    </div>

</body>

</html>