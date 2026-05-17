<?php

include 'includes/fonctions.php'; 

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $nouveau_statut = $_POST['nouveau_statut'] ?? null;

    if ($id_user && $nouveau_statut) {

        $utilisateurs = lireJSON('donnees/utilisateurs.json');
        $trouve = false;

        foreach ($utilisateurs as &$u) {
            if ($u['id'] == $id_user) {
                $u['statut'] = $nouveau_statut;
                $trouve = true;
                break;
            }
        }

        if ($trouve) {
            file_put_contents('donnees/utilisateurs.json', json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false]);
exit;