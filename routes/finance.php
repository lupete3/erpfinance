<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified', 'check.subscription'])->prefix('finance')->group(function () {
    
    // Espace BOSS
    Route::middleware(['check.role:Boss,Admin'])->prefix('boss')->name('finance.boss.')->group(function () {
        Volt::route('/stores', 'boss.stores.index')->name('stores.index');
        Volt::route('/stores/create', 'boss.stores.create')->name('stores.create');
        Volt::route('/stores/{store}/edit', 'boss.stores.edit')->name('stores.edit');

        // Gérants
        Volt::route('/managers', 'boss.managers.index')->name('managers.index');
        Volt::route('/managers/create', 'boss.managers.create')->name('managers.create');
        Volt::route('/managers/{manager}/edit', 'boss.managers.edit')->name('managers.edit');
        
        // Dotations
        Volt::route('/dotations', 'boss.dotations.index')->name('dotations.index');
        Volt::route('/dotations/create', 'boss.dotations.create')->name('dotations.create');
        Volt::route('/dotations/{dotation}/edit', 'boss.dotations.edit')->name('dotations.edit');

        // Catégories de dépenses
        Volt::route('/expense-categories', 'boss.expense-categories.index')->name('expense-categories.index');
        Volt::route('/expense-categories/{category}/edit', 'boss.expense-categories.edit')->name('expense-categories.edit');

        // Reporting
        Volt::route('/reports', 'boss.reports.index')->name('reports.index');
        Volt::route('/reports/summary', 'boss.reports.summary')->name('reports.summary');
        Route::get('/reports/summary/export', [\App\Http\Controllers\ReportController::class, 'exportSummary'])->name('reports.summary.export');
        Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');

        // Paramètres
        Volt::route('/settings/company', 'boss.settings.company')->name('settings.company');

        // États de besoin (Boss)
        Volt::route('/budget-requests', 'boss.budget-requests.index')->name('budget-requests.index');
    });

    // Espace GÉRANT (Accessibles aussi au Boss pour supervision)
    Route::middleware(['check.role:Gérant,Boss,Admin'])->prefix('manager')->name('finance.manager.')->group(function () {
        Volt::route('/expenses', 'manager.expenses.index')->name('expenses.index');
        Volt::route('/expenses/create', 'manager.expenses.create')->name('expenses.create');
        Volt::route('/dotations', 'manager.dotations.index')->name('dotations.index');
        Volt::route('/dotations/create', 'manager.dotations.create')->name('dotations.create');

        // Reporting Gérant
        Volt::route('/reports', 'manager.reports.index')->name('reports.index');
        Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');

        // États de besoin (Gérant)
        Volt::route('/budget-requests', 'manager.budget-requests.index')->name('budget-requests.index');
        Volt::route('/budget-requests/create', 'manager.budget-requests.create')->name('budget-requests.create');
        Volt::route('/budget-requests/{budgetRequest}/edit', 'manager.budget-requests.edit')->name('budget-requests.edit');
    });

});
