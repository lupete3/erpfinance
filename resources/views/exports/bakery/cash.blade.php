@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Utilisateur') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Motif') }}</th>
                <th class="text-end">{{ __('Montant') }}</th>
                <th class="text-end">{{ __('Solde après') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashOperations as $op)
                <tr>
                    <td>{{ $op->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $op->user->name ?? 'N/A' }}</td>
                    <td class="text-center">
                        @php $isEntree = str_contains(strtolower($op->type_operation), 'entre'); @endphp
                        <span class="badge {{ $isEntree ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($op->type_operation) }}
                        </span>
                    </td>
                    <td style="font-size: 8px;">{{ $op->motif }}</td>
                    <td class="text-end" style="color: {{ $isEntree ? 'green' : 'red' }};">
                        {{ $isEntree ? '+' : '-' }}
                        {{ number_format($op->montant, 0, ',', ' ') }} FC
                    </td>
                    <td class="text-end"><strong>{{ number_format($op->solde_apres_operation, 0, ',', ' ') }} FC</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection