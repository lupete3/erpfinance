<div>
    <h4>Produits du magasin : {{ $store->name }}</h4>
    <div class="table-responsive">
      <table class="table">
          <thead>
              <tr>
                  <th>Réf</th>
                  <th>Nom</th>
                  <th>Catégorie</th>
                  <th>Stock</th>
              </tr>
          </thead>
          <tbody>
              @foreach ($products as $product)
                  <tr>
                      <td>{{ $product->reference }}</td>
                      <td>{{ $product->name }}</td>
                      <td>{{ $product->category->name ?? '' }}</td>
                      <td>{{ $product->pivot->quantity }}</td>
                  </tr>
              @endforeach
          </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $products->links() }}
    </div>
</div>
