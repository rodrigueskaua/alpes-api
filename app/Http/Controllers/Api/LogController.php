<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Logs",
 *   description="Operações relacionadas aos logs do importador"
 * )
 */
class LogController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/v1/importer-logs",
     *   tags={"Logs"},
     *   summary="Exibe linhas do log do importador",
     *   description="Retorna as linhas do arquivo de log do importador. Use o parâmetro 'lines' para definir a quantidade ou 'all' para todas.",
     *   @OA\Parameter(
     *     name="lines",
     *     in="query",
     *     description="Número de linhas a retornar ou 'all' para todas",
     *     required=false,
     *     @OA\Schema(type="string", example="100")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Operação bem-sucedida",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="logs", type="array", @OA\Items(type="string"))
     *     )
     *   )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $logPath = storage_path('logs/importer.log');
        if (!file_exists($logPath)) {
            return response()->json(['success' => true, 'logs' => []]);
        }

        $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $linesParam = $request->query('lines', 100);

        if ($linesParam === 'all') {
            $logs = $lines;
        } else {
            $count = (int) $linesParam;
            if ($count > 0) {
                $logs = array_slice($lines, -$count);
            } else {
                $logs = $lines;
            }
        }

        return response()->json(['success' => true, 'logs' => $logs]);
    }
}
