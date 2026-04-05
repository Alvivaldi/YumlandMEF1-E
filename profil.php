<?php
session_start(); // OBLIGATOIRE : Permet d'accéder à la mémoire du serveur
include 'includes/fonctions.php';

// 1. VÉRIFICATION DE SÉCURITÉ
// Si l'utilisateur essaie d'aller sur profil.php sans être connecté, on le vire !
if (!isset($_SESSION['user'])) {
    header('Location: formulaire.php');
    exit();
}

// 2. RÉCUPÉRATION DU VRAI CLIENT
// On remplace le faux utilisateur par celui qui s'est connecté
$user_connecte = $_SESSION['user'];

// 3. CHARGEMENT DE SES COMMANDES
$commandes = lireJSON('donnees/commandes.json');
if (!is_array($commandes)) { 
    $commandes = []; 
}

// On filtre pour ne garder que les commandes de CE client (grâce à son ID)
$mes_commandes = [];
foreach ($commandes as $cmd) {
    if (isset($cmd['id_client']) && $cmd['id_client'] == $user_connecte['id']) {
        $mes_commandes[] = $cmd;
    }
}
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Mon Profil</title>
  <link rel="stylesheet" href="css/profil.css?v=<?= time() ?>" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <div class="profile-container">
    <h1>Mon Profil</h1>

    <?php if(isset($_GET['success']) && $_GET['success'] == 'notation'): ?>
        <p class="success-msg">Merci d'avoir noté votre commande !</p>
    <?php endif; ?>

   <section class="info-user">
      <h2>Informations personnelles</h2>
      
      <div class="field">
        <span>Nom : <?= htmlspecialchars($user_connecte['nom']) ?></span>
        <span class="edit" title="Bientôt modifiable !">✏️</span>
      </div>
      
      <div class="field">
        <span>Prénom : <?= htmlspecialchars($user_connecte['prenom']) ?></span>
        <span class="edit" title="Bientôt modifiable !">✏️</span>
      </div>
      
      <div class="field">
        <span>Email : <?= htmlspecialchars($user_connecte['login']) ?></span>
        <span class="edit" title="Bientôt modifiable !">✏️</span>
      </div>
      
      <div class="field">
        <span>Téléphone : <?= htmlspecialchars($user_connecte['telephone']) ?></span>
        <span class="edit" title="Bientôt modifiable !">✏️</span>
      </div>
      
      <p class="info-note">
        * La modification des informations sera disponible lors de la phase 3.
      </p>
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
                                <span class="note-text"><i class="fa-solid fa-star"></i> <?= htmlspecialchars($cmd['note']) ?>/5</span>
                            
                            <?php else: ?>
                                <span class="status-attente">En attente...</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
          </table>
      <?php endif; ?>
    </section>

    <section class="loyalty">
      <h2>Compte fidélité</h2>
      <p>Points : 120 ⭐</p>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>
</html>