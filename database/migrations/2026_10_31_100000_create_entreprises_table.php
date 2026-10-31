<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Les « clients » du Super Admin deviennent les entreprises (tenants) de la
 * plateforme.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('super_admin_client', 'entreprises');

        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->after('name');
            $table->string('email', 255)->nullable()->after('slug');
            $table->string('logo', 255)->nullable();
            $table->string('couleur_principale', 20)->nullable();
            $table->string('devise', 10)->default('XAF');
            $table->string('fuseau_horaire', 64)->default('Africa/Douala');
            $table->datetime('essai_jusqu_au')->nullable();
            $table->integer('id_proprietaire')->unsigned()->nullable();
        });

        foreach (DB::table('entreprises')->orderBy('id')->get() as $entreprise) {
            $base = Str::slug($entreprise->name ?? '') ?: 'entreprise';
            $slug = $base;
            for ($i = 2; DB::table('entreprises')->where('slug', $slug)->exists(); $i++) {
                $slug = $base.'-'.$i;
            }
            DB::table('entreprises')->where('id', $entreprise->id)->update(['slug' => $slug]);
        }

        Schema::table('entreprises', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'email', 'logo', 'couleur_principale', 'devise', 'fuseau_horaire', 'essai_jusqu_au', 'id_proprietaire']);
        });

        Schema::rename('entreprises', 'super_admin_client');
    }
};
