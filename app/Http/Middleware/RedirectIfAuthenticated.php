<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Ce Middleware redirige les utilisateurs DÉJÀ connectés quand ils essaient 
     * d'accéder à la page de Login ou d'Inscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Check : Est-ce que cet utilisateur a déjà une session active ?
            if (Auth::guard($guard)->check()) {
                // Oui, il est déjà connecté, on le renvoie à l'accueil
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Non, il n'est pas connecté, on le laisse accéder à la page de connexion
        return $next($request);
    }
}
