<?php

namespace Tests\Unit;

use App\Http\Requests\VehicleRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function requisicao_valida_de_veiculo_deve_passar()
    {
        $dados = [
            'external_id' => 12345,
            'type'        => 'Carro',
            'brand'       => 'Toyota',
            'model'       => 'Corolla',
            'version'     => 'XLE',
            'year_model'  => '2023',
            'year_build'  => '2022',
            'optionals'   => ['Airbag', 'GPS'],
            'doors'       => 4,
            'board'       => 'ABC1234',
            'chassi'      => '9BWZZZ377VT004251',
            'transmission'=> 'Automático',
            'km'          => 15000,
            'description' => 'Veículo em ótimo estado',
            'sold'        => false,
            'category'    => 'Sedan',
            'url_car'     => 'https://example.com/car.jpg',
            'old_price'   => 120000,
            'price'       => 100000,
            'color'       => 'Vermelho',
            'fuel'        => 'Gasolina',
            'photos'      => ['foto1.jpg', 'foto2.jpg'],
        ];

        $regras = (new VehicleRequest())->rules();
        $validador = Validator::make($dados, $regras);

        $this->assertTrue($validador->passes(), 'A validação deve passar com todos os campos corretos');
    }

    /** @test */
    public function requisicao_sem_campos_obrigatorios_deve_falhar()
    {
        $dados = [
            'price' => 100000, // faltando external_id
        ];

        $regras = (new VehicleRequest())->rules();
        $validador = Validator::make($dados, $regras);

        $this->assertFalse($validador->passes(), 'A validação deve falhar se campos obrigatórios estiverem faltando');
        $this->assertArrayHasKey('external_id', $validador->errors()->messages(), 'Erro esperado para external_id');
    }

    /** @test */
    public function tipos_invalidos_ou_tamanhos_errados_deve_falhar()
    {
        $dados = [
            'external_id' => 'ABC',      // deveria ser integer
            'price'       => -100,        // min 0
            'year_model'  => '20',        // tamanho 4
            'year_build'  => '202',       // tamanho 4
            'sold'        => 'sim',       // boolean
            'doors'       => 'quatro',    // integer
            'optionals'   => 'Airbag',    // array
            'photos'      => 'foto.jpg',  // array
        ];

        $regras = (new VehicleRequest())->rules();
        $validador = Validator::make($dados, $regras);

        $this->assertFalse($validador->passes(), 'A validação deve falhar com tipos ou tamanhos inválidos');

        $erros = $validador->errors()->messages();

        $this->assertArrayHasKey('external_id', $erros);
        $this->assertArrayHasKey('price', $erros);
        $this->assertArrayHasKey('year_model', $erros);
        $this->assertArrayHasKey('year_build', $erros);
        $this->assertArrayHasKey('sold', $erros);
        $this->assertArrayHasKey('doors', $erros);
        $this->assertArrayHasKey('optionals', $erros);
        $this->assertArrayHasKey('photos', $erros);
    }
}
