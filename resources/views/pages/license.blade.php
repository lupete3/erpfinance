<x-layouts.app>
    <x-slot:title>
        Licence - PFTECHNO
    </x-slot:title>

    <div class="flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <h5 class="card-header">Contrat de Licence Utilisateur Final (CLUF)</h5>
                    <div class="card-body">
                        <h6>1. Octroi de Licence</h6>
                        <p>
                            <strong>PFTECHNO</strong> vous accorde une licence non exclusive et non transférable pour
                            utiliser ce logiciel pour vos besoins de gestion commerciale internes, conformément aux
                            termes du présent contrat.
                        </p>

                        <h6>2. Restrictions</h6>
                        <p>
                            Il vous est interdit de :
                        </p>
                        <ul>
                            <li>Reproduire, copier ou distribuer le logiciel sans autorisation écrite préalable.</li>
                            <li>Modifier, décompiler ou désassembler le code source.</li>
                            <li>Utiliser le logiciel à des fins illégales.</li>
                        </ul>

                        <h6>3. Propriété Intellectuelle</h6>
                        <p>
                            Le logiciel et tous les droits de propriété intellectuelle y afférents sont et resteront la
                            propriété exclusive de <strong>PFTECHNO</strong>.
                        </p>

                        <h6>4. Limitation de Responsabilité</h6>
                        <p>
                            <strong>PFTECHNO</strong> ne sera pas responsable des pertes de données ou des pertes de
                            profits résultant de l'utilisation ou de l'incapacité d'utiliser le logiciel.
                        </p>

                        <hr class="my-4">

                        <p class="text-muted small">
                            Dernière mise à jour : {{ date('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>