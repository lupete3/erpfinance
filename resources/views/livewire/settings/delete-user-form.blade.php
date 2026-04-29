<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Supprime l'utilisateur actuellement connecté.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        // Supprime l'utilisateur et déconnecte
        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
};
?>

@section('title', 'Supprimer le compte')

<section>
    <hr class="my-4 w-50" />
    <div class="mb-5">
        <h5 class="mb-2">{{ __('Supprimer le compte') }}</h5>
        <p class="text-muted">{{ __('Supprimez votre compte et toutes ses ressources') }}</p>
    </div>

    <!-- Bouton pour ouvrir le modal -->
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        {{ __('Supprimer le compte') }}
    </button>

    <!-- Modal de confirmation -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Une fois votre compte supprimé, toutes ses données et ressources seront définitivement effacées. Veuillez saisir votre mot de passe pour confirmer la suppression.') }}</p>

                    <form wire:submit="deleteUser" class="space-y-3">
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                            <input type="password" id="password" wire:model="password" class="form-control" required />
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('Supprimer le compte') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
