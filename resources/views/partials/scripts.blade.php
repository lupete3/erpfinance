<!-- BEGIN: Vendor JS-->
@vite([
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
])
<!-- END: Vendor JS-->

<!-- Page Vendor JS -->
@yield('vendor-script')

@vite(['resources/js/app.js'])

<!-- BEGIN: Page JS -->
@yield('page-script')
<!-- END: Page JS -->

<script>
    document.addEventListener('livewire:init', () => {

        const handleModal = (modalId, action) => {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                if (action === 'hide') modal.hide();
            }
        };

        // Fermer toutes les modales ouvertes
        Livewire.on('close-modal', () => {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modalEl => {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            });
        });

        // Confirmation de validation
        Livewire.on('show-validate-confirmation', () => {
            Swal.fire({
                title: @json(__('alerts.confirm_title')),
                text: @json(__('alerts.validate_text')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: @json(__('alerts.confirm_button')),
                cancelButtonText: @json(__('alerts.cancel_button')),
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('validateConfirmed');
                }
            });
        });

        // Confirmation de suppression
        Livewire.on('show-delete-confirmation', () => {
            Swal.fire({
                title: @json(__('alerts.confirm_title')),
                text: @json(__('alerts.delete_text')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: @json(__('alerts.delete_button')),
                cancelButtonText: @json(__('alerts.cancel_button')),
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed');
                }
            });
        });

    });
</script>

@stack('scripts')

<!-- GitHub buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
