<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class ClientProfileController extends Controller
{
    /**
     * Constructeur pour sécuriser l'accès de ce contrôleur
     * uniquement aux utilisateurs avec le rôle "client".
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:client']);
    }

    /**
     * Affiche la page des paramètres (profil) du client.
     */
    public function settings()
    {
        $user = Auth::user();
        
        // Fetch unread notifications for navbar
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->where('lu', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('client.settings', compact('user', 'notifications'));
    }

    /**
     * Met à jour les informations du profil client (Nom, Email, Téléphone, Mot de passe).
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'telephone' => [
                'required', 
                'string', 
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'nullable', 
                'string', 
                'email', 
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'nullable|string|min:8',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->prenom = $request->prenom;
        $user->nom = $request->nom;
        $user->telephone = $request->telephone;
        $user->email = $request->email;

        // Update password if requested
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
            
            // Notification for password change security
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'message' => 'Votre mot de passe a été modifié avec succès.',
                'date_envoi' => now(),
                'lu' => false,
            ]);
        }

        $user->save();

        return redirect()->route('client.settings')->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    /**
     * Affiche la page d'Aide / FAQ dédiée aux clients.
     */
    public function help()
    {
        $user = Auth::user();
        
        // Fetch unread notifications for navbar
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->where('lu', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('client.help', compact('user', 'notifications'));
    }
}
