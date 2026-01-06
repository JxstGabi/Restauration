@extends('layouts.app')

@section('title', 'Restauration scolaire - Carte des écoles')

@section('styles')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
    <header class="bg-blue-600 text-white py-6 shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold">Carte des écoles d'Angers</h1>
            <p class="text-blue-100 mt-1">Localisez facilement les établissements scolaires</p>
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
    <main>
        <div id="map"></div>
    </main>

</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

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
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            L.marker([lat, lng], { icon: iconRed })
             .addTo(map)
             .bindPopup("Vous êtes ici");

            map.setView([lat, lng], 14);
        });
    }

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

            const popupContent = `<strong><a href="/menus?school=${encodeURIComponent(ecole.nom)}">${ecole.nom}</a></strong><br>${ecole.type}`;
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

});
</script>
@endsection
