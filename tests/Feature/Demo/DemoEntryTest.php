<?php

namespace Tests\Feature\Demo;

use App\Actions\Commandes\ChangerStatutCommande;
use App\Demo\Demo;
use App\Demo\Sandbox;
use App\Demo\SandboxPool;
use App\Models\User;
use App\Models\users\Users;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class DemoEntryTest extends DemoTestCase
{
    private function enter(string $profile = 'routeur')
    {
        return $this->post('/demo', ['profile' => $profile]);
    }

    // §5.10 : désactivée, la démo répond 404.
    public function test_pages_answer_404_while_disabled(): void
    {
        config(['demo.enabled' => false]);
        $this->buildSandbox();

        $this->get('/demo')->assertNotFound();
        $this->post('/demo', ['profile' => 'routeur'])->assertNotFound();
        $this->assertNull(Sandbox::first()->claimed_at);
    }

    public function test_profile_page_lists_profiles_and_is_not_indexed(): void
    {
        $this->buildSandbox();

        $this->get('/demo')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('name="robots" content="noindex, nofollow"', false)
            ->assertSee('Agent routeur')
            ->assertSee('Coursier')
            ->assertDontSee('id_utilisateur');
    }

    // §3, §5.1 : POST + CSRF, profil en liste fermée, session régénérée.
    public function test_visitor_enters_as_the_chosen_profile_of_their_own_sandbox(): void
    {
        $sandbox = $this->buildSandbox();

        $response = $this->enter('routeur')->assertRedirect(route('home'));
        $sandbox->refresh();
        $this->assertNotNull($sandbox->claimed_at);
        $response->assertSessionHas(Demo::SESSION_KEY, $sandbox->claim_token);

        $this->carrySession($response)->get('/admin')
            ->assertOk()
            ->assertSee('données fictives')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->assertSame(0, Users::count(), 'la base principale reste vide');
    }

    public function test_an_unknown_profile_is_refused_and_nothing_is_claimed(): void
    {
        $this->buildSandbox();

        $this->enter('superadmin')->assertSessionHasErrors('profile');
        $this->post('/demo', ['profile' => 'routeur', 'user_id' => 1]); // ignoré : seul le slug compte
        $this->assertSame(1, Sandbox::whereNotNull('claimed_at')->count());
    }

    // §5.2 : un utilisateur réel connecté ne bascule pas en démo.
    public function test_a_logged_in_real_user_cannot_enter(): void
    {
        $this->buildSandbox();
        $real = $this->realUser();

        $this->actingAs($real)->enter()->assertRedirect();
        $this->assertNull(Sandbox::first()->claimed_at);
    }

    public function test_a_super_admin_session_cannot_enter(): void
    {
        $this->buildSandbox();

        $this->withSession(['SuperAdmin_infos' => ['connect' => true]])->enter()
            ->assertRedirect(route('demo.index'));
        $this->assertNull(Sandbox::first()->claimed_at);
    }

    // §4 : plafond de bacs ouverts, fraîcheur de la réserve.
    public function test_capacity_and_freshness_are_enforced(): void
    {
        $this->buildSandbox();

        config(['demo.max_active' => 0]);
        $this->enter()->assertRedirect(route('demo.index'))->assertSessionHas('demo_message');
        $this->assertNull(Sandbox::first()->claimed_at);

        config(['demo.max_active' => 100]);
        $this->travel(7)->hours(); // plus vieux que pool_max_age_hours (6)
        $this->enter()->assertRedirect(route('demo.index'));
        $this->assertNull(Sandbox::first()->claimed_at);
    }

    public function test_a_sandbox_built_another_day_is_not_served(): void
    {
        $sandbox = $this->buildSandbox();
        $sandbox->built_on = now()->subDay()->toDateString();
        $sandbox->save();

        $this->assertNull(app(SandboxPool::class)->claim());
    }

    // §4 : attribution atomique, deux visiteurs n'ont jamais le même bac.
    public function test_two_claims_never_get_the_same_sandbox(): void
    {
        $this->buildSandbox(1);
        $this->buildSandbox(2);
        $pool = app(SandboxPool::class);

        $a = $pool->claim();
        $b = $pool->claim();
        $c = $pool->claim();

        $this->assertNotNull($a);
        $this->assertNotNull($b);
        $this->assertNotSame($a->id, $b->id);
        $this->assertNull($c, 'réserve vide, pas de construction à la volée');

        // Le comparer-et-poser échoue sur un bac déjà pris.
        $this->assertSame(0, Sandbox::whereKey($a->id)->whereNull('claimed_at')->update(['claim_token' => 'x']));
    }

    public function test_an_empty_pool_builds_on_demand_when_allowed(): void
    {
        config(['demo.build_on_demand' => true]);

        $this->enter()->assertRedirect(route('home'));
        $this->assertSame(1, Sandbox::whereNotNull('claimed_at')->count());
    }

    // §5.1 : débit limité par adresse.
    public function test_entry_is_rate_limited(): void
    {
        config(['demo.build_on_demand' => true, 'demo.throttle.attempts' => 2]);
        RateLimiter::clear('demo');

        $this->enter()->assertRedirect(route('home'));
        $this->flushSession();
        $this->app['auth']->forgetGuards();
        $this->enter()->assertRedirect(route('home'));
        $this->flushSession();
        $this->app['auth']->forgetGuards();
        $this->enter()->assertStatus(429);
    }

    // §3.5, §5.6 : changer de profil dans le même bac.
    public function test_switching_profile_stays_in_the_same_sandbox(): void
    {
        $sandbox = $this->buildSandbox();
        $response = $this->enter('routeur');

        $switched = $this->carrySession($response)->post('/demo/profile', ['profile' => 'coursier'])
            ->assertRedirect(route('home'));
        $this->carrySession($switched)->get('/coursier')->assertOk()->assertSee('données fictives');

        $coursierEmail = $this->inSandbox($sandbox, fn () => Users::where('email', 'coursier@demo.invalid')->value('email'));
        $this->assertSame('coursier@demo.invalid', $coursierEmail);
    }

    public function test_switching_profile_is_refused_outside_a_demo(): void
    {
        $this->buildSandbox();

        $this->actingAs($this->realUser())->post('/demo/profile', ['profile' => 'coursier'])->assertForbidden();
        $this->actingAs($this->realUser('autre@example.com'))->post('/demo/end')->assertForbidden();
    }

    // §3.6, §5.9 : « Terminer » puis purge réelle.
    public function test_ending_logs_out_and_the_sandbox_is_then_deleted(): void
    {
        $sandbox = $this->buildSandbox();
        $response = $this->enter();

        $ended = $this->carrySession($response)->post('/demo/end')->assertRedirect(route('demo.index'));
        $this->assertNotNull($sandbox->refresh()->ended_at);

        $this->carrySession($ended)->get('/admin')->assertRedirect('/login');

        $this->assertSame(1, app(SandboxPool::class)->prune());
        $this->assertFileDoesNotExist($sandbox->path());
        $this->assertSame(0, Sandbox::count());
    }

    // §5.7 : expiration. Le point le plus sensible : l'identifiant d'utilisateur
    // de la session désigne une ligne du bac ; il ne doit jamais ouvrir le compte
    // réel de même identifiant dans la base principale.
    public function test_an_expired_session_never_falls_back_to_the_real_account_with_the_same_id(): void
    {
        $sandbox = $this->buildSandbox();
        $response = $this->enter('administrateur');

        $demoUserId = $this->inSandbox($sandbox, fn () => Users::where('email', 'administrateur@demo.invalid')->value('id'));
        $real = $this->realUser('vrai.admin@example.com', $demoUserId);
        $this->assertSame($demoUserId, $real->id);

        $sandbox->refresh();
        $sandbox->expires_at = now()->subMinute();
        $sandbox->save();

        $after = $this->carrySession($response)->get('/admin')->assertRedirect(route('demo.index'));
        $this->assertGuest();
        $this->assertNotNull($sandbox->refresh()->ended_at);

        $this->carrySession($after)->get('/admin')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_a_ready_sandbox_without_its_file_is_never_served_and_is_pruned(): void
    {
        $lost = $this->buildSandbox(1);
        File::delete($lost->path());
        $kept = $this->buildSandbox(2);

        $claimed = app(SandboxPool::class)->claim();
        $this->assertSame($kept->id, $claimed->id);
        $this->assertNull(Sandbox::find($lost->id));

        $other = $this->buildSandbox(3);
        File::delete($other->path());
        app(SandboxPool::class)->prune();
        $this->assertNull(Sandbox::find($other->id));
    }

    public function test_a_deleted_sandbox_file_ends_the_session(): void
    {
        $sandbox = $this->buildSandbox();
        $response = $this->enter();
        File::delete($sandbox->path());

        $this->carrySession($response)->get('/admin')->assertRedirect(route('demo.index'));
        $this->assertGuest();
    }

    public function test_disabling_the_demo_ends_open_sessions(): void
    {
        $this->buildSandbox();
        $response = $this->enter();
        config(['demo.enabled' => false]);

        $this->carrySession($response)->get('/admin')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_activity_extends_the_idle_deadline_without_passing_the_maximum(): void
    {
        $sandbox = $this->buildSandbox();
        $response = $this->enter();
        $first = $sandbox->refresh()->expires_at;

        $this->travel(30)->minutes();
        $this->carrySession($response)->get('/admin')->assertOk();
        $this->assertTrue($sandbox->refresh()->expires_at->greaterThan($first));
        $this->assertTrue($sandbox->expires_at->lessThanOrEqualTo($sandbox->ends_at));
    }

    // §5.7 : console plateforme, abonnement et Monetbil interdits en démo.
    public function test_platform_and_payment_routes_are_closed_in_a_demo(): void
    {
        $this->buildSandbox();
        $response = $this->enter('administrateur');

        foreach (['/superadmin/Sa-home', '/Sc-transaction', '/Sc-transaction.checkpay/1', '/password/reset'] as $uri) {
            $this->carrySession($response)->get($uri)->assertRedirect(route('home'));
        }
    }

    // §5.3 : aucun mot de passe utilisable.
    public function test_password_login_refuses_demo_addresses_even_with_the_right_password(): void
    {
        $this->realUser('administrateur@demo.invalid');

        $this->post('/login', ['email' => 'administrateur@demo.invalid', 'password' => 'secret-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_reset_link_for_a_demo_address_answers_like_an_unknown_address(): void
    {
        Notification::fake();
        $this->realUser('routeur@demo.invalid');

        $demo = $this->from('/password/reset')->post('/password/email', ['email' => 'routeur@demo.invalid']);
        $unknown = $this->from('/password/reset')->post('/password/email', ['email' => 'personne@example.com']);

        $this->assertSame(session('errors')?->first('email'), $unknown->getSession()->get('errors')?->first('email'));
        $demo->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    // §5.4 : aucun message sortant.
    public function test_mail_to_a_reserved_domain_or_from_a_sandbox_is_withheld(): void
    {
        config(['mail.default' => 'array']);
        $transport = app('mail.manager')->mailer('array')->getSymfonyTransport();

        Mail::raw('x', fn ($m) => $m->to('routeur@demo.invalid'));
        $this->assertCount(0, $transport->messages());

        $sandbox = $this->buildSandbox();
        Demo::enter($sandbox);
        Mail::raw('x', fn ($m) => $m->to('client@example.com'));
        Demo::leave();
        $this->assertCount(0, $transport->messages());

        Mail::raw('x', fn ($m) => $m->to('client@example.com'));
        $this->assertCount(1, $transport->messages());
    }

    // §5.9, §4 : purge des bacs périmés, des constructions interrompues et des orphelins.
    public function test_prune_removes_stale_and_orphan_sandboxes_but_keeps_fresh_ones(): void
    {
        $fresh = $this->buildSandbox(1);
        $stale = $this->buildSandbox(2);
        $stale->built_on = now()->subDay()->toDateString();
        $stale->save();

        $orphan = $this->dir.'/orphelin.sqlite';
        File::put($orphan, '');
        touch($orphan, now()->subHours(2)->getTimestamp());

        $this->assertSame(0, Artisan::call('demo:prune'));

        $this->assertFileExists($fresh->path());
        $this->assertFileDoesNotExist($stale->path());
        $this->assertFileDoesNotExist($orphan);
        $this->assertSame([$fresh->id], Sandbox::pluck('id')->all());
    }

    public function test_refill_builds_up_to_the_pool_size_and_nothing_when_disabled(): void
    {
        config(['demo.pool_size' => 2]);
        $this->assertSame(0, Artisan::call('demo:refill'));
        $this->assertSame(2, app(SandboxPool::class)->readyCount());

        Sandbox::query()->delete();
        config(['demo.enabled' => false]);
        Artisan::call('demo:refill');
        $this->assertSame(0, Sandbox::count());
    }

    public function test_refill_and_prune_are_scheduled(): void
    {
        $commands = collect(app(Schedule::class)->events())->map(fn ($e) => $e->command)->implode(' ');

        $this->assertStringContainsString('demo:refill', $commands);
        $this->assertStringContainsString('demo:prune', $commands);
    }

    // Action extraite : cycle de vie d'une commande.
    public function test_status_transitions(): void
    {
        $action = new ChangerStatutCommande;

        $this->assertSame(['attribue', 'annulee', 'echoue'], $action->transitionsPossibles('attente'));
        $this->assertSame(['encours', 'livre', 'annulee', 'echoue'], $action->transitionsPossibles('attribue'));
        $this->assertSame(['livre', 'annulee', 'echoue'], $action->transitionsPossibles('encours'));
        $this->assertSame([], $action->transitionsPossibles('livre'));
        $this->assertSame([], $action->transitionsPossibles('supprime'));
    }

    private function realUser(string $email = 'vrai@example.com', ?int $id = null): User
    {
        $type = \App\Models\typeUtilisateur\TypeUtilisateur::where('libelle', 'Super Admin')->first();
        if ($type === null) {
            $type = new \App\Models\typeUtilisateur\TypeUtilisateur;
            $type->libelle = 'Super Admin';
            $type->save();
        }
        $user = new User;
        if ($id !== null) {
            $user->id = $id;
        }
        $user->noms = 'Compte réel';
        $user->email = $email;
        $user->password = Hash::make('secret-password');
        $user->id_type_utilisateur = $type->id;
        $user->statut = 1;
        $user->save();

        return $user;
    }
}
