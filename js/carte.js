let platsEnMemoire = [];

async function filtrerEtCharger() {
    const cat = document.getElementById('sel-categorie').value;
    const reg = document.getElementById('sel-regime').value;
    const sav = document.getElementById('sel-saveur').value;
    const alg = document.getElementById('sel-allergene').value;

    try {
        const url = `recuperer_plats.php?categorie=${cat}&regime=${reg}&saveur=${sav}&allergene=${alg}`;
        const response = await fetch(url);
        
        if (!response.ok) throw new Error('Erreur de chargement réseau');
        
        platsEnMemoire = await response.json();
        appliquerTriLocal();
    } catch (error) {
        console.error("Erreur :", error);
        document.getElementById('zone-plats').innerHTML = "<p style='color:red; font-weight:bold;'>Erreur lors de la récupération des plats.</p>";
    }
}

function appliquerTriLocal() {
    const tri = document.getElementById('sel-tri').value;
    let copiesPlats = [...platsEnMemoire];

    if (tri === 'prix-croissant') {
        copiesPlats.sort((a, b) => a.prix - b.prix);
    } else if (tri === 'prix-decroissant') {
        copiesPlats.sort((a, b) => b.prix - a.prix);
    } else if (tri === 'nom') {
        copiesPlats.sort((a, b) => a.nom.localeCompare(b.nom));
    }

    genererAffichage(copiesPlats);
}

function genererAffichage(liste) {
    const container = document.getElementById('zone-plats');
    container.innerHTML = '';

    if (liste.length === 0) {
        container.innerHTML = '<p style="grid-column: 1/-1; text-align:center; font-weight:bold;">Aucun produit ne correspond à ces filtres.</p>';
        return;
    }

    liste.forEach(plat => {
        container.innerHTML += `
            <div class="box1">
                <img src="${plat.image}" alt="${plat.nom}" style="width:100%; height:200px; object-fit:cover;">
                <div class="box-content" style="padding:15px;">
                    <h3 style="font-family:'Mogra', cursive; color:#14213d;">${plat.nom}</h3>
                    <p style="font-size:0.9rem; color:#666; margin:10px 0;">${plat.description}</p>
                    <span style="font-weight:bold; color:#fca311; font-size:1.2rem;">${plat.prix.toFixed(2)} €</span>
                    <form action="ajout_panier.php" method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="id_plat" value="${plat.id}">
                        <input type="number" name="quantite" value="1" min="1" style="width: 50px; padding:3px;">
                        <button type="submit" style="cursor:pointer; background-color: #fca311; border: none; padding: 5px 10px; border-radius: 5px; color:white; font-weight:bold;">Ajouter</button>
                    </form>
                </div>
            </div>`;
    });
}

// Lancement automatique au chargement
window.onload = filtrerEtCharger;