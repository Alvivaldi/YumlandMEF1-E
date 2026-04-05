<?php
session_start(); 
include 'includes/fonctions.php';

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $telephone = $_POST['telephone'] ?? '';

    $utilisateurs = lireJSON('donnees/utilisateurs.json');
    if (!is_array($utilisateurs)) { $utilisateurs = []; }

    $existe = false;
    foreach ($utilisateurs as $user) {
        if (isset($user['login']) && $user['login'] === $email) {
            $existe = true;
            break;
        }
    }

    if ($existe) {
        $message = "Cet email est déjà utilisé.";
    } else {
        $nouvelUser = [
            "id" => time(), 
            "login" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "role" => "client", 
            "nom" => $nom,
            "prenom" => $prenom,
            "adresse" => $adresse,
            "telephone" => $telephone,
            "date_inscription" => date("Y-m-d")
        ];

        $utilisateurs[] = $nouvelUser;
        
        // Petit test de sécurité pour vérifier les droits d'écriture
        $ecriture_reussie = ecrireJSON('donnees/utilisateurs.json', $utilisateurs);
        
        if ($ecriture_reussie === false) {
             // Si ça passe par ici sur l'autre PC, c'est un problème de droits !
             $message = "Erreur serveur : Impossible d'écrire dans le fichier. Vérifiez les permissions du dossier 'donnees'.";
        } else {
             header("Location: formulaire.php?success=1");
             exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
        <h1>Inscription</h1>

        <?php if ($message != ""): ?>
            <p style="color: red; text-align: center; font-weight: bold; background: white; padding: 5px; border-radius: 5px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="inscription.php" method="post">
            <div class="input-box">
                <input type="text" name="nom" placeholder="Nom" required>
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="text" name="prenom" placeholder="Prénom" required>
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="e-mail" required>
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="input-box">
                <input type="text" name="adresse" placeholder="Adresse de livraison" required>
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="input-box">
                <input type="text" name="telephone" placeholder="Numéro de téléphone" required>
                <i class="fa-solid fa-phone"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" required> J'accepte les termes et conditions
                </label>
            </div>
            <button type="submit" class="login-btn">S'inscrire</button>
            <div class="register-link">
                <p>Déjà un compte ? <a href="formulaire.php">Se connecter</a></p>
            </div>
        </form>
    </section>

</body>
</html>