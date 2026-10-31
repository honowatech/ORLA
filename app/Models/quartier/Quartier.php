<?php

namespace App\Models\Quartier;

use Illuminate\Database\Eloquent\Model;

class Quartier extends Model 
{
    use \App\Models\Concerns\AppartientAEntreprise;

    /** Quartier technique représentant les locaux de l'entreprise. */
    public const LIBELLE_SIEGE = 'Siège';

    /**
     * Quartier « Siège » de l'entreprise courante, créé au besoin dans sa
     * première ville (ou dans une ville « À préciser » si elle n'en a aucune).
     */
    public static function siege(): self
    {
        $siege = static::where('libelle', self::LIBELLE_SIEGE)->first();
        if ($siege !== null) {
            return $siege;
        }

        $ville = \App\Models\ville\Ville::orderByRaw('entreprise_id is null')->orderBy('id')->first();
        if ($ville === null) {
            $ville = new \App\Models\ville\Ville;
            $ville->libelle = 'À préciser';
            $ville->code = 'NA';
            $ville->save();
        }

        $siege = new static;
        $siege->libelle = self::LIBELLE_SIEGE;
        $siege->id_ville = $ville->id;
        $siege->save();

        return $siege;
    }


    protected $table = 'quartier';
    public $timestamps = true;
    protected $visible = array('id_zone','libelle','id_ville');

    public function zone()
    {
        return $this->belongsTo('App\Models\zone\Zone', 'id_zone');
    }
    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }
    public function boutiques()
    {
        return $this->hasMany('App\Models\boutiques\Boutiques', 'id_quartier');
    }

    public function point_relais()
    {
        return $this->hasMany('App\Models\point_relais\Point_relais', 'id_quartier');
    }

    public function commandes_pointdepart()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_quartier_colis');
    }

    public function commandes_pointarrivee()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_quartier_livraison');
    }

}