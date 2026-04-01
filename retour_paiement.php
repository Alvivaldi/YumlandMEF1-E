<?php
session_start();
include 'includes/fonctions.php';
include 'includes/getapikey.php';

$vendeur = "MEF-1_E"; 
$api_key = getAPIKey($vendeur);


$transaction = $_GET['transaction'] ?? '';
$montant = $_GET['montant'] ?? '';
$vendeur_recu = $_GET['vendeur'] ?? '';

$statut_paiement = $_GET['status'] ?? $_GET['statut'] ?? ''; 
$control_recu = $_GET['control'] ?? '';

$hash_verif = md5($api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur_recu . "#" . $statut_paiement . "#");

if ($control_recu !== $hash_verif) {
    die("<h1 style='color:red;'>ALERTE DE SÉCURITÉ</h1><p>Les données de paiement ont été modifiées en cours de route.</p>");
}


$titre = "";
$message = "";

if ($statut_paiement === 'accepted') {

    $timing = $_SESSION['timing'] ?? 'immediat';
    $date_livraison = $_SESSION['date_livraison'] ?? '';
    
 
    $plats = lireJSON('donnees/plats.json');
    $produits_commandes = [];
    foreach ($_SESSION['panier'] as $id => $qte) {
        foreach ($plats as $p) {
            if ($p['id'] == $id) {
                $produits_commandes[] = ["id_plat" => $id, "nom" => $p['nom'], "quantite" => $qte];
            }
        }
    }

    $nouvelle_commande = [
        "id_commande" => $transaction,
        "id_client" => 1, 
        "date_creation" => date('d/m/Y H:i'),
        "timing" => $timing,
        "date_livraison_prevue" => ($timing === 'plus_tard') ? $date_livraison : "Immédiate",
        "prix_total" => (float)$montant,
        "statut" => ($timing === 'immediat') ? "A_PREPARER" : "EN_ATTENTE",
        "produits" => $produits_commandes
    ];

    $chemin_commandes = 'donnees/commandes.json';
    $toutes_les_commandes = lireJSON($chemin_commandes);
    if (!is_array($toutes_les_commandes)) { $toutes_les_commandes = []; }
    $toutes_les_commandes[] = $nouvelle_commande;
    ecrireJSON($chemin_commandes, $toutes_les_commandes);

    // On vide le panier !
    unset($_SESSION['panier']);
    unset($_SESSION['timing']);
    unset($_SESSION['date_livraison']);

    $titre = "Paiement réussi !";
    $message = "Merci ! Votre commande n°$transaction d'un montant de $montant € a bien été validée.";
} else {
    // Échec du paiement
    $titre = "Paiement refusé";
    $message = "La banque a refusé la transaction. Votre commande n'a pas été validée.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $titre ?></title>
    <link rel="stylesheet" href="css/carte.css" />
</head>
<body style="text-align: center; padding-top: 50px; font-family: sans-serif;">
    <?php include 'includes/header.php'; ?>
    <div style="margin: 40px auto; padding: 20px; max-width: 600px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h1 style="color: <?= ($statut_paiement === 'accepted') ? 'green' : 'red' ?>;"><?= $titre ?></h1>
        <p><?= $message ?></p>
        <br>
        <a href="profil.php" style="padding: 10px 20px; background: #fca311; color: white; text-decoration: none; border-radius: 5px;">Voir mon profil</a>
    </div>
</body>
</html>