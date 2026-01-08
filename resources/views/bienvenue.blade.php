@extends('layouts.app')

@section('title', 'Bienvenue')

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md relative z-20">
    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center space-x-4">
        @auth
            <span class="text-sm text-blue-100 hidden sm:inline">Bonjour, {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded transition-colors border border-white/20">
                    Déconnexion
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-sm font-medium text-blue-100 hover:text-white transition-colors">Connexion</a>
            <a href="{{ route('register') }}" class="px-3 py-1.5 rounded text-sm font-medium bg-white text-blue-600 hover:bg-blue-50 transition-colors shadow-sm">Inscription</a>
        @endauth
    </div>
    <div class="w-full px-4 text-center">
        <h1 class="text-3xl font-bold">Restauration Scolaire</h1>
        <p class="text-blue-100 mt-2 text-lg">Découvrez les menus de votre établissement</p>
    </div>
</header>
@endsection

@section('content')

<div class="h-[calc(100vh-96px)] flex items-center justify-center px-4 overflow-hidden">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-5xl max-h-full">
        
        @auth
        <!-- Card: Menu du jour des enfants -->
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full transform transition-all animate-fade-in-up border border-gray-100 flex flex-col order-2 md:order-1 max-h-[60vh] md:max-h-[calc(100vh-140px)] overflow-hidden">
            <div class="mb-5 text-center">
                <h2 class="text-xl font-bold text-gray-800 tracking-tight">Menu du jour</h2>
                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM') }}</p>
            </div>

            @if(isset($enfants) && $enfants->count() > 0)
                <div id="children-menus-container" class="space-y-6 flex-grow overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                    @foreach($enfants as $enfant)
                        <div class="child-menu-item bg-gray-50 rounded-xl p-4 border border-gray-100" data-school="{{ $enfant->ecole->nom }}">
                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ is_null($enfant->sexe) ? 'bg-gray-400' : ($enfant->sexe == 1 ? 'bg-pink-500' : 'bg-blue-500') }}"></span>
                                    {{ $enfant->prenom }}
                                </h3>
                                <span class="text-[10px] text-gray-500 bg-white px-2 py-0.5 rounded border border-gray-200 truncate max-w-[150px]">{{ $enfant->ecole->nom }}</span>
                            </div>
                            <div class="menu-content text-sm text-gray-600 min-h-[60px] flex items-center justify-center">
                                <span class="animate-pulse">Chargement du menu...</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('enfants.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Gérer mes enfants</a>
                </div>
            @else
                <div class="text-center py-6 flex-grow flex flex-col justify-center">
                    <p class="text-gray-500 text-sm mb-4">Aucun enfant inscrit pour le moment.</p>
                    <a href="{{ route('enfants.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Ajouter un enfant
                    </a>
                </div>
            @endif
        </div>
        @endauth

        <!-- Card: Accès aux menus (Recherche) -->
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full text-center transform transition-all animate-fade-in-up border border-gray-100 h-fit order-1 md:order-2 {{ Auth::check() ? '' : 'md:col-span-2 md:max-w-sm md:mx-auto' }}">
        
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">Accès aux menus</h2>
            <p class="text-sm text-gray-500 mt-1">Sélectionnez une école ou consultez la carte</p>
        </div>

        <div class="space-y-4">
            <!-- Sélection directe -->
            <form action="{{ route('menus.index') }}" method="GET" class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-left">
            <!-- Sélection directe -->
            <form action="{{ route('menus.index') }}" method="GET" class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-left">
                <label for="school-input" class="block text-xs font-semibold text-gray-700 mb-1">Rechercher une école</label>
                <div class="space-y-2">
                    <div class="relative">
                        <input type="text" 
                               id="school-input" 
                               name="school" 
                               class="block w-full pl-3 pr-8 py-2 text-sm border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg shadow-sm border transition-colors bg-white"
                               placeholder="Ex: Marie Curie..."
                               autocomplete="off"
                               required>
                        
                        <!-- Liste de suggestions -->
                        <ul id="suggestions-list" class="absolute z-50 w-full bg-white border border-gray-200 mt-1 max-h-48 overflow-y-auto rounded-xl shadow-xl hidden text-left divide-y divide-gray-100"></ul>
                        
                        <div id="toggle-list" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 cursor-pointer hover:text-blue-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                        Voir les menus
                    </button>
                </div>
            </form>

            <div class="relative flex py-1 items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink-0 mx-3 text-gray-400 text-xs">ou</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <!-- Bouton vers la carte (Accueil) -->
            <a href="{{ route('map') }}" 
               class="group block w-full py-2.5 px-4 bg-white border border-blue-600 hover:bg-blue-50 text-blue-700 font-bold text-sm rounded-xl shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <span class="flex items-center justify-center gap-2">
                    🗺️ Carte interactive
                </span>
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
</style>
@endsection

@section('scripts')
<script>
    // --- PARTIE RECHERCHE ECOLE ---
    const schools = @json($ecoles->pluck('nom'));
    const input = document.getElementById('school-input');
    const list = document.getElementById('suggestions-list');
    const toggleBtn = document.getElementById('toggle-list');

    function showSuggestions(val) {
        list.innerHTML = '';
        
        // Si vide, on montre toutes les écoles
        const filtered = val 
            ? schools.filter(s => s.toLowerCase().includes(val.toLowerCase()))
            : schools;
        
        if (filtered.length > 0) {
            filtered.forEach(school => {
                const li = document.createElement('li');
                li.textContent = school;
                li.className = "px-4 py-3 hover:bg-blue-50 cursor-pointer text-gray-700 text-sm transition-colors";
                li.onclick = () => {
                    input.value = school;
                    list.classList.add('hidden');
                };
                list.appendChild(li);
            });
            list.classList.remove('hidden');
        } else {
            const li = document.createElement('li');
            li.textContent = "Aucune école trouvée";
            li.className = "px-4 py-3 text-gray-400 text-sm italic";
            list.appendChild(li);
            list.classList.remove('hidden');
        }
    }

    input.addEventListener('input', function() {
        showSuggestions(this.value);
    });

    input.addEventListener('focus', function() {
        // Affiche toujours la liste au focus (filtrée ou complète)
        showSuggestions(this.value);
    });

    // Toggle via le chevron
    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation(); 
            if (list.classList.contains('hidden')) {
                showSuggestions(input.value);
                input.focus();
            } else {
                list.classList.add('hidden');
            }
        });
    }

    // Fermer si on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !list.contains(e.target) && (!toggleBtn || !toggleBtn.contains(e.target))) {
            list.classList.add('hidden');
        }
    });

    // --- PARTIE MENU ENFANTS (SI AUTH) ---
    @auth
    (function() {
        // Mapping identiques à menus.blade.php
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

        const normalize = s => s ? s.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim() : '';

        // Date du jour ISO
        const today = new Date();
        const offset = today.getTimezoneOffset();
        const local = new Date(today.getTime() - (offset * 60 * 1000));
        const todayISO = local.toISOString().slice(0, 10);
        
        // Pour les tests ou le WE, on peut vouloir tricher ici si c'est vide
        // const todayISO = "2026-01-08"; // Décommenter pour forcer une date

        const childrenItems = document.querySelectorAll('.child-menu-item');
        
        childrenItems.forEach(item => {
            const schoolName = item.dataset.school;
            const contentDiv = item.querySelector('.menu-content');

            // Trouver la clé API
            let apiKey = null;
            for (const [key, officialName] of Object.entries(mappingMenusEcoles)) {
                if (normalize(officialName).includes(normalize(schoolName))) {
                    apiKey = key;
                    break;
                }
            }

            if (!apiKey) {
                // Fallback ou recherche floue par nom
                // on ne change rien, le fallback restera "Chargement..." ou on met erreur
                 contentDiv.innerHTML = '<span class="text-xs text-orange-500">École non trouvée API.</span>';
                return;
            }

            // Fetch
            const apiUrl = `https://data.angers.fr/api/records/1.0/search/?dataset=scdl_menus_restauration_scolaire_angers&rows=50&q=menudate:${todayISO} AND menurestaurantnom:"${apiKey}"`;
            
            fetch(apiUrl)
                .then(r => r.json())
                .then(data => {
                    const records = data.records || [];
                    
                    if (records.length === 0) {
                        contentDiv.innerHTML = '<span class="text-xs text-gray-400 italic">Pas de menu trouvé pour aujourd\'hui.</span>';
                        return;
                    }

                    // On a les plats, il faut les trier par type/catégorie
                    const plats = [];
                    
                    records.forEach(rec => {
                        const f = rec.fields;
                        const type = (f.menuplattype || "").toLowerCase();
                        const nom = f.menuplatnom;
                        
                        // Détermination de la catégorie pour le tri et la couleur
                        let category = 'divers';
                        let colorClass = 'bg-gray-400';

                        if (type.includes('entrée') || type.includes('entree')) {
                            category = 'entree';
                            colorClass = 'bg-green-400';
                        } else if (type.includes('plat')) {
                            category = 'plat';
                            colorClass = 'bg-blue-400';
                        } else if (type.includes('laitier') || type.includes('fromage') || type.includes('yaourt')) {
                            category = 'laitier';
                            colorClass = 'bg-cyan-400';
                        } else if (type.includes('dessert') || type.includes('fruit')) {
                            category = 'dessert';
                            colorClass = 'bg-pink-400';
                        } else if (type.includes('garniture') || type.includes('accompagnement')) {
                            category = 'garniture';
                            colorClass = 'bg-yellow-400';
                        } else if (type.includes('pain')) {
                            category = 'pain';
                            colorClass = 'bg-amber-600';
                        }

                        // Labels
                        const bio = !!(f.menuplatlabelabio && f.menuplatlabelabio.trim() !== "");
                        const aop = !!((f.menuplatlabelaop && f.menuplatlabelaop.trim() !== "") || (f.menuplatlabelaoc && f.menuplatlabelaoc.trim() !== ""));
                        const sansViande = (f.menuplatregime && f.menuplatregime.toLowerCase().includes('sans viande'));

                        if(nom) plats.push({ nom, category, colorClass, bio, aop, sansViande });
                    });

                    // Construction HTML
                    // Ordre de priorité
                    const order = ['entree', 'plat', 'garniture', 'laitier', 'dessert', 'pain', 'divers'];
                    
                    // Simple sort
                    plats.sort((a,b) => {
                        let iA = order.indexOf(a.category);
                        let iB = order.indexOf(b.category);
                        if(iA === -1) iA = 99;
                        if(iB === -1) iB = 99;
                        return iA - iB;
                    });
                    
                    // Deduplicate
                    const uniquePlats = [];
                    const seen = new Set();
                    plats.forEach(p => {
                        if(!seen.has(p.nom)) {
                            seen.add(p.nom);
                            uniquePlats.push(p);
                        }
                    });

                    if(uniquePlats.length > 0) {
                        let html = '<ul class="w-full space-y-2 text-left">';
                        uniquePlats.forEach(p => {
                           let labels = "";
                           if (p.bio) labels += `<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-green-100 text-green-700 font-bold uppercase tracking-tighter border border-green-200" title="Bio">BIO</span>`;
                           if (p.aop) labels += `<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-red-100 text-red-700 font-bold uppercase tracking-tighter border border-red-200" title="AOP">AOP</span>`;
                           if (p.sansViande) labels += `<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-indigo-100 text-indigo-700 font-bold uppercase tracking-tighter border border-indigo-200" title="VÉGÉ">VÉGÉ</span>`;

                           html += `<li class="flex items-start gap-3 text-sm leading-snug">
                                        <span class="w-2 h-2 rounded-full ${p.colorClass} mt-1.5 flex-shrink-0 shadow-sm" title="${p.category}"></span> 
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="text-gray-700 font-medium">${p.nom}</span>
                                            ${labels}
                                        </div>
                                    </li>`;
                        });
                        html += '</ul>';
                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML = '<span class="text-xs text-gray-400 italic">Menu vide.</span>';
                    }

                })
                .catch(err => {
                    console.error(err);
                    contentDiv.innerHTML = '<span class="text-xs text-red-400">Erreur réseau.</span>';
                });
        });

    })();
    @endauth
</script>
@endsection
