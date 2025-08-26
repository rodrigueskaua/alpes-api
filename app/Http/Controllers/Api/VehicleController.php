<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    public function index(): JsonResponse
    {
        $vehicles = Vehicle::orderBy('external_id', 'asc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'message' => 'Lista de veículos carregada.'
        ]);
    }

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

    public function store(VehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $vehicle,
            'message' => 'Veículo criado.'
        ], 201);
    }

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
