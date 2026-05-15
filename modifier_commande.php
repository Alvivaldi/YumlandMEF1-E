<?php
session_start();
include 'includes/fonctions.php';

// Sécurité : Être connecté
if (!isset($_SESSION['user'])) {
    header('Location: formulaire.php');
    exit();
}

$id_commande = $_GET['id'] ?? '';
$commandes = lireJSON('donnees/commandes.json');
$plats = lireJSON('donnees/plats.json');

// 1. Chercher la commande
$ma_commande = null;
$index_commande = -1;
foreach ($commandes as $i => $cmd) {
    if ($cmd['id_commande'] === $id_commande && $cmd['id_client'] == $_SESSION['user']['id']) {
        $ma_commande = $cmd;
        $index_commande = $i;
        break;
    }
}

// 2. Vérifier si elle existe et si elle est modifiable
if (!$ma_commande || !in_array(strtoupper($ma_commande['statut']), ['A_PREPARER', 'EN_ATTENTE'])) {
    die("<h1 style='color:red; text-align:center;'>Erreur : Cette commande n'est plus modifiable (déjà en cuisine).</h1>");
}

// --- TRAITEMENT DU FORMULAIRE APRÈS MODIFICATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_panier_json = $_POST['nouveau_panier'] ?? '{}';
    $nouveau_panier = json_decode($nouveau_panier_json, true);

    $nouveau_total = 0;
    $nouveaux_produits = [];
    
    // On recalcule le vrai prix côté serveur (sécurité !)
    foreach ($nouveau_panier as $id_plat => $qte) {
        foreach ($plats as $p) {
            if ($p['id'] == $id_plat && $qte > 0) {
                $nouveau_total += $p['prix'] * $qte;
                $nouveaux_produits[] = [
                    "id_plat" => $id_plat,
                    "nom" => $p['nom'],
                    "quantite" => $qte
                ];
                break;
            }
        }
    }

    $ancien_total = $ma_commande['prix_total'];
    $difference = $nouveau_total - $ancien_total;

    // On met à jour la commande
    $commandes[$index_commande]['produits'] = $nouveaux_produits;
    $commandes[$index_commande]['prix_total'] = $nouveau_total;
    
    // Gestion de la différence (Cahier des charges Phase 3)
    if ($difference > 0) {
        $commandes[$index_commande]['reste_a_payer'] = $difference; // On simule le nouveau paiement
        ecrireJSON('donnees/commandes.json', $commandes);
        // On redirige vers le profil avec un message (idéalement, ça irait vers CYBank)
        header("Location: profil.php?success=1&msg=Commande modifiée ! " . $difference . "€ supplémentaires ont été facturés.");
        exit();
    } else {
        if ($difference < 0) {
            $commandes[$index_commande]['ticket_reduction'] = abs($difference); // On lui donne un ticket
        }
        ecrireJSON('donnees/commandes.json', $commandes);
        header("Location: profil.php?success=1&msg=Commande modifiée ! Un ticket de réduction de " . abs($difference) . "€ a été généré.");
        exit();
    }
}

// 3. Préparer le panier actuel pour le donner au JavaScript
$panier_actuel = [];
foreach ($ma_commande['produits'] as $prod) {
    $prix_unitaire = 0;
    foreach ($plats as $p) {
        if ($p['id'] == $prod['id_plat']) { $prix_unitaire = $p['prix']; break; }
    }
    $panier_actuel[$prod['id_plat']] = [
        'nom' => $prod['nom'],
        'quantite' => $prod['quantite'],
        'prix' => $prix_unitaire
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier ma commande</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/modifier_commande.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mogra&display=swap" />
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Mogra&display=swap" rel="stylesheet" />

</head>
<body>
    <?php include 'includes/header.php'; ?>

    <h1 style="text-align: center; color: #d23508; margin-top: 20px;">Modifier la commande #<?= htmlspecialchars($id_commande) ?></h1>

    <div class="modif-container">
        <div class="catalogue">
            <h2>Carte des plats</h2>
            <?php foreach ($plats as $plat): ?>
                <div class="plat-item">
                    <div>
                        <strong><?= htmlspecialchars($plat['nom']) ?></strong><br>
                        <span style="color: #666;"><?= number_format($plat['prix'], 2) ?> €</span>
                    </div>
                    <button class="btn-add" onclick="ajouterAuPanierJS(<?= $plat['id'] ?>, '<?= addslashes($plat['nom']) ?>', <?= $plat['prix'] ?>)">
                        <i class="fa-solid fa-plus"></i> Ajouter
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="panier-live">
            <h2>Ma Commande</h2>
            <div id="cart-content">
                </div>

            <div class="totals">
                <p>Ancien Total : <span id="ancien-total"><?= number_format($ma_commande['prix_total'], 2) ?></span> €</p>
                <p>Nouveau Total : <span id="nouveau-total">0.00</span> €</p>
                <p>Différence : <span id="diff-total">0.00 €</span></p>
            </div>

            <form method="POST" action="modifier_commande.php?id=<?= $id_commande ?>" id="form-modif">
                <input type="hidden" name="nouveau_panier" id="nouveau_panier_input" value="">
                <button type="submit" class="btn-valider">Valider la modification</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // On récupère le panier actuel depuis PHP !
        let monPanier = <?= json_encode($panier_actuel) ?>;
        let ancienTotal = <?= $ma_commande['prix_total'] ?>;

        function afficherPanier() {
            let html = "";
            let nouveauTotal = 0;

            // On parcourt l'objet JavaScript
            for (const [id, plat] of Object.entries(monPanier)) {
                if (plat.quantite > 0) {
                    nouveauTotal += plat.prix * plat.quantite;
                    html += `
                        <div class="cart-item">
                            <span>${plat.nom} (x${plat.quantite})</span>
                            <div class="qte-controls">
                                <button onclick="modifierQuantite(${id}, -1)">-</button>
                                <button onclick="modifierQuantite(${id}, 1)">+</button>
                            </div>
                        </div>
                    `;
                }
            }

            if(html === "") {
                html = "<p style='color:red;'>La commande ne peut pas être vide.</p>";
                document.querySelector('.btn-valider').disabled = true;
            } else {
                document.querySelector('.btn-valider').disabled = false;
            }

            document.getElementById('cart-content').innerHTML = html;
            document.getElementById('nouveau-total').innerText = nouveauTotal.toFixed(2);

            // Calcul de la différence
            let diff = nouveauTotal - ancienTotal;
            let diffEl = document.getElementById('diff-total');
            
            if (diff > 0) {
                diffEl.innerHTML = `<span class="diff-plus">+ ${diff.toFixed(2)} € (À payer)</span>`;
            } else if (diff < 0) {
                diffEl.innerHTML = `<span class="diff-moins">${diff.toFixed(2)} € (Ticket Réduc)</span>`;
            } else {
                diffEl.innerHTML = `<span>0.00 €</span>`;
            }

            // On met à jour l'input caché avec le nouveau panier formaté en JSON pour l'envoyer au PHP
            let panierPourServeur = {};
            for (const [id, plat] of Object.entries(monPanier)) {
                if (plat.quantite > 0) {
                    panierPourServeur[id] = plat.quantite;
                }
            }
            document.getElementById('nouveau_panier_input').value = JSON.stringify(panierPourServeur);
        }

        function ajouterAuPanierJS(id, nom, prix) {
            if (monPanier[id]) {
                monPanier[id].quantite += 1;
            } else {
                monPanier[id] = { nom: nom, prix: prix, quantite: 1 };
            }
            afficherPanier();
        }

        function modifierQuantite(id, changement) {
            if (monPanier[id]) {
                monPanier[id].quantite += changement;
                if (monPanier[id].quantite <= 0) {
                    delete monPanier[id]; // Retire du panier si quantité = 0
                }
                afficherPanier();
            }
        }

        // On affiche le panier dès le chargement de la page
        afficherPanier();
    </script>
</body>
</html>