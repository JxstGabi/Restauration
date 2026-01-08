@extends('layouts.app')

@section('title', 'Modifier un enfant')

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md relative">
    <div class="w-full px-4 text-center">
        <h1 class="text-3xl font-bold">Modifier un enfant</h1>
    </div>
</header>
@endsection

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('enfants.update', $enfant) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom de l'enfant</label>
                        <div class="mt-1">
                            <input type="text" name="prenom" id="prenom" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border" placeholder="Ex: Léo" value="{{ old('prenom', $enfant->prenom) }}">
                        </div>
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 mb-1">Sexe (Optionnel)</span>
                        <div class="flex items-center space-x-4">
                            <label class="cursor-pointer group">
                                <input type="radio" name="sexe" value="0" class="peer sr-only" @checked(old('sexe', $enfant->sexe) !== null && (int)old('sexe', $enfant->sexe) === 0)>
                                <div class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white group-hover:bg-gray-50 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 transition-all shadow-sm flex items-center gap-2">
                                     Garçon
                                </div>
                            </label>
                            <label class="cursor-pointer group">
                                <input type="radio" name="sexe" value="1" class="peer sr-only" @checked(old('sexe', $enfant->sexe) !== null && (int)old('sexe', $enfant->sexe) === 1)>
                                <div class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white group-hover:bg-gray-50 peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-500 transition-all shadow-sm flex items-center gap-2">
                                    Fille
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="ecole_input" class="block text-sm font-medium text-gray-700">École</label>
                        <div class="mt-1 relative">
                            @php
                                $currentEcoleId = old('ecole_id', $enfant->ecole_id);
                                $currentEcoleName = $ecoles->firstWhere('id', $currentEcoleId)?->nom ?? '';
                            @endphp
                            <input type="hidden" name="ecole_id" id="ecole_id" required value="{{ $currentEcoleId }}">
                            <input type="text" 
                                   id="ecole_input" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border" 
                                   placeholder="Rechercher une école..."
                                   autocomplete="off"
                                   value="{{ $currentEcoleName }}">
                            
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
                            Mettre à jour
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
    // Initialiser previousValue si un radio est déjà coché
    let previousValue = document.querySelector('input[type="radio"][name="sexe"]:checked')?.value || null;

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
        hiddenInput.value = ''; // Reset ID only if user types something
        showSuggestions(this.value);
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

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !list.contains(e.target) && !toggleBtn.contains(e.target)) {
            list.classList.add('hidden');
        }
    });

    // Form submission validation
    submitBtn.addEventListener('click', function(e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une école valide dans la liste.');
            input.focus();
        }
    });
</script>
@endsection
