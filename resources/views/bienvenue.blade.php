@extends('layouts.app')

@section('title', 'Bienvenue - Restauration Scolaire')

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md">
    <div class="w-full px-4 text-center">
        <h1 class="text-3xl font-bold">Restauration Scolaire</h1>
        <p class="text-blue-100 mt-2 text-lg">Découvrez les menus de votre établissement</p>
    </div>
</header>
@endsection

@section('content')
<div class="h-[calc(100vh-140px)] flex flex-col justify-center items-center px-4 overflow-hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full text-center transform transition-all animate-fade-in-up border border-gray-100">
        
        <div class="mb-5">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl shadow-sm">
                🍽️
            </div>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">Accès aux menus</h2>
            <p class="text-sm text-gray-500 mt-1">Sélectionnez une école ou consultez la carte</p>
        </div>

        <div class="space-y-4">
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
</script>
@endsection
