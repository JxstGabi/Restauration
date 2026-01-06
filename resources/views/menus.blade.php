@extends('layouts.app')

@section('title', 'Menus de restauration scolaire')

@section('header')
    <header class="bg-blue-600 text-white py-6 shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-2xl font-bold">Menus de restauration scolaire</h1>
            @if($school)
                <p class="text-blue-100 mt-1">École recherchée : <strong>{{ $school }}</strong></p>
            @endif
        </div>
    </header>
@endsection

@section('content')
    <main class="max-w-6xl mx-auto px-4 py-6">
        <div id="menuList" class="space-y-4">
            <div class="text-sm text-gray-500">Chargement des menus…</div>
        </div>
    </main>
@endsection

@section('scripts')
<script>
const school = {!! json_encode($school) !!};
const menuList = document.getElementById('menuList');

// Construire l'URL de l'API Socrata (Data.angers.fr)
let apiUrl = 'https://data.angers.fr/api/records/1.0/search/?dataset=scdl_menus_restauration_scolaire_angers&rows=50&sort=-menudate';
if (school) {
    apiUrl += '&q=' + encodeURIComponent(school);
}

fetch(apiUrl)
    .then(r => r.json())
    .then(data => {
        const records = data.records || [];
        menuList.innerHTML = '';
        if (records.length === 0) {
            menuList.innerHTML = '<div class="text-sm text-gray-600">Aucun menu trouvé pour cette école.</div>';
            return;
        }

        records.forEach(rec => {
            const f = rec.fields || {};
            const date = f.menudate || f.date || '';
            const libelle = f.libelle || f.menu || JSON.stringify(f);

            const el = document.createElement('div');
            el.className = 'p-4 bg-white rounded shadow-sm';
            el.innerHTML = `
                <div class="text-sm text-gray-500">${date}</div>
                <div class="mt-1 text-gray-800">${libelle}</div>
            `;
            menuList.appendChild(el);
        });
    })
    .catch(err => {
        menuList.innerHTML = '<div class="text-sm text-red-500">Erreur lors du chargement des menus.</div>';
        console.error(err);
    });
</script>
@endsection
