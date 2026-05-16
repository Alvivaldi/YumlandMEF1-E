<?php
// recup_plats.php
include 'includes/fonctions.php';
error_reporting(0);

$categorie = $_GET['categorie'] ?? 'tous';
$regime = $_GET['regime'] ?? 'tous';
$saveur = $_GET['saveur'] ?? 'tous';
$allergene = $_GET['allergene'] ?? 'tous';

// Sélection de la bonne source de données selon le filtre
if ($categorie === 'formule') {
    $source = lireJSON('donnees/menus.json');
} else {
    $source = lireJSON('donnees/plats.json');
}

$resultats = array_filter($source, function($item) use ($categorie, $regime, $saveur, $allergene) {
    
    // Si formule, on accepte tout le fichier menus.json, sinon on filtre par clé catégorie
    $matchCat = ($categorie === 'tous' || $categorie === 'formule' || (isset($item['categorie']) && $item['categorie'] === $categorie));
    $matchRegime = ($regime === 'tous' || (isset($item['regime']) && $item['regime'] === $regime));
    $matchSaveur = ($saveur === 'tous' || (isset($item['saveur']) && $item['saveur'] === $saveur));
    $matchAllergene = ($allergene === 'tous' || (isset($item['allergene']) && $item['allergene'] !== $allergene));
    
    return $matchCat && $matchRegime && $matchSaveur && $matchAllergene;
});

header('Content-Type: application/json');
echo json_encode(array_values($resultats));
exit;
?>
