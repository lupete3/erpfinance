<x-layouts.app>
    <x-slot:title>
        Support Technique - PFTECHNO
    </x-slot:title>

    <div class="flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <h5 class="card-header">Support Technique & Contact</h5>
                    <div class="card-body">
                        <p>
                            Besoin d'assistance ou d'une personnalisation supplémentaire ? Notre équipe est à votre
                            disposition.
                        </p>

                        <div class="row g-4 mt-2">
                            <!-- WhatsApp -->
                            <div class="col-md-6">
                                <div class="card bg-success text-white shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bxl-whatsapp fs-3 me-2"></i>
                                            <h5 class="card-title text-white mb-0">WhatsApp</h5>
                                        </div>
                                        <p class="card-text">Contactez-nous directement sur WhatsApp pour une réponse
                                            rapide.</p>
                                        <a href="https://wa.me/{{ str_replace('+', '', config('variables.creatorWhatsApp')) }}"
                                            target="_blank" class="btn btn-white text-success bg-white fw-bold">
                                            Discuter sur WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="card bg-primary text-white shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-envelope fs-3 me-2"></i>
                                            <h5 class="card-title text-white mb-0">Email</h5>
                                        </div>
                                        <p class="card-text">Envoyez-nous un email pour des demandes plus détaillées.
                                        </p>
                                        <a href="mailto:{{ config('variables.creatorEmail') }}"
                                            class="btn btn-white text-primary bg-white fw-bold">
                                            Nous envoyer un email
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h6>À propos de PFTECHNO</h6>
                            <p>
                                <strong>Site Web :</strong> <a href="{{ config('variables.creatorUrl') }}"
                                    target="_blank">{{ config('variables.creatorUrl') }}</a><br>
                                <strong>Nom du Concepteur :</strong> PFTECHNO
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>