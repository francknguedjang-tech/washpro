<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientCreatedMail;
use App\Services\NotificationService;

class clientController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Affiche la liste de tous les clients avec leurs statistiques d'achat et soldes impayés.
     */
    public function index()
    {
        $clients = \App\Models\User::where('role', 'client')
            ->with('depots')
            ->withCount('depots')
            ->get()
            ->map(function($client) {
                $client->total_depense = $client->depots->sum('prix_total');
                $client->a_impayes = $client->depots->where('etat', '!=', 'recuperer')->isNotEmpty();
                return $client;
            })
            ->sortByDesc('total_depense');

        $totalClients = $clients->count();
        $meilleurClient = $clients->first();
        $clientsAvecImpayes = $clients->where('a_impayes', true)->count();

        return view('clients.index', compact('clients', 'totalClients', 'meilleurClient', 'clientsAvecImpayes'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau client depuis l'administration.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistre un nouveau client (avec notification par email "ClientCreatedMail").
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:users',
            'email' => 'nullable|email|unique:users',
        ]);

        $password = 'client123';
        $user = \App\Models\User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'email' => $request->email ?? ($request->telephone . '@washpro.com'),
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'client',
            'password_changed' => 0,
        ]);

        if ($request->filled('email')) {
            try {
                Mail::to($user->email)->queue(new ClientCreatedMail($user, $password));
            } catch (\Exception $e) {
                \Log::error("Échec de l'envoi de l'email de bienvenue : " . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'client' => $user,
                'message' => 'Client créé avec succès.'
            ]);
        }

        $this->notificationService->sendToAdmins("Nouveau client : Un nouveau client ({$user->prenom} {$user->nom}) a été créé.");
        $this->notificationService->sendToReceptionists("Nouveau client : Un nouveau client ({$user->prenom} {$user->nom}) a été créé.");

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }
    /**
     * Affiche le profil détaillé d'un client spécifique (Admin/Réception).
     */
    public function show($id)
    {
        $client = \App\Models\User::where('role', 'client')->with(['depots.linges.service'])->findOrFail($id);
        
        $stats = [
            'total_depots' => $client->depots->count(),
            'total_depense' => $client->depots->sum('prix_total'),
            'en_cours' => $client->depots->where('etat', 'en cours')->count(),
            'a_impayes' => $client->depots->where('etat', '!=', 'recuperer')->count(),
        ];

        return view('clients.show', compact('client', 'stats'));
    }

    /**
     * Affiche le formulaire de modification des informations d'un client.
     */
    public function edit($id)
    {
        $client = \App\Models\User::where('role', 'client')->findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Met à jour les informations de contact d'un client existant.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:users,telephone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
        ]);

        $client = \App\Models\User::where('role', 'client')->findOrFail($id);
        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprime un client si et seulement s'il n'a pas de dépôts en cours/impayés.
     */
    public function destroy($id)
    {
        $client = \App\Models\User::where('role', 'client')->findOrFail($id);
        
        // Safety check: don't delete if has active depots
        if ($client->depots()->where('etat', '!=', 'recuperer')->exists()) {
            return back()->with('error', 'Impossible de supprimer un client ayant des dépôts en cours.');
        }

        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    /**
     * Envoie une notification personnalisée au tableau de bord du client.
     */
    public function sendNotification(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $client = \App\Models\User::where('role', 'client')->findOrFail($id);
        
        \App\Models\Notification::create([
            'user_id' => $client->id,
            'message' => $request->message,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        return redirect()->back()->with('success', 'Notification envoyée avec succès au client.');
    }

    /**
     * API AJAX : Recherche instantanée d'un client par son numéro de téléphone ou nom.
     */
    public function rechercheBytel($tel)
    {
        $clients = \App\Models\User::where('role', 'client')
            ->where(function($q) use ($tel) {
                $q->where('telephone', 'LIKE', "%{$tel}%")
                  ->orWhere('nom', 'LIKE', "%{$tel}%")
                  ->orWhere('prenom', 'LIKE', "%{$tel}%");
            })
            ->limit(10)
            ->get();
    
        return response()->json($clients);
    }
}
