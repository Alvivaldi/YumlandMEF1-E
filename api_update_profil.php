<?php
session_start();
include 'includes/fonctions.php';


if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$id_user = $_SESSION['user']['id'];
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$adresse = $_POST['adresse'] ?? '';


$utilisateurs = lireJSON('donnees/utilisateurs.json');

$mise_a_jour = false;


foreach ($utilisateurs as &$user) {
    if (isset($user['id']) && $user['id'] == $id_user) {
        $user['nom'] = $nom;
        $user['prenom'] = $prenom;
        $user['telephone'] = $telephone;
        $user['adresse'] = $adresse;
        

        $_SESSION['user'] = $user;
        $mise_a_jour = true;
        break;
    }
}

if ($mise_a_jour) {
    ecrireJSON('donnees/utilisateurs.json', $utilisateurs);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>