<?php
include 'includes/fonctions.php';

$plats = lireJSON('donnees/plats.json');


$cat = isset($_GET['categorie']) ? strtolower($_GET['categorie']) : 'tous';
$reg = isset($_GET['regime']) ? strtolower($_GET['regime']) : 'tous';
$sav = isset($_GET['saveur']) ? strtolower($_GET['saveur']) : 'tous';
$alg = isset($_GET['allergene']) ? strtolower($_GET['allergene']) : 'tous';

$resultats = [];

foreach ($plats as $plat) {
    $correspond = true;


    
    if ($cat !== 'tous' && isset($plat['categorie']) && strpos(strtolower($plat['categorie']), $cat) === false) {
        $correspond = false;
    }
    
    if ($reg !== 'tous' && isset($plat['regime']) && strpos(strtolower($plat['regime']), $reg) === false) {
        $correspond = false;
    }
    
    if ($sav !== 'tous' && isset($plat['saveur']) && strpos(strtolower($plat['saveur']), $sav) === false) {
        $correspond = false;
    }
    

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


header('Content-Type: application/json');
echo json_encode($resultats);
?>