<?php
session_start();
include 'includes/fonctions.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $utilisateurs = lireJSON('donnees/utilisateurs.json');
    
    // LA CORRECTION EST ICI : Si le JSON est vide ou mal lu, on force un tableau vide !
    if (!is_array($utilisateurs)) { 
        $utilisateurs = []; 
    }

    $connecte = false;
    foreach ($utilisateurs as $user) {
        // On vérifie que 'login' existe bien pour cet utilisateur avant de comparer
        if (isset($user['login']) && $user['login'] === $email) {
            // Vérification du mot de passe haché
            if (password_verify($password, $user['password'])) {
                // Connexion réussie : on stocke les infos en session
                $_SESSION['user'] = $user;
                $connecte = true;
                
                // On redirige vers le profil
                header("Location: profil.php");
                exit();
            }
        }
    }
    
    // Si la boucle est terminée et qu'on n'est pas connecté
    if (!$connecte) {
        $erreur = "Identifiants ou mot de passe incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/formulaire.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        
        <?php if ($erreur != ""): ?>
            <p style="color: red; text-align: center; font-weight: bold; margin-bottom: 15px;"><?php echo $erreur; ?></p>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <p style="color: #2ecc71; text-align: center; font-weight: bold; margin-bottom: 15px;">Inscription réussie ! Connectez-vous.</p>
        <?php endif; ?>

        <form action="formulaire.php" method="post">
            <div class="input-box">
                <input type="email" name="email" placeholder="E-mail" required>
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Mot de passe" required>
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