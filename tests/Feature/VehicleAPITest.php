<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleAPITest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_listar_veiculos_com_paginacao()
    {
        Vehicle::factory()->count(30)->create();

        $response = $this->getJson('/api/v1/vehicles?page=1');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'current_page',
                         'data' => [
                             '*' => [
                                 'id',
                                 'external_id',
                                 'type',
                                 'brand',
                                 'model',
                                 'price',
                                 'created_at',
                                 'updated_at',
                             ]
                         ],
                         'last_page',
                         'per_page',
                         'total',
                     ],
                     'message'
                 ]);

        $this->assertEquals(1, $response->json('data.current_page'));
        $this->assertCount(20, $response->json('data.data')); // se paginate(20)
    }

    /** @test */
    public function pode_mostrar_um_veiculo_por_external_id()
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->external_id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'external_id' => $vehicle->external_id,
                         'brand' => $vehicle->brand,
                         'model' => $vehicle->model,
                     ],
                     'message' => 'Veículo carregado.'
                 ]);
    }

    /** @test */
    public function retorna_404_quando_veiculo_nao_encontrado()
    {
        $response = $this->getJson('/api/v1/vehicles/999999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'data' => null,
                     'message' => 'Veículo não encontrado.'
                 ]);
    }

    /** @test */
    public function pode_criar_um_veiculo()
    {
        $dados = [
            'external_id' => 12345,
            'price' => 50000,
            'brand' => 'Toyota',
            'model' => 'Corolla',
        ];

        $response = $this->postJson('/api/v1/vehicles', $dados);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'external_id' => 12345,
                         'brand' => 'Toyota',
                         'model' => 'Corolla',
                     ],
                     'message' => 'Veículo criado.'
                 ]);

        $this->assertDatabaseHas('vehicles', ['external_id' => 12345]);
    }

    /** @test */
    public function pode_atualizar_um_veiculo()
    {
        $vehicle = Vehicle::factory()->create([
            'external_id' => 12345,
            'brand'       => 'Toyota',
            'model'       => 'Corolla',
            'price'       => 50000,
        ]);

        $dados = [
            'price' => 45000,
        ];

        $response = $this->putJson("/api/v1/vehicles/{$vehicle->id}", $dados);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'price' => 45000,
                     ],
                     'message' => 'Veículo atualizado com sucesso.'
                 ]);

        $this->assertDatabaseHas('vehicles', ['external_id' => 12345, 'price' => 45000]);
    }

    /** @test */
    public function pode_deletar_um_veiculo()
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->deleteJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => null,
                     'message' => 'Veículo deletado com sucesso.'
                 ]);

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }
}
