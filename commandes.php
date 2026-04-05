<?php
session_start();
require_once 'includes/fonctions.php';

// Sécurité : Seul le restaurateur (ou l'admin) peut accéder à cette page
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'restaurateur' && $_SESSION['user']['role'] !== 'admin')) {
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

// --- LOGIQUE DE MISE À JOUR (NOUVEAU) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_commande'])) {
    $id_cmd_to_update = $_POST['id_commande_hidden'];
    $nouveau_statut = $_POST['nouveau_statut'];
    $id_livreur = $_POST['id_livreur'];

    foreach ($commandes_donnees as &$cmd) {
        if ($cmd['id_commande'] === $id_cmd_to_update) {
            $cmd['statut'] = $nouveau_statut;
            if (!empty($id_livreur)) {
                $cmd['id_livreur'] = $id_livreur; // On enregistre l'ID numérique pour ton code de livraison
            }
            break;
        }
    }
    ecrireJSON('donnees/commandes.json', $commandes_donnees);
    // Rafraîchir pour voir les changements
    header("Location: commandes.php");
    exit();
}

// Création d'un dictionnaire pour retrouver facilement les infos du client
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
    <title>Gestion des Commandes - Restaurateur</title>
    <link rel="stylesheet" href="css/global.css">
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
        <div class="commandes-grid">
            <section class="column-prepa">
                <h2 class="entree">🔥 Commandes en Cuisine</h2>

                <?php foreach ($commandes_donnees as $cmd):
                    $statut = $cmd['statut'] ?? '';
                    if (in_array($statut, ['A_PREPARER', 'EN_PREPARATION', 'EN_ATTENTE'])):
                        $id_client = $cmd['id_client'] ?? 0;
                        $nom_client = "Client Inconnu";
                        $adresse = "Adresse non renseignée";

                        if (isset($users_lookup[$id_client])) {
                            $client_trouve = $users_lookup[$id_client];
                            $nom_client = htmlspecialchars(($client_trouve['prenom'] ?? '') . " " . ($client_trouve['nom'] ?? ''));
                            $adresse = htmlspecialchars($client_trouve['adresse'] ?? 'Sur place');
                        }
                ?>
                        <div class="box-commande">
                            <form method="POST" action="commandes.php">
                                <input type="hidden" name="id_commande_hidden"
                                    value="<?php echo htmlspecialchars($cmd['id_commande']); ?>">

                                <div class="commande-header">
                                    <span class="num-commande">#<?php echo htmlspecialchars($cmd['id_commande']); ?></span>
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
                                    <select name="nouveau_statut" class="select-admin">
                                        <option value="A_PREPARER" <?php if ($statut == 'A_PREPARER') echo 'selected'; ?>>À
                                            préparer</option>
                                        <option value="EN_PREPARATION"
                                            <?php if ($statut == 'EN_PREPARATION') echo 'selected'; ?>>En préparation</option>
                                        <option value="EN_LIVRAISON">Prêt (Envoyer en livraison)</option>
                                    </select>
                                </div>

                                <div class="gestion-livreur">
                                    <label>Attribuer Livreur :</label>
                                    <select name="id_livreur" class="select-admin">
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

                                <button type="submit" name="update_commande" class="btn-action-status">Valider les
                                    modifications</button>
                            </form>
                        </div>
                <?php endif;
                endforeach; ?>
            </section>

            <section class="column-livraison">
                <h2 class="entree" style="color: #d23508;">🚚 Suivi des Livreurs</h2>
                <?php foreach ($commandes_donnees as $cmd):
                    if (strtolower($cmd['statut'] ?? '') === 'EN_LIVRAISON'):
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
</body>

</html>