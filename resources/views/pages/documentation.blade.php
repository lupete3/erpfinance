<x-layouts.app>
    <x-slot:title>
        Guide Utilisateur Complet - PFTECHNO
    </x-slot:title>

    <div class="flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Sommaire</h6>
                        <nav id="doc-nav" class="nav flex-column">
                            <a class="nav-link text-body fw-medium" href="#intro">Introduction</a>
                            <a class="nav-link text-body" href="#stock-maison">1. Dépôt Maison</a>
                            <a class="nav-link text-body" href="#stock-usine">2. Usine & Production</a>
                            <a class="nav-link text-body" href="#stock-pf">3. Distribution (PF)</a>
                            <a class="nav-link text-body" href="#pos">4. Point de Vente (POS)</a>
                            <a class="nav-link text-body" href="#cloture">5. Clôture & Finances</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8 col-12">
                <!-- Introduction -->
                <div id="intro" class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title">Bienvenue sur PFTECHNO</h4>
                        <p>
                            Votre système de gestion de boulangerie est conçu pour suivre l'intégralité du cycle de vie
                            de vos produits, de l'achat de la farine à la vente finale du pain.
                            Le système repose sur une hiérarchie de stocks pour assurer une traçabilité parfaite.
                        </p>
                        <div class="alert alert-info">
                            <h6 class="alert-heading mb-1"><i class="bx bx-user me-2"></i>Rôles et Accès</h6>
                            <ul class="mb-0">
                                <li><strong>Admin :</strong> Accès total à tous les sites et rapports financiers.</li>
                                <li><strong>Gérant Dépôt Magasin :</strong> Gère les achats de matières premières et le
                                    Dépôt Maison.</li>
                                <li><strong>Gérant Dépôt Usine :</strong> Gère la production, les charges et le
                                    transfert des ingrédients vers le fournil.</li>
                                <li><strong>Gérant Dépôt Boulangerie :</strong> Gère les ventes (POS), les clients et la
                                    clôture de son site.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 1. Dépôt Maison -->
                <div id="stock-maison" class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">1. Gestion des Approvisionnements (Maison)</h5>
                        <span class="badge bg-label-primary">Logistique Amont</span>
                    </div>
                    <div class="card-body">
                        <h6>Approvisionnement initial</h6>
                        <p>Tout commence dans le <strong>Dépôt Maison</strong>. C'est ici que vous enregistrez les
                            achats auprès de vos fournisseurs.</p>
                        <ul>
                            <li><strong>Achats Stock :</strong> Enregistrez la quantité reçue et le prix d'achat. Le
                                système calcule automatiquement vos dettes fournisseurs si le paiement n'est pas total.
                            </li>
                            <li><strong>Matières Premières :</strong> Liste de tous vos ingrédients de base (Farine,
                                Sucre, Sel, etc.) avec leur unité (Sac, Kg, etc.).</li>
                            <li><strong>Transfert vers Usine :</strong> Une fois les matières premières prêtes pour la
                                production, transférez-les vers l'usine. Le stock sera déduit de la Maison et ajouté à
                                l'Usine.</li>
                        </ul>
                    </div>
                </div>

                <!-- 2. Usine & Production -->
                <div id="stock-usine" class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">2. Cycle de Production & Fournil</h5>
                        <span class="badge bg-label-warning">Transformation</span>
                    </div>
                    <div class="card-body">
                        <h6>Journal de Production</h6>
                        <p>Le module de production transforme vos matières premières en <strong>Produits Finis
                                (PF)</strong>.</p>
                        <ul>
                            <li><strong>Lancement d'une fournée :</strong> Sélectionnez le produit à fabriquer (ex:
                                Baguette) et les ingrédients utilisés.</li>
                            <li><strong>Déduction automatique :</strong> Le système retire automatiquement les
                                ingrédients du stock Usine selon la quantité utilisée.</li>
                            <li><strong>Gestion des Coûts :</strong> Saisissez les charges de personnel (boulangers) et
                                autres frais pour calculer la rentabilité de chaque fournée.</li>
                        </ul>
                        <div class="bg-light p-3 rounded">
                            <small><strong>Note :</strong> Seuls les produits finis résultant de la production
                                apparaîtront plus tard dans le stock disponible pour la vente.</small>
                        </div>
                    </div>
                </div>

                <!-- 3. Distribution (PF) -->
                <div id="stock-pf" class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">3. Stock Central & Distribution</h5>
                        <span class="badge bg-label-info">Logistique Aval</span>
                    </div>
                    <div class="card-body">
                        <h6>Stock Produits Finis (PF)</h6>
                        <p>Une fois les pains sortis du four, ils sont stockés au <strong>Stock PF (Fournil)</strong>.
                        </p>
                        <ul>
                            <li><strong>Expédition vers les Sites (Shipping) :</strong> Pour vendre, vous devez
                                "expédier" les produits vers vos différents points de vente ou sites externes.</li>
                            <li><strong>Mouvements PF :</strong> Consultez l'historique des transferts entre le fournil
                                central et les boutiques.</li>
                        </ul>
                    </div>
                </div>

                <!-- 4. Point de Vente (POS) -->
                <div id="pos" class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">4. Ventes & Interface POS</h5>
                        <span class="badge bg-label-success">Commercial</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6><i class="bx bx-list-ul me-2"></i>Mode Catalogue</h6>
                                <p>Idéal pour les ventes au comptoir. Vous sélectionnez les produits un par un,
                                    saisissez le montant reçu et validez la vente avec impression de facture.</p>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h6><i class="bx bx-repost me-2"></i>Mode Inventaire</h6>
                                <p>Méthode spécifique à la boulangerie : saisissez ce qu'il reste sur les étagères à la
                                    fin d'un shift, et le système calculera automatiquement la <strong>quantité
                                        vendue</strong>.</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Ventes à Crédit</h6>
                        <p>Si un client régulier ne paie pas immédiatement, le système enregistre une <strong>Dette
                                Client</strong>. Vous pouvez suivre et solder ces dettes dans le module dédié.</p>
                    </div>
                </div>

                <!-- 5. Clôture & Finances -->
                <div id="cloture" class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">5. Clôture de Journée & Synthèse</h5>
                        <span class="badge bg-label-danger">Finances</span>
                    </div>
                    <div class="card-body">
                        <p>C'est l'étape la plus cruciale pour le contrôle financier.</p>
                        <ol>
                            <li><strong>Clôture Produits :</strong> Pour chaque produit, confirmez le stock final, les
                                <strong>Avaries</strong> (produits gâtés) et les <strong>Consommations</strong> (ex:
                                petit-déjeuner personnel).</li>
                            <li><strong>Synthèse Financière :</strong> Le système compare :
                                <br><em>Vente Théorique (Inventaire) - Avaries - Dépenses du jour = <strong>Total
                                        Attendu en Caisse</strong>.</em>
                            </li>
                            <li><strong>Vérification du Manquant :</strong> Saisissez l'espèce réelle en caisse. S'il y
                                a une différence, elle apparaîtra comme <strong>Manquant</strong>.</li>
                        </ol>
                        <div class="alert alert-warning mt-3">
                            <i class="bx bx-error me-2"></i><strong>Important :</strong> La synthèse doit être effectuée
                            chaque jour pour maintenir une comptabilité saine.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>