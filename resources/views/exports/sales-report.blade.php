@extends('components.layouts.pdf')

@section('title', __('salereport.report_title'))
@section('report-title', __('salereport.report_title'))

@section('content')
    {{-- Filtre du rapport --}}
    <h4>{{ __('salereport.store') }}: {{ $store ? $store->name : __('salereport.all_stores') }}</h4>
    
    {{-- Tableau des ventes --}}
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('salereport.number') }}</th>
                <th>{{ __('salereport.client') }}</th>
                <th>{{ __('salereport.total_amount') }}</th>
                <th>{{ __('salereport.paid') }}</th>
                <th>{{ __('salereport.remaining') }}</th>
                <th>{{ __('salereport.date') }}</th>
                <th>{{ __('salereport.store') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->id }}</td>
                <td>{{ $sale->client?->name ?? __('salereport.regular_client') }}</td>
                <td>{{ number_format($sale->total_amount, 2) }} {{ company()?->devise }}</td>
                <td>{{ number_format($sale->total_paid, 2) }} {{ company()?->devise }}</td>
                <td>{{ number_format($sale->total_amount - $sale->total_paid, 2) }} {{ company()?->devise }}</td>
                <td>{{ $sale->sale_date }}</td>
                <td>{{ $sale->store?->name ?? __('salereport.undefined') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection