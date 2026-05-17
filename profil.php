<?php
session_start(); 
include 'includes/fonctions.php';


$valeurs_autorisees = ['css/global.css', 'css/accessible.css'];
$cookie_val  = isset($_COOKIE['theme_choice']) ? $_COOKIE['theme_choice'] : 'css/global.css';
$theme_actif = in_array($cookie_val, $valeurs_autorisees) ? $cookie_val : 'css/global.css';

if (!isset($_SESSION['user'])) {
    header('Location: formulaire.php');
    exit();
}


$user_connecte = $_SESSION['user'];


$commandes = lireJSON('donnees/commandes.json');
if (!is_array($commandes)) { 
    $commandes = []; 
}


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
    <link rel="stylesheet" id="dynamic-theme" href="<?php echo htmlspecialchars($theme_actif); ?>">
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

            <p id="msg-profil" style="color: green; font-weight: bold; display: none;"></p>

            <form id="form-profil">
                <div class="field">
                    <label>Nom : </label>
                    <span class="display-val"><?= htmlspecialchars($user_connecte['nom']) ?></span>
                    <input type="text" name="nom" class="input-val"
                        value="<?= htmlspecialchars($user_connecte['nom']) ?>" style="display:none;" required>
                    <span class="edit">✏️</span>
                </div>

                <div class="field">
                    <label>Prénom : </label>
                    <span class="display-val"><?= htmlspecialchars($user_connecte['prenom']) ?></span>
                    <input type="text" name="prenom" class="input-val"
                        value="<?= htmlspecialchars($user_connecte['prenom']) ?>" style="display:none;" required>
                    <span class="edit">✏️</span>
                </div>

                <div class="field">
                    <label>Téléphone : </label>
                    <span class="display-val"><?= htmlspecialchars($user_connecte['telephone']) ?></span>
                    <input type="text" name="telephone" class="input-val"
                        value="<?= htmlspecialchars($user_connecte['telephone']) ?>" style="display:none;" required>
                    <span class="edit">✏️</span>
                </div>

                <div class="field">
                    <label>Adresse : </label>
                    <span class="display-val"><?= htmlspecialchars($user_connecte['adresse'] ?? '') ?></span>
                    <input type="text" name="adresse" class="input-val"
                        value="<?= htmlspecialchars($user_connecte['adresse'] ?? '') ?>" style="display:none;" required>
                    <span class="edit">✏️</span>
                </div>

                <div class="field">
                    <label>Email : </label>
                    <span><?= htmlspecialchars($user_connecte['login']) ?></span>
                </div>

                <button type="submit" id="btn-save"
                    style="display: none; margin-top: 15px; padding: 10px; background-color: #d23508; color: white; border: none; border-radius: 5px; cursor: pointer;">Valider
                    les modifications</button>
            </form>
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
                            <a href="notation.php?id=<?= $cmd['id_commande'] ?>" class="btn-noter"><i
                                    class="fa-solid fa-star"></i> Noter</a>

                            <?php elseif (isset($cmd['note'])): ?>
                            <span class="note-text"><i class="fa-solid fa-star"></i>
                                <?= htmlspecialchars($cmd['note']) ?>/5</span>

                            <?php elseif (in_array(strtoupper($cmd['statut']), ['A_PREPARER', 'EN_ATTENTE'])): ?>
                            <span class="status-attente" style="display:block; margin-bottom:5px;">En attente...</span>
                            <a href="modifier_commande.php?id=<?= $cmd['id_commande'] ?>"
                                style="color: #fca311; text-decoration: none; font-weight: bold; border: 1px solid #fca311; padding: 3px 8px; border-radius: 5px;">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>

                            <?php else: ?>
                            <span class="status-attente"><?= htmlspecialchars($cmd['statut']) ?></span>
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
    <script>

    document.querySelectorAll('.edit').forEach(btn => {
        btn.addEventListener('click', function() {
            let fieldDiv = this.closest('.field');
            fieldDiv.querySelector('.display-val').style.display = 'none'; 
            fieldDiv.querySelector('.input-val').style.display = 'inline-block'; 
            this.style.display = 'none'; // Cache le crayon
            document.getElementById('btn-save').style.display = 'block'; 
        });
    });


    document.getElementById('form-profil').addEventListener('submit', function(e) {
        e.preventDefault(); 


        let formData = new FormData(this);


        fetch('api_update_profil.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
  
                    document.querySelectorAll('.field').forEach(fieldDiv => {
                        let input = fieldDiv.querySelector('.input-val');
                        let display = fieldDiv.querySelector('.display-val');
                        let editBtn = fieldDiv.querySelector('.edit');

                        if (input && display) {
                            display.innerText = input.value; 
                            input.style.display = 'none'; 
                            display.style.display = 'inline-block'; 
                            editBtn.style.display = 'inline-block'; 
                        }
                    });
                    document.getElementById('btn-save').style.display = 'none'; 


                    let msgBox = document.getElementById('msg-profil');
                    msgBox.innerText = "Profil mis à jour avec succès !";
                    msgBox.style.display = 'block';
                    setTimeout(() => msgBox.style.display = 'none', 3000); 
                } else {
                    alert("Erreur lors de la mise à jour.");
                }
            })
            .catch(error => console.error('Erreur:', error));
    });
    </script>
</body>

</html>