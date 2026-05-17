<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/fonctions.php';


$utilisateurs = lireJSON('donnees/utilisateurs.json');

foreach ($utilisateurs as $u) {
    if (isset($_SESSION['user']) && $u['id'] == $_SESSION['user']['id']) {
        if (($u['statut'] ?? 'actif') === 'bloqué') {
            session_unset();
            session_destroy();
            header("Location: formulaire.php?erreur=compte_bloque");
            exit();
        }
    }
}
?>

<nav class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="Logo Tradimiam" />
        </a>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <?php if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['livreur', 'restaurateur', 'admin'])): ?>
        <li><a href="carte.php">Menu</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
        <?php if (!in_array($_SESSION['user']['role'] ?? '', ['livreur', 'restaurateur', 'admin'])): ?>
        <li><a href="panier.php"><i class="fa-solid fa-basket-shopping"></i> Mon Panier</a></li>
        <?php endif; ?>
        <li><a href="profil.php">Mon profil</a></li>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <li><a href="admin.php">Admin</a></li>
        <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
        <li><a href="commandes.php">Commandes</a></li>
        <?php elseif ($_SESSION['user']['role'] === 'livreur'): ?>
        <li><a href="livraison.php">Livraisons</a></li>
        <?php endif; ?>

        <li><a href="deconnexion.php" style="color: #d23508; font-weight: bold;">Déconnexion</a></li>

        <?php else: ?>
        <li><a href="formulaire.php">Se Connecter</a></li>
        <li><a href="inscription.php">S'inscrire</a></li>
        <?php endif; ?>

        <li>
            <?php
            
            $valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
            $cookie_val = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
            $theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';
            ?>
            <button id="theme-switch" style="background: none; border: 1px solid white; color: white; cursor: pointer;
                       padding: 8px 12px; border-radius: 5px; font-family: 'Chewy', cursive;
                       font-size: clamp(16px, 1.5vw, 22px); white-space: nowrap; flex-shrink: 0;">
                <?php echo ($theme_actif === 'css/accessible.css') ? '☀️ Mode Clair' : '🌓 Mode Accessible'; ?>
            </button>
        </li>
    </ul>
</nav>

<script src="js/theme_switcher.js"></script>