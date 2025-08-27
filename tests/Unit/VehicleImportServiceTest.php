<?php

namespace Tests\Unit;

use App\Services\VehicleImportService;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleImportServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function importacao_de_veiculo_processa_dados_corretamente()
    {
        $service = new VehicleImportService();

        $dados = [
            'external_id' => 12345,
            'brand'       => 'Toyota',
            'model'       => 'Corolla',
            'price'       => 50000,
        ];

        $resultado = $service->import($dados);

        $this->assertTrue($resultado['success'], 'O serviço deve retornar sucesso');
        $this->assertInstanceOf(Vehicle::class, $resultado['vehicle'], 'O resultado deve conter o modelo Vehicle');
        $this->assertEquals(12345, $resultado['vehicle']->external_id, 'O external_id do veículo deve ser igual ao informado');

        $this->assertDatabaseHas('vehicles', [
            'external_id' => 12345,
            'brand'       => 'Toyota',
            'model'       => 'Corolla',
            'price'       => 50000,
        ]);
    }

    /** @test */
    public function importacao_nao_cria_duplicados_com_mesmo_external_id()
    {
        Vehicle::factory()->create([
            'external_id' => 12345,
            'price' => 50000,
        ]);

        $service = new VehicleImportService();

        $dados = [
            'external_id' => 12345,
            'brand'       => 'Toyota',
            'model'       => 'Corolla',
            'price'       => 50000,
        ];

        $resultado = $service->import($dados);

        $this->assertDatabaseCount('vehicles', 1);
    }
}
