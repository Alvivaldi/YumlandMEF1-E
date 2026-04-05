<?php
session_start();      // On récupère la session en cours
session_unset();      // On vide toutes les variables de session
session_destroy();    // On détruit complètement la session

// On redirige l'utilisateur vers la page d'accueil
header("Location: index.php");
exit;
?>