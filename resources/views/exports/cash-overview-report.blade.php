@extends('components.layouts.pdf')

@section('title', __('cashreport.title'))
@section('report-title', __('cashreport.header'))

@section('content')
    {{-- Informations principales --}}
    <h4>{{ __('cashreport.store') }}: {{ $store ? $store->name : __('cashreport.all_stores') }}</h4>
    <h4>{{ __('cashreport.period') }}: {{ $start_date }} - {{ $end_date }}</h4>

    {{-- Résumé --}}
    <table class="table mb-4" style="width:100%; border-collapse: collapse; text-align:center;">
        <thead>
            <tr>
                <th>{{ __('cashreport.total_in') }}</th>
                <th>{{ __('cashreport.total_out') }}</th>
                <th>{{ __('cashreport.net_balance') }}</th>
                @if($current_balance !== null)
                    <th>{{ __('cashreport.current_balance') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-success">
                    {{ number_format($total_in, 2) }} {{ company()?->devise }}
                </td>
                <td class="text-danger">
                    {{ number_format($total_out, 2) }} {{ company()?->devise }}
                </td>
                <td>
                    <strong>{{ number_format($net_balance, 2) }} {{ company()?->devise }}</strong>
                </td>
                @if($current_balance !== null)
                    <td>
                        <strong>{{ number_format($current_balance, 2) }} {{ company()?->devise }}</strong>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>

    {{-- Détail des opérations --}}
    <h5 class="mt-3">{{ __('cashreport.details') }}</h5>
    <table class="table" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('cashreport.type') }}</th>
                <th>{{ __('cashreport.description') }}</th>
                <th>{{ __('cashreport.amount') }}</th>
                <th>{{ __('cashreport.user') }}</th>
                <th>{{ __('cashreport.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($transactions as $t)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        @if($t->type === 'in')
                            <span class="text-success">{{ __('cashreport.in') }}</span>
                        @elseif($t->type === 'out')
                            <span class="text-danger">{{ __('cashreport.out') }}</span>
                        @elseif($t->type === 'opening')
                            <span class="text-primary">{{ __('cashreport.opening') }}</span>
                        @elseif($t->type === 'closing')
                            <span class="text-warning">{{ __('cashreport.closing') }}</span>
                        @endif
                    </td>
                    <td>{{ $t->description ?? '-' }}</td>
                    <td>
                        @if($t->type === 'out')
                            <span class="text-danger">-{{ number_format($t->amount, 2) }} {{ company()?->devise }}</span>
                        @else
                            <span class="text-success">{{ number_format($t->amount, 2) }} {{ company()?->devise }}</span>
                        @endif
                    </td>
                    <td>{{ $t->user?->name ?? '-' }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection