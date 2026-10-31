<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Suivi public des commandes (lien à jeton) et journal d'audit par entreprise.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('jeton_suivi', 40)->nullable()->unique();
        });

        DB::table('commandes')->whereNull('jeton_suivi')->orderBy('id')->chunkById(500, function ($commandes) {
            foreach ($commandes as $commande) {
                DB::table('commandes')->where('id', $commande->id)->update(['jeton_suivi' => Str::random(32)]);
            }
        });

        Schema::create('journal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entreprise_id')->index();
            $table->integer('id_user')->unsigned()->nullable();
            $table->string('action', 20);
            $table->string('sujet_table', 64);
            $table->unsignedBigInteger('sujet_id')->nullable();
            $table->json('changements')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->foreign('entreprise_id')->references('id')->on('entreprises')->onDelete('restrict')->onUpdate('restrict');
            $table->index(['entreprise_id', 'sujet_table', 'sujet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal');
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropUnique(['jeton_suivi']);
            $table->dropColumn('jeton_suivi');
        });
    }
};
