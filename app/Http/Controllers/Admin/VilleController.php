<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ville\Ville;
use App\Models\Zone\Zone;
use Illuminate\Http\Request;

class VilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request = request();
        $villes = Ville::where('libelle', 'like', '%'.$request->input('recherche').'%')
            ->paginate(20);
        session()->flash('search', $request->input('recherche'));

        return view('admin.pages.villes.index', compact('villes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.villes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $libelle = $request->input('libelle');
        $code = $request->input('code');
        $request->validate([
            'libelle' => 'bail|required|min:4|unique:ville',
            'code' => 'bail|required|min:2|max:4|unique:ville',
        ]);
        $ville = new Ville;
        $ville->libelle = $libelle;
        $ville->code = $code;
        $ville->save();
        $message = "Ville enregistrée avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('ville.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($libelle)
    {
        $ville = Ville::where('libelle', $libelle)->latest('id')->firstOrFail();

        return view('admin.pages.villes.edit', compact('ville'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $libelle = $request->input('libelle');
        $code = $request->input('code');
        $validated = $request->validate([
            'code' => 'bail|required|min:2|max:4',
            'libelle' => 'bail|required|min:4',
        ]);
        if (Ville::where('libelle', $libelle)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'libelle' => 'unique:ville',
            ]);
        }
        if (Ville::where('code', $code)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'code' => 'unique:ville',
            ]);
        }
        $ville = Ville::findOrFail($id);
        $ville->libelle = $libelle;
        $ville->code = $code;
        $ville->save();
        $message = "Ville mise à jour avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('ville.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Request $request, $id)
    {
        if (Zone::where('id_ville', [$id])->count() > 0) {
            $message = "<b class='text-danger'>Echec... </b> Cette Ville est liée à ".Zone::where('id_ville', [$id])->count().' Zone';
            session()->flash('message', $message);

            return redirect()->back();
        }
        $ville = Ville::findOrFail($id);
        $ville->delete();
        $message = "Ville supprimée <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('ville.index');
    }
}
