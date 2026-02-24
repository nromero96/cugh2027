<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Participante')) {

            if (!$user->inscription) {

                // Rutas permitidas sin inscripción
                if (
                    !$request->routeIs(
                        'inscriptions.myinscription',
                        'inscriptions.storemyinscription',
                        'countries.byInstitution',
                        'logout'
                    )
                ) {
                    return redirect()->route('inscriptions.myinscription');
                }
            }
        }

        return $next($request);
    }
}
