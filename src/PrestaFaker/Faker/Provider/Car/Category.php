<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Faker\Provider\Car;

use Faker\Provider\Base;
use PrestaFaker\Faker\Provider\ILevel;

class Category
    extends Base
    implements ILevel
{
    static protected $firsts = array(
        'Freinage',
        'Direction / Suspension / Train',
        'Echappement',
        'Embrayage et Boîte de vitesse',
        'Pièces moteur',
        'Filtration',
        'Démarrage et Charge',
        'Pièces Thermiques et Climatisation',
        'Visibilité',
        'Accessoires et Entretien',
        'Pièces Habitacle',
    );

    static protected $seconds = array(
        'Freinage' => array(
            'Disques',
            'Plaquettes',
            'Freins à tambours',
            'Etrier de frein',
            'Flexible de frein',
            'Assistance au freinage',
            'Capteurs et câbles de freinage',
            'Hydraulique',
            'Plaquettes',
        ),
        'Direction / Suspension / Train' => array(
            'Amortisseurs',
            'Triangle de suspension',
            'Rotules /Direction',
            'Roulement de roue',
            'Cardan',
            'Autres pièces Suspension',
            'Autres pièces Transmission',
            'Butées',
            'Direction',
            'Kits de réparation et d\'assemblage',
            'Moyeux et Roulements',
            'Ressorts et Soufflets',
            'Rotules / Direction',
            'Rotules / Suspension',
            'Roue',
            'Sphères',
            'Suspension d\'Essieux',
            'Transmission',
        ),
        'Echappement' => array(
            'Silencieux arrière',
            'Tuyau d\'échappement',
            'Catalyseur',
            'Silencieux et Tubes',
            'Capteurs d\'échappement',
            'Accessoires de montage',
            'Autres pièces d\'échappement',
            'Catalyseurs et Filtres à particules',
        ),
        'Embrayage et Boîte de vitesse' => array(
            'Kit d\'embrayage',
            'Kit d\'embrayage + Volant moteur',
            'Volant moteur',
            'Emetteur d\'embrayage',
            'Câble d\'embrayage',
            'Accessoires de boîte de vitesse',
            'Autres pièces d\'Embrayage',
            'Embrayage et Volant-moteur',
        ),
        'Pièces moteur' => array(
            'Huile / Accessoires vidange',
            'Kit de distribution',
            'Bougie de préchauffage',
            'Turbo',
            'Pompes',
            'Bougies et Pièces d\'allumage',
            'Bougies et Pièces de préchauffage',
            'Capteurs et câbles moteur',
            'Courroies et Distribution',
            'Injection carburation',
            'Lubrification',
            'Moteur et Culasse',
            'Soupapes du moteur',
            'Support moteur',
        ),
        'Filtration' => array(
            'Filtre à carburant',
            'Filtre à air',
            'Filtre à huile',
            'Filtre d\'habitacle',
            'Autres Pièces de Filtration',
            'Joint d\'étanchéité, boîtier de filtre à huile',
            'Joint d\'étanchéité, filtre de carburant',
            'Kit de filtres hydrauliques, boîte automatique',
            'Soupape, filtre à carburant',
            'Soupape, filtre de diesel',
            'Support, boîtier de filtre à air',
        ),
        'Démarrage et Charge' => array(
            'Batterie',
            'Alternateur',
            'Démarreur',
            'Alterno-démarreur',
            'Batterie de démarrage',
        ),
        'Pièces Thermiques et Climatisation' => array(
            'Radiateur du moteur',
            'Condenseur de climatisation',
            'Radiateur de chauffage',
            'Compresseur de climatisation',
            'Refroidisseur d\'Air de Suralimentation (Intercooler)',
            'Capteurs et Sondes thermiques',
            'Chauffage et Ventilation',
            'Climatisation',
            'Refroidissement',
        ),
        'Visibilité' => array(
            'Balai d\'essuie-glace',
            'Projecteur principal',
            'Ampoule de grand phare',
            'Rétroviseur extérieur',
            'Essuyage des phares',
            'Ampoules',
            'Essuie-glaces',
            'Optiques et Phares',
            'Rétroviseurs',
        ),
        'Pièces Habitacle' => array(
            'Vérin de hayon',
            'Mécanisme lève-vitre',
            'Serrure-Fermeture',
            'Electricité',
            'Commandes et Pédalier',
            'Autres pièces d\'habitacle',
            'Avertisseurs sonores',
            'Joint d\'habitacle',
            'Lève-vitres',
            'Verins',
        ),
        'Accessoires et Entretien' => array(
            'Chaines et chaussettes neige',
            'Dépannage / Réparation',
            'Liquide de fonctionnement',
            'Nettoyage et Entretien interieur',
            'Guide d\'entretien auto',
            'Additifs entretien moteur',
            'Chaînes neige',
            'Nettoyage Carrosserie et Extérieur',
            'Produits d\'entretien',
            'Vis et Boulons',
        ),
    );



    public function getAll()
    {
        return static::$seconds;
    }

    public function getMaxLevel()
    {
        return 2;
    }


    public function getFirst()
    {
        return static::randomElement(static::$firsts);
    }


    public function getSecond()
    {
        return static::randomElement(static::$seconds[$this->getFirst()]);
    }

    public function mainCategory()
    {
        return $this->getFirst();
    }

    public function subCategory()
    {
        return $this->getSecond();
    }
}