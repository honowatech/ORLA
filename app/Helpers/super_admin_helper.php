<?php

use Carbon\Carbon;

if (! function_exists('check_superadmin')) {
    function check_superadmin()
    {
        if (session()->has('SuperAdmin_infos')) {
            return 'true';
        } else {
            return 'SuperAdmin.login';
        }
    }
}
if (! function_exists('Sa_check_abonnement')) {
    function Sa_check_abonnement($clients)
    {
        $result = true;
        $today = Carbon::now();
        foreach ($clients as $client) {
            if ($client->date_fin == null) {
                $result = true;

                continue;
            }
            $date = Carbon::parse($client->date_fin);
            if (isset($client->abonnement->periode_grace)) {
                $date->addDays($client->abonnement->periode_grace);
            }
            if (! $date->lt($today)) {
                $result = false;
                break;
            }
        }
        if ($result) {
            return 'true';
        } else {
            return 'login';
        }

    }
}
if (! function_exists('Sa_statut')) {
    function Sa_statut($clients)
    {
        $result = true;
        foreach ($clients as $client) {
            if ($client->statut == 1) {
                $result = false;
                break;
            }
        }

        return $result;

    }
}
if (! function_exists('Sa_prochaine_date_paie')) {
    function Sa_prochaine_date_paie($type_periode, $accumulateur, $ancienne_date, $nombre_fois)
    {
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////

        $dt = Carbon::parse($ancienne_date);

        if ($type_periode == 'jour') {
            $prochaine_date = $dt->addDays($accumulateur * $nombre_fois);
        } elseif ($type_periode == 'semaine') {
            $prochaine_date = $dt->addWeeks($accumulateur * $nombre_fois);
        } elseif ($type_periode == 'mois') {
            $prochaine_date = $dt->addMonths($accumulateur * $nombre_fois);
        } elseif ($type_periode == 'annee') {
            $prochaine_date = $dt->addYears($accumulateur * $nombre_fois);
        }

        // //////////////////////////////////////////////////////////////////////////////////////////////////////////
        return $prochaine_date;
    }
}
if (! function_exists('Sa_date_debut')) {
    function Sa_date_debut($client)
    {
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////
        return true;
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
}
if (! function_exists('Sa_pay')) {
    function Sa_pay($montant, $id_transaction)
    {
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once __DIR__.'/../Http/Controllers/SuperAdmin/monetbil/monetbil.php';
        // Setup Monetbil arguments
        Monetbil::setAmount($montant);
        Monetbil::setCurrency('XAF');
        Monetbil::setLocale('fr'); // Display language fr or en
        Monetbil::setCountry('CM');
        Monetbil::setPayment_ref(Sa_payment_ref($id_transaction));
        Monetbil::setUser(12);
        Monetbil::setReturn_url(route('Sc-transaction.checkpay', $id_transaction));
        Monetbil::setLogo('https://focus-rent.honowa.com/public/app-assets/images/logo/fichier_2.svg');

        // Start a payment
        // You will be redirected to the payment page
        return Monetbil::startPayment();
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////

    }
}
if (! function_exists('Sa_payment_ref')) {
    // Référence de paiement envoyée à Monetbil : lie le paiement à la transaction locale.
    function Sa_payment_ref($id_transaction)
    {
        return 'speedex-transaction-'.$id_transaction;
    }
}
if (! function_exists('Sa_checkpay')) {
    function Sa_checkpay()
    {
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once __DIR__.'/../Http/Controllers/SuperAdmin/monetbil/monetbil.php';
        // Setup Monetbil arguments

        $params = Monetbil::getQueryParams();
        $service_secret = Monetbil::getServiceSecret();

        if (! Monetbil::checkSign($service_secret, $params)) {
            return null;
        }

        $transaction_id = Monetbil::getQuery('transaction_id');
        $status = Monetbil::getQuery('status');
        $phone = Monetbil::getQuery('phone');
        $user = Monetbil::getQuery('user');
        $item_ref = Monetbil::getQuery('item_ref');
        $payment_ref = Monetbil::getQuery('payment_ref');
        $first_name = Monetbil::getQuery('first_name');
        $last_name = Monetbil::getQuery('last_name');
        $email = Monetbil::getQuery('email');

        [$payment_status] = Monetbil::checkPayment($transaction_id);
        if ($payment_status == Monetbil::STATUS_SUCCESS) {
            $statut = 'success';
        } elseif ($payment_status == Monetbil::STATUS_CANCELLED) {
            $statut = 'cancelled';
        } else {
            $statut = 'failed';
        }

        return [
            'transaction_id' => $transaction_id,
            'phone' => $phone,
            'user' => $user,
            'item_ref' => $item_ref,
            'payment_ref' => $payment_ref,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'statut' => $statut,
        ];
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
}
if (! function_exists('Sa_phone')) {
    function Sa_phone($number)
    {
        if (strlen(trim($number)) > 9 || strlen(trim($number)) < 9) {
            $result = $number;
        } else {
            $result = '+237 '.$number[0].' '.$number[1].$number[2].' '.$number[3].$number[4].' '.$number[5].$number[6].' '.$number[7].$number[8];
        }

        return $result;
    }
}
if (! function_exists('Sa_site_home')) {
    function Sa_home()
    {
        return route('home');
    }
}
if (! function_exists('Sa_site_login')) {
    function Sa_site_login()
    {
        return route('login');
    }
}
if (! function_exists('Sa_phone2')) {
    function Sa_phone2($number)
    {
        if (strlen(trim($number)) > 9 || strlen(trim($number)) < 9) {
            $result = $number;
        } else {
            $result = $number[0].' '.$number[1].$number[2].' '.$number[3].$number[4].' '.$number[5].$number[6].' '.$number[7].$number[8];
        }

        return $result;
    }
}
if (! function_exists('Sa_Ladate')) {
    function Sa_Ladate($ladate)
    {
        $mois = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
        $ladate = new DateTime($ladate);

        return $ladate->format('d ').$mois[$ladate->format('m') - 1].$ladate->format(' Y');
    }
}
if (! function_exists('Sa_name')) {
    function Sa_name($chaine)
    {
        $result = '';
        $divise = explode(' ', $chaine);

        foreach ($divise as $key => $value) {

            $result .= ucfirst($value).' ';

        }

        return $result;
    }
}
if (! function_exists('Sa_montant')) {

    function Sa_montant($montant)
    {
        $montant = str_replace(' ', '', $montant);
        $montant = floatval($montant);

        return number_format($montant, 0, '.', ',');
    }
}
if (! function_exists('Sa_password')) {

    function Sa_password($password)
    {
        if (strlen($password) > 7) {
            $password = $password['0'].$password['1'].$password['2'].'********'.$password[strlen($password) - 2].$password[strlen($password) - 1];
        } else {
            $password = '*******';
        }

        return $password;
    }
}
// mettre le logo de l'application ici
if (! function_exists('Sa_logo')) {
    function Sa_logo()
    {
        return asset('app-assets/images/logo/fichier_2.svg');
    }
}
if (! function_exists('Sa_Ladate')) {
    function Sa_Ladate($ladate)
    {
        $mois = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
        $ladate = new DateTime($ladate);

        return $ladate->format('d ').$mois[$ladate->format('m') - 1].$ladate->format(' Y');
    }
}
if (! function_exists('Sa_Heure')) {
    function Sa_Heure($ladate)
    {
        $time = explode(' ', $ladate)[1];
        $table = explode(':', $time);

        return $table[0].'h'.$table[1].'min';
    }
}
if (! function_exists('Sa_Heure2')) {
    function Sa_Heure2($lheure)
    {
        $table = explode(':', $lheure);

        return $table[0].'h'.$table[1].'min';
    }
}
