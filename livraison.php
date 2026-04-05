<?php
session_start();
require_once 'includes/fonctions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'livreur') {
  header("Location: formulaire.php");
  exit();
}

// --- INITIALISATION CRUCIALE ---
$id_livreur = $_SESSION['user']['id'];
$message_status = "";
$mes_commandes = []; // On initialise le tableau vide ICI pour éviter le Warning
$client_info = null;

// 2. Traitement des actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
  $id_cmd_a_modifier = $_POST['id_commande'];
  $nouveau_statut = ($_POST['action'] === 'terminer') ? 'LIVREE' : 'ABANDONNEE';

  $commandes = lireJSON('donnees/commandes.json');
  foreach ($commandes as &$cmd) {
    if ($cmd['id_commande'] == $id_cmd_a_modifier) {
      $cmd['statut'] = $nouveau_statut;
      break;
    }
  }
  ecrireJSON('donnees/commandes.json', $commandes);
  $message_status = "La commande $id_cmd_a_modifier a été marquée comme $nouveau_statut.";
}

// 3. Récupération
$all_commandes = lireJSON('donnees/commandes.json');
foreach ($all_commandes as $cmd) {
  // On utilise strtolower pour être sûr de trouver "en livraison" peu importe la casse
  if (isset($cmd['id_livreur']) && $cmd['id_livreur'] == $id_livreur && strtolower($cmd['statut'] ?? '') === 'EN_LIVRAISON') {
    $mes_commandes[] = $cmd;
  }
}
?>




<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Livraison</title>
  <link rel="stylesheet" href="css/livraison.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <div class="livraison-container">
    <h1>Mes livraisons en cours</h1>

    <?php if (!empty($mes_commandes)): ?>
      <?php
      $utilisateurs = lireJSON('donnees/utilisateurs.json');
      foreach ($mes_commandes as $ma_commande):
        // On cherche les infos du client pour CETTE commande
        $client_info = null;
        foreach ($utilisateurs as $u) {
          if ($u['id'] == $ma_commande['id_client']) {
            $client_info = $u;
            break;
          }
        }
      ?>
        <div class="info-livraison"
          style="border: 2px solid #f39c12; border-radius: 10px; padding: 15px; margin-bottom: 20px; background: rgba(255,255,255,0.1);">
          <h3 style="color: #f39c12;">Commande #<?php echo $ma_commande['id_commande']; ?></h3>

          <div class="field">
            <span>Client :</span>
            <span><?php echo $client_info['prenom'] . " " . $client_info['nom']; ?></span>
          </div>
          <div class="field">
            <span>Adresse :</span>
            <span><?php echo $client_info['adresse']; ?></span>
          </div>
          <div class="field">
            <span>Téléphone :</span>
            <span><a
                href="tel:<?php echo $client_info['telephone']; ?>"><?php echo $client_info['telephone']; ?></a></span>
          </div>

          <div class="actions" style="margin-top: 15px;">
            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($client_info['adresse']); ?>"
              target="_blank" class="btn nav-btn"
              style="display: block; margin-bottom: 10px; text-align: center;">Voir sur Maps</a>

            <form method="POST" style="display: flex; gap: 10px;">
              <input type="hidden" name="id_commande" value="<?php echo $ma_commande['id_commande']; ?>">
              <button type="submit" name="action" value="terminer" class="btn complete-btn"
                style="flex: 1;">Livrée</button>
              <button type="submit" name="action" value="abandonner" class="btn complete-btn"
                style="flex: 1; background-color: #e74c3c;">Abandonner</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

    <?php else: ?>
      <p style="text-align: center; font-family: 'Chewy';">Aucune livraison en cours pour vous actuellement.</p>
    <?php endif; ?>

    <?php if (!empty($message_status)): ?>
      <p style="color: green; text-align: center; margin-top: 20px;"><?php echo $message_status; ?></p>
    <?php endif; ?>
  </div>
</body>

</html>