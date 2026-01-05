<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Restauration scolaire - Carte des écoles</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- HEADER -->
    <header class="bg-blue-600 text-white py-6 shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold">Carte des écoles d'Angers</h1>
            <p class="text-blue-100 mt-1">Localisez facilement les établissements scolaires de la ville</p>
        </div>
    </header>

    <!-- LAYOUT : Sidebar + Map -->
    <div class="flex">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-white shadow-lg h-[calc(100vh-96px)] p-6 border-r overflow-y-auto">

        <!-- SECTION 1 : FILTRES -->
        <h2 class="text-xl font-semibold mb-4">Filtres</h2>

        <div class="space-y-5">

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="filter" value="maternelle" checked>
                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png"
                    class="w-4 h-7" alt="">
                <span class="text-sm">Maternelle</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="filter" value="elementaire" checked>
                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png"
                    class="w-4 h-7" alt="">
                <span class="text-sm">Élémentaire</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="filter" value="primaire" checked>
                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png"
                    class="w-4 h-7" alt="">
                <span class="text-sm">Groupe scolaire / Primaire</span>
            </label>

        </div>

        <!-- SÉPARATEUR -->
        <div class="my-8 border-t border-gray-300"></div>

        <!-- SECTION 2 : RECHERCHE + LISTE DES ÉCOLES -->
        <h2 class="text-xl font-semibold mb-4">Écoles</h2>

        <!-- Barre de recherche -->
        <input 
            type="text" 
            id="searchInput"
            placeholder="Rechercher une école..."
            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
        >

        <!-- Liste des écoles -->
        <div id="schoolList" class="mt-4 space-y-2 text-sm text-gray-700"></div>

    </aside>

        <!-- MAP -->
        <main class="flex-1">
            <div id="map"></div>
        </main>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        /* ------------------------------
           1. Initialisation de la carte
        ------------------------------ */
        const map = L.map('map').setView([47.4736, -0.5542], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        /* ------------------------------
        2. Géolocalisation utilisateur
        ------------------------------ */
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;

                map.setView([userLat, userLon], 14);

                L.marker([userLat, userLon], {
                    icon: iconRed
                }).addTo(map).bindPopup("Vous êtes ici");
            });
        }


        /* ------------------------------
           3. Icônes colorées
        ------------------------------ */
        const iconBlue = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const iconGreen = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const iconOrange = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const iconRed = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        function getMarkerIcon(type) {
            switch (type) {
                case "maternelle": return iconBlue;
                case "elementaire": return iconGreen;
                case "primaire": return iconOrange;
                default: return iconRed;
            }
        }

        /* ------------------------------
           4. Gestion des types d'école
        ------------------------------ */
        function getTypeSimplifie(type) {
            const t = type.toLowerCase();

            if (t.includes("maternelle")) return "maternelle";
            if (t.includes("élémentaire") || t.includes("elementaire")) return "elementaire";
            if (t.includes("primaire") || t.includes("groupe")) return "primaire";
            return "autre";
        }

        /* ------------------------------
           5. Affichage des écoles
        ------------------------------ */
        const ecoles = @json($ecoles);
        const markers = [];

        function afficherMarkers() {
            markers.forEach(m => map.removeLayer(m));
            markers.length = 0;

            const filtresActifs = Array.from(document.querySelectorAll(".filter:checked"))
                                       .map(f => f.value);

            ecoles.forEach(ecole => {
                if (!ecole.latitude || !ecole.longitude) return;

                const typeSimplifie = getTypeSimplifie(ecole.type);

                if (!filtresActifs.includes(typeSimplifie)) return;

                const marker = L.marker([ecole.latitude, ecole.longitude], {
                    icon: getMarkerIcon(typeSimplifie)
                }).addTo(map);

                marker.bindPopup(`
                    <strong>${ecole.nom}</strong><br>
                    Type : ${ecole.type}<br>
                    Adresse : ${ecole.adresse ?? 'Non renseignée'}<br>
                `);

                markers.push(marker);
            });
        }

        afficherMarkers();

        document.querySelectorAll(".filter").forEach(f => {
            f.addEventListener("change", afficherMarkers);
        });
        /* ------------------------------
        6. Recherche dynamique
        ------------------------------ */

        const searchInput = document.getElementById("searchInput");
        const schoolList = document.getElementById("schoolList");

        function afficherListeEcoles() {
            const query = searchInput.value.toLowerCase();
            schoolList.innerHTML = "";

            const filtresActifs = Array.from(document.querySelectorAll(".filter:checked"))
                                    .map(f => f.value);

            ecoles.forEach(ecole => {
                if (!ecole.latitude || !ecole.longitude) return;

                const typeSimplifie = getTypeSimplifie(ecole.type);
                if (!typeSimplifie) return;
                if (!filtresActifs.includes(typeSimplifie)) return;

                if (!ecole.nom.toLowerCase().includes(query)) return;

                const item = document.createElement("div");
                item.className = "p-2 rounded hover:bg-gray-100 cursor-pointer";

                item.innerHTML = `
                    <div class="font-medium">${ecole.nom}</div>
                    <div class="text-xs text-gray-500">${ecole.type}</div>
                `;

                item.addEventListener("click", () => {
                    map.setView([ecole.latitude, ecole.longitude], 16);
                    L.popup()
                        .setLatLng([ecole.latitude, ecole.longitude])
                        .setContent(`
                            <strong>${ecole.nom}</strong><br>
                            Type : ${ecole.type}<br>
                            Adresse : ${ecole.adresse ?? 'Non renseignée'}
                        `)
                        .openOn(map);
                });

                schoolList.appendChild(item);
            });
        }

        searchInput.addEventListener("input", afficherListeEcoles);
        document.querySelectorAll(".filter").forEach(f => {
            f.addEventListener("change", () => {
                afficherMarkers();
                afficherListeEcoles();
            });
        });

        // Initialisation
        afficherListeEcoles();
    </script>

</body>
</html>