<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    /**
     * Exibe as linhas do log do importador.
     * @param Request $request
     * @return JsonResponse
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
