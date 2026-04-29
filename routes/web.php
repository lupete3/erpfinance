<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\Bakery\BakeryInvoiceController;

Route::get('/', function () {
  return redirect()->route('login');
})->name('home');

Route::get('/documentation', function () {
  return view('pages.documentation');
})->name('documentation');

Route::get('/license', function () {
  return view('pages.license');
})->name('license');

Route::get('/support', function () {
  return view('pages.support');
})->name('support');

Route::get('dashboard', [DashboardController::class, 'index'])
  ->middleware(['auth', 'verified', 'check.subscription'])
  ->name('dashboard');

// Dashboard boulangerie (Accessible à tous les rôles boulangerie)
Route::get('dashboard-boulangerie', App\Livewire\Dashboard\Index::class)
  ->middleware(['auth', 'verified', 'check.subscription'])
  ->name('dashboard.boulangerie');

// Espace admin boulangerie
Route::middleware(['auth', 'verified', 'check.subscription', 'check.bakery.role:admin'])->group(function () {
  Route::get('bakery/admin/settings', App\Livewire\Bakery\Admin\Settings::class)->name('bakery.admin.settings');
  Route::get('bakery/reports', App\Livewire\Bakery\Reports::class)->name('bakery.reports');
  Route::get('bakery/stock/transfert', App\Livewire\Bakery\Stock\Transfert::class)->name('bakery.stock.transfert');
});

// Espace Gérant Dépôt Magasin (MP Dépôt)
Route::middleware(['auth', 'verified', 'check.subscription', 'check.bakery.role:geran_depot_magasin'])->group(function () {
  Route::get('bakery/fournisseurs', App\Livewire\Bakery\Fournisseur::class)->name('bakery.fournisseurs');
  Route::get('bakery/achats', App\Livewire\Bakery\AchatStock::class)->name('bakery.achats');
  Route::get('bakery/stock/maison', App\Livewire\Bakery\Stock\Maison::class)->name('bakery.stock.maison');
});

// Espace Gérant Dépôt Usine (Production / PF)
Route::middleware(['auth', 'verified', 'check.subscription', 'check.bakery.role:geran_depot_usine'])->group(function () {
  Route::get('bakery/stock/usine', App\Livewire\Bakery\Stock\Usine::class)->name('bakery.stock.usine');
  Route::get('bakery/stock/pf', App\Livewire\Bakery\Stock\Pf::class)->name('bakery.stock.pf');
  Route::get('bakery/production', App\Livewire\Bakery\Production\Index::class)->name('bakery.production');
});

// Espace Gérant Dépôt Boulangerie (POS / Point de Vente)
Route::middleware(['auth', 'verified', 'check.subscription', 'check.bakery.role:geran_depot_boulangerie'])->group(function () {
  Route::get('bakery/stock/boulangerie', App\Livewire\Bakery\Stock\Boulangerie::class)->name('bakery.stock.boulangerie');
  Route::get('bakery/clients', App\Livewire\Bakery\Client::class)->name('bakery.clients');
  Route::get('bakery/ventes', App\Livewire\Bakery\Pos::class)->name('bakery.pos');
  Route::get('bakery/clients/{client}/overview', App\Livewire\Bakery\ClientDetail::class)->name('bakery.clients.overview');
  Route::get('bakery/clients/{client}/export-pdf', [App\Http\Controllers\Bakery\ClientReportController::class, 'exportPdf'])->name('bakery.clients.export');
  Route::get('bakery/dettes', App\Livewire\Bakery\ClientDebt::class)->name('bakery.dettes');
  Route::get('bakery/caisse', App\Livewire\Bakery\Caisse\Index::class)->name('bakery.caisse');
  Route::get('bakery/cloture', App\Livewire\Bakery\Cloture::class)->name('bakery.cloture');

  // Impression Factures Bakery
  Route::get('bakery/invoice/pos/{id}', [BakeryInvoiceController::class, 'printPos'])->name('bakery.invoice.pos');
  Route::get('bakery/invoice/a4/{id}', [BakeryInvoiceController::class, 'printA4'])->name('bakery.invoice.a4');
});

// Routes partagées pour l'historique des mouvements
Route::middleware(['auth', 'verified', 'check.subscription', 'check.bakery.role:geran_depot_magasin,geran_depot_usine'])->group(function () {
  Route::get('bakery/stock/mouvements', App\Livewire\Bakery\Stock\Mouvements::class)->name('bakery.stock.mouvements');
});

Route::middleware(['auth', 'verified', 'check.subscription', 'check.bakery.role:geran_depot_usine,geran_depot_boulangerie'])->group(function () {
  Route::get('bakery/stock/mouvements-pf', App\Livewire\Bakery\Stock\MouvementsPf::class)->name('bakery.stock.mouvements-pf');
});

Route::middleware(['auth', 'verified', 'check.subscription', 'check.superadmin'])->group(function () {
  Route::get('plans', [PlanController::class, 'index'])->name('plan.index');
  Route::get('tenants', [TenantController::class, 'index'])->name('tenant.index');
  Route::get('souscription', [SubscriptionController::class, 'index'])->name('souscription.index');
  Route::get('clients-overview', [DashboardController::class, 'superAdminOverview'])->name('overviewsuperadmin.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
  // Settings (Shared)
  Route::redirect('settings', 'settings/profile');
  Volt::route('settings.profile', 'settings.profile')->name('settings.profile');
  Volt::route('settings.password', 'settings.password')->name('settings.password');

  // Company Settings
  Route::get('/settings/company', [DashboardController::class, 'settings'])->name('company.settings');
});

Route::fallback(function () {
  return response()->view('errors.404', [], 404);
});

require __DIR__ . '/finance.php';
require __DIR__ . '/auth.php';


