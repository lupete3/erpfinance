<div>
    <div class="row mb-3">
        <div class="col">
            <label>Magasin Source</label>
            <select wire:model.lazy="from_store_id" class="form-control">
                <option value="">-- choisir --</option>
                @foreach ($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label>Magasin Destination</label>
            <select wire:model.lazy="to_store_id" class="form-control">
                <option value="">-- choisir --</option>
                @foreach ($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">

      <div class="col-md-6">

        @if ($products && count($products) > 0)
            <h5>Produits disponibles</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Stock</th>
                        <th>Quantité à transférer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>
                                @if ($p->pivot)
                                    {{ $p->pivot->quantity }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>
                                <input type="number" wire:model.defer="cartQty.{{ $p->id }}" class="form-control"
                                    min="0"
                                    max="
                                    @if ($p->pivot) {{ $p->pivot->quantity }}
                                    @else
                                        0 @endif ">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button wire:click="addAllToCart" class="btn btn-primary mt-3">Ajouter au panier</button>
        @endif

      </div>
      <div class="col-md-6">

        @if ($productsTo && count($productsTo) > 0)
            <h5>Produits disponibles</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Stock Actuel</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productsTo as $productsTo)
                        <tr>
                            <td>{{ $productsTo->name }}</td>
                            <td>
                                @if ($productsTo->pivot)
                                    {{ $productsTo->pivot->quantity }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button wire:click="addAllToCart" class="btn btn-primary mt-3">Ajouter au panier</button>
        @endif

      </div>

      <div class="col-md-5">

        @if ($cart && count($cart) > 0)
            <h5>Panier de transfert</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $id => $item)
                        <tr>
                            <td>{{ $item['product']->name }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>
                                <button wire:click="removeFromCart({{ $id }})"
                                    class="btn btn-sm btn-danger">Retirer</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button wire:click="validateTransfer" class="btn btn-success">Valider le transfert</button>
        @endif

      </div>

    </div>

</div>
