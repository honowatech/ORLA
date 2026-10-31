<?php

namespace App\Http\Controllers\Admin;

use App\Models\Journal;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Tenancy\EntrepriseCourante;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Export complet des données de l'entreprise : un fichier CSV par table dans
 * une archive ZIP. Réservé à l'administrateur ; chaque export est journalisé.
 */
class ExportController extends Controller
{
  /** Colonnes jamais exportées. */
  public const MASQUEES = ['password', 'remember_token'];

  public function telecharger(EntrepriseCourante $courante)
  {
    $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
    if ($type != strtoupper(InscriptionEntreprise::ROLE_ADMINISTRATEUR)) {
        session()->flash('message', "<b class='text-danger'>Accès réservé</b> à l'administrateur de l'entreprise.");
        return redirect()->route('home');
    }
    $entreprise = $courante->entreprise();

    $chemin = tempnam(sys_get_temp_dir(), 'export');
    $zip = new ZipArchive;
    $zip->open($chemin, ZipArchive::OVERWRITE);
    $tables = array_merge(config('tenancy.tables'), config('tenancy.tables_partagees'));
    foreach ($tables as $table) {
        $zip->addFromString($table.'.csv', $this->csv($table, $entreprise->id));
    }
    $zip->close();

    $journal = new Journal;
    $journal->entreprise_id = $entreprise->id;
    $journal->id_user = auth()->id();
    $journal->action = 'export';
    $journal->sujet_table = 'entreprises';
    $journal->sujet_id = $entreprise->id;
    $journal->ip = request()->ip();
    $journal->save();

    $nom = 'export-'.Str::slug($entreprise->name).'-'.now()->format('Y-m-d').'.zip';
    return response()->download($chemin, $nom, ['Content-Type' => 'application/zip'])->deleteFileAfterSend();
  }

  protected function csv(string $table, int $entrepriseId): string
  {
    $flux = fopen('php://temp', 'r+');
    fwrite($flux, "\xEF\xBB\xBF"); // BOM : accents lisibles dans Excel
    $entete = false;
    DB::table($table)->where('entreprise_id', $entrepriseId)->orderBy('id')->chunk(500, function ($lignes) use ($flux, &$entete) {
        foreach ($lignes as $ligne) {
            $ligne = array_diff_key((array) $ligne, array_flip(self::MASQUEES));
            if (! $entete) {
                fputcsv($flux, array_keys($ligne), ';');
                $entete = true;
            }
            fputcsv($flux, array_map(fn ($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'".$v : $v, $ligne), ';');
        }
    });
    rewind($flux);
    $contenu = stream_get_contents($flux);
    fclose($flux);

    return $contenu;
  }
}
