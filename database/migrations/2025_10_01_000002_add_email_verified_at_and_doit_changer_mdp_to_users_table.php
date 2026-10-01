<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - email_verified_at : le modèle User le castait déjà en datetime sans que la colonne existe.
 * - doit_changer_mdp  : indicateur positionné lors d'une réinitialisation par un administrateur
 *                       (le mot de passe temporaire doit être changé à la prochaine connexion).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'doit_changer_mdp')) {
                $table->boolean('doit_changer_mdp')->default(false)->after('statut');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $colonnes = array_filter(
                ['email_verified_at', 'doit_changer_mdp'],
                fn (string $colonne) => Schema::hasColumn('users', $colonne)
            );
            if ($colonnes !== []) {
                $table->dropColumn(array_values($colonnes));
            }
        });
    }
};
