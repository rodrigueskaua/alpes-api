<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Alpes One - API de Veículos",
 * description="API para gerenciamento de veículos, com todos os detalhes e exemplos de dados."
 * )
 * @OA\Tag(
 * name="Vehicles",
 * description="Operações relacionadas a veículos"
 * )
 * @OA\Schema(
 * schema="Vehicle",
 * type="object",
 * title="Vehicle Model",
 * description="Representa um veículo com todos os seus detalhes.",
 * @OA\Property(property="id", type="integer", description="ID interno do veículo no banco de dados.", example=41),
 * @OA\Property(property="external_id", type="integer", description="ID externo do veículo (para referência).", example=125306),
 * @OA\Property(property="type", type="string", description="Tipo do veículo (ex: carro, moto).", example="carro"),
 * @OA\Property(property="brand", type="string", description="Marca do veículo.", example="Hyundai"),
 * @OA\Property(property="model", type="string", description="Modelo do veículo.", example="CRETA"),
 * @OA\Property(property="version", type="string", description="Versão específica do modelo.", example="CRETA 16A ACTION"),
 * @OA\Property(property="year_model", type="string", description="Ano do modelo.", example="2025"),
 * @OA\Property(property="year_build", type="string", description="Ano de fabricação.", example="2025"),
 * @OA\Property(property="optionals", type="array", @OA\Items(type="string"), description="Lista de opcionais do veículo."),
 * @OA\Property(property="doors", type="integer", description="Número de portas.", example=5),
 * @OA\Property(property="board", type="string", description="Placa do veículo.", example="JCU2I93"),
 * @OA\Property(property="chassi", type="string", description="Número do chassi.", example="123456789"),
 * @OA\Property(property="transmission", type="string", description="Tipo de transmissão.", example="Automática"),
 * @OA\Property(property="km", type="integer", description="Quilometragem do veículo.", example=24208),
 * @OA\Property(property="description", type="string", description="Descrição e observações sobre o veículo.", example="*revisado *procedência *garantia"),
 * @OA\Property(property="sold", type="boolean", description="Indica se o veículo foi vendido.", example=false),
 * @OA\Property(property="category", type="string", description="Categoria do veículo.", example="SUV"),
 * @OA\Property(property="url_car", type="string", description="URL amigável para o veículo.", example="hyundai-creta-2025-automatica-125306"),
 * @OA\Property(property="old_price", type="string", format="decimal", nullable=true, description="Preço antigo (para promoções).", example=null),
 * @OA\Property(property="price", type="string", format="decimal", description="Preço atual do veículo.", example="115900.00"),
 * @OA\Property(property="color", type="string", description="Cor do veículo.", example="Branco"),
 * @OA\Property(property="fuel", type="string", description="Tipo de combustível.", example="Flex"),
 * @OA\Property(property="photos", type="array", @OA\Items(type="string", format="url"), description="Lista de URLs das fotos do veículo."),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Data de criação do registro."),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Data da última atualização do registro.")
 * )
 * @OA\Schema(
 * schema="VehicleRequest",
 * type="object",
 * title="Vehicle Request Body",
 * description="Corpo da requisição para criar ou atualizar um veículo. Apenas 'external_id' e 'price' são obrigatórios.",
 * required={"external_id", "price"},
 * @OA\Property(property="external_id", type="integer", description="ID externo do veículo (obrigatório).", example=125307),
 * @OA\Property(property="price", type="string", format="decimal", description="Preço do veículo (obrigatório).", example="150000.00"),
 * @OA\Property(property="type", type="string", description="Tipo do veículo (opcional).", example="carro"),
 * @OA\Property(property="brand", type="string", description="Marca do veículo (opcional).", example="Fiat"),
 * @OA\Property(property="model", type="string", description="Modelo do veículo (opcional).", example="Toro"),
 * @OA\Property(property="version", type="string", description="Versão do modelo (opcional).", example="1.3 Turbo 270"),
 * @OA\Property(property="year_model", type="string", description="Ano do modelo (opcional).", example="2025"),
 * @OA\Property(property="year_build", type="string", description="Ano de fabricação (opcional).", example="2025"),
 * @OA\Property(property="km", type="integer", description="Quilometragem (opcional).", example=0),
 * @OA\Property(property="color", type="string", description="Cor do veículo (opcional).", example="Preto"),
 * @OA\Property(property="doors", type="integer", description="Número de portas (opcional).", example=4),
 * @OA\Property(property="board", type="string", description="Placa do veículo (opcional).", example="XYZ5678"),
 * @OA\Property(property="chassi", type="string", description="Número do chassi (opcional)."),
 * @OA\Property(property="transmission", type="string", description="Tipo de transmissão (opcional).", example="Automática"),
 * @OA\Property(property="description", type="string", description="Descrição (opcional)."),
 * @OA\Property(property="category", type="string", description="Categoria (opcional).", example="Picape"),
 * @OA\Property(property="fuel", type="string", description="Tipo de combustível (opcional).", example="Flex"),
 * @OA\Property(property="optionals", type="array", @OA\Items(type="string"), description="Lista de opcionais (opcional)."),
 * @OA\Property(property="photos", type="array", @OA\Items(type="string", format="url"), description="Lista de URLs das fotos (opcional).")
 * )
 */
class VehicleController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/vehicles",
     * operationId="getVehiclesList",
     * tags={"Vehicles"},
     * summary="Listar veículos",
     * description="Retorna uma lista paginada de veículos.",
     * @OA\Response(
     * response=200,
     * description="Operação bem-sucedida",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="current_page", type="integer"),
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Vehicle")),
     * @OA\Property(property="first_page_url", type="string"),
     * @OA\Property(property="from", type="integer"),
     * @OA\Property(property="last_page", type="integer"),
     * @OA\Property(property="last_page_url", type="string"),
     * @OA\Property(property="next_page_url", type="string", nullable=true),
     * @OA\Property(property="path", type="string"),
     * @OA\Property(property="per_page", type="integer"),
     * @OA\Property(property="prev_page_url", type="string", nullable=true),
     * @OA\Property(property="to", type="integer"),
     * @OA\Property(property="total", type="integer"),
     * ),
     * @OA\Property(property="message", type="string", example="Lista de veículos carregada.")
     * )
     * )
     * )
     */
    public function index(): JsonResponse
    {
        $vehicles = Vehicle::orderBy('external_id', 'asc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'message' => 'Lista de veículos carregada.'
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/v1/vehicles/{id}",
     * operationId="getVehicleById",
     * tags={"Vehicles"},
     * summary="Mostrar um veículo",
     * description="Retorna os dados de um único veículo pelo seu ID externo.",
     * @OA\Parameter(
     * name="id",
     * description="ID externo do veículo",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Operação bem-sucedida",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", ref="#/components/schemas/Vehicle"),
     * @OA\Property(property="message", type="string", example="Veículo carregado.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Veículo não encontrado"
     * )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $vehicle = Vehicle::where('external_id', $id)->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Veículo não encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vehicle,
            'message' => 'Veículo carregado.'
        ]);
    }
    /**
     * @OA\Post(
     * path="/api/v1/vehicles",
     * operationId="storeVehicle",
     * tags={"Vehicles"},
     * summary="Criar um novo veículo",
     * description="Cria um novo registro de veículo.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/VehicleRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Veículo criado com sucesso",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", ref="#/components/schemas/Vehicle"),
     * @OA\Property(property="message", type="string", example="Veículo criado.")
     * )
     * )
     * )
     */
    public function store(VehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $vehicle,
            'message' => 'Veículo criado.'
        ], 201);
    }

    /**
     * @OA\Put(
     * path="/api/v1/vehicles/{id}",
     * operationId="updateVehicle",
     * tags={"Vehicles"},
     * summary="Atualizar um veículo existente",
     * description="Atualiza os dados de um veículo existente.",
     * @OA\Parameter(
     * name="id",
     * description="ID do veículo a ser atualizado",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/VehicleRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Veículo atualizado com sucesso",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", ref="#/components/schemas/Vehicle"),
     * @OA\Property(property="message", type="string", example="Veículo atualizado com sucesso.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Veículo não encontrado"
     * )
     * )
     */
    public function update(VehicleRequest $request, $id): JsonResponse
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Veículo não encontrado.'
            ], 404);
        }

        $vehicle->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $vehicle,
            'message' => 'Veículo atualizado com sucesso.'
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/v1/vehicles/{id}",
     * operationId="deleteVehicle",
     * tags={"Vehicles"},
     * summary="Deletar um veículo",
     * description="Deleta um registro de veículo.",
     * @OA\Parameter(
     * name="id",
     * description="ID do veículo a ser deletado",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Veículo deletado com sucesso",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object", example=null),
     * @OA\Property(property="message", type="string", example="Veículo deletado com sucesso.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Veículo não encontrado"
     * )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Veículo não encontrado.'
            ], 404);
        }

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Veículo deletado com sucesso.'
        ]);
    }
}
