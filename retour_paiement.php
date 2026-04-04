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
    // On garde le style en ligne ici car le die() empêche le chargement du CSS en dessous
    die("<h1 style='color:red; text-align:center;'>ALERTE DE SÉCURITÉ</h1><p style='text-align:center;'>Les données de paiement ont été modifiées en cours de route.</p>");
}

$titre = "";
$message = "";

if ($statut_paiement === 'accepted') {

    $timing = $_SESSION['timing'] ?? 'immediat';
    $date_livraison = $_SESSION['date_livraison'] ?? '';
    
    $plats = lireJSON('donnees/plats.json');
    $produits_commandes = [];
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $id => $qte) {
            foreach ($plats as $p) {
                if ($p['id'] == $id) {
                    $produits_commandes[] = ["id_plat" => $id, "nom" => $p['nom'], "quantite" => $qte];
                }
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

// On définit la classe CSS selon le statut du paiement
$classe_statut = ($statut_paiement === 'accepted') ? 'status-success' : 'status-error';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $titre ?></title>
    <link rel="stylesheet" href="css/carte.css" />
    <link rel="stylesheet" href="css/retour_paiement.css" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="payment-container">
        
        <h1 class="payment-title <?= $classe_statut ?>"><?= $titre ?></h1>
        
        <p class="payment-message"><?= $message ?></p>
        
        <a href="profil.php" class="btn-profil">Voir mon profil</a>
        
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>