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
                return redirect()->route('SuperAdmin.site_inactif');
            }
            $pass = false;
            foreach($clients as $client){
                if ($client->id_abonnement != null ) {
                    $pass = true;
                    break;
                }
            }
            if(!$pass){
                return redirect()->route('SuperAdmin.empty_abonnement');
            }
            $check = Sa_check_abonnement($clients);
            if($check == 'true'){
                return redirect()->route('SuperAdmin.no_abonnement');
            }
            return $next($request);
        }else{
            return redirect()->route('SuperAdmin.empty_client');
        }
        
    }
}
