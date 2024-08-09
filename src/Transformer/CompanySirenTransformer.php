<?php

namespace App\Transformer;

use App\Helper\CalculateHelper;

class CompanySirenTransformer
{
    private CalculateHelper $calculateHelper;

    public function __construct(CalculateHelper $calculateHelper)
    {
        $this->calculateHelper = $calculateHelper;
    }

    public function transform(array $data = []): array
    {
        $index = 0;
        $status = true;

        $legal= $data['unite_legale'];

        // pour chaque établissement, on vérifie si l'état administratif est 'A', sinon on passe a l'établissement suivant, si aucun n'est 'A', on retourne status false et on prend le dernier établissement
        foreach ($legal['etablissements'] as $key => $etablissement) {
            if ($etablissement['etat_administratif'] === 'A') {
                $index = $key;
                break;
            }
            $status = false;
        }

        $formattedData = [
            'name' => $legal['denomination'] ?? 
                      $legal['prenom_1'].' '
                      .$legal['nom'].' ('
                      .$legal['etablissements'][$index]['denomination_usuelle'] .')',
            'address' => $legal['etablissements'][$index]['numero_voie'].' '
                         .$legal['etablissements'][$index]['type_voie'].' '
                         .$legal['etablissements'][$index]['libelle_voie'].', '
                         .$legal['etablissements'][$index]['code_postal'].' '
                         .$legal['etablissements'][$index]['libelle_commune'],
            'siren' => $legal['siren'],
            'siret' => $legal['etablissements'][$index]['siret'],
            'tva_number' => $this->calculateHelper->getTvaNumberBySiren($legal['siren']),
            'status' => $status
        ];

        return $formattedData;
    }
}