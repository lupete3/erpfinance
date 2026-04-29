<?php

namespace App\Livewire;

use App\Models\Language;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LanguageSwitcher extends Component
{
    public $locale;
    protected $supportedLocales = ['fr', 'en', 'es', 'de'];

    public function mount()
    {
        $this->locale = Auth::check()
            ? Auth::user()->language?->locale ?? config('app.locale')
            : session('locale', config('app.locale'));

        app()->setLocale($this->locale);
    }

    public function changeLocale(string $locale)
    {
        if (!in_array($locale, $this->supportedLocales)) return;

        // Mise à jour base ou session
        if (Auth::check()) {
            Language::updateOrCreate(
                ['user_id' => Auth::id()],
                ['locale' => $locale]
            );
        } else {
            session(['locale' => $locale]);
        }

        // Mise à jour Livewire et application
        $this->locale = $locale;
        app()->setLocale($locale);

        $this->dispatch('updateLocale', ['locale' => $locale]);
        $this->dispatch('localeUpdatedGlobally', ['locale' => $locale]);

        notyf()->success(__('dashboard.language_change'));

        $this->redirect(request()->header('Referer'), navigate: false);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}

