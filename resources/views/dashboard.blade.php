<x-layouts.app>
    <x-slot:title>
        Tableau de bord
    </x-slot:title>

    @if (Auth::user()->hasRoleString('Super Admin'))
        <livewire:super-admin.dashboard />
    @elseif (Auth::user()->hasRoleString('Boss'))
        <livewire:boss.dashboard />
    @elseif (Auth::user()->hasRoleString('Gérant'))
        <livewire:manager.dashboard />
    @else
        <div class="alert alert-warning">
            Rôle non reconnu. Veuillez contacter l'administrateur.
        </div>
    @endif
</x-layouts.app>