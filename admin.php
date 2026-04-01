<?php
include 'includes/fonctions.php';
$utilisateurs = lireJSON('donnees/utilisateurs.json'); 
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css" />



    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />

</head>

<body class="admin-body">
    <header>

        <h1 class="big-title">Page Administrateur</h1>
    </header>

    <div class="admin-wrapper">
        <nav class="admin-sidebar">
            <div class="sidebar-content">
                <div class="filtreTexteIcons active-admin">
                    <div class="filtreIcons">📊</div>
                    <span class="filtreTexte">Dernières commandes</span>
                </div>
                <div class="filtreTexteIcons">
                    <div class="filtreIcons">👤</div>
                    <span class="filtreTexte">Utilisateurs</span>
                </div>
                <div class="filtreTexteIcons">
                    <div class="filtreIcons">🍴</div>
                    <span class="filtreTexte">Modifier la Carte</span>
                </div>
            </div>
        </nav>

        <main class="admin-main">
            <h2 class="specialite">Gestion des Utilisateurs</h2>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td><?php echo $user['nom']; ?></td>
                                <td>
                                    <span class="badge <?php echo ($user['role'] !== 'client') ? $user['role'] : ''; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <button class="btn-edit">Modifier Statut (<?php echo $user['statut']; ?>)</button>
                                    <button class="btn-edit" style="background-color: #777;">
                                        <?php echo $user['est_bloque'] ? 'Débloquer' : 'Bloquer'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>