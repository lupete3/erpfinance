@extends('components.layouts.pdf')

@section('title', __('expensereport.title'))
@section('report-title', __('expensereport.header'))

@section('content')
    {{-- Filtres du rapport --}}
    <h4>{{ __('expensereport.store') }}: {{ $store ? $store->name : __('expensereport.all_stores') }}</h4>
    <h4>{{ __('expensereport.category') }}: {{ $category ? $category->name : __('expensereport.all_categories') }}</h4>

    {{-- Tableau des dépenses --}}
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('expensereport.id') }}</th>
                <th>{{ __('expensereport.description') }}</th>
                <th>{{ __('expensereport.amount') }}</th>
                <th>{{ __('expensereport.category') }}</th>
                <th>{{ __('expensereport.store') }}</th>
                <th>{{ __('expensereport.user') }}</th>
                <th>{{ __('expensereport.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_amount = 0;
            @endphp
            @foreach($expenses as $exp)
                @php
                    $total_amount += $exp->amount;
                @endphp
                <tr>
                    <td>{{ $exp->id }}</td>
                    <td>{{ $exp->description }}</td>
                    <td>{{ number_format($exp->amount, 2) }} {{ company()?->devise }}</td>
                    <td>{{ $exp->category?->name ?? '-' }}</td>
                    <td>{{ $exp->store?->name ?? '-' }}</td>
                    <td>{{ $exp->user?->name ?? '-' }}</td>
                    <td>{{ $exp->expense_date }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-end">{{ __('expensereport.total_out') }}</th>
                <th colspan="5" class="text-danger">
                    {{ number_format($total_amount, 2) }} {{ company()?->devise }}
                </th>
            </tr>
        </tfoot>
    </table>
@endsection