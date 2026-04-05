<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{       
    /**
     * Affiche le formulaire d'inscription pour l'Administrateur principal.
     * Accessible uniquement s'il n'y a pas encore d'admin dans le système.
     */
    public function showAdminRegister(){
        if(\App\Models\User::where('role', 'admin')->count()>0){
             return redirect()->route('login');
        }
        return view('register_admin');
    }
    /**
     * Traite l'inscription du premier Administrateur.
     */
    public function adminRegister(Request $request){
        $request->validate([
            'nom'=>'required',
            'prenom'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6',
        ]);
        \App\Models\User::create([
            'nom'=>$request->nom,
            'prenom'=>$request->prenom,
            'email'=> $request->email,
            'password'=>\Illuminate\Support\Facades\Hash::make($request->password),
            'role'=>'admin',
            'password_changed'=>1,
        ]);
        return redirect()->route('login')->with('success','Administrateur creer!');
    }
    /**
     * Affiche la page de connexion sécurisée.
     */
    public function showlogin(){
        return view('auth.login');
    }
    /**
     * Authentifie l'utilisateur, vérifie si son compte est actif,
     * et gère la redirection conditionnelle selon son statut (nouveau mot de passe requis ou non).
     */
    public function login(Request $request){
        $credentials= $request->only('email','password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if (!$user->actif) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé.']);
            }

            $request->session()->regenerate();

            return $this->redirectByRole();
        }
        return back()->withErrors([
            'email'=>'identifiants incorrect.',
        ]);
    }
    /**
     * Méthode interne pour rediriger l'utilisateur vers le bon tableau de bord
     * en fonction de son rôle (Admin, Technicien, Réceptionniste, Client).
     */
    private function redirectByRole(){
        $role= Auth::user()->role;
        switch ($role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'receptionniste':
                return redirect('/receptionniste/dashboard');
            case 'technicien':
                return redirect('/technicien/dashboard');
            default:
                return redirect('/client/dashboard');    
        }
    }
    /**
     * Déconnecte l'utilisateur et détruit sa session active.
     */
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showFirstChange(){
        return view('auth.first-login');
    }

    /**
     * Affiche le formulaire d'inscription publique pour les nouveaux clients clients.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Enregistre un nouveau client depuis la page d'inscription publique
     * et le connecte automatiquement.
     */
    public function register(Request $request)
    {
        $messages = [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez fournir une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà pris.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], $messages);

        // On utilise une transaction base de données (DB transaction). 
        // Si une erreur grave survient en plein milieu (ex: plantage), TOUT est annulé. 
        // Ainsi, on ne se retrouve pas avec des données à moitié enregistrées.
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Détermination du rôle : le tout premier utilisateur enregistré devient Administrateur (admin)
            // Les utilisateurs suivants créés via cette page seront automatiquement des clients.
            $role = \App\Models\User::count() === 0 ? 'admin' : 'client';

            // Création du compte utilisateur dans la base de données
            $user = \App\Models\User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                // Le mot de passe est haché avant son enregistrement pour garantir la sécurité
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => $role,
                'actif' => true,                 // Le compte est actif par défaut
                'password_changed' => true,      // Confirme que le client a bien configuré son propre mot de passe
            ]);

            // Envoi de l'email de bienvenue
            // Un bloc try...catch spécifique pour l'email évite que l'inscription échoue 
            // juste parce que le serveur mail est momentanément en panne.
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\WelcomeClientMail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erreur lors de l'envoi de l'email de bienvenue : " . $e->getMessage());
            }

            // Connexion automatique du nouveau client après sa création
            Auth::login($user);

            // Validation de toutes les opérations dans la base de données
            \Illuminate\Support\Facades\DB::commit();

            // Redirection vers le tableau de bord avec un message de succès
            return $this->redirectByRole()->with('success', 'Bienvenue sur WashPro !');

        } catch (\Exception $e) {
            // En cas de blocage intempestif ou problème serveur, on annule l'enregistrement en base
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Erreur globale lors de l'inscription : " . $e->getMessage());
            
            // On renvoie l'utilisateur vers le formulaire avec un message d'erreur
            return back()->withInput()->withErrors(['error' => 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.']);
        }
    }


}
