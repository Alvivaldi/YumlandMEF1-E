<?php
include 'includes/fonctions.php';

$commandes_donnees = lireJSON('donnees/commandes.json');
if (!is_array($commandes_donnees)) { $commandes_donnees = []; }

$utilisateurs_donnees = lireJSON('donnees/utilisateurs.json');
if (!is_array($utilisateurs_donnees)) { $utilisateurs_donnees = []; }

// Création d'un dictionnaire pour retrouver facilement les infos du client via son id_client
$users_lookup = [];
foreach ($utilisateurs_donnees as $u) {
    if (isset($u['id'])) {
        $users_lookup[$u['id']] = $u;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commandes</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/commandes.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body class="admin-body">
    <?php include 'includes/header.php'; ?>

    <header>
        <h1 class="big-title">Commandes & Livraison</h1>
    </header>

    <main class="restaurateur-main">
        <div class="stats-container">
            <div class="box1-content admin-card">
                <h3>À cuisiner</h3>
                <p class="stat-number">Commandes en attente...</p>
            </div>
            <div class="box1-content admin-card" style="background-color: #d23508;">
                <h2 class="entree" style="color: white;">🚚 En cours de livraison</h2>
                <p class="stat-number entree" style="color: white;">Suivi</p>
            </div>
        </div>

        <div class="commandes-grid">
            <section class="column-prepa">
                <h2 class="entree">🔥 À Préparer</h2>

                <?php foreach ($commandes_donnees as $cmd): 
                    $statut = $cmd['statut'] ?? '';
                    
                    // On ne garde que les statuts du nouveau format
                    if ($statut === 'A_PREPARER' || $statut === 'EN_PREPARATION' || $statut === 'EN_ATTENTE'): 
                        
                        // Récupération des infos du NOUVEAU format
                        $date_creation = $cmd['date_creation'] ?? '';
                        $timing = $cmd['timing'] ?? 'immediat';
                        $date_prevue = $cmd['date_livraison_prevue'] ?? '';
                        $prix = $cmd['prix_total'] ?? 0;
                        $produits = $cmd['produits'] ?? [];
                        $id_client = $cmd['id_client'] ?? 0;
                        
                        // Récupération exacte des infos client depuis utilisateurs.json
                        $nom_client = "Client Inconnu";
                        $adresse = "Adresse non renseignée";
                        
                        if (isset($users_lookup[$id_client])) {
                            $client_trouve = $users_lookup[$id_client];
                            $nom_client = htmlspecialchars(($client_trouve['prenom'] ?? '') . " " . ($client_trouve['nom'] ?? ''));
                            $adresse = htmlspecialchars($client_trouve['adresse'] ?? 'Sur place');
                        }
                ?>
                        <div class="box-commande" <?php if($timing === 'plus_tard') echo 'style="border-left-color: #fca311;"'; ?>>
                            <div class="commande-header">
                                <span class="num-commande">#<?php echo htmlspecialchars($cmd['id_commande']); ?></span>
                                <span class="heure"><?php echo htmlspecialchars($date_creation); ?></span>
                            </div>
                            
                            <?php if($timing === 'plus_tard'): ?>
                                <div style="background: #fff8eb; color: #e59400; padding: 5px; border-radius: 5px; margin-bottom: 10px; font-weight: bold; text-align: center;">
                                    ⏳ Pour plus tard : <?php echo htmlspecialchars($date_prevue); ?>
                                </div>
                            <?php endif; ?>

                            <div class="commande-details">
                                <ul>
                                    <?php foreach ($produits as $article): ?>
                                        <li>
                                            <?php echo htmlspecialchars($article['quantite'] ?? 1); ?>x 
                                            <?php echo htmlspecialchars($article['nom'] ?? 'Produit'); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="commande-infos-sup">
                                <p><strong>Client :</strong> <?php echo $nom_client; ?></p>
                                <p><strong>Adresse :</strong> <?php echo $adresse; ?></p>
                                <p><strong>Montant :</strong> <?php echo htmlspecialchars($prix); ?> €</p>
                            </div>

                            <div class="gestion-statut">
                                <label>Statut :</label>
                                <select class="select-admin">
                                    <option value="A_PREPARER" <?php echo ($statut == 'A_PREPARER') ? 'selected' : ''; ?>>À préparer</option>
                                    <option value="EN_ATTENTE" <?php echo ($statut == 'EN_ATTENTE') ? 'selected' : ''; ?>>En attente (Plus tard)</option>
                                    <option value="EN_PREPARATION" <?php echo ($statut == 'EN_PREPARATION') ? 'selected' : ''; ?>>En préparation</option>
                                </select>
                            </div>

                            <div class="gestion-livreur">
                                <label>Livreur :</label>
                                <select class="select-admin">
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
                            <button class="btn-action-status" style="margin-top:10px;">Mettre à jour</button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="column-livraison">
                <h2 class="entree" style="color: #010000;">🚚 En cours de livraison</h2>

                <?php foreach ($commandes_donnees as $cmd): 
                    $statut = $cmd['statut'] ?? '';
                    if ($statut === 'EN_LIVRAISON'): 
                        
                        $id_client = $cmd['id_client'] ?? 0;
                        $prix = $cmd['prix_total'] ?? 0;
                        
                        $nom_client = "Client Inconnu";
                        $adresse = "Adresse non renseignée";
                        
                        if (isset($users_lookup[$id_client])) {
                            $client_trouve = $users_lookup[$id_client];
                            $nom_client = htmlspecialchars(($client_trouve['prenom'] ?? '') . " " . ($client_trouve['nom'] ?? ''));
                            $adresse = htmlspecialchars($client_trouve['adresse'] ?? 'Sur place');
                        }
                ?>
                        <div class="box-commande delivery-mode">
                            <div class="commande-header">
                                <span class="num-commande">#<?php echo htmlspecialchars($cmd['id_commande']); ?></span>
                                <span class="badge livreur">Livreur : <?php echo htmlspecialchars($cmd['livreur_attribue'] ?? 'Non assigné'); ?></span>
                            </div>
                            <div class="commande-details">
                                <p><strong>Client :</strong> <?php echo $nom_client; ?></p>
                                <p><strong>Destination :</strong> <?php echo $adresse; ?></p>
                                <p><strong>Montant :</strong> <?php echo htmlspecialchars($prix); ?> €</p>
                            </div>
                            <button class="btn-secondary" disabled>En cours de livraison...</button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        </div>
    </main>

</body>

</html>