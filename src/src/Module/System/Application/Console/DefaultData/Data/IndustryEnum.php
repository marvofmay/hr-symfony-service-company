<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData\Data;

use App\Common\Domain\Interface\EnumInterface;

enum IndustryEnum: string implements EnumInterface
{
    case TECHNOLOGY       = 'technology';       // Technologia
    case HEALTHCARE       = 'healthcare';       // Opieka zdrowotna
    case FINANCE          = 'finance';          // Finanse
    case EDUCATION        = 'education';        // Edukacja
    case MANUFACTURING    = 'manufacturing';    // Produkcja
    case RETAIL           = 'retail';           // Handel detaliczny
    case ENERGY           = 'energy';           // Energetyka
    case TRANSPORTATION   = 'transportation';   // Transport
    case CONSTRUCTION     = 'construction';     // Budownictwo
    case HOSPITALITY      = 'hospitality';      // Hotelarstwo i gastronomia

    public function label(): string
    {
        return match ($this) {
            self::TECHNOLOGY       => 'Technologia',
            self::HEALTHCARE       => 'Opieka zdrowotna',
            self::FINANCE          => 'Finanse',
            self::EDUCATION        => 'Edukacja',
            self::MANUFACTURING    => 'Produkcja',
            self::RETAIL           => 'Handel detaliczny',
            self::ENERGY           => 'Energetyka',
            self::TRANSPORTATION   => 'Transport',
            self::CONSTRUCTION     => 'Budownictwo',
            self::HOSPITALITY      => 'Hotelarstwo i gastronomia',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function description(): string
    {
        return match ($this) {
            self::TECHNOLOGY       => 'Branża technologiczna i rozwój oprogramowania',
            self::HEALTHCARE       => 'Usługi medyczne i sektor opieki zdrowotnej',
            self::FINANCE          => 'Bankowość, inwestycje i usługi finansowe',
            self::EDUCATION        => 'Instytucje edukacyjne i usługi szkoleniowe',
            self::MANUFACTURING    => 'Produkcja przemysłowa i wytwórcza',
            self::RETAIL           => 'Handel detaliczny i sprzedaż konsumencka',
            self::ENERGY           => 'Produkcja energii, w tym źródła odnawialne',
            self::TRANSPORTATION   => 'Logistyka, spedycja i usługi transportowe',
            self::CONSTRUCTION     => 'Budownictwo i rozwój infrastruktury',
            self::HOSPITALITY      => 'Hotele, restauracje i branża rekreacyjna',
        };
    }
}