<?php

if (! function_exists('textBreack')) {
    function textBreack($chaine, $nombre)
    {
        $result = '';

        if (strlen($chaine) <= $nombre) {
            return true;
        }

        for ($i = 0; $i < strlen($chaine); $i++) {
            if ($i > 0 && $i % $nombre === 0) {
                if (ctype_space($chaine[$i - 1])) {
                    $result .= "\n";
                } else {
                    $result .= "-\n";
                }
            }
            $result .= $chaine[$i];
        }

        return $result;
    }
}
if (! function_exists('formatMontant')) {

    function formatMontant($montant)
    {
        $montant = str_replace(' ', '', $montant);
        $montant = floatval($montant);

        return number_format($montant, 0, ',', ' ');
    }
}
if (! function_exists('Ladate')) {
    function Ladate($ladate)
    {
        $mois = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
        $ladate = new DateTime($ladate);

        return $ladate->format('d ').$mois[$ladate->format('m') - 1].$ladate->format(' Y');
    }
}
if (! function_exists('Heure')) {
    function Heure($ladate)
    {
        $time = explode(' ', $ladate)[1];
        $table = explode(':', $time);

        return $table[0].'h'.$table[1].'min';
    }
}
if (! function_exists('phone')) {
    function phone($number)
    {
        if (strlen(trim($number)) > 9 || strlen(trim($number)) > 9) {
            $result = $number;
        } else {
            $result = '+237 '.$number[0].' '.$number[1].$number[2].' '.$number[3].$number[4].' '.$number[5].$number[6].' '.$number[7].$number[8];
        }

        return $result;
    }
}
if (! function_exists('phone2')) {
    function phone2($number)
    {
        if (strlen(trim($number)) > 9 || strlen(trim($number)) > 9) {
            $result = $number;
        } else {
            $result = $number[0].' '.$number[1].$number[2].' '.$number[3].$number[4].' '.$number[5].$number[6].' '.$number[7].$number[8];
        }

        return $result;
    }
}
if (! function_exists('cutText')) {
    function cutText($text, $number)
    {
        $result = '';
        if (strlen($text) <= $number) {
            $result = $text;
        } else {
            for ($i = 0; $i < $number - 1; $i++) {
                $result .= $text[$i];
            }
            $result .= '...';
        }

        return $result;
    }
}
if (! function_exists('name')) {
    function name($chaine)
    {
        $result = '';
        $divise = explode(' ', $chaine);

        foreach ($divise as $key => $value) {

            $result .= ucfirst($value).' ';

        }

        return $result;
    }
}
if (! function_exists('TempsEcoule')) {

    function TempsEcoule($date)
    {
        $dateSoumise = new DateTime($date);
        $dateActuelle = new DateTime;
        $intervalle = $dateSoumise->diff($dateActuelle);
        $annees = $intervalle->y;
        $mois = $intervalle->m;
        $jours = $intervalle->days;
        $heures = $intervalle->h;
        $minutes = $intervalle->i;
        $secondes = $intervalle->s;

        if ($minutes == 0 && $heures == 0 && $jours == 0 && $mois == 0 && $annees == 0) {
            return "{$secondes} secondes";
        } elseif ($minutes != 0 && $heures == 0 && $jours == 0 && $mois == 0 && $annees == 0) {
            return "{$minutes} minutes";
        } elseif ($heures != 0 && $jours == 0 && $mois == 0 && $annees == 0) {
            return "{$heures} heures";
        } elseif ($jours != 0 && $mois == 0 && $annees == 0) {
            return "{$jours} jours";
        } elseif ($mois != 0 && $annees == 0) {
            return "{$mois} mois";
        } else {
            return "{$annees} années";
        }
    }
}
