<?php

session_start();


if (isset($_POST['id_plat']) && isset($_POST['quantite'])) {
    

    $id_plat = (int)$_POST['id_plat'];
    $quantite = (int)$_POST['quantite'];


    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

  
    if (isset($_SESSION['panier'][$id_plat])) {
        $_SESSION['panier'][$id_plat] += $quantite;
    } else {
        
        $_SESSION['panier'][$id_plat] = $quantite;
    }
}


header('Location: carte.php');
exit;
?>