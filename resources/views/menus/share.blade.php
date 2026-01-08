@extends('layouts.app')

@section('title', 'Menu partagé')
@section('no_background', true)

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md relative">
    <div class="w-full px-4 text-center">
        @if(isset($childName))
            <h1 class="text-3xl font-bold">Menu de {{ $childName }}</h1>
        @else
            <h1 class="text-3xl font-bold">Menu de la cantine</h1>
        @endif
        
        <div class="mt-2 text-blue-100 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="font-medium bg-blue-700/50 px-3 py-1 rounded-full border border-blue-500/30">
                {{ $school }}
            </span>
        </div>
    </div>
</header>
@endsection

@section('content')
<main class="w-full px-4 py-8">

    <div class="mb-4 max-w-2xl mx-auto flex gap-2">
        <button id="prevWeekBtn" class="px-4 py-3 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 text-gray-700 transition-colors">
            ← Semaine précédente
        </button>
        <div class="flex-grow text-center flex items-center justify-center bg-white border border-gray-300 rounded-xl shadow-sm font-semibold text-gray-700 select-none">
            <span id="currentWeekRange">Chargement...</span>
        </div>
        <button id="nextWeekBtn" class="px-4 py-3 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 text-gray-700 transition-colors">
            Semaine suivante →
        </button>
    </div>

    <!-- Planning hebdomadaire en colonnes -->
    <div class="flex justify-center">
        <div id="weekGrid" class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full"></div>
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
    
    <div class="mt-8 text-center">
         <a href="{{ route('bienvenue') }}" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium bg-blue-50 px-4 py-2 rounded-lg hover:bg-blue-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à l'accueil
        </a>
    </div>

</main>
@endsection

@section('scripts')
<script>
const weekGrid = document.getElementById('weekGrid');
const prevWeekBtn = document.getElementById('prevWeekBtn');
const nextWeekBtn = document.getElementById('nextWeekBtn');
const currentWeekRange = document.getElementById('currentWeekRange');

/* ============================
   DATE : Lundi de la semaine
============================ */
function getMonday(d) {
    d = new Date(d);
    d.setHours(0,0,0,0);
    const day = d.getDay(), diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

function formatDateISO(d) {
    const offset = d.getTimezoneOffset();
    const local = new Date(d.getTime() - (offset * 60 * 1000));
    return local.toISOString().slice(0, 10);
}

let currentMonday = getMonday(new Date());

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

// Injection de l'école depuis le contrôleur
const selectedEcole = @json($school);

// Fonction pout normaliser chaîne
const normalize = s => s ? s.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim() : '';

/* ============================
   FETCH & DISPLAY MENUS
============================ */
function loadWeekMenus() {
    const startStr = formatDateISO(currentMonday);
    
    // Calcul fin de semaine (Dimanche) pour le filtrage
    const nextSunday = new Date(currentMonday);
    nextSunday.setDate(nextSunday.getDate() + 6);
    const endStr = formatDateISO(nextSunday);

    const friday = new Date(currentMonday);
    friday.setDate(friday.getDate() + 4);
    const options = {day: 'numeric', month: 'long'};
    currentWeekRange.textContent = `Semaine du ${currentMonday.toLocaleDateString('fr-FR', options)} au ${friday.toLocaleDateString('fr-FR', options)}`;

    weekGrid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500 flex flex-col items-center"><span class="text-3xl animate-bounce mb-3">🍽️</span><span>Chargement des menus...</span></div>';

    let apiUrl =
        "https://data.angers.fr/api/records/1.0/search/?" +
        "dataset=scdl_menus_restauration_scolaire_angers" +
        "&rows=1000" +
        "&sort=menudate" +
        "&q=menudate:[" + startStr + " TO " + endStr + "]";

    let selectedKey = null;
    if (selectedEcole) {
        for (const [key, officialName] of Object.entries(mappingMenusEcoles)) {
            if (normalize(officialName).includes(normalize(selectedEcole))) {
                selectedKey = key;
                break;
            }
        }
        if (selectedKey) {
            apiUrl += "&refine.menurestaurantnom=" + encodeURIComponent(selectedKey);
        }
    }

    fetch(apiUrl)
    .then(r => r.json())
    .then(data => {
        let records = data.records || [];

        records = records.filter(r => {
            const date = r.fields?.menudate;
            return date >= startStr && date <= endStr;
        });

        if (selectedEcole) {
            if (selectedKey) {
                records = records.filter(r => r.fields?.menurestaurantnom === selectedKey);
            } else {
                records = records.filter(r =>
                    normalize(r.fields?.menurestaurantnom).includes(normalize(selectedEcole))
                );
            }
        }

        if (records.length === 0) {
            weekGrid.innerHTML = '<div class="text-sm text-gray-600 p-4 w-full text-center">Aucun menu trouvé pour cette période.</div>';
            return;
        }

        const grouped = {};
        records.forEach(rec => {
            const f = rec.fields;
            const date = f.menudate;
            if (!grouped[date]) grouped[date] = [];
            grouped[date].push(f);
        });

        weekGrid.innerHTML = "";
        
        // Jours à afficher : Lundi (0) à Vendredi (4)
        const daysOffsets = [0, 1, 3, 4];
        
        daysOffsets.forEach(offset => {
            const d = new Date(currentMonday);
            d.setDate(d.getDate() + offset);
            const dateStr = formatDateISO(d);
            
            if (!grouped[dateStr]) return;
            
            const dayRecords = grouped[dateStr] || [];

            const col = document.createElement('div');
            col.className = "bg-white p-4 rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 min-h-[350px] flex flex-col w-full min-w-0 overflow-hidden";

            const title = document.createElement('h3');
            title.className = "font-bold text-lg mb-6 text-center pb-4 border-b-2 border-blue-50 text-blue-900 uppercase tracking-wider bg-gradient-to-r from-transparent via-blue-50/50 to-transparent truncate px-2";
            title.textContent = new Date(dateStr).toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
            col.appendChild(title);

            const uniqueMenus = {};
            
            dayRecords.forEach(menu => {
                const restaurantName = menu.menurestaurantnom;

                if (!uniqueMenus[restaurantName]) {
                    uniqueMenus[restaurantName] = {
                        entrees: [],
                        plats: [],
                        accompagnements: [],
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
                    let list = target.divers;

                    if (type.includes('entrée') || type.includes('entree')) list = target.entrees;
                    else if (type.includes('plat')) list = target.plats;
                    else if (type.includes('garniture') || type.includes('accompagnement') || type.includes('legume')) list = target.accompagnements;
                    else if (type.includes('laitier') || type.includes('fromage') || type.includes('yaourt')) list = target.laitiers;
                    else if (type.includes('dessert') || type.includes('fruit') || type.includes('gouter')) list = target.desserts;
                    else if (type.includes('pain')) list = target.pains;

                    const itemData = {
                        nom: nom,
                        bio: !!(menu.menuplatlabelabio && menu.menuplatlabelabio.trim() !== ""),
                        aop: !!((menu.menuplatlabelaop && menu.menuplatlabelaop.trim() !== "") || (menu.menuplatlabelaoc && menu.menuplatlabelaoc.trim() !== "")),
                        sansViande: (menu.menuplatregime && menu.menuplatregime.toLowerCase().includes('sans viande'))
                    };

                    if (!list.some(i => i.nom === nom)) {
                        list.push(itemData);
                    }
                }
            });

            Object.values(uniqueMenus).forEach(menuData => {
                const box = document.createElement('div');
                box.className = "mb-4 p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm shadow-sm";

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
        weekGrid.innerHTML = '<div class="col-span-full text-center text-red-500 p-4">Erreur lors du chargement des menus.</div>';
    });
}

loadWeekMenus();

prevWeekBtn.addEventListener('click', () => {
    currentMonday.setDate(currentMonday.getDate() - 7);
    loadWeekMenus();
});

nextWeekBtn.addEventListener('click', () => {
    currentMonday.setDate(currentMonday.getDate() + 7);
    loadWeekMenus();
});
</script>
@endsection
