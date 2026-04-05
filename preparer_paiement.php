<?php
session_start();

// SÉCURITÉ : On vérifie que le client est bien connecté avant d'aller à la banque
if (!isset($_SESSION['user'])) {
    // S'il n'est pas connecté, on le renvoie à la page de connexion
    header("Location: formulaire.php");
    exit();
}

include 'includes/fonctions.php';
include 'includes/getapikey.php'; 

$vendeur = "MEF-1_E"; 
$api_key = getAPIKey($vendeur);


$_SESSION['timing'] = isset($_POST['timing']) ? $_POST['timing'] : 'immediat';
$_SESSION['date_livraison'] = isset($_POST['date_livraison']) ? $_POST['date_livraison'] : '';


$plats = lireJSON('donnees/plats.json');
$total = 0;

if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $id => $qte) {
        foreach ($plats as $p) {
            if ($p['id'] == $id) { 
                $total += $p['prix'] * $qte; 
                break; 
            }
        }
    }
}


if ($total <= 0) {
    die("<h2 style='text-align:center; color:red; margin-top:50px;'>Erreur : Votre panier est vide. <a href='carte.php'>Retour</a></h2>");
}

$transaction = "CMD" . time() . rand(10, 99);
$montant = number_format($total, 2, '.', ''); 
$url_retour = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/retour_paiement.php";

$control = md5($api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $url_retour . "#");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Redirection CYBank</title>
</head>
<body onload="document.getElementById('cybank-form').submit();" style="text-align: center; margin-top: 50px; font-family: sans-serif;">
    
    <h2>Redirection vers le paiement sécurisé CYBank...</h2>
    <p>Veuillez patienter.</p>

    <form id="cybank-form" action="https://www.plateforme-smc.fr/cybank/" method="POST">
        <input type="hidden" name="transaction" value="<?php echo $transaction; ?>">
        <input type="hidden" name="montant" value="<?php echo $montant; ?>">
        <input type="hidden" name="vendeur" value="<?php echo $vendeur; ?>">
        <input type="hidden" name="retour" value="<?php echo $url_retour; ?>">
        <input type="hidden" name="control" value="<?php echo $control; ?>">
    </form>

</body>
</html>