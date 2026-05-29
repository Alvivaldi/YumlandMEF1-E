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




// --- FONCTION POUR LA PHASE 4 : LOGS DE SÉCURITÉ ---
function ajouterLogSecurite($action, $email_concerne) {
    // 1. On définit le chemin vers le fichier texte
    $fichier_log = __DIR__ . '/../donnees/securite.log';
    
    // 2. On récupère la date et l'heure exactes
    $date_heure = date('Y-m-d H:i:s');
    
    // 3. On prépare la phrase à écrire
    $message = "[$date_heure] ALERTE : $action - Cible : $email_concerne" . PHP_EOL;
    
    // 4. On écrit la phrase à la fin du fichier (FILE_APPEND permet de ne pas écraser l'historique)
    file_put_contents($fichier_log, $message, FILE_APPEND);
}
?>