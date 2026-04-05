<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrateur par défaut
        \App\Models\User::create([
            'nom' => 'Administrateur',
            'prenom' => 'WashPro',
            'email' => 'admin@washpro.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'password_changed' => 1,
            'actif' => true
        ]);

        // Receptionniste par défaut
        \App\Models\User::create([
            'nom' => 'DIOP',
            'prenom' => 'Moussa',
            'email' => 'reception@washpro.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'receptionniste',
            'password_changed' => 1,
            'actif' => true
        ]);

        // Technicien par défaut
        \App\Models\User::create([
            'nom' => 'SOW',
            'prenom' => 'Ibrahima',
            'email' => 'technicien@washpro.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'technicien',
            'password_changed' => 1,
            'actif' => true
        ]);

        // Services de base
        \App\Models\service::create([
            'libelle' => 'Lavage simple',
            'description' => 'Lavage de vêtements courants',
            'prix_unitaire' => 1000
        ]);

        \App\Models\service::create([
            'libelle' => 'Repassage seul',
            'description' => 'Repassage professionnel',
            'prix_unitaire' => 500
        ]);

        \App\Models\service::create([
            'libelle' => 'Nettoyage à sec',
            'description' => 'Traitement pour tissus délicats',
            'prix_unitaire' => 2500
        ]);
    }
}
