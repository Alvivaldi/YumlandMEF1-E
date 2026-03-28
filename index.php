<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tradimiam</title>
    <link rel="stylesheet" href="css/accueil.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
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