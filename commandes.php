<?php

session_start();
require_once 'includes/fonctions.php';


$valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
$cookie_val  = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
$theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';



if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'restaurateur' && $_SESSION['user']['role'] !=='admin')) {
header("Location: formulaire.php");
exit();
}

$commandes_donnees = lireJSON('donnees/commandes.json');
if (!is_array($commandes_donnees)) {
$commandes_donnees = [];
}

$utilisateurs_donnees = lireJSON('donnees/utilisateurs.json');
if (!is_array($utilisateurs_donnees)) {
$utilisateurs_donnees = [];
}


$users_lookup = [];
foreach ($utilisateurs_donnees as $u) {
if (isset($u['id'])) {
$users_lookup[$u['id']] = $u;
}
}


//  calcul CA et meilleur plat
$stats_ca_total = 0;
$stats_nb_commandes = count($commandes_donnees);
$compteur_plats = [];

foreach ($commandes_donnees as $cmd) {

    if (isset($cmd['prix_total'])) {
        $stats_ca_total += (float)$cmd['prix_total'];
    }

   
    if (isset($cmd['produits']) && is_array($cmd['produits'])) {
        foreach ($cmd['produits'] as $produit) {
            $nom_plat = $produit['nom'] ?? 'Inconnu';
            $qte = (int)($produit['quantite'] ?? 1);
            
            if (!isset($compteur_plats[$nom_plat])) {
                $compteur_plats[$nom_plat] = 0;
            }
            $compteur_plats[$nom_plat] += $qte;
        }
    }
}


$best_seller = "Aucun plat vendu";
if (!empty($compteur_plats)) {
    arsort($compteur_plats); //du plus grand au plus petit
    $best_seller = array_key_first($compteur_plats); 
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Commandes - Restaurateur</title>
    <link rel="stylesheet" id="dynamic-theme" href="<?php echo htmlspecialchars($theme_actif); ?>">

    <link rel="stylesheet" href="css/commandes.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body class="admin-body">
    <?php include 'includes/header.php'; ?>

    <header>
        <h1 class="big-title">Tableau de Bord Restaurateur</h1>
    </header>

    <main class="restaurateur-main">
        <div class="stats-dashboard">
            <div class="stat-box">
                <i class="fa-solid fa-chart-line"></i>
                <div class="stat-info">
                    <span class="stat-titre">Chiffre d'Affaires</span>
                    <span class="stat-valeur"><?php echo number_format($stats_ca_total, 2, ',', ' '); ?> €</span>
                </div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-receipt"></i>
                <div class="stat-info">
                    <span class="stat-titre">Commandes Totales</span>
                    <span class="stat-valeur"><?php echo $stats_nb_commandes; ?></span>
                </div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-crown"></i>
                <div class="stat-info">
                    <span class="stat-titre">Best-Seller</span>
                    <span class="stat-valeur" style="font-size: 1.2rem;"><?php echo htmlspecialchars($best_seller); ?></span>
                </div>
            </div>
        </div>
        <div class="commandes-grid">
            <section class="column-prepa">
                <h2 class="entree">🔥 Commandes en Cuisine</h2>

                <?php foreach ($commandes_donnees as $cmd):
                    $statut = $cmd['statut'] ?? '';
                    if (in_array($statut, [
                        'PAYEE',
                        'A_PREPARER',
                        'EN_PREPARATION',
                        'PRETE'
                    ])):
                        $id_client = $cmd['id_client'] ?? 0;
                        $nom_client = "Client Inconnu";
                        $adresse = "Adresse non renseignée";

                        if (isset($users_lookup[$id_client])) {
                            $client_trouve = $users_lookup[$id_client];
                            $nom_client = htmlspecialchars(($client_trouve['prenom'] ?? '') . " " . ($client_trouve['nom'] ?? ''));
                            $adresse = htmlspecialchars($client_trouve['adresse'] ?? 'Sur place');
                        }
                ?>
                <div class="box-commande" id="commande-<?php echo $cmd['id_commande']; ?>">
                    <div class="commande-form">
                        <input type="hidden" name="id_commande_hidden"
                            value="<?php echo htmlspecialchars($cmd['id_commande']); ?>">

                        <div class="commande-header">

                            <span class="num-commande">
                                #<?php echo htmlspecialchars($cmd['id_commande']); ?>
                            </span>

                            <p class="badge-statut" id="badge-<?php echo $cmd['id_commande']; ?>">
                                <?php echo htmlspecialchars($statut); ?>
                            </p>

                        </div>

                        <div class="commande-details">
                            <ul>
                                <?php foreach (($cmd['produits'] ?? []) as $article): ?>
                                <li><?php echo htmlspecialchars($article['quantite'] ?? 1); ?>x
                                    <?php echo htmlspecialchars($article['nom'] ?? 'Produit'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="commande-infos-sup">
                            <p><strong>Client :</strong> <?php echo $nom_client; ?></p>
                            <p><strong>Adresse :</strong> <?php echo $adresse; ?></p>
                        </div>

                        <div class="gestion-statut">
                            <label>Changer statut :</label>
                            <select name="nouveau_statut" class="select-admin"
                                id="statut-<?php echo $cmd['id_commande']; ?>">
                                <?php if ($statut == 'PAYEE'): ?>

                                <option value="EN_PREPARATION">
                                    Passer en préparation
                                </option>

                                <?php elseif ($statut == 'EN_PREPARATION'): ?>

                                <option value="PRETE">
                                    Marquer comme prête
                                </option>

                                <?php elseif ($statut == 'PRETE'): ?>

                                <option value="EN_LIVRAISON">
                                    Envoyer en livraison
                                </option>

                                <?php endif; ?>

                            </select>
                        </div>

                        <div class="gestion-livreur">
                            <label>Attribuer Livreur :</label>
                            <select name="id_livreur" class="select-admin"
                                id="livreur-<?php echo $cmd['id_commande']; ?>">
                                <option value="">-- Choisir un livreur --</option>
                                <?php foreach ($utilisateurs_donnees as $u): ?>
                                <?php if (strtolower($u['role'] ?? '') === 'livreur'): ?>
                                <option value="<?php echo htmlspecialchars($u['id']); ?>">
                                    <?php echo htmlspecialchars(($u['prenom'] ?? '') . " " . ($u['nom'] ?? '')); ?>
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="button" class="btn-update-commande" data-id="<?php echo $cmd['id_commande']; ?>">
                            Valider
                        </button>
                    </div>
                </div>
                <?php endif;
                endforeach; ?>
            </section>

            <section class="column-livraison">
                <h2 class="entree" style="color: #d23508;">🚚 Suivi des Livreurs</h2>
                <?php foreach ($commandes_donnees as $cmd):
                    if (($cmd['statut'] ?? '') === 'EN_LIVRAISON'):
                ?>
                <div class="box-commande delivery-mode">
                    <div class="commande-header">
                        <span class="num-commande">#<?php echo htmlspecialchars($cmd['id_commande']); ?></span>
                    </div>
                    <p><strong>Livreur :</strong> ID
                        #<?php echo htmlspecialchars($cmd['id_livreur'] ?? 'Non assigné'); ?></p>
                    <button class="btn-secondary" disabled>Course en cours...</button>
                </div>
                <?php endif;
                endforeach; ?>
            </section>
        </div>
    </main>
    <script>
    document.querySelectorAll('.btn-update-commande').forEach(button => {

        button.addEventListener('click', function() {

            const idCommande = this.dataset.id;

            const statut =
                document.getElementById(`statut-${idCommande}`).value;

            const livreur =
                document.getElementById(`livreur-${idCommande}`).value;

            if (statut === 'EN_LIVRAISON' && livreur === '') {

                alert("Choisissez un livreur.");

                return;
            }

            const params = new URLSearchParams();

            params.append('id_commande', idCommande);
            params.append('nouveau_statut', statut);
            params.append('id_livreur', livreur);

            fetch('update_commande.php', {

                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },

                    body: params

                })

                .then(response => response.json())

                .then(data => {

                    if (data.success) {

                        const badge =
                            document.getElementById(`badge-${idCommande}`);

                        badge.textContent = data.nouveau_statut;

                        badge.classList.add('updated');

                        setTimeout(() => {
                            badge.classList.remove('updated');
                        }, 600);

                    } else {

                        alert("Erreur.");

                    }

                });

        });

    });
    </script>
</body>

</html>