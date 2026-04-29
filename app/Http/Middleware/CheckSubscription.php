<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 🔹 Bypass si c'est un Super Admin (role_id 4 ou check role string)
        if ($user->role_id == 4 || $user->hasRoleString('Super Admin')) {
            return $next($request);
        }

        // 🔹 Vérification de l'activité du compte utilisateur
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte est désactivé.');
        }

        $tenant = $user->tenant;

        // 🔹 Si l'utilisateur appartient à une organisation, elle doit être active
        if ($tenant && !$tenant->is_active) {
            return abort(403, 'Votre organisation est suspendue. Contactez l\'administration.');
        }

        // 🔹 Vérification de la souscription (Uniquement si l'utilisateur appartient à un tenant)
        if ($tenant) {
            $subscription = $tenant->activeSubscription()->first();

            if (!$subscription || !$subscription->isValid()) {
                return abort(403, 'Votre abonnement a expiré. Merci de renouveler.');
            }
        }

        return $next($request);
    }
}
