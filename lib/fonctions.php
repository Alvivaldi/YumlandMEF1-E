<?php

function lireDonnees($nomFichier)
{
    // Chemin vers le dossier data 
    $chemin = "donnees/" . $nomFichier;

    // 1. Vérifier si le fichier existe pour éviter les erreurs
    if (!file_exists($chemin)) {
        return []; // Retourne un tableau vide si le fichier n'existe pas encore
    }

    // 2. Lire le contenu brut du fichier (chaîne de caractères)
    $contenuBrut = file_get_contents($chemin);

    // 3. Transformer le JSON en tableau associatif PHP
    // Le paramètre 'true' est crucial pour obtenir un tableau et non un objet
    $tableau = json_decode($contenuBrut, true);

    return $tableau ?? []; // Retourne le tableau ou un tableau vide si le JSON est invalide
}

function sauvegarderDonnees($nomFichier, $donnees)
{
    $chemin = "donnees/" . $nomFichier;

    // 1. Transformer le tableau PHP en chaîne de caractères au format JSON
    // JSON_PRETTY_PRINT permet de garder le fichier lisible pour vous (avec des indentations)
    $json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // 2. Écrire la chaîne dans le fichier
    // Si le fichier n'existe pas, PHP le créera automatiquement
    return file_put_contents($chemin, $json);
}
