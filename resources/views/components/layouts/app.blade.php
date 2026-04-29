<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed" data-base-url="{{url('/')}}"
  data-framework="laravel">

<head>
  @include('partials.head')
</head>

<body>

  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Layout Content -->
      @if (Auth::user()->hasRoleString('Super Admin'))
        <x-layouts.menu.vertical_admin :title="$title ?? null"></x-layouts.menu.vertical_admin>
      @else
        <x-layouts.menu.vertical :title="$title ?? null"></x-layouts.menu.vertical>
      @endif
      <!--/ Layout Content -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        @include('components.layouts.navbar.default')
        <!--/ Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            {{ $slot }}
          </div>
          <!-- / Content -->

          <!-- Footer -->
          <x-layouts.footer.default :title="$title ?? null"></x-layouts.footer.default>
          <!--/ Footer -->
          <div class="content-backdrop fade"></div>
          <!-- / Content wrapper -->
        </div>
      </div>
      <!-- / Layout page -->
    </div>
  </div>

  <!-- Include Scripts -->
  @include('partials.scripts')
  <!-- / Include Scripts -->

  <script>
    function initMenuToggle() {
      const toggleBtn = document.querySelector(".layout-menu-toggle a");
      const body = document.body;
      const overlay = document.querySelector(".layout-overlay");

      if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
          body.classList.toggle("layout-menu-expanded");
        });
      }

      if (overlay) {
        overlay.addEventListener("click", function () {
          body.classList.remove("layout-menu-expanded");
        });
      }
    }

    document.addEventListener("DOMContentLoaded", initMenuToggle);

    // ✅ Réattacher après chaque navigation Livewire
    document.addEventListener("livewire:navigated", initMenuToggle);
  </script>

  <script>
    function printFacture(url) {
      const showprint = window.open(url, "height=900, width=800");

      showprint.addEventListener("load", function () {
        showprint.print();

        showprint.addEventListener("afterprint", function () {
          showprint.close();
        });
      });
    }

    window.addEventListener('facture-validee', function (event) {
      const url = event.detail.url;
      printFacture(url);
    });
  </script>

  <script>
    document.addEventListener("livewire:load", () => {
      Livewire.hook('request.failed', ({ status }) => {
        if (status === 419) {
          alert("⚠️ Votre session a expiré, veuillez recharger la page.");
          window.location.reload();
        }
      });
    });
  </script>
</body>

</html>