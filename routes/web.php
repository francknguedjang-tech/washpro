<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\clientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaiementController;
/*
|--------------------------------------------------------------------------
| Fichier des Routes Web (web.php)
|--------------------------------------------------------------------------
|
| C'est ici que l'on définit toutes les URL (liens) de l'application.
| Chaque URL est associée à une fonction dans un Contrôleur spécifique.
| "get" = afficher une page, "post" = envoyer un formulaire, "put/patch" = modifier, "delete" = supprimer.
|
*/

// ==========================================================
// ROUTES PUBLIQUES (Accessibles à tout le monde sans compte)
// ==========================================================
Route::get('/', [App\Http\Controllers\ServiceController::class, 'landing'])->name('home');

// Authentification (Connexion, Déconnexion)
Route::get('/login',[AuthController::class, 'showlogin'])->name('login');
Route::post('/login',[AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Mot de passe oublié
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'request'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'email'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'update'])->middleware('guest')->name('password.update');

// Inscription publique des clients
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Page de premier changement de mot de passe obligatoire
Route::get('/first-login', [AuthController::class, 'showFirstChange'])->name('password.first_changed');

// Inscription de l'admin (désactivée automatiquement dès qu'un admin existe)
Route::get('register-admin',[AuthController::class, 'showAdminRegister'])->name('register.admin');
Route::post('register-admin',[AuthController::class, 'adminRegister'])->name('register.admin.post');
// ==========================================================
// ROUTES PROTÉGÉES (Nécessitent obligatoirement d'être connecté)
// Le "middleware('auth')" agit comme un videur de boîte de nuit
// ==========================================================
Route::middleware(['auth'])->group(function() {
    
    // Vérification de l'E-mail retirée (Accès direct)

    // Notifications (Ajoutées pour tout le monde connecté via AJAX)
    Route::get('/api/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/api/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/api/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // ------------------------------------------------------
    // SECTION ADMINISTRATEUR (Nécessite le rôle 'admin')
    // ------------------------------------------------------
    Route::middleware(['role:admin'])->group(function(){
        Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
        

        Route::get('/admin/employes', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/admin/employes', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/admin/employes/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/admin/employes/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::patch('/admin/employes/{id}/toggle', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle');
        
        // Rapports
        Route::get('/admin/rapports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/admin/rapports/journalier', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/admin/rapports/mensuel', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/admin/rapports/service', [ReportController::class, 'byService'])->name('reports.service');
        Route::get('/admin/rapports/revenus', [ReportController::class, 'revenues'])->name('reports.revenues');
    });

    // ------------------------------------------------------
    // SECTION CLIENT (Nécessite le rôle 'client')
    // ------------------------------------------------------
    Route::middleware(['role:client'])->group(function(){
        Route::get('/client/dashboard', [App\Http\Controllers\ClientDashboardController::class, 'index'])->name('client.dashboard');
        Route::get('/client/depots/{id}/recu', [App\Http\Controllers\ClientDashboardController::class, 'genererRecu'])->name('client.depots.recu');
        
        // Paiement en ligne G2TPay
        Route::get('/client/paiement/{id}/initier', [\App\Http\Controllers\G2TPayController::class, 'initier'])->name('client.paiement.initier');
        Route::get('/client/paiement/retour', [\App\Http\Controllers\G2TPayController::class, 'retour'])->name('client.paiement.retour');
        
        // Settings & Help
        Route::get('/client/parametres', [App\Http\Controllers\ClientProfileController::class, 'settings'])->name('client.settings');
        Route::put('/client/parametres', [App\Http\Controllers\ClientProfileController::class, 'update'])->name('client.settings.update');
        Route::get('/client/aide', [App\Http\Controllers\ClientProfileController::class, 'help'])->name('client.help');
    });

    // ------------------------------------------------------
    // SECTION RÉCEPTIONNISTE (Nécessite le rôle 'receptionniste')
    // ------------------------------------------------------
    Route::middleware(['role:receptionniste'])->group(function(){
        Route::get('/receptionniste/dashboard', [App\Http\Controllers\ReceptionnisteController::class, 'dashboard'])->name('receptionniste.dashboard');
    });

    // ------------------------------------------------------
    // SECTION TECHNICIEN (Nécessite le rôle 'technicien')
    // ------------------------------------------------------
    Route::middleware(['role:technicien'])->group(function(){
        Route::get('/technicien/dashboard', [App\Http\Controllers\TechnicienController::class, 'dashboard'])->name('technicien.dashboard');
    });

    // ------------------------------------------------------
    // SECTION PARTAGÉE : Admin ET Réceptionniste
    // Ces deux rôles ont les droits de créer/modifier clients, dépôts et paiements
    // ------------------------------------------------------
    Route::middleware(['role:admin,receptionniste'])->group(function(){
        
        // Clients
        Route::resource('clients', clientController::class);
        Route::get('/api/clients/recherche/{tel}', [clientController::class, 'rechercheBytel']);
        Route::post('/admin/clients/{id}/notify', [clientController::class, 'sendNotification'])->name('clients.notify');

        // Dépôts (Général)
        Route::get('/admin/depots', [DepotController::class, 'index'])->name('depots.index');
        Route::get('/admin/depots/nouveau',[DepotController::class, 'create'])->name('depots.create');
        Route::post('/admin/depots/store', [DepotController::class, 'store'])->name('depots.store');
        Route::get('/admin/depots/{id}', [DepotController::class, 'show'])->name('depots.show');
        Route::get('/admin/depots/{id}/modifier', [DepotController::class, 'edit'])->name('depots.edit');
        Route::patch('/admin/depots/{id}', [DepotController::class, 'update'])->name('depots.update');
        Route::delete('/admin/depots/{id}', [DepotController::class, 'destroy'])->name('depots.destroy');
        Route::get('/admin/depots/{id}/facture', [DepotController::class, 'genererFacture'])->name('depots.facture');

        // Paiements
        Route::get('/admin/paiements', [PaiementController::class, 'index'])->name('paiements.index');
        Route::post('/admin/paiements/store', [PaiementController::class, 'store'])->name('paiements.store');
        Route::delete('/admin/paiements/{id}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
        Route::post('/admin/depots/{id}/marquer-paye', [PaiementController::class, 'marquerPaye'])->name('paiements.marquer-paye');
        Route::get('/admin/depots/{id}/recu', [PaiementController::class, 'genererRecu'])->name('paiements.recu');
        Route::get('/admin/depots/{id}/paiements', [PaiementController::class, 'depotPaiements'])->name('depots.paiements');

        // Services
        Route::resource('services', ServiceController::class);
    });

    // ------------------------------------------------------
    // SECTION PARTAGÉE : Admin, Réceptionniste ET Technicien
    // Utilisé ici par exemple pour changer le statut (En cours -> Prêt)
    // ------------------------------------------------------
    Route::middleware(['role:admin,receptionniste,technicien'])->group(function(){
        Route::patch('/admin/depots/{id}/status', [DepotController::class, 'updateStatus'])->name('depots.updateStatus');
    });
});
