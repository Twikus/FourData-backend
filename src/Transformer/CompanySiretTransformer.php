<?php

namespace App\Transformer;

use App\Helper\CalculateHelper;

class CompanySiretTransformer
{
    private CalculateHelper $calculateHelper;

    public function __construct(CalculateHelper $calculateHelper)
    {
        $this->calculateHelper = $calculateHelper;
    }

    public function transform(array $data = []): array
    {
        $etablissement = $data['etablissement'];

        $status = $etablissement['etat_administratif'] === 'A' ? true : false;

        $formattedData = [
            'name' => $etablissement['unite_legale']['denomination'] ?? 
                      $etablissement['unite_legale']['prenom_1'].' '
                      .$etablissement['unite_legale']['nom'].' ('
                      .$etablissement['denomination_usuelle'] .')',
            'address' => $etablissement['numero_voie'].' '
                         .$etablissement['type_voie'].' '
                         .$etablissement['libelle_voie'].', '
                         .$etablissement['code_postal'].' '
                         .$etablissement['libelle_commune'],
            'siren' => $etablissement['siren'],
            'siret' => $etablissement['siret'],
            'tva_number' => $this->calculateHelper->getTvaNumberBySiren($etablissement['siren']),
            'status' => $status
        ];

        return $formattedData;
    }
}