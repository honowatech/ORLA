<?php

namespace App\Demo\Generator;

/**
 * Données fixes de l'agence démo : Douala, XAF, noms du marché camerounais.
 * Tout est fictif ; aucune enseigne réelle.
 */
class DemoCatalogue
{
    public const AGENCY = 'Speedex Douala (démo)';

    /** Zone => quartiers. */
    public const ZONES = [
        'Centre' => ['Akwa', 'Bonanjo', 'Bali', 'Akwa Nord'],
        'Bonapriso - Deido' => ['Bonapriso', 'Deido', 'Bonadibong', 'Bonamouti'],
        'Bonamoussadi - Makepe' => ['Bonamoussadi', 'Makepe', 'Kotto', 'Logpom'],
        'Ndokoti - Bepanda' => ['Ndokoti', 'Bepanda', 'New Bell', 'Nkololoun'],
        'Est' => ['Logbessou', 'PK 14', 'Yassa', 'Japoma'],
        'Bonaberi' => ['Bonaberi', 'Bojongo', 'Ngwele'],
    ];

    /** Rang géographique approximatif, pour graduer le tarif entre deux zones. */
    public const ZONE_RANK = [
        'Centre' => 0, 'Bonapriso - Deido' => 1, 'Ndokoti - Bepanda' => 2,
        'Bonamoussadi - Makepe' => 3, 'Est' => 4, 'Bonaberi' => 3,
    ];

    public const FIRST_NAMES = [
        'Jean', 'Paul', 'Marie', 'Brice', 'Armel', 'Christelle', 'Sandrine', 'Hervé', 'Yannick',
        'Ulrich', 'Rodrigue', 'Carine', 'Nadège', 'Larissa', 'Franck', 'Patrick', 'Aïcha', 'Ibrahim',
        'Moussa', 'Fatimatou', 'Joël', 'Serge', 'Estelle', 'Gaëlle', 'Boris', 'Landry', 'Rostand',
        'Steve', 'Vanessa', 'Murielle', 'Cédric', 'Danielle', 'Arnaud', 'Flore', 'Raoul', 'Inès',
    ];

    public const LAST_NAMES = [
        'Mbarga', 'Ngono', 'Essomba', 'Tchoupo', 'Kamga', 'Fotso', 'Nana', 'Ndjock', 'Ekotto',
        'Mballa', 'Abena', 'Onana', 'Tchatchoua', 'Djoko', 'Nkeng', 'Wandji', 'Bello', 'Hamadou',
        'Kouam', 'Talla', 'Mvondo', 'Atangana', 'Ngassa', 'Nguema', 'Simo', 'Takam', 'Ewane',
        'Moukoko', 'Ebongue', 'Ndoumbe', 'Priso', 'Manga', 'Epee', 'Noah',
    ];

    /**
     * Boutiques clientes : libellé, quartier, produits [nom, prix XAF].
     * La première est celle du profil « Client entreprise ».
     */
    public const SHOPS = [
        ['Ebène Mode', 'Akwa', [['Robe en pagne', 18000], ['Chemise wax', 12000], ['Sac à main', 15000], ['Sandales cuir', 9500]]],
        ['Saveurs du Wouri', 'Bonapriso', [['Panier de fruits', 7500], ['Jus de bissap (6)', 4500], ['Plateau traiteur', 25000]]],
        ['TechZone Bonamoussadi', 'Bonamoussadi', [['Écouteurs sans fil', 14000], ['Chargeur rapide', 6000], ['Coque de téléphone', 3500], ['Power bank', 11000]]],
        ['Belle Peau Cosmétiques', 'Deido', [['Beurre de karité', 3000], ['Coffret soin', 16500], ['Savon noir (3)', 4500]]],
        ['Librairie des Écoles', 'Ndokoti', [['Lot de cahiers', 5000], ['Calculatrice', 8500], ['Dictionnaire', 12500]]],
        ['Pharma Plus Logbessou', 'Logbessou', [['Kit premiers soins', 9000], ['Tensiomètre', 22000]]],
    ];

    public const PARCEL_DESCRIPTIONS = [
        'Colis fragile, à manipuler avec soin', 'Documents administratifs sous enveloppe',
        'Vêtements emballés, livraison en main propre', 'Repas chaud, livrer rapidement',
        'Pièces détachées, carton scellé', 'Cadeau d\'anniversaire, ne pas ouvrir',
        'Commande de boutique, paiement à la livraison', 'Médicaments, remettre au destinataire',
    ];

    public const LANDMARKS = [
        'face pharmacie', 'derrière la station', 'carrefour principal', 'près du marché',
        'immeuble bleu, 2e étage', 'entrée de la chefferie', 'à côté de l\'école publique',
        'portail noir', 'après le rond-point', 'en face de la boulangerie',
    ];
}
