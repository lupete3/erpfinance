@extends('components.layouts.pdf')

@section('title', __('supplierreport.report_title'))
@section('report-title', __('supplierreport.report_title'))

@section('content')
    {{-- Période du rapport --}}
    @if($date_from && $date_to)
        <p>{{ __('supplierreport.period') }} : {{ $date_from }} {{ __('supplierreport.to') }} {{ $date_to }}</p>
    @endif
    
    {{-- Tableau des fournisseurs --}}
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('supplierreport.supplier') }}</th>
                <th>{{ __('supplierreport.contact') }}</th>
                <th>{{ __('supplierreport.purchases_count') }}</th>
                <th>{{ __('supplierreport.total_purchases') }}</th>
                <th>{{ __('supplierreport.total_paid') }}</th>
                <th>{{ __('supplierreport.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $supplier)
                @php
                    // Assurez-vous que la logique de calcul de la période est gérée dans le contrôleur si des filtres sont appliqués.
                    // Ici, nous utilisons les totaux des achats disponibles.
                    $totalAchats = $supplier->purchases->sum('total_amount');
                    $totalPaye = $supplier->purchases->sum('total_paid');
                    $solde = $totalAchats - $totalPaye;
                @endphp
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->purchases->count() }}</td>
                    <td>{{ number_format($totalAchats, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($totalPaye, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($solde, 2, ',', ' ') }} {{ company()?->devise }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection