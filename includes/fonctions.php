<?php
// Fichier : includes/fonctions.php

// Fonction pour lire n'importe quel fichier JSON et le transformer en tableau PHP
function lireJSON($cheminFichier) {
    if (file_exists($cheminFichier)) {
        $donnees = file_get_contents($cheminFichier);
        return json_decode($donnees, true); // true = transforme en tableau (array)
    }
    return []; // Retourne un tableau vide si le fichier n'existe pas
}

// Fonction pour sauvegarder des données dans un fichier JSON (pour Lasugaa et Alexis)
function ecrireJSON($cheminFichier, $donnees) {
    $json = json_encode($donnees, JSON_PRETTY_PRINT);
    file_put_contents($cheminFichier, $json);
}
?>