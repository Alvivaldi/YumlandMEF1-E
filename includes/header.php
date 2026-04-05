<nav class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="Logo" />
        </a>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="carte.php">Menu</a></li>
        
        <?php 
        // SI L'UTILISATEUR EST CONNECTÉ
        if (isset($_SESSION['user'])): 
        ?>
            <li><a href="panier.php"><i class="fa-solid fa-basket-shopping"></i> Mon Panier</a></li>
            <li><a href="profil.php">Mon profil</a></li>
            
            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php elseif($_SESSION['user']['role'] === 'restaurateur'): ?>
                <li><a href="commandes.php">Commandes</a></li>
            <?php elseif($_SESSION['user']['role'] === 'livreur'): ?>
                <li><a href="livraison.php">Livraisons</a></li>
            <?php endif; ?>
            
            <li><a href="deconnexion.php" style="color: #d23508; font-weight: bold;">Déconnexion</a></li>
            
        <?php 
        // SI L'UTILISATEUR N'EST PAS CONNECTÉ
        else: 
        ?>
            <li><a href="formulaire.php">Se Connecter</a></li>
            <li><a href="inscription.php">S'inscrire</a></li>
        <?php endif; ?>
    </ul>
</nav>