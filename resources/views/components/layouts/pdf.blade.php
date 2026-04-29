<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', __('pdf.report'))</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 5px;
            color: #000;
        }
        .footer { text-align: center; margin-top: 30px; font-size: 9px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .table td, .table th { border: 1px solid #000; padding: 4px; }
        th { background-color: #6EB8F1; }
        .badge {
            display: inline-block; padding: 2px 6px; border-radius: 4px;
            font-size: 9px; font-weight: bold;
        }
        .badge-success { background: #28a745; color: #fff; }
        .badge-danger { background: #dc3545; color: #fff; }
        .logo { width: 80px; }
        .section-title {
            margin-top: 10px; font-weight: bold; text-align: center; font-size: 11px;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    {{-- HEADER ENTREPRISE --}}
    <div class="header" style="padding-bottom: 5px;">
        <table style="width:100%;">
            <tr>
                <td style="width: 15%;">
                    @if(company()?->logo && file_exists(public_path(company()->logo)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path(company()->logo))) }}" class="logo" alt="{{ __('pdf.logo') }}">
                    @else
                        <img src="{{ public_path('default-logo.png') }}" class="logo" alt="{{ __('pdf.logo') }}">
                    @endif
                </td>
                <td style="width: 60%; text-align:center;">
                    <h2 style="margin: 0; font-size: 14px;">{{ strtoupper(company()?->name ?? config('app.name')) }}</h2>
                    <p style="margin: 0;">{{ __('pdf.address') }} : {{ company()?->address ?? __('pdf.undefined') }}</p>
                    <p style="margin: 0;">{{ __('pdf.phone') }} : {{ company()?->phone ?? '-' }} – {{ __('pdf.email') }} : {{ company()?->email ?? '-' }}</p>
                    @if(company()?->rccm || company()?->id_nat)
                        <p style="margin: 0;">{{ __('pdf.rccm') }} : {{ company()?->rccm }} | {{ __('pdf.id_nat') }} : {{ company()?->id_nat }}</p>
                    @endif
                </td>
                <td style="width: 25%; text-align:right; font-size: 9px;">
                    <strong>{{ __('pdf.date') }} :</strong> {{ now()->format('d/m/Y') }}<br>
                    <strong>{{ __('pdf.time') }} :</strong> {{ now()->format('H:i') }}<br>
                    <strong>{{ __('pdf.user') }} :</strong><br>
                    {{ auth()->user()->name ?? '-' }}
                </td>
            </tr>
        </table>
        <hr style="margin: 10px 0; border-bottom: 2px solid #6EB8F1;">
        <h3 class="text-center" style="text-decoration: underline; margin-bottom: 2px;">
            @yield('report-title', __('pdf.report_title'))
        </h3>
    </div>

    {{-- CONTENU DU RAPPORT --}}
    <div>
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        {{ __('pdf.generated_on') }} {{ now()->format('d/m/Y H:i') }} - {{ company()?->name ?? config('app.name') }}
    </div>

</body>
</html>