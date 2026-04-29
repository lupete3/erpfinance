<div>
    {{-- Add button --}}
    <div class="d-flex justify-content-end align-items-center mb-3">
        <a href="{{ route('inventories.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> 
            {{-- Clé : demarrer_nouvel_inventaire --}}
            {{ __('inventory.demarrer_nouvel_inventaire') }}
        </a>
    </div>

    {{-- Inventories Table --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('inventory.id') }}</th>
                    <th>{{ __('inventory.date') }}</th>
                    <th>{{ __('inventory.utilisateur') }}</th>
                    <th>{{ __('inventory.magasin') }}</th>
                    <th>{{ __('inventory.statut') }}</th>
                    <th>{{ __('inventory.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($inventories as $inventory)
                    <tr wire:key="{{ $inventory->id }}">
                        <td>{{ $inventory->id }}</td>
                        <td>{{ $inventory->inventory_date }}</td>
                        <td>{{ $inventory->user->name ?? __('inventory.na') }}</td>
                        <td>{{ $inventory->store->name }}</td>
                        <td>
                            <span class="badge bg-label-{{ $inventory->status == 'validated' ? 'success' : 'warning' }}">
                                {{-- Clés : valide / en_attente --}}
                                {{ $inventory->status == 'validated' ? __('inventory.valide') : __('inventory.en_attente') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('inventories.show', $inventory->id) }}" class="btn btn-info btn-sm" wire:navigate>
                                <i class="bx bx-show me-1"></i> 
                                {{-- Clé : voir --}}
                                {{ __('inventory.voir') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            {{-- Clé : aucun_inventaire --}}
                            {{ __('inventory.aucun_inventaire') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $inventories->links() }}
    </div>
</div>