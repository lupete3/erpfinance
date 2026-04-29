<div>
    <div class="row">
        <div class="col-md-8">
            {{-- Clé : titre_inventaire --}}
            <h3>{{ __('inventory_form.titre_inventaire') }}</h3>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-success" wire:click="saveInventory" wire:loading.attr="disabled">
                <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                <i class="bx bx-check me-1"></i> 
                {{-- Clé : valider_inventaire --}}
                {{ __('inventory_form.valider_inventaire') }}
            </button>
        </div>
    </div>

    <div>
      <div class="row mt-2">
        <div class="col-md-4">
          <select wire:model.live="selectedStoreId" class="form-select mb-3">
              {{-- Clé : selectionner_magasin --}}
              <option value="">{{ __('inventory_form.selectionner_magasin') }}</option>
              @foreach ($stores as $store)
                  <option value="{{ $store->id }}">{{ $store->name }}</option>
              @endforeach
          </select>
        </div>
      </div>
        @if ($selectedStoreId)
            {{-- Inventory Table --}}
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    {{-- Clé : produit --}}
                                    <th>{{ __('inventory_form.produit') }}</th>
                                    {{-- Clé : stock_theorique --}}
                                    <th>{{ __('inventory_form.stock_theorique') }}</th>
                                    {{-- Clé : stock_physique --}}
                                    <th>{{ __('inventory_form.stock_physique') }}</th>
                                    {{-- Clé : difference --}}
                                    <th>{{ __('inventory_form.difference') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr wire:key="{{ $product->id }}">
                                        <td>{{ $product->name }}</td>
                                        @php
                                            $theoretical_quantity = $product->stores->where('id', $selectedStoreId)->first()->pivot->quantity ?? 0;
                                        @endphp
                                        <td>{{ $theoretical_quantity }}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                wire:model.live.debounce.500ms="physical_quantities.{{ $product->id }}">
                                        </td>
                                        <td>
                                            {{ (intval($physical_quantities[$product->id] ?? 0)) - intval($theoretical_quantity) }}
                                        </td>
                                    </tr>
                                @empty
                                     <tr>
                                        <td colspan="4" class="text-center">
                                            {{-- Clé : aucun_produit --}}
                                            {{ __('inventory_form.aucun_produit') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>