<?php

namespace Tests\Feature;

use App\Models\clients\Clients;
use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Client as AgencyAccount;
use App\Models\typeClient\TypeClient;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Référentiels que l'application attend (1 = Super Admin… 4 = Client ; 1 = entreprise, 2 = particulier).
        foreach (['Super Admin', 'Agent', 'Coursier', 'Client'] as $libelle) {
            $type = new TypeUtilisateur;
            $type->libelle = $libelle;
            $type->save();
        }
        foreach (['Entreprise', 'Particulier'] as $libelle) {
            $type = new TypeClient;
            $type->libelle = $libelle;
            $type->save();
        }

        // Agence active, sinon Check_Sa_Client_Error renvoie vers les pages d'erreur.
        $abonnement = new Abonnement;
        $abonnement->titre = 'Mensuel';
        $abonnement->type_periode = 'mois';
        $abonnement->statut = 1;
        $abonnement->save();
        $account = new AgencyAccount;
        $account->name = 'Agence';
        $account->id_abonnement = $abonnement->id;
        $account->date_fin = now()->addMonth();
        $account->statut = 1;
        $account->save();
    }

    private function form(array $overrides = []): array
    {
        return array_merge([
            'noms' => 'Mbarga',
            'prenoms' => 'Christelle',
            'telephone' => '677123456',
            'email' => 'christelle@example.com',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse',
        ], $overrides);
    }

    public function test_the_page_displays(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Créer votre compte')
            ->assertSee('name="telephone"', false);
    }

    public function test_the_login_page_links_to_registration(): void
    {
        $this->get('/login')->assertOk()->assertSee(route('register'));
    }

    public function test_a_visitor_creates_a_client_account_and_is_logged_in(): void
    {
        $this->post('/register', $this->form())->assertRedirect('/home');

        $user = User::where('email', 'christelle@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Client', $user->type_utilisateur->libelle);
        $this->assertSame('Mbarga Christelle', $user->noms);
        $this->assertSame('677123456', $user->telephone);
        $this->assertEquals(1, $user->statut);
        $this->assertTrue(Hash::check('motdepasse', $user->password));

        $client = Clients::findOrFail($user->id_client);
        $this->assertSame($user->id, (int) $client->id_utilisateur);
        $this->assertEquals(2, $client->type_client, 'client particulier');
        $this->assertSame('677123456', $client->telephone);

        // Il arrive sur son espace client.
        $this->get('/home')->assertRedirect(route('home.client'));
        $this->get('/')->assertOk();
    }

    public function test_the_role_cannot_be_chosen_by_the_visitor(): void
    {
        $this->post('/register', $this->form([
            'id_type_utilisateur' => 1,
            'type_utilisateur' => 'Super Admin',
            'statut' => 1,
        ]))->assertRedirect('/home');

        $this->assertSame('Client', User::where('email', 'christelle@example.com')->first()->type_utilisateur->libelle);
    }

    public function test_invalid_data_is_refused_with_french_messages(): void
    {
        $this->from('/register')->post('/register', $this->form([
            'noms' => '',
            'telephone' => '12345',
            'email' => 'pas-une-adresse',
            'password_confirmation' => 'autre',
        ]))
            ->assertRedirect('/register')
            ->assertSessionHasErrors(['noms', 'telephone', 'email', 'password']);

        $this->assertSame('Le numéro doit compter 9 chiffres et commencer par 6 ou 2.', session('errors')->first('telephone'));
        $this->assertSame(0, User::count());
        $this->assertSame(0, Clients::count());
        $this->assertGuest();
    }

    public function test_an_existing_email_is_refused(): void
    {
        $this->post('/register', $this->form());
        auth()->logout();

        $this->from('/register')->post('/register', $this->form(['telephone' => '699000000']))
            ->assertSessionHasErrors('email');
        $this->assertSame(1, User::count());
        $this->assertSame(1, Clients::count());
    }

    public function test_the_demo_domain_is_refused(): void
    {
        $this->from('/register')->post('/register', $this->form(['email' => 'client@demo.invalid']))
            ->assertSessionHasErrors('email');
        $this->assertSame(0, User::count());
    }

    public function test_a_logged_in_user_is_sent_away(): void
    {
        $this->post('/register', $this->form());

        $this->get('/register')->assertRedirect();
    }
}
