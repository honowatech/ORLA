<?php

namespace App\Demo\Generator;

use App\Demo\DemoClock;
use App\Models\agents\Agents;
use App\Models\boutiques\Boutiques;
use App\Models\clients\Clients;
use App\Models\coursiers\Coursiers;
use App\Models\details_zone\Details_zone;
use App\Models\montant_livraison\Montant_livraison;
use App\Models\point_relais\Point_relais;
use App\Models\produits\Produits;
use App\Models\quartier\Quartier;
use App\Models\stock\Stock;
use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Client as AgencyAccount;
use App\Models\typeClient\TypeClient;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\users\Users;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Mise en place de l'agence démo, datée deux semaines avant l'historique :
 * référentiels, zones et tarifs, utilisateurs (un par profil), coursiers,
 * boutiques et clients. Garde les références dont le simulateur a besoin.
 */
class DemoAgency
{
    public Randomizer $rng;

    public CarbonInterface $historyStart;

    /** @var array<string, Users> slug de profil => utilisateur */
    public array $profileUsers = [];

    public Users $agentUser;

    /** @var array<int, array{coursier: Coursiers, user: Users, zones: int[]}> */
    public array $coursiers = [];

    /** @var array<int, array{client: Clients, boutique: Boutiques, produits: array<int, array{0: Produits, 1: int}>, user: ?Users}> */
    public array $shops = [];

    /** @var Clients[] */
    public array $individuals = [];

    /** @var Quartier[] */
    public array $quartiers = [];

    public int $villeId;

    /** Actions refusées pendant le rejeu : le contrôle final l'exige nul. */
    public int $refused = 0;

    /** Mot de passe aléatoire, jamais affiché, haché une fois pour tous. */
    private string $passwordHash;

    private int $phoneSerial = 0;

    public static function setUp(int $seed, CarbonInterface $now, int $historyDays): self
    {
        $agency = new self;
        $agency->rng = new Randomizer(new Mt19937($seed));
        $agency->passwordHash = Hash::make(Str::random(64));

        $tz = config('demo.timezone');
        $agency->historyStart = $now->copy()->setTimezone($tz)->startOfDay()->subDays($historyDays);
        DemoClock::at($agency->historyStart->copy()->subDays(14)->setTime(8, 0)->utc());

        $agency->references();
        $agency->geography();
        $agency->staff();
        $agency->customers();
        $agency->account($now);

        return $agency;
    }

    private function references(): void
    {
        // L'ordre fixe les identifiants que le code attend (1 = Super Admin… 4 = Client).
        foreach (['Super Admin', 'Agent', 'Coursier', 'Client'] as $libelle) {
            $type = new TypeUtilisateur;
            $type->libelle = $libelle;
            $type->save();
        }
        // HomeController : type_client 1 = entreprise, 2 = particulier.
        foreach (['Entreprise', 'Particulier'] as $libelle) {
            $type = new TypeClient;
            $type->libelle = $libelle;
            $type->save();
        }
    }

    private function geography(): void
    {
        $ville = new Ville;
        $ville->libelle = 'Douala';
        $ville->code = 'Dla';
        $ville->save();
        $this->villeId = $ville->id;

        $zones = [];
        foreach (DemoCatalogue::ZONES as $libelle => $quartiers) {
            $zone = new Zone;
            $zone->libelle = $libelle;
            $zone->id_ville = $ville->id;
            $zone->statut = 1;
            $zone->save();
            $zones[$libelle] = $zone;

            foreach ($quartiers as $nom) {
                $quartier = new Quartier;
                $quartier->libelle = $nom;
                $quartier->id_zone = $zone->id;
                $quartier->id_ville = $ville->id;
                $quartier->save();
                $this->quartiers[$nom] = $quartier;
            }
        }

        // Un tarif par paire de zones (la recherche est symétrique).
        $names = array_keys($zones);
        foreach ($names as $i => $a) {
            foreach (array_slice($names, $i + 1) as $b) {
                $tarif = new Montant_livraison;
                $tarif->id_zone_colis = $zones[$a]->id;
                $tarif->id_zone_livraison = $zones[$b]->id;
                $distance = abs(DemoCatalogue::ZONE_RANK[$a] - DemoCatalogue::ZONE_RANK[$b]);
                $tarif->montant = 1500 + 500 * $distance + ($a === 'Bonaberi' || $b === 'Bonaberi' ? 500 : 0);
                $tarif->save();
            }
        }
    }

    private function staff(): void
    {
        $admin = $this->user('administrateur', 'Super Admin', 'Direction Speedex');
        $this->profileUsers['administrateur'] = $admin;

        [$first, $last] = $this->name();
        $agent = new Agents;
        $agent->noms = $last;
        $agent->prenoms = $first;
        $agent->telephone = $this->phone();
        $agent->statut = 1;
        $agent->save();
        $this->agentUser = $this->user('routeur', 'Agent', "$first $last", ['id_agent' => $agent->id]);
        $agent->id_utilisateur = $this->agentUser->id;
        $agent->save();
        $this->profileUsers['routeur'] = $this->agentUser;

        // Huit coursiers, chacun sur une ou deux zones ; chaque zone a au moins un coursier.
        $zoneIds = Zone::orderBy('id')->pluck('id')->all();
        for ($i = 0; $i < 8; $i++) {
            [$first, $last] = $this->name();
            $coursier = new Coursiers;
            $coursier->noms = $last;
            $coursier->prenoms = $first;
            $coursier->telephone = $this->phone();
            $coursier->statut = 1;
            $coursier->save();

            $slug = $i === 0 ? 'coursier' : 'coursier-'.($i + 1);
            $user = $this->user($slug, 'Coursier', "$first $last", ['id_coursier' => $coursier->id]);
            $coursier->id_utilisateur = $user->id;
            $coursier->save();

            $zones = [$zoneIds[$i % count($zoneIds)]];
            if ($this->rng->getInt(0, 1) === 1) {
                $zones[] = $zoneIds[($i + 1) % count($zoneIds)];
            }
            foreach ($zones as $zoneId) {
                $detail = new Details_zone;
                $detail->id_coursier = $coursier->id;
                $detail->id_zone = $zoneId;
                $detail->save();
            }
            $this->coursiers[] = ['coursier' => $coursier, 'user' => $user, 'zones' => $zones];
            if ($i === 0) {
                $this->profileUsers['coursier'] = $user;
            }
        }

        // Un point relais en centre-ville.
        $relais = new Point_relais;
        $relais->libelle = 'Relais Akwa';
        $relais->id_coursier = $this->coursiers[0]['coursier']->id;
        $relais->id_quartier = $this->quartiers['Akwa']->id;
        $relais->quartier = 'Akwa';
        $relais->statut = 1;
        $relais->save();
    }

    private function customers(): void
    {
        foreach (DemoCatalogue::SHOPS as $i => [$libelle, $quartier, $produits]) {
            [$first, $last] = $this->name();
            $client = new Clients;
            $client->noms = $libelle;
            $client->Prenoms = "($first $last)";
            $client->type_client = 1;
            $client->telephone = $this->phone();
            $client->statut = 1;
            $client->save();

            $boutique = new Boutiques;
            $boutique->libelle = $libelle;
            $boutique->id_client = $client->id;
            $boutique->id_quartier = $this->quartiers[$quartier]->id;
            $boutique->quartier = $quartier;
            $boutique->statut = 1;
            $boutique->save();

            $items = [];
            foreach ($produits as [$nom, $prix]) {
                $produit = new Produits;
                $produit->noms = $nom;
                $produit->libelle = $nom;
                $produit->description = $libelle;
                $produit->statut = 1;
                $produit->save();

                $stock = new Stock;
                $stock->id_produit = $produit->id;
                $stock->id_boutique = $boutique->id;
                $stock->libelle = $nom;
                $stock->statut = 1;
                $stock->type_gestion = 'ajout';
                $stock->qute_changement = $this->rng->getInt(20, 60);
                $stock->Quantite_en_stock = $stock->qute_changement;
                $stock->save();

                $items[] = [$produit, $prix];
            }

            $user = null;
            if ($i === 0) {
                $user = $this->user('client', 'Client', "$libelle", ['id_client' => $client->id]);
                $client->id_utilisateur = $user->id;
                $client->save();
                $this->profileUsers['client'] = $user;
            }
            $this->shops[] = ['client' => $client, 'boutique' => $boutique, 'produits' => $items, 'user' => $user];
        }

        for ($i = 0; $i < 45; $i++) {
            [$first, $last] = $this->name();
            $client = new Clients;
            $client->noms = $last;
            $client->Prenoms = $first;
            $client->type_client = 2;
            $client->telephone = $this->phone();
            $client->statut = 1;
            $client->save();
            $this->individuals[] = $client;
        }
    }

    /** Fiche d'abonnement de l'agence, propre au bac : Check_Sa_Client_Error la lit. */
    private function account(CarbonInterface $now): void
    {
        $abonnement = new Abonnement;
        $abonnement->titre = 'Mensuel';
        $abonnement->accumulateur = 1;
        $abonnement->type_periode = 'mois';
        $abonnement->montant = 25000;
        $abonnement->periode_grace = 3;
        $abonnement->statut = 1;
        $abonnement->save();

        $account = new AgencyAccount;
        $account->name = DemoCatalogue::AGENCY;
        $account->adresse = 'Akwa, Douala';
        $account->telephone = $this->phone();
        $account->id_abonnement = $abonnement->id;
        $account->date_fin = $now->copy()->addDays(30);
        $account->date_dernier_paiement = $this->historyStart;
        $account->statut = 1;
        $account->save();
    }

    private function user(string $slug, string $type, string $noms, array $links = []): Users
    {
        $user = new Users;
        $user->noms = $noms;
        $user->email = $slug.'@'.config('demo.email_domain');
        $user->password = $this->passwordHash;
        $user->telephone = $this->phone();
        $user->id_type_utilisateur = TypeUtilisateur::where('libelle', $type)->value('id');
        $user->id_ville = $this->villeId;
        $user->statut = 1;
        foreach ($links as $column => $value) {
            $user->{$column} = $value;
        }
        $user->save();

        return $user;
    }

    /** @return array{0: string, 1: string} */
    public function name(): array
    {
        return [$this->pick(DemoCatalogue::FIRST_NAMES), $this->pick(DemoCatalogue::LAST_NAMES)];
    }

    /** Numéro fictif à 9 chiffres (6XXXXXXXX), unique dans le bac. */
    public function phone(): string
    {
        $this->phoneSerial++;

        return '6'.$this->rng->getInt(5, 9).str_pad((string) (($this->phoneSerial * 7919) % 1000000), 6, '0', STR_PAD_LEFT).$this->rng->getInt(0, 9);
    }

    public function pick(array $items): mixed
    {
        return $items[$this->rng->getInt(0, count($items) - 1)];
    }

    /** Vrai avec la probabilité donnée. */
    public function chance(float $p): bool
    {
        return $this->rng->getFloat(0, 1) < $p;
    }
}
