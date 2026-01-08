@extends('layouts.app')

@section('title', 'Ajouter un enfant')

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md relative">
    <div class="w-full px-4 text-center">
        <h1 class="text-3xl font-bold">Ajouter un enfant</h1>
    </div>
</header>
@endsection

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('enfants.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom de l'enfant</label>
                        <div class="mt-1">
                            <input type="text" name="prenom" id="prenom" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border" placeholder="Ex: Léo">
                        </div>
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 mb-1">Sexe (Optionnel)</span>
                        <div class="flex items-center space-x-4">
                            <label class="cursor-pointer group">
                                <input type="radio" name="sexe" value="0" class="peer sr-only">
                                <div class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white group-hover:bg-gray-50 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 transition-all shadow-sm flex items-center gap-2">
                                    Garçon
                                </div>
                            </label>
                            <label class="cursor-pointer group">
                                <input type="radio" name="sexe" value="1" class="peer sr-only">
                                <div class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white group-hover:bg-gray-50 peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-500 transition-all shadow-sm flex items-center gap-2">
                                    Fille
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Section raccourci "Même école que" --}}
                    @if(isset($siblings) && $siblings->count() > 0)
                    <div class="bg-blue-50/50 rounded-lg p-4 border border-blue-100">
                        <p class="text-xs font-semibold text-blue-800 uppercase tracking-wider mb-2">Choisir la même école que :</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($siblings as $sibling)
                                @if($sibling->ecole)
                                <button type="button" 
                                        class="sibling-school-btn inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-white border border-blue-200 shadow-sm text-gray-700 hover:bg-blue-500 hover:text-white hover:border-blue-500 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 group"
                                        data-school-id="{{ $sibling->ecole->id }}"
                                        data-school-name="{{ $sibling->ecole->nom }}">
                                    <span class="group-hover:text-white">{{ $sibling->prenom }}</span>
                                </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Champ de recherche d'école -->
                    <div>
                        <label for="ecole_input" class="block text-sm font-medium text-gray-700">École</label>
                        <div class="mt-1 relative">
                            <input type="hidden" name="ecole_id" id="ecole_id" required>
                            <input type="text" 
                                   id="ecole_input" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border" 
                                   placeholder="Rechercher une école..."
                                   autocomplete="off">
                            
                            <!-- Liste de suggestions -->
                            <ul id="suggestions-list" class="absolute z-50 w-full bg-white border border-gray-200 mt-1 max-h-48 overflow-y-auto rounded-md shadow-lg hidden text-left divide-y divide-gray-100"></ul>
                            
                            <div id="toggle-list" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 cursor-pointer hover:text-blue-600 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('ecole_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                        <a href="{{ route('enfants.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Deselect radio button on double click
    const radios = document.querySelectorAll('input[type="radio"][name="sexe"]');
    let previousValue = null;

    radios.forEach(radio => {
        radio.addEventListener('click', function() {
            if (this.value === previousValue) {
                this.checked = false;
                previousValue = null;
            } else {
                previousValue = this.value;
            }
        });
    });

    const schools = @json($ecoles);
    const input = document.getElementById('ecole_input');
    const hiddenInput = document.getElementById('ecole_id');
    const list = document.getElementById('suggestions-list');
    const toggleBtn = document.getElementById('toggle-list');
    const submitBtn = document.querySelector('button[type="submit"]');

    // Gestion du clic sur les boutons "Même école que"
    let currentSiblingSchoolButton = null;

    document.querySelectorAll('.sibling-school-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Si on clique sur le bouton déjà actif, on désélectionne
            if (currentSiblingSchoolButton === this) {
                input.value = '';
                hiddenInput.value = '';
                
                this.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                this.classList.add('bg-white', 'text-gray-700', 'border-blue-200');
                
                currentSiblingSchoolButton = null;
                return;
            }

            const id = this.dataset.schoolId;
            const name = this.dataset.schoolName;
            
            // Remplir les champs
            input.value = name;
            hiddenInput.value = id;
            
            // Masquer la liste si ouverte
            list.classList.add('hidden');
            
            // Feedback visuel temporaire sur le champ input
            input.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
            setTimeout(() => {
                input.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
            }, 600);

            // Désélectionner visuellement les autres boutons
            document.querySelectorAll('.sibling-school-btn').forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                b.classList.add('bg-white', 'text-gray-700', 'border-blue-200');
            });
            // Activer celui cliqué (style 'active')
            this.classList.remove('bg-white', 'text-gray-700', 'border-blue-200');
            this.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
            
            currentSiblingSchoolButton = this;
        });
    });

    function showSuggestions(val) {
        list.innerHTML = '';
        
        const filtered = val 
            ? schools.filter(s => s.nom.toLowerCase().includes(val.toLowerCase()))
            : schools;
        
        if (filtered.length > 0) {
            filtered.forEach(school => {
                const li = document.createElement('li');
                li.textContent = school.nom;
                li.className = "px-4 py-3 hover:bg-blue-50 cursor-pointer text-gray-700 text-sm transition-colors";
                li.onclick = () => {
                    input.value = school.nom;
                    hiddenInput.value = school.id;
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
        hiddenInput.value = ''; // Reset ID if user types something new
        showSuggestions(this.value);

        // Deselect sibling button if active
        if (currentSiblingSchoolButton) {
             currentSiblingSchoolButton.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
             currentSiblingSchoolButton.classList.add('bg-white', 'text-gray-700', 'border-blue-200');
             currentSiblingSchoolButton = null;
        }
    });

    input.addEventListener('focus', function() {
        showSuggestions(this.value);
    });

    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (list.classList.contains('hidden')) {
                showSuggestions(input.value);
            } else {
                list.classList.add('hidden');
            }
        });
    }

    // Ferme la liste si clic en dehors
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !list.contains(e.target) && !toggleBtn.contains(e.target)) {
            list.classList.add('hidden');
        }
    });
</script>
@endsection
