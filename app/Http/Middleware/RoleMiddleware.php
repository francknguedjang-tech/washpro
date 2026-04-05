<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Gère les requêtes entrantes pour vérifier le rôle de l'utilisateur.
     * C'est le "vigile" de l'application qui s'assure que vous avez le droit d'entrer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next La suite de l'exécution (laisser passer)
     * @param  mixed  ...$roles La liste des rôles autorisés (ex: 'admin', 'client')
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Si l'utilisateur n'est pas connecté OU que son rôle n'est pas dans la liste permise
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // On bloque l'accès et on affiche la page d'erreur 403 (Interdit)
            abort(403);
        }
        
        // Tout va bien, on le laisse continuer vers l'URL demandée
        return $next($request);
    }
}
