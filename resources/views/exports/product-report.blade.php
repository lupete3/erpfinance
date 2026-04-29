@extends('components.layouts.pdf')

@section('title', __('productreport.report_title'))
@section('report-title', __('productreport.report_title'))

@section('content')
    {{-- Filtre du rapport --}}
    <h4>{{ __('productreport.store') }}: {{ $store ? $store->name : __('productreport.all_stores') }}</h4>
    
    {{-- Tableau produits --}}
    <table class="table table-bordered" width="100%" cellspacing="0" cellpadding="4" style="font-size: 9px;">
        <thead>
            <tr>
                <th>{{ __('productreport.ref') }}</th>
                <th>{{ __('productreport.name') }}</th>
                <th>{{ __('productreport.category') }}</th>
                <th>{{ __('productreport.brand') }}</th>
                <th>{{ __('productreport.stock') }}</th>
                <th>{{ __('productreport.purchase_price') }}</th>
                <th>{{ __('productreport.sale_price') }}</th>
                <th>{{ __('productreport.stock_value') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $prod)
                @php
                    // Récupérer le stock du magasin sélectionné ou total
                    $storeStock = $store_id
                        ? ($prod->stores->where('id', $store_id)->first()->pivot->quantity ?? 0)
                        : $prod->stores->sum('pivot.quantity');
                @endphp
                <tr>
                    <td>{{ $prod->reference }}</td>
                    <td>{{ $prod->name }}</td>
                    <td>{{ $prod->category?->name ?? __('productreport.undefined') }}</td>
                    <td>{{ $prod->brand?->name ?? __('productreport.undefined') }}</td>
                    <td>{{ $storeStock }}</td>
                    <td>{{ number_format($prod->purchase_price, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($prod->sale_price, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($storeStock * $prod->sale_price, 2, ',', ' ') }} {{ company()?->devise }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection