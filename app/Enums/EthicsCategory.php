<?php

namespace App\Enums;

enum EthicsCategory: string
{
    case CLINICAL = 'clinical';
    case NON_CLINICAL = 'non_clinical';

    public function label(): string
    {
        return match ($this) {
            self::CLINICAL => 'Etik Klinik atau Uji Coba Hewan',
            self::NON_CLINICAL => 'Etik Non Klinis',
        };
    }
}
