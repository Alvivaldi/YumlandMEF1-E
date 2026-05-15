<?php
session_start();

include 'includes/fonctions.php';

$plats = lireJSON('donnees/plats.json');

// Validation du cookie thème
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
    <!-- Thème dynamique : doit être AVANT carte.css pour que les variables soient définies -->
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
            <form class="search-bar">
                <input type="text" placeholder="Rechercher un plat..." />
                <button type="submit">🔍</button>
            </form>
        </section>
    </div>
    
    <?php 
        </div> <div class="barre-outils">
        <div class="filtre margFiltre">
            <div class="margFiltreTitre">
                <h2>Filtres : </h2>
            </div>
            
            <select id="sel-categorie" onchange="filtrerEtCharger()" class="select-filtre">
                <option value="tous">Toutes les catégories</option>
                <option value="specialite">Spécialités</option>
                <option value="entree">Entrées</option>
                <option value="plat">Plats</option>
                <option value="dessert">Desserts</option>
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
                <option value="tous">Aucun allergène à éviter</option>
                <option value="gluten">Sans Gluten</option>
                <option value="lactose">Sans Lactose</option>
            </select>

            <div class="margFiltreTitre" style="margin-top: 20px;">
                <h2>Tri : </h2>
            </div>
            <select id="sel-tri" onchange="appliquerTriLocal()" class="select-filtre">
                <option value="nom">Nom (A-Z)</option>
                <option value="prix-croissant">Prix croissant</option>
                <option value="prix-decroissant">Prix décroissant</option>
            </select>
        </div>

        <main class="affichage-produits">
            <div id="zone-plats" class="grille-plats-dynamique">
                </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="js/carte.js"></script>
</body>
</html>

    
    
    
   
    /* <div class="barre-outils">
        <div class="filtre margFiltre">
            <div class="margFiltreTitre">
                <h2>Filtres : </h2>
            </div>
            <a href="#specialites" class="flexBetween filtreTexteIcons">
                <div class="filtreIcons flexCentre"><i class="fa-solid fa-utensils fa-lg"></i></div>
                <div class="filtreTexte flexCentre">Type de plats</div>
            </a>
            <a href="#entrees" class="flexBetween filtreTexteIcons">
                <div class="filtreIcons flexCentre"><i class="fa-solid fa-bowl-food fa-lg"></i></div>
                <div class="filtreTexte flexCentre">Nos Formules</div>
            </a>
            <a href="#plats" class="flexBetween filtreTexteIcons">
                <div class="filtreIcons flexCentre"><i class="fa-solid fa-seedling fa-lg"></i></div>
                <div class="filtreTexte flexCentre">Veggie</div>
            </a>
            <a href="#boissons" class="flexBetween filtreTexteIcons">
                <div class="filtreIcons flexCentre"><i class="fa-solid fa-martini-glass-citrus fa-lg"></i></div>
                <div class="filtreTexte flexCentre">Boissons</div>
            </a>
            <div class="flexBetween filtreTexteIcons">
                <div class="filtreIcons flexCentre"><i class="fa-solid fa-ban fa-lg"></i></div>
                <div class="filtreTexte flexCentre">Allergènes</div>
            </div>
        </div>

       <section id="specialites" class="specialite">
            <h1>Specialité du moment </h1>
            <div class="specialite-list">
                <?php foreach ($plats as $plat): ?>
                <?php if ($plat['categorie'] === 'specialite'): ?>
                <div class="box">
                    <img src="<?= htmlspecialchars($plat['image']) ?>" alt="<?= htmlspecialchars($plat['nom']) ?>">
                    <div class="box-content">
                        <h3><?= htmlspecialchars($plat['nom']) ?></h3>
                        <p><?= htmlspecialchars($plat['description']) ?></p>
                        <span><?= number_format($plat['prix'], 2) ?> €</span>
                        <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id_plat" value="<?= $plat['id'] ?>">
                            <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                            <button type="submit"
                                style="cursor:pointer; background-color: #fca311; border: none; padding: 5px 10px; border-radius: 5px;">Ajouter</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="entrees" class="entree">
            <h1>Entrées</h1>
            <div class="entree-list">
                <?php foreach ($plats as $plat): ?>
                <?php if ($plat['categorie'] === 'entree'): ?>
                <div class="box1">
                    <img src="<?= htmlspecialchars($plat['image']) ?>" alt="<?= htmlspecialchars($plat['nom']) ?>">
                    <div class="box-content">
                        <h3><?= htmlspecialchars($plat['nom']) ?></h3>
                        <p><?= htmlspecialchars($plat['description']) ?></p>
                        <span><?= number_format($plat['prix'], 2) ?> €</span>
                        <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id_plat" value="<?= $plat['id'] ?>">
                            <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                            <button type="submit"
                                style="cursor:pointer; background-color: #fca311; border: none; padding: 5px 10px; border-radius: 5px;">Ajouter</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="plats" class="plat">
            <h1>Plats</h1>
            <div class="plat-list">
                <?php foreach ($plats as $plat): ?>
                <?php if ($plat['categorie'] === 'plat'): ?>
                <div class="box1">
                    <img src="<?= htmlspecialchars($plat['image']) ?>" alt="<?= htmlspecialchars($plat['nom']) ?>">
                    <div class="box-content">
                        <h3><?= htmlspecialchars($plat['nom']) ?></h3>
                        <p><?= htmlspecialchars($plat['description']) ?></p>
                        <span><?= number_format($plat['prix'], 2) ?> €</span>
                        <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id_plat" value="<?= $plat['id'] ?>">
                            <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                            <button type="submit"
                                style="cursor:pointer; background-color: #fca311; border: none; padding: 5px 10px; border-radius: 5px;">Ajouter</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="desserts" class="dessert">
            <h1>Desserts</h1>
            <div class="dessert-list">
                <?php foreach ($plats as $plat): ?>
                <?php if ($plat['categorie'] === 'dessert'): ?>
                <div class="box1">
                    <img src="<?= htmlspecialchars($plat['image']) ?>" alt="<?= htmlspecialchars($plat['nom']) ?>">
                    <div class="box-content">
                        <h3><?= htmlspecialchars($plat['nom']) ?></h3>
                        <p><?= htmlspecialchars($plat['description']) ?></p>
                        <span><?= number_format($plat['prix'], 2) ?> €</span>
                        <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id_plat" value="<?= $plat['id'] ?>">
                            <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                            <button type="submit"
                                style="cursor:pointer; background-color: #fca311; border: none; padding: 5px 10px; border-radius: 5px;">Ajouter</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="boissons" class="boisson">
            <h1>Boissons </h1>
            <div class="boisson-list">
                <?php foreach ($plats as $plat): ?>
                <?php if ($plat['categorie'] === 'boisson'): ?>
                <div class="box2">
                    <img src="<?= htmlspecialchars($plat['image']) ?>" alt="<?= htmlspecialchars($plat['nom']) ?>">
                    <div class="box-content">
                        <h3><?= htmlspecialchars($plat['nom']) ?></h3>
                        <p><?= htmlspecialchars($plat['description']) ?></p>
                        <span><?= number_format($plat['prix'], 2) ?> €</span>
                        <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id_plat" value="<?= $plat['id'] ?>">
                            <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                            <button type="submit"
                                style="cursor:pointer; background-color: #fca311; border: none; padding: 5px 10px; border-radius: 5px;">Ajouter</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
*/ ?>
