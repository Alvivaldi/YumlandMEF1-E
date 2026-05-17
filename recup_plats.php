<?php
include 'includes/fonctions.php';

$plats = lireJSON('donnees/plats.json');

// On récupère les filtres choisis par l'utilisateur (ou 'tous' par défaut)
$cat = isset($_GET['categorie']) ? strtolower($_GET['categorie']) : 'tous';
$reg = isset($_GET['regime']) ? strtolower($_GET['regime']) : 'tous';
$sav = isset($_GET['saveur']) ? strtolower($_GET['saveur']) : 'tous';
$alg = isset($_GET['allergene']) ? strtolower($_GET['allergene']) : 'tous';

$resultats = [];

foreach ($plats as $plat) {
    $correspond = true;

    // On utilise strpos() pour vérifier si le filtre est contenu dans la chaîne
    // Ex: Si $cat est "specialite" et que la catégorie du plat est "plat, specialite", strpos() dira OUI !
    
    if ($cat !== 'tous' && isset($plat['categorie']) && strpos(strtolower($plat['categorie']), $cat) === false) {
        $correspond = false;
    }
    
    if ($reg !== 'tous' && isset($plat['regime']) && strpos(strtolower($plat['regime']), $reg) === false) {
        $correspond = false;
    }
    
    if ($sav !== 'tous' && isset($plat['saveur']) && strpos(strtolower($plat['saveur']), $sav) === false) {
        $correspond = false;
    }
    
    // Pour les allergènes (qui fonctionnait déjà comme ça)
    if ($alg !== 'tous' && isset($plat['allergene'])) {
        $allergene_plat = strtolower($plat['allergene']);
        if ($alg === 'gluten' && strpos($allergene_plat, 'gluten') === false) {
            $correspond = false;
        }
        if ($alg === 'lactose' && strpos($allergene_plat, 'lactose') === false) {
            $correspond = false;
        }
    }

    if ($correspond) {
        $resultats[] = $plat;
    }
}

// On renvoie les résultats au JavaScript !
header('Content-Type: application/json');
echo json_encode($resultats);
?>