let platsEnMemoire = [];

async function filtrerEtCharger() {
    const elCat = document.getElementById('sel-categorie');
    const elReg = document.getElementById('sel-regime');
    const elSav = document.getElementById('sel-saveur');
    const elAlg = document.getElementById('sel-allergene');
    const elZone = document.getElementById('zone-plats');

    if (!elCat || !elReg || !elSav || !elAlg || !elZone) return;

    const cat = elCat.value;
    const reg = elReg.value;
    const sav = elSav.value;
    const alg = elAlg.value;

    try {
        const url = `recup_plats.php?categorie=${cat}&regime=${reg}&saveur=${sav}&allergene=${alg}`;
        const response = await fetch(url);
        
        if (!response.ok) throw new Error(`Erreur réseau: ${response.status}`);
        
        let dataBrute = await response.json();
        
        platsEnMemoire = dataBrute.filter((plat, index, self) =>
            index === self.findIndex((p) => (
                p.id === plat.id
            ))
        );

        appliquerTriLocal();
    } catch (error) {
        console.error("Erreur critique :", error);
        elZone.innerHTML = "<p style='color:red; font-weight:bold; text-align:center; grid-column:1/-1;'>Erreur lors de la récupération des données.</p>";
    }
}

function appliquerTriLocal() {
    const elTri = document.getElementById('sel-tri');
    const searchInput = document.getElementById('search-input');
    if (!elTri) return;

    const tri = elTri.value;
    let copiesPlats = [...platsEnMemoire];

    if (searchInput && searchInput.value.trim() !== "") {
        const motCle = searchInput.value.toLowerCase();
        copiesPlats = copiesPlats.filter(plat => 
            (plat.nom && plat.nom.toLowerCase().includes(motCle)) || 
            (plat.description && plat.description.toLowerCase().includes(motCle))
        );
    }

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
    if (!container) return;

    container.innerHTML = '';

    if (liste.length === 0) {
        container.innerHTML = '<p style="text-align:center; font-weight:bold; color:#14213d; padding:40px; width:100%;">Aucun produit ne correspond à vos critères.</p>';
        return;
    }


    const ordresCategories = {
        'specialite': 'Nos Spécialités',
        'entree': 'Nos Entrées',
        'plat': 'Nos Plats',
        'dessert': 'Nos Desserts',
        'formule': 'Nos Formules',
        'boisson': 'Nos Boissons'
    };


    const groupePlats = {
        'specialite': [],
        'entree': [],
        'plat': [],
        'dessert': [],
        'formule': [],
        'boisson': []
    };

    liste.forEach(plat => {
        if (plat.categorie) {
            const categoriesDuPlat = plat.categorie.toLowerCase();
            Object.keys(groupePlats).forEach(catKey => {
                if (categoriesDuPlat.includes(catKey)) {
                    groupePlats[catKey].push(plat);
                }
            });
        }
    });


    Object.keys(ordresCategories).forEach(catKey => {
        const listePlatsDeLaCat = groupePlats[catKey];

        if (listePlatsDeLaCat && listePlatsDeLaCat.length > 0) {
            
            const titreSection = document.createElement('h2');
            titreSection.className = 'titre-categorie-carte';
            titreSection.innerText = ordresCategories[catKey];
            container.appendChild(titreSection);

            const grilleFlex = document.createElement('div');
            grilleFlex.className = 'grille-categorie-flex';

            listePlatsDeLaCat.forEach(plat => {
                const prixFixe = plat.prix ? parseFloat(plat.prix).toFixed(2) : "0.00";
                const imagePlat = plat.image ? plat.image : "images/logo.png";
                const descPlat = plat.description ? plat.description : "Aucune description disponible.";

                grilleFlex.innerHTML += `
                    <div class="box1">
                        <img src="${imagePlat}" alt="${plat.nom}">
                        <div class="box-content">
                            <h3>${plat.nom}</h3>
                            <p>${descPlat}</p>
                            <span>${prixFixe} €</span>
                            <form action="ajout_panier.php" method="POST">
                                <input type="hidden" name="id_plat" value="${plat.id}">
                                <input type="number" name="quantite" value="1" min="1">
                                <button type="submit">Ajouter</button>
                            </form>
                        </div>
                    </div>`;
            });

            container.appendChild(grilleFlex);
        }
    });
}


filtrerEtCharger();