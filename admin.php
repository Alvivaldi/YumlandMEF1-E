<?php
//$_SESSION['user'] = ['nom' => 'Admin Test', 'email' => 'admin@test.com'];
//$_SESSION['role'] = 'admin';

session_start();
include 'includes/fonctions.php';

//1. SÉCURITÉ : Vérifier si l'utilisateur est connecté ET s'il est admin
if (!isset($_SESSION['user'])) {
        // S'il n'est pas connecté, on l'envoie vers la connexion
            header("Location: formulaire.php");
    exit();
}

// On récupère son rôle en minuscules (pour éviter les bugs avec "Admin" ou "ADMIN")
$role_user = strtolower($_SESSION['user']['role'] ?? '');

if ($role_user !== 'admin') {
    // Si c'est un client ou un livreur qui essaie de tricher en tapant admin.php dans l'URL
   header("Location: index.php");
   exit();
}

// 2. Chargement des données pour l'affichage de la page
$utilisateurs = lireJSON('donnees/utilisateurs.json');
if (!is_array($utilisateurs)) { 
    $utilisateurs = []; 
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css" />
    <link rel="stylesheet" href="css/global.css">



    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />

</head>

<body class="admin-body">
    <?php include 'includes/header.php'; ?>
    <header>

        <h1 class="big-title">Page Admin</h1>
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

        <main style="max-width: 1000px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px;">
    <h2 style="color: #da570b; margin-bottom: 30px;">Gestion des utilisateurs par l'administrateur</h2>

    <?php
    $utilisateurs = lireJSON('donnees/utilisateurs.json');
    ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left;">
        <thead>
            <tr style="background-color: #da570b; color: white;">
                <th style="padding: 12px;">Nom</th>
                <th style="padding: 12px;">Email</th>
                <th style="padding: 12px;">Rôle</th>
                <th style="padding: 12px;">Statut</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $u): ?>
                <?php 
                // On ignore l'administrateur lui-même pour éviter de se bloquer par erreur
                if (($u['role'] ?? '') === 'admin') continue; 
                $statutActuel = $u['statut'] ?? 'actif';
                ?>
                <tr style="border-bottom: 1px solid #eef2f5;" id="user-row-<?php echo $u['id']; ?>">
                    <td style="padding: 12px;"><?php echo htmlspecialchars($u['nom']); ?></td>
                    <td style="padding: 12px;"><?php echo htmlspecialchars($u['login']); ?></td>
                    <td style="padding: 12px; text-transform: uppercase; font-size: 0.85rem; font-weight: bold;"><?php echo htmlspecialchars($u['role'] ?? 'client'); ?></td>
                    <td style="padding: 12px;" id="status-badge-<?php echo $u['id']; ?>">
                        <span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; 
                              background-color: <?php echo $statutActuel === 'bloqué' ? '#e63946; color: white;' : '#2a9d8f; color: white;'; ?>">
                            <?php echo htmlspecialchars($statutActuel); ?>
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <button class="btn-toggle-block" 
                                data-id="<?php echo $u['id']; ?>" 
                                data-statut="<?php echo $statutActuel; ?>"
                                style="padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: white;
                                       background-color: <?php echo $statutActuel === 'bloqué' ? '#2a9d8f' : '#da570b'; ?>;">
                            <?php echo $statutActuel === 'bloqué' ? '🔓 Débloquer' : '🔒 Bloquer'; ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
document.querySelectorAll('.btn-toggle-block').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.getAttribute('data-id');
        const statutActuel = this.getAttribute('data-statut');
        const nouveauStatut = statutActuel === 'bloqué' ? 'actif' : 'bloqué';

        const params = new URLSearchParams();
        params.append('id_user', userId);
        params.append('nouveau_statut', nouveauStatut);

        // Envoi asynchrone corrigé avec les bons en-têtes
        fetch('statut.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badgeContainer = document.getElementById(`status-badge-${userId}`);
                
                if (badgeContainer) {
                    if (nouveauStatut === 'bloqué') {
                        badgeContainer.innerHTML = `<span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; background-color: #e63946; color: white;">bloqué</span>`;
                        this.textContent = '🔓 Débloquer';
                        this.style.backgroundColor = '#2a9d8f';
                        this.setAttribute('data-statut', 'bloqué');
                    } else {
                        badgeContainer.innerHTML = `<span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; background-color: #2a9d8f; color: white;">actif</span>`;
                        this.textContent = '🔒 Bloquer';
                        this.style.backgroundColor = '#e63946';
                        this.setAttribute('data-statut', 'actif');
                    }
                }
            } else {
                alert("Le serveur n'a pas pu modifier le statut.");
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
            alert("Erreur de communication avec le serveur.");
        });
    });
});
</script>
    </div>
</body>

</html>