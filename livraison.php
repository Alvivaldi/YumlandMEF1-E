<?php
session_start();
require_once 'includes/fonctions.php';

// Validation du cookie thème
$valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
$cookie_val  = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
$theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'livreur') {
    header("Location: formulaire.php");
    exit();
}

$id_livreur   = (string) $_SESSION['user']['id'];
$message_status = "";

// --- Traitement du formulaire POST ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'], $_POST['id_commande'])) {
    $id_cmd_a_modifier = $_POST['id_commande'];
    $nouveau_statut    = ($_POST['action'] === 'terminer') ? 'LIVREE' : 'ABANDONNEE';

    $commandes = lireJSON('donnees/commandes.json');
    foreach ($commandes as &$cmd) {
        if ((string)$cmd['id_commande'] === (string)$id_cmd_a_modifier) {
            $cmd['statut']           = $nouveau_statut;
            $cmd['date_livraison']   = date("d/m/Y H:i"); // horodatage réel
            break;
        }
    }
    unset($cmd);
    ecrireJSON('donnees/commandes.json', $commandes);

    $libelle = ($nouveau_statut === 'LIVREE') ? '✅ marquée comme livrée' : '❌ abandonnée';
    $message_status = "Commande $id_cmd_a_modifier $libelle.";
}

// --- Récupération des commandes du livreur connecté ---
// On accepte les deux formats de statut présents dans le JSON
$statuts_en_cours = ['en_livraison', 'en livraison'];

$all_commandes = lireJSON('donnees/commandes.json');
$mes_commandes = [];
foreach ($all_commandes as $cmd) {
    $statut_normalise = strtolower(str_replace('_', ' ', $cmd['statut'] ?? ''));
    $livreur_ok       = isset($cmd['id_livreur']) && (string)$cmd['id_livreur'] === $id_livreur;
    if ($livreur_ok && in_array($statut_normalise, ['en livraison'])) {
        $mes_commandes[] = $cmd;
    }
}

$utilisateurs = lireJSON('donnees/utilisateurs.json');
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mes Livraisons</title>
    <link rel="stylesheet" id="dynamic-theme" href="<?php echo htmlspecialchars($theme_actif); ?>">
    <link rel="stylesheet" href="css/livraison.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="livraison-container">
        <h1>Mes livraisons en cours</h1>

        <?php if (!empty($message_status)): ?>
        <p class="message-status"><?php echo htmlspecialchars($message_status); ?></p>
        <?php endif; ?>

        <?php if (!empty($mes_commandes)): ?>
        <?php foreach ($mes_commandes as $cmd):
                // Trouver les infos client
                $client = null;
                foreach ($utilisateurs as $u) {
                    if ((string)$u['id'] === (string)$cmd['id_client']) {
                        $client = $u;
                        break;
                    }
                }
            ?>
        <div class="carte-livraison">

            <div class="carte-header">
                <span class="cmd-id">
                    <i class="fa-solid fa-box"></i>
                    Commande #<?php echo htmlspecialchars($cmd['id_commande']); ?>
                </span>
                <span class="cmd-date">
                    <i class="fa-regular fa-clock"></i>
                    <?php echo htmlspecialchars($cmd['date_creation']); ?>
                </span>
            </div>

            <?php if ($client): ?>
            <div class="info-livraison">
                <div class="field">
                    <span><i class="fa-solid fa-user"></i> Client</span>
                    <span><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></span>
                </div>
                <div class="field">
                    <span><i class="fa-solid fa-location-dot"></i> Adresse</span>
                    <span><?php echo htmlspecialchars($client['adresse']); ?></span>
                </div>
                <div class="field">
                    <span><i class="fa-solid fa-phone"></i> Téléphone</span>
                    <span>
                        <a href="tel:<?php echo htmlspecialchars($client['telephone']); ?>">
                            <?php echo htmlspecialchars($client['telephone']); ?>
                        </a>
                    </span>
                </div>
            </div>
            <?php endif; ?>

            <div class="produits-list">
                <strong><i class="fa-solid fa-utensils"></i> Contenu de la commande :</strong>
                <ul>
                    <?php foreach ($cmd['produits'] as $p): ?>
                    <li><?php echo htmlspecialchars($p['nom']); ?> × <?php echo (int)$p['quantite']; ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="prix-total">
                    Total : <strong><?php echo number_format($cmd['prix_total'], 2); ?> €</strong>
                </div>
            </div>

            <div class="actions">
                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($client['adresse'] ?? ''); ?>"
                    target="_blank" class="btn nav-btn">
                    <i class="fa-solid fa-map-location-dot"></i> Voir sur Maps
                </a>
                <form method="POST" class="form-actions">
                    <input type="hidden" name="id_commande"
                        value="<?php echo htmlspecialchars($cmd['id_commande']); ?>">
                    <button type="submit" name="action" value="terminer" class="btn complete-btn">
                        <i class="fa-solid fa-circle-check"></i> Livrée
                    </button>
                    <button type="submit" name="action" value="abandonner" class="btn abandon-btn"
                        onclick="return confirm('Confirmer l\'abandon de cette livraison ?')">
                        <i class="fa-solid fa-circle-xmark"></i> Abandonner
                    </button>
                </form>
            </div>

        </div>
        <?php endforeach; ?>

        <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-truck fa-3x"></i>
            <p>Aucune livraison en cours pour vous actuellement.</p>
        </div>
        <?php endif; ?>

    </div>
</body>

</html>