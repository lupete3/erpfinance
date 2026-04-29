<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Restreint l'accès à l'espace Super Admin uniquement.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 🔹 Si aucun utilisateur n'est connecté
        if (!$user) {
            notyf()->error(__('messages.page_not_found_title'));
            return redirect()->route('login');
        }

        // 🔹 Vérifie si le rôle est bien "Super Admin"
        if (!$user->hasRoleString('Super Admin')) {
            // Si c'est une requête AJAX ou API → renvoyer une réponse JSON
            if ($request->expectsJson()) {
                notyf()->error(__('messages.page_not_found_title'));
                return response()->json(['message' => __('messages.page_not_found_title')], 403);
            }

            // Sinon redirection avec un message flash
            notyf()->error(__('messages.page_not_found_title'));
            return redirect()->route('dashboard');
        }

        // ✅ Accès autorisé
        return $next($request);
    }
}

