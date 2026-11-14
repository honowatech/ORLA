<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SuperAdmin\Client;

class Check_Sa_Client_Error
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $erreur = $this->erreur();

        if ($erreur === null) {
            return $next($request);
        }

        // L'API (application mobile) reçoit une réponse JSON au lieu d'une redirection.
        if ($request->is('api/*')) {
            return response()->json([
                'message' => self::MESSAGES_API[$erreur],
                'code' => $erreur,
            ], $erreur === 'abonnement_expire' || $erreur === 'abonnement_absent' ? 402 : 503);
        }

        return redirect()->route(self::ROUTES_WEB[$erreur]);
    }

    private const ROUTES_WEB = [
        'aucun_client' => 'SuperAdmin.empty_client',
        'site_inactif' => 'SuperAdmin.site_inactif',
        'abonnement_absent' => 'SuperAdmin.empty_abonnement',
        'abonnement_expire' => 'SuperAdmin.no_abonnement',
    ];

    private const MESSAGES_API = [
        'aucun_client' => 'La plateforme n\'est pas encore configurée.',
        'site_inactif' => 'La plateforme est momentanément inactive.',
        'abonnement_absent' => 'Aucun abonnement n\'est associé à la plateforme.',
        'abonnement_expire' => 'L\'abonnement de la plateforme a expiré.',
    ];

    /**
     * Retourne le code d'erreur d'abonnement, ou null si l'accès est autorisé.
     */
    private function erreur(): ?string
    {
        $pass = false;
        $clients = Client::get();
        if($clients->count() > 0 ){
            foreach($clients as $client){
                if ($client->statut == 1 ) {
                    $pass = true;
                    break;
                }
            }
            if(!$pass){
                return 'site_inactif';
            }
            $pass = false;
            foreach($clients as $client){
                if ($client->id_abonnement != null ) {
                    $pass = true;
                    break;
                }
            }
            if(!$pass){
                return 'abonnement_absent';
            }
            $check = Sa_check_abonnement($clients);
            if($check == 'true'){
                return 'abonnement_expire';
            }
            return null;
        }else{
            return 'aucun_client';
        }
        
    }
}
