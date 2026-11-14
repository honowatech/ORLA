<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/_test-locale', fn () => App::getLocale().'|'.__('Annuler'));
    }

    public function test_french_is_the_default_language(): void
    {
        $this->get('/_test-locale', ['Accept-Language' => 'fr-FR'])
            ->assertOk()
            ->assertSee('fr|Annuler');
    }

    public function test_browser_preference_is_used_when_nothing_was_chosen(): void
    {
        $this->get('/_test-locale', ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertSee('en|Cancel');
    }

    public function test_switch_route_stores_the_language_and_redirects_back(): void
    {
        $this->from('/_test-locale')
            ->get(route('lang.switch', 'en'))
            ->assertRedirect('/_test-locale')
            ->assertSessionHas('locale', 'en')
            ->assertCookie('locale', 'en');

        $this->get('/_test-locale')->assertSee('en|Cancel');

        $this->get(route('lang.switch', 'fr'));
        $this->get('/_test-locale')->assertSee('fr|Annuler');
    }

    public function test_unknown_language_is_rejected(): void
    {
        $this->get('/lang/de')->assertNotFound();
    }

    public function test_query_parameter_overrides_the_session(): void
    {
        $this->withSession(['locale' => 'fr'])
            ->get('/_test-locale?lang=en')
            ->assertSee('en|Cancel');
    }

    public function test_english_translation_file_keeps_placeholders_and_tags(): void
    {
        $translations = json_decode(file_get_contents(lang_path('en.json')), true, flags: JSON_THROW_ON_ERROR);

        foreach ($translations as $key => $value) {
            preg_match_all('/:v\d+|:n\b/', $key, $expected);
            preg_match_all('/:v\d+|:n\b/', $value, $actual);
            sort($expected[0]);
            sort($actual[0]);
            $this->assertSame($expected[0], $actual[0], "Placeholders differ for: {$key}");

            preg_match_all('/<\/?[a-z]+/i', $key, $expectedTags);
            preg_match_all('/<\/?[a-z]+/i', $value, $actualTags);
            $this->assertSame($expectedTags[0], $actualTags[0], "HTML tags differ for: {$key}");
        }
    }
}
