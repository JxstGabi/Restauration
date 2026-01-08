@extends('layouts.app')

@section('title', 'Modifier mon profil')

@section('header')
<header class="bg-blue-600 text-white py-6 shadow-md relative">
    <div class="w-full px-4 text-center">
        <h1 class="text-3xl font-bold">Modifier mon profil</h1>
    </div>
</header>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8">
            
            @if (session('status') === 'profile-updated')
                <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">Votre profil a été mis à jour avec succès.</span>
                </div>
            @endif

            <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1">
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 my-6"></div>
                <h3 class="text-lg font-medium text-gray-900">Changer de mot de passe</h3>
                <p class="text-xs text-gray-500 mb-4">Laissez vide si vous ne souhaitez pas modifier votre mot de passe.</p>

                <!-- Mot de passe actuel -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                    <div class="mt-1">
                        <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nouveau mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                    <div class="mt-1">
                        <input type="password" name="password" id="password" autocomplete="new-password"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                    <div class="mt-1">
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <a href="{{ route('bienvenue') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Retour à l'accueil
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>

            <div class="border-t border-gray-200 my-8"></div>
            
            <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                <h3 class="text-lg font-medium text-red-800">Supprimer le compte</h3>
                <p class="text-sm text-red-600 mt-1 mb-4">Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Veuillez saisir votre mot de passe pour confirmer.</p>
                
                <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement votre compte ?');">
                    @csrf
                    @method('delete')
                    
                    <div class="flex items-center gap-4">
                        <div class="flex-grow max-w-xs">
                            <label for="password_delete" class="sr-only">Mot de passe</label>
                            <input type="password" name="password" id="password_delete" placeholder="Votre mot de passe" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm px-4 py-2 border">
                            @error('password', 'userDeletion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Supprimer le compte
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
