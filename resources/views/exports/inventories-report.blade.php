@extends('components.layouts.pdf')

@section('title', __('inventoryreport.report_title'))
@section('report-title', __('inventoryreport.report_title'))

@section('content')
    {{-- Informations de l'inventaire --}}
    <p><strong>{{ __('inventoryreport.date') }}:</strong> {{ $inventory->inventory_date }}</p>
    <p><strong>{{ __('inventoryreport.user') }}:</strong> {{ $inventory->user->name ?? __('inventoryreport.undefined') }}</p>
    <p><strong>{{ __('inventoryreport.store') }}:</strong> {{ $inventory->store->name ?? __('inventoryreport.undefined') }}</p>
    <p><strong>{{ __('inventoryreport.status') }}:</strong> {{ $inventory->status }}</p>

    {{-- Détail des articles --}}
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('inventoryreport.product') }}</th>
                <th>{{ __('inventoryreport.theoretical_stock') }}</th>
                <th>{{ __('inventoryreport.physical_stock') }}</th>
                <th>{{ __('inventoryreport.difference') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventory->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? __('inventoryreport.undefined') }}</td>
                    <td>{{ $item->theoretical_quantity }}</td>
                    <td>{{ $item->physical_quantity }}</td>
                    <td>
                        @if ($item->difference > 0)
                            <span class="badge bg-label-success">+{{ $item->difference }}</span>
                        @elseif($item->difference < 0)
                            <span class="badge bg-label-danger">{{ $item->difference }}</span>
                        @else
                            {{ $item->difference }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection