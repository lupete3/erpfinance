<li class="nav-item d-flex align-items-center">
    <div class="btn-group" role="group" aria-label="Changement de langue">
        @foreach(['fr' => 'FR', 'en' => 'EN'] as $code => $label)
            <button 
                class="btn {{ $locale === $code ? 'btn-primary' : 'btn-outline-primary' }} d-flex align-items-center px-3 py-2"
                wire:click.prevent="changeLocale('{{ $code }}')"
                type="button"
            >
                <img src="{{ asset('assets/img/flags/'.$code.'.png') }}" alt="{{ $label }}" width="20" class="me-1 rounded-circle">
                <span class="fw-semibold">{{ $label }}</span>
            </button>
        @endforeach
    </div>
</li>

<script>
    Livewire.on('updateLocale', ({ locale }) => {
        document.documentElement.lang = locale;
        console.log('Langue mise à jour :', locale);
    });
</script>
