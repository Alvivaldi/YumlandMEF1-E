let platsEnMemoire = []; // Stocke les plats pour le tri local

// 1. Fonction pour récupérer les plats (Asynchrone)
async function filtrerEtCharger() {
    const cat = document.getElementById('sel-categorie').value;
    const regime = document.getElementById('sel-regime').value;
    const saveur = document.getElementById('sel-saveur').value;
    const allergene = document.getElementById('sel-allergene').value;

    try {
        // On appelle ton nouveau fichier PHP
        const response = await fetch(`recuperer_plats.php?categorie=${cat}&regime=${regime}&saveur=${saveur}&allergene=${allergene}`);
        
        if (!response.ok) throw new Error('Erreur réseau');

        platsEnMemoire = await response.json();
        appliquerTriLocal(); // On affiche les résultats
    } catch (error) {
        console.error("Erreur lors du chargement :", error);
        document.getElementById('zone-plats').innerHTML = "<p>Erreur lors du chargement des produits.</p>";
    }
}

// 2. Fonction pour trier (Localement)
function appliquerTriLocal() {
    const tri = document.getElementById('sel-tri').value;
    let copiesPlats = [...platsEnMemoire];

    if (tri === 'prix-croissant') {
        copiesPlats.sort((a, b) => a.prix - b.prix);
    } else if (tri === 'prix-decroissant') {
        copiesPlats.sort((a, b) => b.prix - a.prix);
    }

    genererAffichage(copiesPlats);
}

// 3. Fonction pour créer le HTML des produits
function genererAffichage(liste) {
    const container = document.getElementById('zone-plats');
    container.innerHTML = ''; 

    if (liste.length === 0) {
        container.innerHTML = '<p>Aucun plat ne correspond à vos critères.</p>';
        return;
    }

    liste.forEach(plat => {
        container.innerHTML += `
            <div class="box1">
                <img src="${plat.image}" alt="${plat.nom}">
                <div class="box-content">
                    <h3>${plat.nom}</h3>
                    <p>${plat.description}</p>
                    <span>${plat.prix.toFixed(2)} €</span>
                    <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="id_plat" value="${plat.id}">
                        <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                        <button type="submit" class="btn-ajouter">Ajouter</button>
                    </form>
                </div>
            </div>`;
    });
}

// Lancement automatique au chargement
window.onload = filtrerEtCharger;