<?php
session_start();
include 'includes/fonctions.php';

// Si l'utilisateur n'est pas connecté, on arrête tout
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$id_user = $_SESSION['user']['id'];
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$adresse = $_POST['adresse'] ?? '';

// On lit le fichier JSON
$utilisateurs = lireJSON('donnees/utilisateurs.json');

$mise_a_jour = false;

// On cherche notre utilisateur et on modifie ses infos
foreach ($utilisateurs as &$user) {
    if (isset($user['id']) && $user['id'] == $id_user) {
        $user['nom'] = $nom;
        $user['prenom'] = $prenom;
        $user['telephone'] = $telephone;
        $user['adresse'] = $adresse;
        
        // On met aussi à jour la session en mémoire !
        $_SESSION['user'] = $user;
        $mise_a_jour = true;
        break;
    }
}

if ($mise_a_jour) {
    ecrireJSON('donnees/utilisateurs.json', $utilisateurs);
    // On renvoie une réponse au format JSON pour le JavaScript
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>