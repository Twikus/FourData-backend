<?php

namespace App\Helper;

class CalculateHelper
{
    public function getTvaNumberBySiren(int $siren): string
    {
        $tvaNumber = (12 + 3 * ($siren % 97)) % 97;

        return 'FR'.$tvaNumber.$siren;
    }
}