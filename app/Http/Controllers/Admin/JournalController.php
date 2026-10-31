<?php

namespace App\Http\Controllers\Admin;

use App\Models\Journal;
use App\Models\typeUtilisateur\TypeUtilisateur;
use Illuminate\Http\Request;

/**
 * Journal d'audit de l'entreprise : qui a fait quoi, et quand.
 */
class JournalController extends Controller
{
  public function index(Request $request)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        if($type == strtoupper('coursier')){
            return redirect()->route('home.coursier');
        }else if($type == strtoupper('client')){
            return redirect()->route('home.client');
        }
    $entrees = Journal::with('user')
        ->when($request->filled('table'), fn ($q) => $q->where('sujet_table', $request->input('table')))
        ->when($request->filled('action'), fn ($q) => $q->where('action', $request->input('action')))
        ->orderBy('id', 'desc')
        ->paginate(30)
        ->withQueryString();
    $libelles = Journal::LIBELLES;
    $actions = ['creation' => ['Création', 'success'], 'modification' => ['Modification', 'info'], 'suppression' => ['Suppression', 'danger'], 'export' => ['Export', 'warning']];
    return view('admin.pages.journal', compact('entrees', 'libelles', 'actions'));
  }
}
