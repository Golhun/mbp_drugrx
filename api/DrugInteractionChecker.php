<?php

namespace MBPDrugRX;

use GuzzleHttp\Client;

class DrugInteractionChecker
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.fda.gov/drug/']);
    }

    public function getDrugInfo(string $drugName): array
    {
        try {
            $response = $this->client->get('event.json', [
                'query' => [
                    'search' => "patient.drug.medicinalproduct:$drugName",
                    'limit' => 5
                ]
            ]);
            return json_decode($response->getBody(), true)['results'] ?? [];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
