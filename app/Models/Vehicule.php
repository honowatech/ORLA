<?php

namespace App\Models;

use App\Models\Parc\Vehicule_attribution;
use App\Models\Parc\Vehicule_carburant;
use App\Models\Parc\Vehicule_document;
use App\Models\Parc\Vehicule_incident;
use App\Models\Parc\Vehicule_kilometrage;
use App\Models\Parc\Vehicule_maintenance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;
    protected $table = 'vehicule';
    public $timestamps = true;
    protected $visible = array('id','immatriculation','couleur','id_type','id_coursier','marque','modele','description','statut','id_ville','etat','kilometrage','numero_chassis','annee','carburant','capacite_reservoir','date_acquisition','prix_acquisition','photo');
    protected $fillable = array('immatriculation','couleur','id_type','id_coursier','marque','modele','description','statut','id_ville','etat','kilometrage','numero_chassis','annee','carburant','capacite_reservoir','date_acquisition','prix_acquisition','photo');

    /** États opérationnels possibles d'un véhicule (clé => libellé). */
    public const ETATS = [
        'disponible'     => 'Disponible',
        'en_service'     => 'En service',
        'en_maintenance' => 'En maintenance',
        'immobilise'     => 'Immobilisé',
        'hors_service'   => 'Hors service',
    ];

    /** Classe CSS (badge) par état. */
    public const ETATS_BADGE = [
        'disponible'     => 'badge-success',
        'en_service'     => 'badge-info',
        'en_maintenance' => 'badge-warning',
        'immobilise'     => 'badge-danger',
        'hors_service'   => 'badge-dark',
    ];

    /** Types de carburant. */
    public const CARBURANTS = [
        'essence'    => 'Essence',
        'diesel'     => 'Diesel',
        'electrique' => 'Électrique',
        'hybride'    => 'Hybride',
        'gpl'        => 'GPL',
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Type_vehicule', 'id_type');
    }
    public function coursier()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }
    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }

    public function documents()
    {
        return $this->hasMany(Vehicule_document::class, 'id_vehicule');
    }
    public function maintenances()
    {
        return $this->hasMany(Vehicule_maintenance::class, 'id_vehicule');
    }
    public function carburants()
    {
        return $this->hasMany(Vehicule_carburant::class, 'id_vehicule');
    }
    public function incidents()
    {
        return $this->hasMany(Vehicule_incident::class, 'id_vehicule');
    }
    public function attributions()
    {
        return $this->hasMany(Vehicule_attribution::class, 'id_vehicule');
    }
    public function releves_kilometrage()
    {
        return $this->hasMany(Vehicule_kilometrage::class, 'id_vehicule');
    }

    // ------------------------------------------------------------------
    // Libellés
    // ------------------------------------------------------------------

    public function getNomCompletAttribute()
    {
        return trim($this->marque.' '.$this->modele).' ('.$this->immatriculation.')';
    }

    public function getEtatLibelleAttribute()
    {
        return self::ETATS[$this->etat] ?? $this->etat;
    }

    public function getEtatBadgeAttribute()
    {
        return self::ETATS_BADGE[$this->etat] ?? 'badge-secondary';
    }

    public function getCarburantLibelleAttribute()
    {
        return self::CARBURANTS[$this->carburant] ?? $this->carburant;
    }

    // ------------------------------------------------------------------
    // Kilométrage
    // ------------------------------------------------------------------

    /**
     * Enregistre un relevé de kilométrage et met à jour le compteur du
     * véhicule si le relevé est plus élevé que la valeur connue.
     */
    public function enregistrerKilometrage($kilometrage, $source = 'manuel', $date = null, $description = null, $id_saver = null)
    {
        if ($kilometrage === null || $kilometrage === '') {
            return null;
        }
        $releve = new Vehicule_kilometrage;
        $releve->id_vehicule = $this->id;
        $releve->date = $date ?: Carbon::now()->toDateString();
        $releve->kilometrage = (int) $kilometrage;
        $releve->source = $source;
        $releve->description = $description;
        $releve->id_saver = $id_saver;
        $releve->save();

        if ((int) $kilometrage > (int) $this->kilometrage) {
            $this->kilometrage = (int) $kilometrage;
            $this->save();
        }
        return $releve;
    }

    // ------------------------------------------------------------------
    // Indicateurs
    // ------------------------------------------------------------------

    /** Documents expirés ou expirant dans les $jours prochains jours. */
    public function documentsAEcheance($jours = 30)
    {
        $limite = Carbon::now()->addDays($jours)->toDateString();
        return $this->documents->filter(function ($doc) use ($limite) {
            return $doc->date_expiration != null && $doc->date_expiration <= $limite;
        });
    }

    /** Entretiens planifiés dont la date ou le kilométrage est atteint/proche. */
    public function maintenancesDues($jours = 15, $km_marge = 500)
    {
        $limite = Carbon::now()->addDays($jours)->toDateString();
        $km = (int) $this->kilometrage;
        return $this->maintenances->filter(function ($m) use ($limite, $km, $km_marge) {
            if (!in_array($m->statut, ['planifie', 'en_cours'])) {
                return false;
            }
            $date_due = $m->date_prevue != null && $m->date_prevue <= $limite;
            $km_due = $m->prochain_kilometrage != null && $km + $km_marge >= $m->prochain_kilometrage;
            return $date_due || $km_due;
        });
    }

    public function coutCarburant($debut = null, $fin = null)
    {
        $q = $this->carburants();
        if ($debut) $q->where('date', '>=', $debut);
        if ($fin) $q->where('date', '<=', $fin);
        return (float) $q->sum('montant');
    }

    public function coutMaintenance($debut = null, $fin = null)
    {
        $q = $this->maintenances()->where('statut', 'realise');
        if ($debut) $q->where('date_realisation', '>=', $debut);
        if ($fin) $q->where('date_realisation', '<=', $fin);
        return (float) $q->sum('cout');
    }

    public function coutIncidents($debut = null, $fin = null)
    {
        $q = $this->incidents();
        if ($debut) $q->where('date', '>=', $debut);
        if ($fin) $q->where('date', '<=', $fin.' 23:59:59');
        return (float) $q->sum('cout');
    }

    public function coutDocuments($debut = null, $fin = null)
    {
        $q = $this->documents();
        if ($debut) $q->where('date_debut', '>=', $debut);
        if ($fin) $q->where('date_debut', '<=', $fin);
        return (float) $q->sum('montant');
    }

    /** Coût total de possession sur la période (hors acquisition). */
    public function coutTotal($debut = null, $fin = null)
    {
        return $this->coutCarburant($debut, $fin)
            + $this->coutMaintenance($debut, $fin)
            + $this->coutIncidents($debut, $fin)
            + $this->coutDocuments($debut, $fin);
    }

    /**
     * Consommation moyenne en L/100km calculée à partir des pleins complets
     * successifs disposant d'un kilométrage.
     */
    public function consommationMoyenne()
    {
        $pleins = $this->carburants()
            ->where('plein_complet', 1)
            ->whereNotNull('kilometrage')
            ->orderBy('kilometrage')
            ->get();
        if ($pleins->count() < 2) {
            return null;
        }
        $distance = $pleins->last()->kilometrage - $pleins->first()->kilometrage;
        if ($distance <= 0) {
            return null;
        }
        // Le premier plein remplit le réservoir : seuls les suivants mesurent la consommation.
        $litres = $pleins->slice(1)->sum('litres');
        return round(($litres / $distance) * 100, 2);
    }

    /** Coût au kilomètre (carburant + entretien) sur l'ensemble des relevés connus. */
    public function coutParKm()
    {
        $releves = $this->releves_kilometrage()->orderBy('kilometrage')->get();
        if ($releves->count() < 2) {
            return null;
        }
        $distance = $releves->last()->kilometrage - $releves->first()->kilometrage;
        if ($distance <= 0) {
            return null;
        }
        return round($this->coutTotal() / $distance, 2);
    }

    /** Attribution en cours (coursier actuel). */
    public function attributionEnCours()
    {
        return $this->attributions()->whereNull('date_fin')->orderBy('date_debut', 'desc')->first();
    }
}
