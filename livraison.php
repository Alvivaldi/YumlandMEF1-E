<?php
session_start();
require_once 'includes/fonctions.php';

// 1. Vérification du rôle de l'utilisateur connecté
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'livreur') {
  header("Location: formulaire.php");
  exit();
}

$id_livreur = $_SESSION['user']['id'];
$message = "";

// 2. Traitement de la mise à jour du statut (Action du bouton)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
  $id_cmd = $_POST['id_commande'];
  $nouveau_statut = ($_POST['action'] === 'terminer') ? 'livrée' : 'abandonnée';

  $commandes = lireJSON('donnees/commandes.json');
  foreach ($commandes as &$cmd) {
    if ($cmd['id'] == $id_cmd) {
      $cmd['statut'] = $nouveau_statut;
      break;
    }
  }
  ecrireJSON('donnees/commandes.json', $commandes);
  $message_status = "La commande a été marquée comme " . $nouveau_statut . ".";

  // 3. Récupération de la commande attribuée à ce livreur
  $commandes = lireJSON('donnees/commandes.json');
  $ma_commande = null;

  foreach ($commandes as $cmd) {
    // On cherche une commande "en livraison" pour ce livreur spécifique
    if ($cmd['id_livreur'] == $id_livreur_connecte && $cmd['statut'] === 'en livraison') {
      $ma_commande = $cmd;
      break;
    }
  }
}

// 4. Récupération des infos du client pour l'adresse
$client_info = null;
if ($ma_commande) {
  $utilisateurs = lireJSON('donnees/utilisateurs.json');
  foreach ($utilisateurs as $u) {
    if ($u['id'] == $ma_commande['id_client']) {
      $client_info = $u;
      break;
    }
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
    <h1>Détails de livraison</h1>

    <?php if ($ma_commande && $client_info): ?>
      <div class="info-livraison">
        <div class="field">
          <span>Client :</span>
          <span><?php echo $client_info['prenom'] . " " . $client_info['nom']; ?></span>
        </div>
        <div class="field">
          <span>Adresse :</span>
          <span><?php echo $client_info['adresse']; ?></span>
        </div>
        <div class="field">
          <span>Commentaires :</span>
          <span><?php echo $ma_commande['commentaires'] ?? 'Aucun'; ?></span>
        </div>
        <div class="field">
          <span>Téléphone :</span>
          <span><a
              href="tel:<?php echo $client_info['telephone']; ?>"><?php echo $client_info['telephone']; ?></a></span>
        </div>
      </div>

      <div class="actions">
        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($client_info['adresse']); ?>"
          target="_blank" class="btn nav-btn">Ouvrir dans Maps</a>

        <form method="POST" style="display: inline;">
          <input type="hidden" name="id_commande" value="<?php echo $ma_commande['id']; ?>">
          <button type="submit" name="action" value="terminer" class="btn complete-btn">Livraison
            terminée</button>
          <button type="submit" name="action" value="abandonner" class="btn complete-btn"
            style="background-color: #e74c3c; margin-top: 10px;">Abandonner la livraison</button>
        </form>
      </div>

    <?php else: ?>
      <p style="text-align: center; font-family: 'Chewy';">Aucune livraison en cours pour vous actuellement.</p>
    <?php endif; ?>

    <?php if ($message_status): ?>
      <p style="color: green; text-align: center; margin-top: 20px;"><?php echo $message_status; ?></p>
    <?php endif; ?>
  </div>
</body>

</html>