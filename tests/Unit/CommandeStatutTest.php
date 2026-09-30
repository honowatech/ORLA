<?php

namespace Tests\Unit;

use App\Enums\CommandeStatut;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Règles de transition reprises à l'identique des anciens contrôleurs
 * (tableau $statuts_norm et indices de départ).
 */
class CommandeStatutTest extends TestCase
{
    /** @return array<string, array{string, list<string>}> */
    public static function transitions(): array
    {
        return [
            'attente' => ['attente', ['attribue', 'annulee', 'echoue']],
            'attribue' => ['attribue', ['encours', 'livre', 'annulee', 'echoue']],
            'encours' => ['encours', ['livre', 'annulee', 'echoue']],
            'livre' => ['livre', []],
            'annulee' => ['annulee', []],
            'echoue' => ['echoue', []],
        ];
    }

    /** @param list<string> $attendues */
    #[DataProvider('transitions')]
    public function test_transitions_possibles(string $depuis, array $attendues): void
    {
        $possibles = array_map(fn ($s) => $s->value, CommandeStatut::from($depuis)->transitionsPossibles());

        $this->assertSame($attendues, $possibles);
    }

    public function test_une_commande_ne_revient_jamais_en_arriere(): void
    {
        $this->assertFalse(CommandeStatut::EnCours->peutPasserA(CommandeStatut::Attente));
        $this->assertFalse(CommandeStatut::Attente->peutPasserA(CommandeStatut::Livre));
    }
}
