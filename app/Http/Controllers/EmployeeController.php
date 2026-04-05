<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Affiche la liste des employés (réceptionnistes et techniciens).
     */
    public function index()
    {
        $employees = User::whereIn('role', ['receptionniste', 'technicien'])->get();
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Enregistre un nouvel employé dans le système et définit son mot de passe initial.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:receptionniste,technicien',
            'telephone' => 'required|string|unique:users',
        ]);

        User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'telephone' => $request->telephone,
            'password_changed' => 1,
            'actif' => true,
        ]);

        return redirect()->back()->with('success', 'Employé créé avec succès.');
    }

    /**
     * Active ou désactive le compte d'un employé (Toggle Status).
     * Gère les requêtes classiques ainsi que les requêtes AJAX.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->actif = !$user->actif;
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'actif' => $user->actif,
                'message' => 'Statut mis à jour.'
            ]);
        }

        return redirect()->back()->with('success', 'Statut de l\'utilisateur mis à jour.');
    }

    /**
     * Met à jour les informations d'un employé existant (Nom, Rôle, Email, Mot de passe optionnel).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:receptionniste,technicien',
            'telephone' => 'required|string|unique:users,telephone,' . $user->id,
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules);

        $dataToUpdate = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'role' => $request->role,
            'telephone' => $request->telephone,
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->back()->with('success', 'Employé modifié avec succès.');
    }

    /**
     * Supprime définitivement un employé du système et alerte les administrateurs.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $role = $user->role;
        $nomComplet = $user->prenom . ' ' . $user->nom;
        $user->delete();

        $this->notificationService->sendToAdmins("Suppression importante : L'employé {$nomComplet} ({$role}) a été supprimé.");

        return redirect()->back()->with('success', 'Employé supprimé avec succès.');
    }
}
