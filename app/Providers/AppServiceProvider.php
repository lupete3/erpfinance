<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
      if (App::environment('production')) {
          URL::forceScheme('https');
      }

      Livewire::listen('component.dehydrate', function ($component, $response) {
        $response->memo['locale'] = app()->getLocale();
      });

      Livewire::listen('component.hydrate', function ($component, $request) {
          if ($locale = $request->memo['locale'] ?? null) {
              app()->setLocale($locale);
          }
      });
    }
}