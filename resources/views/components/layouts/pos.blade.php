<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-base-url="{{ url('/') }}" data-framework="laravel">
  <head>
    @include('partials.head')
    <style>
      html, body {
        height: 100%;
        margin: 0;
        background-color: #f9fafb;
      }

      body {
        display: flex;
        flex-direction: column;
        overflow: hidden;
      }

      main {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: stretch;
        padding: 1rem;
        overflow-y: auto;
      }

      /* ✅ Animation fluide pour transitions Livewire */
      [wire\:loading] {
        opacity: 0.6;
        pointer-events: none;
        transition: opacity 0.2s ease;
      }

      /* ✅ Améliore le rendu tactile pour POS */
      * {
        -webkit-tap-highlight-color: transparent;
      }
    </style>

    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --success: #4cc9f0;
            --success-dark: #4895ef;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        .pos-container {
            min-height: 95vh;
            padding: 2rem 1rem;
        }
        
        .dashboard-btn {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1000;
            background: var(--primary);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dashboard-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px) rotate(90deg);
            box-shadow: 0 6px 25px rgba(67, 97, 238, 0.4);
        }
        
        .dashboard-btn i {
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .dashboard-btn:hover i {
            transform: rotate(90deg);
        }
        
        .section-header {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-gray);
        }
        
        .section-header h2 {
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
            color: var(--primary);
        }
        
        .search-container {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-gray);
        }
        
        .search-input-group {
            background: var(--light);
            border-radius: 16px;
            padding: 0.5rem 1rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .search-input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .search-input {
            border: none;
            background: transparent;
            padding: 0.5rem 0.5rem 0.5rem 0;
            font-size: 1rem;
            outline: none;
            width: 100%;
        }
        
        .filters-container {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .filter-select {
            background: var(--light);
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            min-width: 180px;
        }
        
        .filter-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.2rem;
            padding: 1rem 0;
            max-height: 60vh;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        
        .products-grid::-webkit-scrollbar {
            width: 6px;
        }
        
        .products-grid::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }
        
        .products-grid::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
        
        .product-item {
            background: white;
            border-radius: 16px;
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .product-item:hover {
            transform: translateY(-4px);
            border-color: var(--primary-light);
            box-shadow: 0 8px 24px rgba(67, 97, 238, 0.15);
        }
        
        .product-item:active {
            transform: translateY(0);
        }
        
        .product-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0.5rem 0;
            color: var(--dark);
            line-height: 1.3;
        }
        
        .product-stock {
            font-size: 0.8rem;
            color: var(--gray);
            margin-bottom: 0.8rem;
        }
        
        .product-price {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .product-price:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .cart-container {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-gray);
            display: flex;
            flex-direction: column;
            height: fit-content;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-gray);
        }
        
        .cart-header h2 {
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
            color: var(--success-dark);
        }
        
        .clear-cart-btn {
            background: var(--light);
            color: var(--danger);
            border: 2px solid var(--danger);
            border-radius: 16px;
            padding: 0.4rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .clear-cart-btn:hover {
            background: var(--danger);
            color: white;
            transform: scale(1.05);
        }
        
        .cart-items {
            max-height: 45vh;
            overflow-y: auto;
            margin-bottom: 1.5rem;
            padding-right: 0.5rem;
        }
        
        .cart-items::-webkit-scrollbar {
            width: 6px;
        }
        
        .cart-items::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }
        
        .cart-items::-webkit-scrollbar-thumb {
            background: var(--success);
            border-radius: 3px;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
            min-width: 0;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
            color: var(--dark);
        }
        
        .item-price {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-left: 1rem;
        }
        
        .quantity-input {
            width: 70px;
            text-align: center;
            padding: 0.4rem;
            border: 2px solid var(--light-gray);
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .quantity-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .item-subtotal {
            font-weight: 700;
            font-size: 1rem;
            color: var(--primary);
            min-width: 80px;
            text-align: right;
        }
        
        .remove-btn {
            background: var(--light);
            color: var(--danger);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            background: var(--danger);
            color: white;
            transform: rotate(90deg);
        }
        
        .totals-section {
            border-top: 2px solid var(--light-gray);
            padding-top: 1.5rem;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }
        
        .total-row:last-child {
            margin-bottom: 1.5rem;
        }
        
        .total-label {
            color: var(--gray);
        }
        
        .total-value {
            font-weight: 700;
            color: var(--dark);
        }
        
        .grand-total {
            font-size: 1.4rem;
            color: var(--success-dark);
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .form-control {
            border: 2px solid var(--light-gray);
            border-radius: 16px;
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        .actions-section {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .action-btn {
            flex: 1;
            min-width: 180px;
            padding: 1rem;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }
        
        .validate-btn {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
            border: none;
        }
        
        .validate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 201, 240, 0.4);
        }
        
        .cancel-btn {
            background: var(--light);
            color: var(--gray);
            border: 2px solid var(--light-gray);
        }
        
        .cancel-btn:hover {
            background: var(--light-gray);
            transform: translateY(-2px);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        @media (max-width: 992px) {
            .pos-container {
                padding: 1rem 0.5rem;
            }
            
            .dashboard-btn {
                top: 1rem;
                right: 1rem;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
            
            .form-row {
                flex-direction: column;
                gap: 1rem;
            }
            
            .form-group {
                min-width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .product-name {
                font-size: 0.85rem;
            }
            
            .actions-section {
                flex-direction: column;
            }
            
            .action-btn {
                min-width: 100%;
            }
        }
    </style>
  </head>

  <body>
    <main>
      {{ $slot }}
    </main>

    @include('partials.scripts')

    <script>
      // ✅ Gestion automatique de la session expirée
      document.addEventListener("livewire:load", () => {
          Livewire.hook('request.failed', ({ status }) => {
              if (status === 419) {
                  alert("⚠️ Votre session a expiré, veuillez vous reconnecter.");
                  window.location.reload();
              }
          });
      });

      // ✅ Impression fluide via iframe (sans popup)
      function printFacture(url) {
          let iframe = document.getElementById('printFrame');
          if (!iframe) {
              iframe = document.createElement('iframe');
              iframe.id = 'printFrame';
              iframe.style.position = 'absolute';
              iframe.style.width = '0';
              iframe.style.height = '0';
              iframe.style.border = 'none';
              document.body.appendChild(iframe);
          }
          
          iframe.src = url;
      }

      window.addEventListener('facture-validee', event => {
          printFacture(event.detail.url);
      });
    </script>
  </body>
</html>
