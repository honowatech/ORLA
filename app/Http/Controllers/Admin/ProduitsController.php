<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produits\Produits;
use Illuminate\Http\Request;

class ProduitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $request = request();
        $produits = Produits::where('libelle', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('description', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('noms', 'like', '%'.$request->input('recherche').'%')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        session()->flash('search', $request->input('recherche'));

        return view('admin.pages.produits.index', compact('produits'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.pages.produits.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $noms = $request->input('noms');
        $libelle = $request->input('libelle');
        $description = $request->input('description');
        $request->validate([
            'noms' => 'bail|required|min:2|unique:produits',
            'libelle' => 'bail|required|min:2|unique:produits',
            'description' => 'bail|required|min:5',
        ]);
        $produits = new Produits;
        $produits->noms = $noms;
        $produits->libelle = $libelle;
        $produits->description = $description;
        $produits->statut = 1;
        $produits->save();
        $message = "Produit créée avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('produits.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $produit = Produits::findOrFail($id);

        return view('admin.pages.produits.info', compact('produit'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $produit = Produits::findOrFail($id);

        return view('admin.pages.produits.edit', compact('produit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

        $noms = $request->input('noms');
        $libelle = $request->input('libelle');
        $description = $request->input('description');
        $produit = Produits::findOrFail($id);
        $validated = $request->validate([
            'description' => 'bail|required|min:5',
        ]);
        if ($produit->details_commande->count() == 0 && $produit->stock->count() == 0) {
            $validated = $request->validate([
                'noms' => 'bail|required|min:2',
                'libelle' => 'bail|required|min:2',
            ]);
            if (Produits::where('libelle', [$libelle])->whereNotIn('id', [$id])->count() > 0) {
                $validated = $request->validate([
                    'libelle' => 'unique:produits',
                ]);
            }
            if (Produits::where('noms', [$noms])->whereNotIn('id', [$id])->count() > 0) {
                $validated = $request->validate([
                    'noms' => 'unique:produits',
                ]);
            }
        }

        $produit->noms = $noms;
        $produit->libelle = $libelle;
        $produit->description = $description;
        $produit->save();
        $message = "Produit modifié avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('produits.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $produit = Produits::findOrFail($id);
        if ($produit->details_commande->count() == 0 && $produit->stock->count() == 0) {
            $produit = Produits::findOrFail($id);
            $produit->delete();
            $message = "Produit supprimé <b class='text-success'> Succès.</b>";
            session()->flash('message', $message);

            return redirect()->route('produits.index');
        }
        $message = "<b class='text-danger'>Echec... </b> Ce Produit possède plusieurs connexions";
        session()->flash('message', $message);

        return redirect()->back();
    }
}
