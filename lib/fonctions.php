<?php

function lireDonnees($nomFichier)
{

    $chemin = "donnees/" . $nomFichier;


    if (!file_exists($chemin)) {
        return []; 
    }


    $contenuBrut = file_get_contents($chemin);


    $tableau = json_decode($contenuBrut, true);

    return $tableau ?? []; 

function sauvegarderDonnees($nomFichier, $donnees)
{
    $chemin = "donnees/" . $nomFichier;


    $json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


    return file_put_contents($chemin, $json);
}
}