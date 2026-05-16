<?php
// recuperer_plats.php
include 'includes/fonctions.php';
error_reporting(0);
$plats = lireJSON('donnees/plats.json');

$categorie = $_GET['categorie'] ?? 'tous';
$regime = $_GET['regime'] ?? 'tous';
$saveur = $_GET['saveur'] ?? 'tous';
$allergene = $_GET['allergene'] ?? 'tous'; // <-- Ajout de l'allergène

$resultats = array_filter($plats, function($plat) use ($categorie, $regime, $saveur, $allergene) {
    
    $matchCat = ($categorie === 'tous' || (isset($plat['categorie']) && $plat['categorie'] === $categorie));
    $matchRegime = ($regime === 'tous' || (isset($plat['regime']) && $plat['regime'] === $regime));
    $matchSaveur = ($saveur === 'tous' || (isset($plat['saveur']) && $plat['saveur'] === $saveur));
    
    // Vérification de l'allergène
    // On garde le plat si l'utilisateur n'a pas de restriction ('tous') 
    // OU si l'allergène du plat est différent de celui sélectionné (ex: l'utilisateur veut éviter le 'gluten')
    $matchAllergene = ($allergene === 'tous' || (isset($plat['allergene']) && $plat['allergene'] !== $allergene));
    
    return $matchCat && $matchRegime && $matchSaveur && $matchAllergene;
});

header('Content-Type: application/json');
echo json_encode(array_values($resultats));
exit;
?>

