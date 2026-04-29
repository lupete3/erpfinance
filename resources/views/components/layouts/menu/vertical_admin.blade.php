<!-- Menu Super Admin -->
<div wire:ignore>
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="padding-top: 2rem; margin-bottom: 2rem;">
      @php
        $logoQuira = \App\Models\CompanySetting::first();
      @endphp
      @if($logoQuira?->logo && file_exists(public_path($logoQuira->logo)))
        <a href="{{ url('/') }}">
          <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoQuira->logo))) }}"
            class="w-100" alt="{{ __('Logo') }}">
        </a>
      @else
        <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>
      @endif
    </div>

    <div class="menu-inner-shadow mt-4"></div>

    <ul class="menu-inner py-1">
      <!-- Tableaux de Bord -->
      <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('dashboard') }}" >
          <i class="menu-icon tf-icons bx bx-stats"></i>
          <div class="text-truncate">{{ __('Dashboard SaaS') }}</div>
        </a>
      </li>

      <li class="menu-item {{ request()->routeIs('overviewsuperadmin.index') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('overviewsuperadmin.index') }}" >
          <i class="menu-icon tf-icons bx bx-tachometer"></i>
          <div class="text-truncate">{{ __('Tableau de Bord Global') }}</div>
        </a>
      </li>

      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __('Gestion SaaS') }}</span>
      </li>

      <!-- Entreprises / Tenants -->
      <li class="menu-item {{ request()->routeIs('tenant.index') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('tenant.index') }}" >
          <i class="menu-icon tf-icons bx bx-buildings"></i>
          <div class="text-truncate">{{ __('Entreprises (Tenants)') }}</div>
        </a>
      </li>

      <!-- Plans d'Abonnement -->
      <li class="menu-item {{ request()->routeIs('plan.index') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('plan.index') }}" >
          <i class="menu-icon tf-icons bx bx-list-ul"></i>
          <div class="text-truncate">{{ __('Plans d’Abonnement') }}</div>
        </a>
      </li>

      <!-- Souscriptions -->
      <li class="menu-item {{ request()->routeIs('souscription.index') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('souscription.index') }}" >
          <i class="menu-icon tf-icons bx bx-check-shield"></i>
          <div class="text-truncate">{{ __('Souscriptions') }}</div>
        </a>
      </li>

      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __('Administration') }}</span>
      </li>


      <!-- Paramètres Système -->
      <li
        class="menu-item {{ request()->is('settings/*') || request()->routeIs('company.settings') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-cog"></i>
          <div class="text-truncate">{{ __('Paramètres Système') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.profile') }}" >{{ __('Mon Profil') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.password') }}" >{{ __('Mot de Passe') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('company.settings') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('company.settings') }}"
              >{{ __('Paramètres Entreprise') }}</a>
          </li>
        </ul>
      </li>
    </ul>
  </aside>
</div>

<!-- Overlay -->
<div wire:ignore>
  <div class="layout-overlay"></div>
</div>

<style>
  #layout-menu {
    max-height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
  }
</style>

<script>
  document.querySelectorAll('.menu-toggle').forEach(function (menuToggle) {
    menuToggle.addEventListener('click', function () {
      const menuItem = menuToggle.closest('.menu-item');
      menuItem.classList.toggle('open');
    });
  });
</script>