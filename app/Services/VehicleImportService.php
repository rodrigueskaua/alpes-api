<?php

namespace App\Services;

use App\Models\Vehicle;

class VehicleImportService
{
    /**
     * Cria ou atualiza um veículo a partir do array de dados da API.
     *
     * @param array $data Dados do veículo do JSON da API
     * @return Vehicle
     */
    public function import(array $data): Vehicle
    {
        return Vehicle::updateOrCreate(
            ['external_id' => $data['id']],
            [
                'type' => $data['type'] ?? null,
                'brand' => $data['brand'] ?? null,
                'model' => $data['model'] ?? null,
                'version' => $data['version'] ?? null,
                'year_model' => $data['year']['model'] ?? null,
                'year_build' => $data['year']['build'] ?? null,
                'optionals' => $data['optionals'] ?? [],
                'doors' => isset($data['doors']) ? (int) $data['doors'] : null,
                'board' => $data['board'] ?? null,
                'chassi' => $data['chassi'] ?? null,
                'transmission' => $data['transmission'] ?? null,
                'km' => isset($data['km']) ? (int) $data['km'] : null,
                'description' => $data['description'] ?? null,
                'sold' => isset($data['sold']) ? (bool) $data['sold'] : false,
                'category' => $data['category'] ?? null,
                'url_car' => $data['url_car'] ?? null,
                'old_price' => isset($data['old_price']) ? (float) $data['old_price'] : null,
                'price' => isset($data['price']) ? (float) $data['price'] : null,
                'color' => $data['color'] ?? null,
                'fuel' => $data['fuel'] ?? null,
                'photos' => $data['fotos'] ?? [],
            ]
        );
    }
}
