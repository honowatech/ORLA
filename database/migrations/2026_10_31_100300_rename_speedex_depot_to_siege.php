<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Le quartier et les boutiques techniques « Speedex » (les locaux de
 * l'entreprise) deviennent « Siège », valable pour toutes les entreprises.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('quartier')->whereRaw('upper(libelle) = ?', ['SPEEDEX'])->update(['libelle' => 'Siège']);
        DB::table('boutiques')->whereRaw('upper(libelle) = ?', ['SPEEDEX'])->update([
            'libelle' => 'Siège',
            'quartier' => "Cette boutique est au siège de l'entreprise",
        ]);
    }

    public function down(): void
    {
        DB::table('quartier')->where('libelle', 'Siège')->update(['libelle' => 'Speedex']);
        DB::table('boutiques')->where('libelle', 'Siège')->update([
            'libelle' => 'Speedex',
            'quartier' => 'Cette boutique est chez speedex',
        ]);
    }
};
