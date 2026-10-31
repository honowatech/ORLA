<?php

namespace App\Validation;

use App\Tenancy\EntrepriseCourante;
use Illuminate\Validation\DatabasePresenceVerifier;

/**
 * Les règles « unique » et « exists » sur une table métier ne regardent que
 * les données de l'entreprise courante (et les référentiels communs).
 *
 * Sans cela, « unique:zone » refuserait une zone parce qu'une autre entreprise
 * en a une du même nom (et révélerait qu'elle existe), et « exists:coursiers »
 * accepterait le livreur d'une autre entreprise.
 */
class PresenceVerifierEntreprise extends DatabasePresenceVerifier
{
    protected ?string $colonne = null;

    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $this->colonne = $column;

        try {
            return parent::getCount($collection, $column, $value, $excludeId, $idColumn, $extra);
        } finally {
            $this->colonne = null;
        }
    }

    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $this->colonne = $column;

        try {
            return parent::getMultiCount($collection, $column, $values, $extra);
        } finally {
            $this->colonne = null;
        }
    }

    protected function table($table)
    {
        $query = parent::table($table);
        $courante = app(EntrepriseCourante::class);
        $id = $courante->id();

        if ($id === null || $courante->filtreDesactive()) {
            return $query;
        }

        $nom = str_contains($table, '.') ? substr($table, strrpos($table, '.') + 1) : $table;

        if (in_array($this->colonne, config("tenancy.unicite_globale.$nom", []), true)) {
            return $query;
        }

        if (in_array($nom, config('tenancy.tables', []), true)) {
            return $query->where('entreprise_id', $id);
        }

        if (in_array($nom, config('tenancy.tables_partagees', []), true)) {
            return $query->where(fn ($q) => $q->where('entreprise_id', $id)->orWhereNull('entreprise_id'));
        }

        return $query;
    }
}
