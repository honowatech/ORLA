<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Référentiels indispensables au fonctionnement de l'application.
 * Idempotent : peut être rejoué sur une base déjà peuplée.
 */
class ReferentielSeeder extends Seeder
{
    /**
     * Identifiants des types d'utilisateur, figés car le code les compare directement.
     * Le libellé « Super Admin » (administrateur de l'espace de travail) est conservé
     * tant que les contrôleurs comparent les libellés ; il deviendra « Administrateur ».
     */
    public const TYPES_UTILISATEUR = [
        1 => 'Super Admin',
        2 => 'Agent',
        3 => 'Coursier',
        4 => 'Client',
    ];

    public const TYPES_CLIENT = [
        1 => 'Entreprise',
        2 => 'Simple',
    ];

    public const VILLES = [
        ['libelle' => 'Douala', 'code' => 'Dla'],
        ['libelle' => 'Yaoundé', 'code' => 'Yde'],
    ];

    public function run(): void
    {
        $now = now();

        foreach (self::TYPES_UTILISATEUR as $id => $libelle) {
            DB::table('type_utilisateur')->updateOrInsert(
                ['id' => $id],
                ['libelle' => $libelle, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        foreach (self::TYPES_CLIENT as $id => $libelle) {
            DB::table('type_client')->updateOrInsert(
                ['id' => $id],
                ['libelle' => $libelle, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        foreach (self::VILLES as $ville) {
            DB::table('ville')->updateOrInsert(
                ['code' => $ville['code']],
                ['libelle' => $ville['libelle'], 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
