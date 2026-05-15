<?php
session_start();

// Validation du cookie : seules deux valeurs sont autorisées
$valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
$cookie_val  = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
$theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tradimiam</title>

    <!--
        IMPORTANT : c'est ici et seulement ici que le <link id="dynamic-theme"> est déclaré.
        header.php NE doit PAS en contenir un second, sinon JavaScript cible le premier
        (dans le <head>) mais le second (dans le <body>) continue d'appliquer l'ancien thème.
    -->
    <link rel="stylesheet" id="dynamic-theme" href="<?php echo htmlspecialchars($theme_actif); ?>">

    <link rel="stylesheet" href="css/accueil.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>TradiMiam</h1>
            <p>Les papilles aussi ont le droit de voyager</p>
        </div>
    </section>

    <section class="recherche">
        <form class="search-bar">
            <input type="text" placeholder="Rechercher un plat..." />
            <button type="submit">🔍</button>
        </form>
    </section>

    <section class="menu-container">
        <div class="menu-box">
            <h2>Menu Fréquent</h2>
            <h3>Entrées</h3>
            <p>Alloco</p>
            <p>Beignets</p>
            <h3>Plats</h3>
            <p>Biryani</p>
            <p>Butter Chicken</p>
            <h3>Desserts</h3>
            <p>Eclair</p>
            <p>Tarte Tatin</p>
        </div>

        <div class="menu-box">
            <h2>Plat du Jour</h2>
            <h3>Entrées</h3>
            <p>Pastel</p>
            <h3>Plat</h3>
            <p>Masala Dosa</p>
            <h3>Dessert</h3>
            <p>Mousse au Chocolat</p>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>