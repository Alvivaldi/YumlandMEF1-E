<?php 
session_start();
include 'includes/fonctions.php';

// 1. Simulation d'utilisateur connecté (A adapter selon votre script de connexion)
// Si l'utilisateur n'est pas connecté, on utilise des fausses données pour tester
$user_connecte = [
    "id" => 1,
    "nom" => "Térieur",
    "prenom" => "Alex",
    "email" => "alex.terieur@manger.com",
    "telephone" => "06 12 34 56 78"
];

// Si vous avez un système de connexion, décommentez ceci plus tard :
/*
if (isset($_SESSION['user'])) {
    $user_connecte = $_SESSION['user'];
} else {
    header('Location: formulaire.php'); // Redirige si non connecté
    exit;
}
*/

// 2. Lecture de l'historique des commandes
$toutes_les_commandes = lireJSON('donnees/commandes.json');
if (!is_array($toutes_les_commandes)) {
    $toutes_les_commandes = [];
}

// On filtre pour ne garder que les commandes de CE client
$mes_commandes = array_filter($toutes_les_commandes, function($cmd) use ($user_connecte) {
    return isset($cmd['id_client']) && $cmd['id_client'] == $user_connecte['id'];
});
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mon Profil</title>
  <link rel="stylesheet" href="css/profil.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
  <style>
      /* Un peu de style pour le tableau d'historique */
      .history-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      .history-table th, .history-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
      .history-table th { background-color: #f9f9f9; }
      .btn-noter { background-color: #fca311; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-weight: bold; }
      .btn-noter:hover { background-color: #e59400; }
  </style>
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <div class="profile-container">
    <h1>Mon Profil</h1>

    <?php if(isset($_GET['success']) && $_GET['success'] == 'notation'): ?>
        <p style="color: green; background: #e8f5e9; padding: 10px; border-radius: 5px; text-align: center;">Merci d'avoir noté votre commande !</p>
    <?php endif; ?>

    <section class="info-user">
      <h2>Informations personnelles</h2>
      <div class="field">
        <span>Nom / Prénom : <?= htmlspecialchars($user_connecte['prenom'] . " " . $user_connecte['nom']) ?></span>
        <span class="edit" title="Modifiable à la phase 3">✏️</span>
      </div>
      <div class="field">
        <span>Email : <?= htmlspecialchars($user_connecte['email']) ?></span>
        <span class="edit" title="Modifiable à la phase 3">✏️</span>
      </div>
      <div class="field">
        <span>Téléphone : <?= htmlspecialchars($user_connecte['telephone']) ?></span>
        <span class="edit" title="Modifiable à la phase 3">✏️</span>
      </div>
      <p style="font-size: 0.8em; color: gray; margin-top: 10px;">* La modification des informations sera disponible lors de la phase 3.</p>
    </section>

    <section class="past-orders">
      <h2>Historique de commandes</h2>
      <?php if (empty($mes_commandes)): ?>
          <p>Vous n'avez pas encore passé de commande.</p>
      <?php else: ?>
          <table class="history-table">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse($mes_commandes) as $cmd): ?>
                    <tr>
                        <td><?= htmlspecialchars($cmd['id_commande']) ?></td>
                        <td><?= htmlspecialchars($cmd['date_creation']) ?></td>
                        <td><?= number_format($cmd['prix_total'], 2) ?> €</td>
                        <td><strong><?= htmlspecialchars($cmd['statut']) ?></strong></td>
                        <td>
                            <?php if (strtoupper($cmd['statut']) === 'LIVREE' && !isset($cmd['note'])): ?>
                                <a href="notation.php?id=<?= $cmd['id_commande'] ?>" class="btn-noter"><i class="fa-solid fa-star"></i> Noter</a>
                            <?php elseif (isset($cmd['note'])): ?>
                                <span style="color: #fca311;"><i class="fa-solid fa-star"></i> <?= $cmd['note'] ?>/5</span>
                            <?php else: ?>
                                <span style="color: gray; font-size: 0.9em;">En attente de livraison</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
          </table>
      <?php endif; ?>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>
</html>