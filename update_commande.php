<?php

session_start();
require_once 'includes/fonctions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_commande = $_POST['id_commande'] ?? '';
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    $id_livreur = $_POST['id_livreur'] ?? '';

    $commandes = lireJSON('donnees/commandes.json');

    foreach ($commandes as &$cmd) {

        if ($cmd['id_commande'] === $id_commande) {

            $cmd['statut'] = $nouveau_statut;

            if (!empty($id_livreur)) {
                $cmd['id_livreur'] = $id_livreur;
            }

            break;
        }
    }

    ecrireJSON('donnees/commandes.json', $commandes);

    echo json_encode([
        'success' => true,
        'nouveau_statut' => $nouveau_statut
    ]);

    exit();
}

echo json_encode([
    'success' => false
]);