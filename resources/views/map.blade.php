@extends('layouts.app')

@section('title', 'Carte des écoles')

@section('styles')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Itinéraire Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        html, body {
            overflow: hidden;
            margin: 0;
            height: 100%;
        }

        /* Container principal en flex */
        .flex-container {
            display: flex;
            height: calc(100vh - 96px); /* header = 96px */
        }

        aside {
            width: 18rem;
            overflow-y: auto;
        }
        main {
            flex: 1;
            height: 100%;
        }

        #map {
            flex: 1;
            height: 100%;
        }
    </style>
@endsection

@section('header')
    <header class="bg-blue-600 text-white py-6 shadow-md relative">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-center relative transform">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                <a href="{{ route('bienvenue') }}" class="p-2 rounded-full hover:bg-blue-500 transition-colors" title="Retour à l'accueil">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <button id="centerUserBtn" class="hidden sm:inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-xs px-3 py-1.5 rounded-full transition-colors border border-blue-500 shadow-sm" title="Me localiser">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Ma position</span>
                </button>
            </div>
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
            <div class="text-center">
                <h1 class="text-3xl font-bold">Carte des écoles d'Angers</h1>
                <p class="text-blue-100 mt-1">Localisez facilement les établissements scolaires</p>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="flex-container">

    <!-- SIDEBAR -->
    <aside class="bg-white shadow-lg p-6 border-r">
        <h2 class="text-xl font-semibold mb-4">Filtres</h2>

        <div class="space-y-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="filter" value="maternelle" checked>
                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png" class="w-4 h-7">
                <span class="text-sm">Maternelle</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="filter" value="elementaire" checked>
                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png" class="w-4 h-7">
                <span class="text-sm">Élémentaire</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="filter" value="primaire" checked>
                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png" class="w-4 h-7">
                <span class="text-sm">Groupe scolaire / Primaire</span>
            </label>
        </div>

        <div class="my-8 border-t border-gray-300"></div>

        <h2 class="text-xl font-semibold mb-4">Écoles</h2>
        <input type="text" id="searchInput" placeholder="Rechercher une école..."
               class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <div id="schoolList" class="mt-4 space-y-2 text-sm"></div>

        <div class="flex justify-between items-center mt-4 text-sm">
            <button id="prevPage" class="px-3 py-1 border rounded disabled:opacity-50">◀</button>
            <span id="pageInfo"></span>
            <button id="nextPage" class="px-3 py-1 border rounded disabled:opacity-50">▶</button>
        </div>
    </aside>

    <!-- MAP -->
    <main class="relative h-full w-full">
        <div id="map" class="h-full w-full"></div>
    </main>

</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // VARIABLES GLOBALES
    let userLat = null;
    let userLng = null;
    let routingControl = null;
    let clearControl = null;

    function clearRoute() {
        if (routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }
        if (clearControl) {
            map.removeControl(clearControl);
            clearControl = null;
        }
    }

    /* =========================
       MAP
    ========================= */
    const map = L.map('map').setView([47.4736, -0.5542], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    /* =========================
       ICONS
    ========================= */
    const iconBlue = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png', shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png', iconSize: [25,41], iconAnchor: [12,41] });
    const iconGreen = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png', shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png', iconSize: [25,41], iconAnchor: [12,41] });
    const iconOrange = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png', shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png', iconSize: [25,41], iconAnchor: [12,41] });
    const iconRed = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png', shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png', iconSize: [25,41], iconAnchor: [12,41] });

    function getMarkerIcon(type) {
        if (type === "maternelle") return iconBlue;
        if (type === "elementaire") return iconGreen;
        if (type === "primaire") return iconOrange;
        return iconRed;
    }

    /* =========================
       GEOLOCALISATION
    ========================= */
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;

            L.marker([userLat, userLng], { icon: iconRed })
             .addTo(map)
             .bindPopup("Vous êtes ici");

            map.setView([userLat, userLng], 14);
        });
    }

    /* =========================
       ROUTING
    ========================= */
    const ClearRouteControl = L.Control.extend({
        options: {
            position: 'topright'
        },
        onAdd: function(map) {
            const container = L.DomUtil.create('div', 'leaflet-control leaflet-bar');
            const button = L.DomUtil.create('a', '', container);
            button.innerHTML = 'Effacer l\'itinéraire';
            button.href = '#';
            button.role = 'button';
            
            // Reset Leaflet specific styles for anchor in bar
            button.style.width = 'auto';
            button.style.height = 'auto';
            
            // Custom styles
            button.style.backgroundColor = 'white';
            button.style.padding = '8px 12px';
            button.style.fontWeight = 'bold';
            button.style.color = '#dc2626';
            button.style.textDecoration = 'none';
            button.style.cursor = 'pointer';
            button.style.fontSize = '13px';
            button.style.display = 'block';
            button.style.whiteSpace = 'nowrap';
            
            L.DomEvent.on(button, 'click', function(e) {
                L.DomEvent.stop(e);
                clearRoute();
            });

            return container;
        }
    });

    window.tracerItineraire = function(destLat, destLng) {
        if (!userLat || !userLng) {
            alert("Votre position n'est pas connue. Autorisez la géolocalisation.");
            return;
        }

        clearRoute(); // Supprime l'existant s'il y en a un

        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(userLat, userLng),
                L.latLng(destLat, destLng)
            ],
            language: 'fr',
            routeWhileDragging: false,
            showAlternatives: false,
            createMarker: function() { return null; },
            lineOptions: {
                styles: [{color: '#6FA1EC', opacity: 0.8, weight: 6}]
            }
        }).addTo(map);
        
        // Ajoute le bouton effacer comme un contrôle Leaflet en dessous du panneau d'itinéraire
        clearControl = new ClearRouteControl().addTo(map);
    };

    /* =========================
       DATA
    ========================= */
    const ecoles = @json($ecoles);
    const markers = [];
    const searchInput = document.getElementById("searchInput");
    const schoolList = document.getElementById("schoolList");
    let currentPage = 1;
    const perPage = 8;

    function getTypeSimplifie(type) {
        if (!type) return null;
        const t = type.toLowerCase();
        if (t.includes("maternelle")) return "maternelle";
        if (t.includes("élémentaire") || t.includes("elementaire")) return "elementaire";
        if (t.includes("primaire") || t.includes("groupe")) return "primaire";
        return null;
    }

    function refreshUI() {
        const query = searchInput.value.toLowerCase();
        const filtresActifs = Array.from(document.querySelectorAll(".filter:checked")).map(f => f.value);

        // clear markers
        markers.forEach(m => map.removeLayer(m));
        markers.length = 0;

        const filtered = ecoles.filter(e => {
            const type = getTypeSimplifie(e.type);
            return e.latitude && e.longitude &&
                   type && filtresActifs.includes(type) &&
                   e.nom.toLowerCase().includes(query);
        });

        filtered.forEach(ecole => {
            const type = getTypeSimplifie(ecole.type);

            const marker = L.marker([ecole.latitude, ecole.longitude], {
                icon: getMarkerIcon(type)
            }).addTo(map);

            const popupContent = `
                <div class="text-center">
                    <strong><a href="/menus?school=${encodeURIComponent(ecole.nom)}" class="text-blue-600 hover:underline">${ecole.nom}</a></strong><br>
                    <span class="text-xs text-gray-500">${ecole.type}</span><br>
                    <button onclick="tracerItineraire(${ecole.latitude}, ${ecole.longitude})" 
                            class="mt-2 text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                        📍 Itinéraire
                    </button>
                </div>
            `;
            marker.bindPopup(popupContent);
            markers.push(marker);
        });

        // Liste paginée
        schoolList.innerHTML = "";
        const start = (currentPage - 1) * perPage;
        const paginated = filtered.slice(start, start + perPage);

        paginated.forEach(ecole => {
            const type = getTypeSimplifie(ecole.type);
            const color =
                type === "maternelle" ? "border-blue-500" :
                type === "elementaire" ? "border-green-500" :
                "border-orange-500";

            const item = document.createElement("div");
            item.className = `border-l-4 ${color} pl-3 py-2 rounded hover:bg-gray-100 cursor-pointer`;
            item.innerHTML = `<div class="font-medium">${ecole.nom}</div><div class="text-xs text-gray-500">${ecole.type}</div>`;

            item.onclick = () => map.setView([ecole.latitude, ecole.longitude], 16);

            schoolList.appendChild(item);
        });

        document.getElementById("pageInfo").textContent =
            `Page ${currentPage} / ${Math.max(1, Math.ceil(filtered.length / perPage))}`;

        document.getElementById("prevPage").disabled = currentPage === 1;
        document.getElementById("nextPage").disabled = currentPage >= Math.ceil(filtered.length / perPage);
    }

    // EVENTS
    searchInput.addEventListener("input", () => { currentPage = 1; refreshUI(); });
    document.querySelectorAll(".filter").forEach(f => f.addEventListener("change", () => { currentPage = 1; refreshUI(); }));
    document.getElementById("prevPage").onclick = () => { currentPage--; refreshUI(); };
    document.getElementById("nextPage").onclick = () => { currentPage++; refreshUI(); };

    refreshUI();

    // Recenter map on user logic
    const centerUserBtn = document.getElementById('centerUserBtn');
    if (centerUserBtn) {
        centerUserBtn.addEventListener('click', () => {
             if (userLat && userLng) {
                 map.setView([userLat, userLng], 14);
             } else {
                 if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        userLat = pos.coords.latitude;
                        userLng = pos.coords.longitude;
                        
                        // Update or create marker (simple version)
                        map.setView([userLat, userLng], 14);
                    }, () => {
                        alert("Impossible d'accéder à votre position.");
                    });
                 } else {
                     alert("Votre navigateur ne supporte pas la géolocalisation.");
                 }
             }
        });
    }

});
</script>
@endsection
