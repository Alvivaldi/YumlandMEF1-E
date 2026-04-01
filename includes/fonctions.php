<?php


function lireJSON($cheminFichier) {
    if (file_exists($cheminFichier)) {
        $donnees = file_get_contents($cheminFichier);
        return json_decode($donnees, true);
    }
    return []; 
}

function ecrireJSON($cheminFichier, $donnees) {
    $json = json_encode($donnees, JSON_PRETTY_PRINT);
    file_put_contents($cheminFichier, $json);
}
?>