<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Mon Profil</title>
  <link rel="stylesheet" href="css/profil.css" />

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />
</head>

<body>
<?php include 'includes/header.php'; ?>
  <div class="profile-container">
    <h1>Mon Profil</h1>

    <section class="info-user">
      <h2>Informations personnelles</h2>
      <div class="field">
        <span>Nom : Alex Térieur</span>
        <span class="edit">✏️</span>
      </div>
      <div class="field">
        <span>Email : alex.terieur@manger.com</span>
        <span class="edit">✏️</span>
      </div>
      <div class="field">
        <span>Téléphone : 06 12 34 56 78</span>
        <span class="edit">✏️</span>
      </div>
    </section>

    <section class="past-orders">
      <h2>Anciennes commandes</h2>
      <ul>
        <li>12/02/2026 - Menu Fréquent</li>
        <li>10/02/2026 - Plat du jour</li>
        <li>05/02/2026 - Menu Fréquent</li>
      </ul>
    </section>

    <section class="loyalty">
      <h2>Compte fidélité</h2>
      <p>Points : 120 ⭐</p>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>

</html>