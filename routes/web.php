<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BonLivraisonController;
use App\Http\Controllers\CamionController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FormulationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\RotationController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:commercial')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('commandes', CommandeController::class);
        Route::post('commandes/{commande}/confirmer', [CommandeController::class, 'confirmer'])->name('commandes.confirmer');
        Route::post('commandes/{commande}/annuler', [CommandeController::class, 'annuler'])->name('commandes.annuler');
    });

    Route::middleware('role:logistique,commercial')->group(function () {
        Route::get('commandes/{commande}/planifier', [CommandeController::class, 'planifier'])->name('commandes.planifier');
        Route::post('commandes/{commande}/planifier', [CommandeController::class, 'storePlanification'])->name('commandes.planifier.store');
    });

    Route::middleware('role:logistique')->group(function () {
        Route::resource('camions', CamionController::class)->except('show');
        Route::resource('chauffeurs', ChauffeurController::class)->except('show');
        Route::get('planning', [RotationController::class, 'planning'])->name('planning');
        Route::post('rotations/{rotation}/statut', [RotationController::class, 'updateStatut'])->name('rotations.statut');
        Route::put('rotations/{rotation}/affectation', [RotationController::class, 'updateAffectation'])->name('rotations.affectation');

        Route::get('stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('stock/create', [StockController::class, 'create'])->name('stock.create');
        Route::post('stock', [StockController::class, 'store'])->name('stock.store');
        Route::get('stock/{matiere}/edit', [StockController::class, 'edit'])->name('stock.edit');
        Route::put('stock/{matiere}', [StockController::class, 'update'])->name('stock.update');
        Route::get('stock/{matiere}/approvisionner', [StockController::class, 'approvisionner'])->name('stock.approvisionner');
        Route::post('stock/{matiere}/approvisionner', [StockController::class, 'storeApprovisionnement'])->name('stock.approvisionner.store');
        Route::get('stock-mouvements', [StockController::class, 'mouvements'])->name('stock.mouvements');

        Route::get('formulations', [FormulationController::class, 'index'])->name('formulations.index');
        Route::get('produits/{produit}/formulation', [FormulationController::class, 'edit'])->name('produits.formulation.edit');
        Route::put('produits/{produit}/formulation', [FormulationController::class, 'update'])->name('produits.formulation.update');
    });

    Route::middleware('role:logistique,caissier,commercial')->group(function () {
        Route::get('bons', [BonLivraisonController::class, 'index'])->name('bons.index');
        Route::get('bons/{bon}', [BonLivraisonController::class, 'show'])->name('bons.show');
        Route::get('bons/{bon}/edit', [BonLivraisonController::class, 'edit'])->name('bons.edit');
        Route::put('bons/{bon}', [BonLivraisonController::class, 'update'])->name('bons.update');
        Route::get('bons/{bon}/imprimer', [BonLivraisonController::class, 'imprimer'])->name('bons.imprimer');
        Route::post('bons/{bon}/signer', [BonLivraisonController::class, 'signer'])->name('bons.signer');
    });

    Route::middleware('role:caissier')->group(function () {
        Route::resource('factures', FactureController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('factures/{facture}/imprimer', [FactureController::class, 'imprimer'])->name('factures.imprimer');
        Route::post('factures/{facture}/bons/{bon}/retirer', [FactureController::class, 'retirerBon'])->name('factures.bons.retirer');
        Route::resource('paiements', PaiementController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('paiements/{paiement}/encaisser', [PaiementController::class, 'marquerEncaisse'])->name('paiements.encaisser');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('produits', ProduitController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
    });
});
