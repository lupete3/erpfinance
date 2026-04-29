<!-- Menu -->
<div wire:ignore>
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="padding-top: 1rem; margin-bottom: 1rem; height: auto;">
      @php
        $company = company();
        $logoPath = $company?->logo ? storage_path('app/public/' . $company->logo) : null;
        $logoBase64 = null;
        if ($logoPath && file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
      @endphp

      @if($logoBase64)
        <a href="{{ url('/') }}" class="w-100 text-center">
          <img src="{{ $logoBase64 }}" style="max-width: 100%; max-height: 60px; object-fit: contain;" alt="Logo">
        </a>
      @else
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2 text-uppercase">{{ $company->name ?? config('app.name') }}</span>
        </a>
      @endif
    </div>

    <div class="menu-inner-shadow mt-2"></div>

    <ul class="menu-inner py-1">

      <!-- Dashboards -->
      @if (Auth::user()->hasRoleString('Super Admin'))
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('dashboard') }}">
            <i class="menu-icon tf-icons bx bx-stats"></i>
            <div class="text-truncate">{{ __('Dashboard SaaS') }}</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('overviewsuperadmin.index') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('overviewsuperadmin.index') }}">
            <i class="menu-icon tf-icons bx bx-tachometer"></i>
            <div class="text-truncate">{{ __('Tableau de Bord Global') }}</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Navigation Boulangerie') }}</span>
        </li>
      @endif

      @if(Auth::user()->hasRoleString('Boss') || Auth::user()->hasRoleString('Super Admin'))
          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Gestion Financière (BOSS)</span>
          </li>
          <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('dashboard') }}">
                  <i class="menu-icon tf-icons bx bx-home-circle"></i>
                  <div class="text-truncate">Tableau de Bord</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.stores.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.stores.index') }}">
                  <i class="menu-icon tf-icons bx bx-store"></i>
                  <div class="text-truncate">Succursales</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.dotations.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.dotations.index') }}">
                  <i class="menu-icon tf-icons bx bx-send"></i>
                  <div class="text-truncate">Dotations</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.managers.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.managers.index') }}">
                  <i class="menu-icon tf-icons bx bx-user-circle"></i>
                  <div class="text-truncate">Gérants</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.expense-categories.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.expense-categories.index') }}">
                  <i class="menu-icon tf-icons bx bx-category"></i>
                  <div class="text-truncate">Catégories Dépenses</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.reports.summary') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.reports.summary') }}">
                  <i class="menu-icon tf-icons bx bx-pie-chart-alt-2"></i>
                  <div class="text-truncate">Bilan Entrées/Sorties</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.reports.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.reports.index') }}">
                  <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                  <div class="text-truncate">Reporting Détaillé</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.budget-requests.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.budget-requests.index') }}">
                  <i class="menu-icon tf-icons bx bx-task"></i>
                  <div class="text-truncate">Validation États de Besoin</div>
              </a>
          </li>
          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Configuration</span>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.boss.settings.company') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.boss.settings.company') }}">
                  <i class="menu-icon tf-icons bx bx-cog"></i>
                  <div class="text-truncate">Paramètres Entreprise</div>
              </a>
          </li>
      @endif

      @if(Auth::user()->hasRoleString('Gérant'))
          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Ma Succursale (GÉRANT)</span>
          </li>
          <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('dashboard') }}">
                  <i class="menu-icon tf-icons bx bx-home-circle"></i>
                  <div class="text-truncate">Tableau de Bord</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.manager.expenses.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.manager.expenses.index') }}">
                  <i class="menu-icon tf-icons bx bx-receipt"></i>
                  <div class="text-truncate">Dépenses</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.manager.dotations.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.manager.dotations.index') }}">
                  <i class="menu-icon tf-icons bx bx-money"></i>
                  <div class="text-truncate">Dotations Reçues</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.manager.reports.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.manager.reports.index') }}">
                  <i class="menu-icon tf-icons bx bx-file"></i>
                  <div class="text-truncate">Mes Rapports</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('finance.manager.budget-requests.*') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('finance.manager.budget-requests.index') }}">
                  <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                  <div class="text-truncate">États de Besoin</div>
              </a>
          </li>
      @endif

      @if(Auth::user()->isBakeryUser() || Auth::user()->hasRoleString('Super Admin'))
        {{-- Dashboard Boulangerie uniquement --}}
        <li class="menu-item {{ request()->routeIs('dashboard.boulangerie') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('dashboard.boulangerie') }}">
            <i class="menu-icon tf-icons bx bx-home"></i>
            <div class="text-truncate">{{ __('Dashboard Boulangerie') }}</div>
          </a>
        </li>

        {{-- LOGISTIQUE & MP --}}
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Logistique & MP') }}</span>
        </li>

        @if(Auth::user()->hasRoleString('admin') || Auth::user()->hasRoleString('geran_depot_magasin'))
          <li class="menu-item {{ request()->routeIs('bakery.fournisseurs') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.fournisseurs') }}">
              <i class="menu-icon tf-icons bx bx-user-voice"></i>
              <div class="text-truncate">{{ __('Fournisseurs') }}</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('bakery.achats') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.achats') }}">
              <i class="menu-icon tf-icons bx bx-cart"></i>
              <div class="text-truncate">{{ __('Achats MP') }}</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('bakery.stock.maison') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.stock.maison') }}">
              <i class="menu-icon tf-icons bx bx-box"></i>
              <div class="text-truncate">{{ __('Stock MP Dépôt') }}</div>
            </a>
          </li>
        @endif

        @if(Auth::user()->hasRoleString('admin') || Auth::user()->hasRoleString('geran_depot_usine'))
          <li class="menu-item {{ request()->routeIs('bakery.stock.usine') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.stock.usine') }}">
              <i class="menu-icon tf-icons bx bx-buildings"></i>
              <div class="text-truncate">{{ __('Stock MP Usine') }}</div>
            </a>
          </li>
        @endif

        @if(Auth::user()->hasRoleString('admin') || Auth::user()->hasRoleString('geran_depot_magasin') || Auth::user()->hasRoleString('geran_depot_usine'))
          <li class="menu-item {{ request()->routeIs('bakery.stock.mouvements') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.stock.mouvements') }}">
              <i class="menu-icon tf-icons bx bx-transfer"></i>
              <div class="text-truncate">{{ __('Hist. Mouvements MP') }}</div>
            </a>
          </li>
        @endif

        {{-- PRODUCTION & PF --}}
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Production & PF') }}</span>
        </li>

        @if(Auth::user()->hasRoleString('admin') || Auth::user()->hasRoleString('geran_depot_usine'))
          <li class="menu-item {{ request()->routeIs('bakery.production') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.production') }}">
              <i class="menu-icon tf-icons bx bx-repost"></i>
              <div class="text-truncate">{{ __('Production') }}</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('bakery.stock.pf') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.stock.pf') }}">
              <i class="menu-icon tf-icons bx bx-package"></i>
              <div class="text-truncate">{{ __('Stock Produits Finis') }}</div>
            </a>
          </li>
        @endif

        @if(Auth::user()->hasRoleString('admin') || Auth::user()->hasRoleString('geran_depot_usine') || Auth::user()->hasRoleString('geran_depot_boulangerie'))
          <li class="menu-item {{ request()->routeIs('bakery.stock.mouvements-pf') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.stock.mouvements-pf') }}">
              <i class="menu-icon tf-icons bx bx-transfer"></i>
              <div class="text-truncate">{{ __('Hist. Mouvements PF') }}</div>
            </a>
          </li>
        @endif

        {{-- VENTES & BOULANGERIE --}}
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Ventes & Boulangerie') }}</span>
        </li>

        @if(Auth::user()->hasRoleString('admin') || Auth::user()->hasRoleString('geran_depot_boulangerie'))
          <li class="menu-item {{ request()->routeIs('bakery.pos') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.pos') }}">
              <i class="menu-icon tf-icons bx bx-cart-alt"></i>
              <div class="text-truncate">{{ __('Vente POS') }}</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('bakery.dettes') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.dettes') }}">
              <i class="menu-icon tf-icons bx bx-money"></i>
              <div class="text-truncate">{{ __('Dettes Clients') }}</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('bakery.clients') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.clients') }}">
              <i class="menu-icon tf-icons bx bx-user-pin"></i>
              <div class="text-truncate">{{ __('Clients') }}</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('bakery.stock.boulangerie') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.stock.boulangerie') }}">
              <i class="menu-icon tf-icons bx bx-store"></i>
              <div class="text-truncate">{{ __('Stock Points de Vente') }}</div>
            </a>
          </li>

          @if(Auth::user()->hasRoleString('admin'))
            <li class="menu-item {{ request()->routeIs('bakery.stock.transfert') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('bakery.stock.transfert') }}">
                <i class="menu-icon tf-icons bx bx-repost"></i>
                <div class="text-truncate">{{ __('Transferts Inter-Sites') }}</div>
              </a>
            </li>
          @endif

          <li class="menu-item {{ request()->routeIs('bakery.cloture') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.cloture') }}">
              <i class="menu-icon tf-icons bx bx-lock-alt"></i>
              <div class="text-truncate">{{ __('Clôture de Journée') }}</div>
            </a>
          </li>
        @endif

        {{-- FINANCES & ADMIN --}}
        @if(Auth::user()->hasRoleString('admin'))
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('Finances & Admin') }}</span>
          </li>

          <li class="menu-item {{ request()->routeIs('bakery.caisse') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.caisse') }}">
              <i class="menu-icon tf-icons bx bx-wallet"></i>
              <div class="text-truncate">{{ __('Gestion Caisse') }}</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('bakery.reports') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.reports') }}">
              <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
              <div class="text-truncate">{{ __('Rapports') }}</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('bakery.admin.settings') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('bakery.admin.settings') }}">
              <i class="menu-icon tf-icons bx bx-cog"></i>
              <div class="text-truncate">{{ __('Administration') }}</div>
            </a>
          </li>
        @endif
      @endif

      @if (Auth::user()->hasRoleString('Super Admin'))


        <!-- Paramètres -->
        <li class="menu-item {{ request()->is('settings/*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div class="text-truncate">{{ __('menu.parametres') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('settings.profile') }}">{{ __('menu.profil') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('settings.password') }}">{{ __('menu.mot_de_passe') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('company.settings') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('company.settings') }}">{{ __('menu.parametres_entreprise') }}</a>
            </li>
          </ul>
        </li>
      @endif

    </ul>
  </aside>
</div>
<!-- / Menu -->

<!-- Overlay (important pour mobile) -->
<div wire:ignore>
  <div class="layout-overlay"></div>
</div>

<style>
  #layout-menu {
    max-height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
  }

  /* Optionnel : pour que le scroll soit plus élégant */
  #layout-menu::-webkit-scrollbar {
    width: 6px;
  }

  #layout-menu::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
  }
</style>

<script>
  // Toggle the 'open' class when the menu-toggle is clicked
  document.querySelectorAll('.menu-toggle').forEach(function (menuToggle) {
    menuToggle.addEventListener('click', function () {
      const menuItem = menuToggle.closest('.menu-item');
      // Toggle the 'open' class on the clicked menu-item
      menuItem.classList.toggle('open');
    });
  });
</script>