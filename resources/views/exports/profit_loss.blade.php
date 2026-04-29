@extends('components.layouts.pdf')

@section('title', __('Rapport Profits & Pertes'))
@section('report-title', __('RAPPORT PROFITS ET PERTES'))

@section('content')
    <h3 style="text-align:center;">
        {{ __('Période') }} : {{ $start_date }} {{ __('au') }} {{ $end_date }}
    </h3>

    <table width="100%" border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th>{{ __('Total Ventes') }}</th>
            <td>{{ number_format($total_sales, 2, ',', ' ') }} {{ company()->devise }}</td>
        </tr>
        <tr>
            <th>{{ __('Total Achats') }}</th>
            <td>{{ number_format($total_purchases, 2, ',', ' ') }} {{ company()->devise }}</td>
        </tr>
        <tr>
            <th>{{ __('Total Dépenses') }}</th>
            <td>{{ number_format($total_expenses, 2, ',', ' ') }} {{ company()->devise }}</td>
        </tr>
        <tr>
            <th>{{ __('Profit Brut') }}</th>
            <td>{{ number_format($profit_brut, 2, ',', ' ') }} {{ company()->devise }}</td>
        </tr>
        <tr>
            <th>{{ __('Profit Net') }}</th>
            <td>{{ number_format($profit_net, 2, ',', ' ') }} {{ company()->devise }}</td>
        </tr>
    </table>
@endsection
