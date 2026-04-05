<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Affiche la liste complète des services proposés (pour l'administration).
     */
    public function index()
    {
        $services = \App\Models\Service::all();
        return view('services.index', compact('services'));
    }

    /**
     * Affiche la page d'accueil publique (Landing Page) avec les services disponibles.
     */
    public function landing()
    {
        $services = \App\Models\Service::all();
        return view('welcome', compact('services'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau service (Optionnel si géré par modal).
     */
    public function create()
    {
        //
    }

    /**
     * Enregistre un nouveau service dans la base de données après validation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required',
            'prix_unitaire' => 'required|numeric',
            'unite' => 'required'
        ]);
    \App\Models\Service::create($request->all());
    return back()->with('success', 'Service ajouté !'); 
    }

    /**
     * Affiche les détails d'un service spécifique.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Affiche le formulaire de modification d'un service (Optionnel).
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Met à jour les informations d'un service (nom, prix, unité) dans la base de données.
     */
    public function update(Request $request, string $id)
    {
        $service = \App\Models\Service::findOrFail($id);
        $service->update($request->all());
        return back()->with('success', 'Service mis à jour !');
    }

    /**
     * Supprime définitivement un service du catalogue.
     */
    public function destroy(string $id)
    {
        $service = \App\Models\Service::findOrFail($id);
    $service->delete();
    return back()->with('success', 'Service supprimé avec succès.');
    }
}
