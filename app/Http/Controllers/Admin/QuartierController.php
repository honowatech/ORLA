<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quartier\Quartier;
use App\Models\Ville\Ville;
use Illuminate\Http\Request;

class QuartierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (Quartier::where(strtoupper('libelle'), strtoupper('Speedex'))->count() == 0) {
            $quartier = new Quartier;
            $quartier->libelle = 'Speedex';
            $quartier->id_ville = 1;
            $quartier->save();
        }
        $request = request();
        $quartiers = Quartier::where('libelle', 'like', '%'.$request->input('recherche').'%')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        session()->flash('search', $request->input('recherche'));

        return view('admin.pages.quartiers.index', compact('quartiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Quartier::where(strtoupper('libelle'), strtoupper('Speedex'))->count() == 0) {
            $quartier = new Quartier;
            $quartier->libelle = 'Speedex';
            $quartier->id_ville = 1;
            $quartier->save();
        }
        $villes = Ville::orderBy('updated_at', 'desc')->get();

        return view('admin.pages.quartiers.create', compact('villes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id_ville = $request->input('id_ville');
        $libelle = $request->input('libelle');
        $request->validate([
            'libelle' => 'bail|required|min:3',
            'id_ville' => 'bail|required|numeric',
        ]);

        if (Quartier::where('libelle', $libelle)->where('id_ville', [$id_ville])->count() > 0) {
            $validated = $request->validate([
                'libelle' => 'unique:quartier',
            ]);
        }
        $quartier = new Quartier;
        $quartier->libelle = $libelle;
        $quartier->id_ville = $id_ville;
        $quartier->save();
        $message = "Quartier créée avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('quartier.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $quartier = Quartier::findOrFail($id);

        return view('admin.pages.quartiers.edit', compact('quartier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $libelle = $request->input('libelle');
        $validated = $request->validate([
            'libelle' => 'bail|required|min:3',
        ]);
        if (Quartier::where('libelle', [$libelle])->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'libelle' => 'unique:quartier',
            ]);
        }
        $quartier = Quartier::findOrFail($id);
        $quartier->libelle = $libelle;
        $quartier->save();
        $message = "Quartier mise à jour avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('quartier.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Request $request, $id)
    {
        $quartier = Quartier::with(['ville', 'zone', 'boutiques', 'point_relais', 'commandes_pointdepart', 'commandes_pointarrivee'])->findOrFail($id);
        if ($quartier->boutiques->count() > 0 || $quartier->point_relais->count() > 0 || $quartier->commandes_pointdepart->count() > 0 || $quartier->commandes_pointarrivee->count() > 0) {
            $message = "<b class='text-danger'>Echec... </b> Ce Quartier possède plusieurs connexions";
            session()->flash('message', $message);

            return redirect()->back();
        }
        $quartier = Quartier::findOrFail($id);
        $quartier->delete();
        $message = "Quartier supprimée <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('quartier.index');
    }
}
