<x-layouts.auth>
    <x-slot:title>
        {{ __('messages.access_denied_title') }}
    </x-slot:title>

    @php
        $tenant = Auth::user()->tenant ?? null;
        $tenantName = $tenant?->name ?? 'Organisation inconnue';
        $tenantEmail = $tenant?->email ?? 'non défini';
        $appName = config('app.name');

        // 🔹 Préparation du message WhatsApp dynamique
        $whatsappMessage = urlencode("Bonjour, je souhaite renouveler mon abonnement sur {$appName}.\n\nDétails de mon organisation :\n🏢 Nom : {$tenantName}\n📧 Email : {$tenantEmail}");
        $whatsappNumber = '243978333654'; // Remplace avec ton numéro WhatsApp sans + ni espaces
    @endphp

    <div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: calc(100vh - 85px)">
        <h1 class="mb-2 mx-2 text-danger fw-bold" style="font-size: 6rem;">403</h1>
        <h4 class="mb-2">🚫 {{ __('messages.access_denied_heading') }}</h4>

        <p class="mb-4 mx-3 text-muted">
            @isset($exception)
                {{ $exception->getMessage() }}
            @else
                {{ __('messages.access_denied_text') }}<br>
                {{ __('messages.access_denied_tip') }}
            @endisset
        </p>

        {{-- ✅ Section abonnement --}}
            <div class="mt-3 mb-4">
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
                   target="_blank"
                   class="btn btn-success w-100">
                    💬 {{ __('messages.contact_support') }}
                </a>
            </div>

        {{-- ✅ Boutons secondaires --}}
        <form method="POST" action="{{ route('logout') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="bx bx-power-off me-2"></i> {{ __('navbar.se_deconnecter') }}
            </button>
        </form>

        <a href="{{ url('/') }}" class="btn btn-link text-decoration-none">{{ __('messages.back_to_home') }}</a>
    </div>
</x-layouts.auth>
