<?php

namespace App\Livewire\Settings;


use App\Models\CompanySetting;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use function Flasher\Notyf\Prime\notyf;

/**
 * Gère la configuration de l'entreprise et l'historique des souscriptions.
 */
class CompanySettingsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $company;
    public $name, $address, $email, $phone, $rccm, $id_nat, $logo, $new_logo, $devise;
    public $activeTab = 'settings';

    /**
     * Initialisation du composant : charge les paramètres existants ou crée une nouvelle instance.
     */
    public function mount()
    {
        // Utilise firstOrCreate pour s'assurer qu'il y a toujours une ligne CompanySetting
        $this->company = CompanySetting::where('tenant_id', Auth::user()->tenant_id)->firstOrCreate([]);

        $this->fill([
            'name'      => $this->company->name,
            'address'   => $this->company->address,
            'email'     => $this->company->email,
            'phone'     => $this->company->phone,
            'rccm'      => $this->company->rccm,
            'id_nat'    => $this->company->id_nat,
            'devise'    => $this->company->devise,
            'logo'      => $this->company->logo,
        ]);
    }

    /**
     * Valide et sauvegarde les paramètres de l'entreprise.
     */
    public function save()
    {
        $this->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string|max:20',
            'new_logo'  => 'nullable|image|max:2048',
            'devise'    => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'rccm'      => 'nullable|string|max:100',
            'id_nat'    => 'nullable|string|max:100',
        ]);

        if ($this->new_logo) {
            // 1. Définir le chemin de destination PUBLIC
            $destinationPath = public_path('logos');

            // 2. S'assurer que le dossier existe
            if (!File::isDirectory($destinationPath)) {
                if (!File::makeDirectory($destinationPath, 0755, true)) {
                    notyf()->error(__('company.error_create_dir'));
                    return;
                }
            }

            // 3. Générer un nom unique et sécurisé
            $safeOriginalName = pathinfo($this->new_logo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeOriginalName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $safeOriginalName);
            $extension = $this->new_logo->getClientOriginalExtension();
            $imageName = time() . '_' . $safeOriginalName . '.' . $extension;
            $fullDestination = $destinationPath . '/' . $imageName;

            $success = false;

            try {
                $this->new_logo->move($destinationPath, $imageName);
                $success = true;
            } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {

                // Tenter une méthode de secours (copy et delete) en cas de problème de permissions
                if (copy($this->new_logo->getRealPath(), $fullDestination)) {
                    if (File::delete($this->new_logo->getRealPath())) {
                        $success = true;
                    } else {
                        // La copie a réussi mais la suppression a échoué (un cas rare)
                        notyf()->error(__('company.error_temp_delete'));
                        $success = true; // On considère l'opération réussie pour le chemin
                    }
                } else {
                    // Si move ET copy échouent, c'est clairement un problème de permissions
                    notyf()->error(__('company.error_permission_denied') . ": " . $e->getMessage());
                    return;
                }
            }

            if ($success) {
                // 5. Supprimer l'ancien logo APRÈS avoir vérifié que le nouveau a bien été enregistré
                if ($this->company->logo && File::exists(public_path($this->company->logo))) {
                    File::delete(public_path($this->company->logo));
                }

                // 6. Enregistrer le chemin RELATIF dans la DB
                $this->logo = 'logos/' . $imageName;
                $this->new_logo = null; // Réinitialiser le champ de téléchargement
            } else {
                notyf()->error(__('company.error_save_fail_generic'));
                return;
            }
        }

        $this->company->update([
            'tenant_id' => Auth::user()?->tenant_id,
            'name'      => $this->name,
            'address'   => $this->address,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'rccm'      => $this->rccm,
            'id_nat'    => $this->id_nat,
            'logo'      => $this->logo, // Utilise la nouvelle valeur ou l'ancienne (car $this->logo est mis à jour)
            'devise'    => $this->devise,
        ]);

        notyf()->success(__('company.saved_success'));
    }

    /**
     * Rend la vue Blade et récupère les souscriptions pour l'affichage.
     */
    public function render()
    {
        $tenant = Auth::user()->tenant;

        // Assurez-vous d'avoir accès au modèle Subscription et à la relation 'plan'
        $subscriptions = Subscription::with('plan')
            ->where('tenant_id', $tenant?->id)
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('livewire.settings.company-settings-manager', [
            'subscriptions' => $subscriptions,
        ]);
    }
}