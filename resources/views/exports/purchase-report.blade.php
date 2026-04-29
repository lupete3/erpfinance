@extends('components.layouts.pdf')

@section('title', __('purchasereport.report_title'))
@section('report-title', __('purchasereport.report_title'))

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('purchasereport.number') }}</th>
                <th>{{ __('purchasereport.supplier') }}</th>
                <th>{{ __('purchasereport.total_amount') }}</th>
                <th>{{ __('purchasereport.paid') }}</th>
                <th>{{ __('purchasereport.remaining') }}</th>
                <th>{{ __('purchasereport.status') }}</th>
                <th>{{ __('purchasereport.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
            <tr>
                <td>{{ $purchase->id }}</td>
                <td>{{ $purchase->supplier?->name ?? __('purchasereport.free_supplier') }}</td>
                <td>{{ number_format($purchase->total_amount, 2) }} {{ company()?->devise }}</td>
                <td>{{ number_format($purchase->total_paid, 2) }} {{ company()?->devise }}</td>
                <td>{{ number_format($purchase->total_amount - $purchase->total_paid, 2) }} {{ company()?->devise }}</td>
                <td>
                    {{-- Utiliser directement la clé de statut pour la traduction --}}
                    {{ ucfirst(__('purchasereport.' . $purchase->status)) }}
                </td>
                <td>{{ $purchase->purchase_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection