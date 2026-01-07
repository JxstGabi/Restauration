@extends('layouts.app')

@section('title', 'Menus de restauration scolaire - Semaine')

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md relative">
    <div class="w-full px-4 flex items-center justify-center relative">
        <a href="{{ route('map') }}" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full hover:bg-blue-500 transition-colors" title="Retour à la carte">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div class="text-center">
            <h1 class="text-3xl font-bold">Menus de restauration scolaire</h1>
            @if(request()->get('school'))
                <p class="text-blue-100 mt-2 text-lg">
                    École : <strong>{{ request()->get('school') }}</strong>
                </p>
            @endif
        </div>
    </div>
</header>
@endsection

@section('content')
<main class="w-full px-4 py-8">

    <div class="mb-8 max-w-2xl mx-auto">
        <input type="text" id="searchInput" placeholder="Rechercher un plat (ex: frites, bio...)"
               class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm text-gray-700 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 focus:outline-none transition-all duration-200">
    </div>

    <!-- Planning hebdomadaire en colonnes -->
    <div class="flex justify-center">
        <div id="weekGrid" class="grid grid-cols-1 md:grid-cols-5 gap-4 w-full"></div>
    </div>

    <!-- Légende -->
    <div class="mt-10 flex flex-wrap justify-center gap-8 pt-6 border-t border-gray-100 max-w-4xl mx-auto">
        <div class="flex items-center gap-2">
            <span class="px-1.5 py-0.5 rounded text-[10px] bg-green-100 text-green-700 font-bold uppercase tracking-tighter border border-green-200">BIO</span>
            <span class="text-sm text-gray-600">Produit Bio</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-1.5 py-0.5 rounded text-[10px] bg-red-100 text-red-700 font-bold uppercase tracking-tighter border border-red-200">AOP</span>
            <span class="text-sm text-gray-600">Appellation d'Origine Protégée</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-1.5 py-0.5 rounded text-[10px] bg-indigo-100 text-indigo-700 font-bold uppercase tracking-tighter border border-indigo-200">VÉGÉ</span>
            <span class="text-sm text-gray-600">Plat sans viande</span>
        </div>
    </div>

</main>
@endsection

@section('scripts')
<script>

const weekGrid = document.getElementById('weekGrid');
const searchInput = document.getElementById('searchInput');

/* ============================
   DATE : Lundi de la semaine
============================ */
function getMonday(d) {
    d = new Date(d);
    d.setHours(0,0,0,0);
    const day = d.getDay(), diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

// Fonction utilitaire pour format YYYY-MM-DD local
function formatDateISO(d) {
    const offset = d.getTimezoneOffset();
    const local = new Date(d.getTime() - (offset * 60 * 1000));
    return local.toISOString().slice(0, 10);
}

const today = new Date();
const monday = getMonday(today);
const start = formatDateISO(monday);

/* ============================
   MAPPING ÉCOLES
============================ */
const mappingMenusEcoles = {
    "ECOLE BENIER": "École maternelle Charles Benier École élémentaire Charles Benier",
    "ECOLE BLANCHERAIE": "École élémentaire La Blancheraie École maternelle La Blancheraie",
    "ECOLE BROSSARD": "École primaire René Brossard",
    "ECOLE CHIRON": "École élémentaire Henri Chiron École maternelle Henri Chiron",
    "ECOLE CONDORCET": "École élémentaire Condorcet École maternelle Condorcet",
    "ECOLE CURIE": "École élémentaire Pierre et Marie Curie École maternelle Pierre et Marie Curie",
    "ECOLE DACIER": "École primaire Anne Dacier",
    "ECOLE DESCARTES": "École maternelle Descartes",
    "ECOLE FERRARO": "École maternelle Aldo Ferraro École élémentaire Aldo Ferraro",
    "ECOLE FRATELLINI": "École primaire Annie Fratellini",
    "ECOLE GASNIER": "École primaire René Gasnier",
    "ECOLE HUGO": "École élémentaire Victor Hugo École maternelle Victor Hugo",
    "ECOLE ISORET": "École maternelle Isoret École élémentaire Isoret",
    "ECOLE LAREVELLIERE": "École primaire Larévellière",
    "ECOLE LEBAS": "Groupe scolaire Pierre-Louis Lebas",
    "ECOLE MAULEVRIES": "École primaire Les Grandes Maulévriès",
    "ECOLE MONET": "École élémentaire Claude Monet École maternelle Claude Monet",
    "ECOLE MONTESQUIEU": "École maternelle Montesquieu",
    "ECOLE MUSSET": "École élémentaire Alfred de Musset École maternelle Alfred de Musset",
    "ECOLE PAGNOL": "École élémentaire Marcel Pagnol École maternelle Marcel Pagnol",
    "ECOLE PARCHEMINERIE": "École maternelle Parcheminerie",
    "ECOLE PERUSSAIE": "École primaire La Pérussaie",
    "ECOLE PREVERT": "École maternelle Jacques Prévert École élémentaire Jacques Prévert",
    "ECOLE RASPAIL": "École primaire François Raspail",
    "ECOLE ROSTAND": "École primaire Jean Rostand",
    "ECOLE ROUSSEAU": "École maternelle Jean-Jacques Rousseau École élémentaire Jean-Jacques Rousseau",
    "ECOLE TALET": "École élémentaire Marie Talet École maternelle Marie Talet",
    "ECOLE TIGEOT": "École élémentaire Adrien Tigeot École maternelle Adrien Tigeot",
    "ECOLE VALERY": "École maternelle Paul Valéry École élémentaire Paul Valéry",
    "ECOLE VERNE": "École élémentaire Jules Verne École maternelle Jules Verne"
};

/* ============================
   PARAMÈTRE URL
============================ */
function getSchoolParam() {
    const params = new URLSearchParams(window.location.search);
    return params.get('school');
}
const selectedEcole = getSchoolParam();

// Fonction pour normaliser chaîne (retirer accents, minuscule)
const normalize = s => s ? s.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim() : '';

/* ============================
   FETCH MENUS
============================ */
const apiUrl =
    "https://data.angers.fr/api/records/1.0/search/?" +
    "dataset=scdl_menus_restauration_scolaire_angers" +
    "&rows=2000" +
    "&sort=menudate" +
    "&where=menudate >= '" + start + "'";

fetch(apiUrl)
    .then(r => r.json())
    .then(data => {

        let records = data.records || [];

        /* ============================
           FILTRE PAR ÉCOLE
        ============================ */
        if (selectedEcole) {
            let selectedKey = null;

            // Cherche la clé API correspondant à l'école choisie
            for (const [key, officialName] of Object.entries(mappingMenusEcoles)) {
                if (normalize(officialName).includes(normalize(selectedEcole))) {
                    selectedKey = key;
                    break;
                }
            }

            if (selectedKey) {
                records = records.filter(r => r.fields?.menurestaurantnom === selectedKey);
            } else {
                // tentative match direct sur le nom API si aucune correspondance
                records = records.filter(r =>
                    normalize(r.fields?.menurestaurantnom).includes(normalize(selectedEcole))
                );
            }
        }

        if (records.length === 0) {
            weekGrid.innerHTML = '<div class="text-sm text-gray-600 p-4">Aucun menu trouvé pour cette période.</div>';
            return;
        }

        /* ============================
           GROUPEMENT PAR JOUR
        ============================ */
        const grouped = {};
        records.forEach(rec => {
            const f = rec.fields;
            const date = f.menudate;
            if (!grouped[date]) grouped[date] = [];
            grouped[date].push(f);
        });

        /* ============================
           AFFICHAGE EN COLONNES
        ============================ */
        weekGrid.innerHTML = "";

        const orderedDates = Object.keys(grouped).sort();

        orderedDates.forEach(date => {
            const col = document.createElement('div');
            // Ajout de min-w-0 pour forcer le retour à la ligne dans la grille, et overflow-hidden par sécurité
            col.className = "bg-white p-4 rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 min-h-[350px] flex flex-col hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] transition-all duration-300 transform hover:-translate-y-1 w-full min-w-0 overflow-hidden";

            const title = document.createElement('h3');
            title.className = "font-bold text-lg mb-6 text-center pb-4 border-b-2 border-blue-50 text-blue-900 uppercase tracking-wider bg-gradient-to-r from-transparent via-blue-50/50 to-transparent truncate px-2";
            title.textContent = new Date(date).toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
            col.appendChild(title);

            // Regrouper les plats par catégories basées sur 'menuplattype'
            const uniqueMenus = {};
            const dayRecords = grouped[date] || [];

            // Si c'est mercredi (ou un jour sans données), on laisse uniqueMenus vide
            // Le rendu affichera juste la colonne avec le titre
            
            dayRecords.forEach(menu => {
                const restaurantName = menu.menurestaurantnom;

                if (!uniqueMenus[restaurantName]) {
                    uniqueMenus[restaurantName] = {
                        restaurant: restaurantName,
                        entrees: [],
                        plats: [],
                        accompagnements: [], // correspond à Garniture
                        laitiers: [],
                        desserts: [],
                        pains: [],
                        divers: []
                    };
                }

                const type = (menu.menuplattype || "").toLowerCase();
                const nom = menu.menuplatnom;

                if (nom) {
                    const target = uniqueMenus[restaurantName];
                    let list = null;

                    if (type.includes('entrée') || type.includes('entree')) {
                        list = target.entrees;
                    } else if (type.includes('plat')) {
                        list = target.plats;
                    } else if (type.includes('garniture') || type.includes('accompagnement') || type.includes('legume')) {
                        list = target.accompagnements;
                    } else if (type.includes('laitier') || type.includes('fromage') || type.includes('yaourt')) {
                        list = target.laitiers;
                    } else if (type.includes('dessert') || type.includes('fruit') || type.includes('gouter')) {
                        list = target.desserts;
                    } else if (type.includes('pain')) {
                        list = target.pains;
                    } else {
                        list = target.divers;
                    }

                    // Objet plat avec ses propriétés
                    const itemData = {
                        nom: nom,
                        bio: !!(menu.menuplatlabelabio && menu.menuplatlabelabio.trim() !== ""),
                        aop: !!((menu.menuplatlabelaop && menu.menuplatlabelaop.trim() !== "") || (menu.menuplatlabelaoc && menu.menuplatlabelaoc.trim() !== "")),
                        sansViande: (menu.menuplatregime && menu.menuplatregime.toLowerCase().includes('sans viande'))
                    };

                    // Vérifier doublon (sur le nom uniquement pour éviter répétition)
                    if (!list.some(i => i.nom === nom)) {
                        list.push(itemData);
                    }
                }
            });

            // Afficher les menus uniques
            Object.values(uniqueMenus).forEach(menuData => {
                const box = document.createElement('div');
                box.className = "mb-4 p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm shadow-sm hover:bg-white hover:border-blue-100 hover:shadow-md transition-all duration-200 group";

                // Helper pour afficher une ligne
                const addLine = (colorClass, label, items) => {
                    if (items.length > 0) {
                        const itemsHtml = items.map(item => {
                            let labels = "";
                            if (item.bio) labels += `<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-green-100 text-green-700 font-bold uppercase tracking-tighter border border-green-200" title="Bio">BIO</span>`;
                            if (item.aop) labels += `<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-red-100 text-red-700 font-bold uppercase tracking-tighter border border-red-200" title="Appellation d'Origine Protégée">AOP</span>`;
                            if (item.sansViande) labels += `<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-indigo-100 text-indigo-700 font-bold uppercase tracking-tighter border border-indigo-200" title="Sans viande">VÉGÉ</span>`;
                            
                            return `<span class="text-slate-600">${item.nom}${labels}</span>`;
                        }).join('<span class="mx-1 text-slate-400">ou</span>');

                        box.innerHTML += `
                            <div class="mb-2 last:mb-0">
                                <span class="inline-block w-2 h-2 rounded-full ${colorClass} mr-2"></span>
                                <span class="font-bold text-slate-700">${label} :</span> 
                                ${itemsHtml}
                            </div>`;
                    }
                };

                addLine('bg-green-400', 'Entrée', menuData.entrees);
                addLine('bg-blue-400', 'Plat principal', menuData.plats);
                addLine('bg-yellow-400', 'Garniture', menuData.accompagnements);
                addLine('bg-cyan-400', 'Produit laitier', menuData.laitiers);
                addLine('bg-pink-400', 'Dessert', menuData.desserts);
                addLine('bg-amber-600', 'Pain', menuData.pains);
                addLine('bg-gray-400', 'Divers', menuData.divers);

                col.appendChild(box);
            });

            weekGrid.appendChild(col);
        });
    })
    .catch(err => {
        console.error('Erreur:', err);
        weekGrid.innerHTML = '<div class="text-sm text-red-500 p-4">Erreur lors du chargement des menus.</div>';
    });

/* ============================
   FILTRE PAR PLAT
============================ */
searchInput.addEventListener('input', () => {
    const filter = searchInput.value.toLowerCase();

    document.querySelectorAll('#weekGrid > div').forEach(col => {
        let visible = false;

        col.querySelectorAll('.mb-4').forEach(box => {
            if (box.textContent.toLowerCase().includes(filter)) {
                visible = true;
                box.style.display = "";
            } else {
                box.style.display = "none";
            }
        });

        col.style.display = visible ? "" : "none";
    });
});

</script>
@endsection
