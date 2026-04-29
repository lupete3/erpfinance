@extends('components.layouts.pdf')

@section('title', __('stockreport.report_title'))
@section('report-title', __('stockreport.report_title'))

@section('content')
    {{-- Information Magasin --}}
    <h3><strong>{{ __('stockreport.store') }}:</strong>
        @if($store)
            {{ $store->name ?? '-' }}
        @else
            {{ __('stockreport.all_stores') }}
        @endif
    </h3>
    
    {{-- Tableau de stock --}}
    <table class="table">
        <thead class="table-light">
            <tr>
                <th>{{ __('stockreport.product') }}</th>
                <th>{{ __('stockreport.category') }}</th>
                <th>{{ __('stockreport.store') }}</th>
                <th>{{ __('stockreport.current_stock') }}</th>
                <th>{{ __('stockreport.purchase_value') }}</th>
                <th>{{ __('stockreport.sale_value') }}</th>
                <th>{{ __('stockreport.expected_profit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
                @php
                    $stock = $store
                        ? ($prod->stores->first()->pivot->quantity ?? 0)
                        : $prod->stores->sum('pivot.quantity');
                    $valeurAchat = $stock * $prod->purchase_price;
                    $valeurVente = $stock * $prod->sale_price;
                    $benefice = $valeurVente - $valeurAchat;
                @endphp
                <tr>
                    <td>{{ $prod->name }}</td>
                    <td>{{ $prod->category->name ?? '-' }}</td>
                    <td>
                        {{-- Affichage du magasin dans la ligne du tableau --}}
                        @if($store)
                            {{ $store->name ?? '-' }}
                        @else
                            {{ __('stockreport.all_stores') }}
                        @endif
                    </td>
                    <td>{{ $stock }}</td>
                    <td>{{ number_format($valeurAchat, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($valeurVente, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($benefice, 2, ',', ' ') }} {{ company()?->devise }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection