<?php
// On inclut la bibliothèque de fonctions créée à l'étape 1
include 'lib/fonctions.php';

$message = ""; // Pour afficher des erreurs ou succès à l'utilisateur

// Vérifie si le formulaire a été envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Récupération des données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $tel = $_POST['telephone'];
    $password = $_POST['password'];

    // 2. Chargement des utilisateurs existants
    $utilisateurs = lireDonnees('utilisateurs.json');

    // 3. Vérification si l'email existe déjà
    $existe = false;
    foreach ($utilisateurs as $user) {
        if ($user['login'] === $email) {
            $existe = true;
            break;
        }
    }

    if ($existe) {
        $message = "Cet email est déjà utilisé.";
    } else {
        // 4. Création du nouvel utilisateur avec mot de passe haché
        $nouvelUser = [
            "id" => time(), // Génère un ID unique simple basé sur l'heure
            "login" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT), // Sécurité obligatoire
            "role" => "client", // Par défaut, un inscrit est un client 
            "nom" => $nom,
            "prenom" => $prenom,
            "adresse" => $adresse,
            "telephone" => $tel,
            "date_inscription" => date("Y-m-d")
        ];

        // 5. Ajout et sauvegarde
        $utilisateurs[] = $nouvelUser;
        sauvegarderDonnees('utilisateurs.json', $utilisateurs);
        
        // Redirection vers la connexion après succès
        header("Location: formulaire.php?success=1");
        exit();
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


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />


</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/logo.png" alt="Logo" />
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="carte.php">Menu</a></li>
            <li><a href="profil.php">Mon profil</a></li>
        </ul>
    </nav>

    <div class="background">
        <div class="slide slide1"></div>
        <div class="slide slide2"></div>
        <div class="slide slide3"></div>
    </div>
    <section class="form-section">
        <h1>Inscription</h1>

        <?php if($message != ""): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>

    <form action="inscription.php" method="post">
        <div class="input-box">
            <input type="text" name="nom" placeholder="Nom">
            <i class="fa-solid fa-user"></i>
        </div>
        <div class="input-box">
            <input type="text" name="prenom" placeholder="Prénom">
            <i class="fa-solid fa-user"></i>
        </div>
        <div class="input-box">
            <input type="text" name="email" placeholder="e-mail">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="input-box">
            <input type="text" name="address" placeholder="Adresse de livraison">
            <i class="fa-solid fa-location-dot"></i>
        </div>
        <div class="input-box">
            <input type="text" name="phone" placeholder="Numéro de téléphone">
            <i class="fa-solid fa-phone"></i>
        </div>
        <div class="input-box">
            <input type="password" name="password" placeholder="Mot de passe">
            <i class="fa-solid fa-lock"></i>
        </div>
        <div class="remember-forgot">
            <label>
                <input type="checkbox"> J'accepte les termes et conditions
            </label>
        </div>
        <button type="submit" class="login-btn">S'inscrire</button>
        <div class=" register-link">
            <p>Déjà un compte ? <a href="formulaire.html">Se connecter</a></p>
            </div>


    </section>

</body>

</html>