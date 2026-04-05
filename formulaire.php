<?php
session_start();
require_once 'includes/fonctions.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $utilisateurs = lireJSON('donnees/utilisateurs.json');

    foreach ($utilisateurs as $user) {
        if ($user['login'] === $email && password_verify($password, $user['password'])) {
            // Connexion réussie : on stocke les infos en session
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit();
        }
    }
    $erreur = "Identifiants incorrects.";
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/formulaire.css">


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />

</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="background">
        <div class="slide slide1"></div>
        <div class="slide slide2"></div>
        <div class="slide slide3"></div>
    </div>

    <section class="form-section">
        <span class="close-btn" onclick="window.location.href='index.php'">
            <i class="fa-solid fa-xmark"></i>
        </span>
        <h1>Connexion</h1>
        <form action="formulaire.php" method="post">
            <div class="input-box">
                <input type="text" name="email" placeholder="E-mail">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Mot de passe">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox"> Se souvenir de moi
                </label>
                <a href="#">Mot de passe oublié ?</a>
            </div>
            <button type="submit" class="login-btn">Se connecter</button>
            <div class="register-link">
                <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
            </div>
        </form>

    </section>
</body>

</html>