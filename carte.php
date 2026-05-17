<?php
session_start();

include 'includes/fonctions.php';

$plats = lireJSON('donnees/plats.json');


$valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
$cookie_val  = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
$theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Carte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" id="dynamic-theme" href="<?php echo htmlspecialchars($theme_actif); ?>">
    <link rel="stylesheet" type="text/css" href="css/carte.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="header-carte">
        <h1 class="titre-carte">Notre Carte</h1>
        <section class="recherche">
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Rechercher un plat..." oninput="filtrerEtCharger()" />
                <button type="button">🔍</button>
            </div>
        </section>
    </div>
    
    <div class="zone-filtres-haut">
        <select id="sel-categorie" onchange="filtrerEtCharger()" class="select-filtre">
            <option value="tous">Toutes les catégories</option>
            <option value="specialite">Spécialités</option>
            <option value="entree">Entrées</option>
            <option value="plat">Plats</option>
            <option value="dessert">Desserts</option>
            <option value="formule">Nos Formules</option>
            <option value="boisson">Boissons</option>
        </select>

        <select id="sel-regime" onchange="filtrerEtCharger()" class="select-filtre">
            <option value="tous">Tous les régimes</option>
            <option value="végétarien">Végétarien</option>
            <option value="vegan">Vegan</option>
            <option value="halal">Halal</option>
            <option value="sans gluten">Sans Gluten</option>
        </select>

        <select id="sel-saveur" onchange="filtrerEtCharger()" class="select-filtre">
            <option value="tous">Toutes les saveurs</option>
            <option value="salé">Salé</option>
            <option value="sucré">Sucré</option>
            <option value="épicé">Épicé</option>
        </select>

        <select id="sel-allergene" onchange="filtrerEtCharger()" class="select-filtre">
            <option value="tous">Allergènes</option>
            <option value="gluten">Sans Gluten</option>
            <option value="lactose">Sans Lactose</option>
        </select>

        <select id="sel-tri" onchange="appliquerTriLocal()" class="select-filtre">
            <option value="nom">Prix</option>
            <option value="prix-croissant">Prix croissant</option>
            <option value="prix-decroissant">Prix décroissant</option>
        </select>
    </div>

    <main class="affichage-produits">
        <div id="zone-plats" class="grille-plats-dynamique">
            </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/carte.js"></script>
</body>
</html>