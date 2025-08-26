<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\VehicleImportService;

class ImportVehicles extends Command
{
    protected $signature = 'vehicles:import';

    protected VehicleImportService $vehicleService;

    public function __construct(VehicleImportService $vehicleService)
    {
        parent::__construct();
        $this->vehicleService = $vehicleService;
    }

    public function handle(): int
    {
        $url = 'https://hub.alpes.one/api/v1/integrator/export/1902';

        $this->info('Importação iniciada em: ' . now()->format('Y-m-d H:i:s'));
        Log::info('Importação iniciada', ['url' => $url, 'executed_at' => now()]);

        $response = Http::get($url);

        if (!$response->ok()) {
            $this->error('Falha ao acessar a API');
            Log::error('Falha ao acessar a API', ['url' => $url, 'status' => $response->status()]);
            return 1;
        }

        $vehicles = $response->json();
        $processed = 0;
        $skipped = 0;

        foreach ($vehicles as $data) {
            if (!isset($data['id'])) {
                $skipped++;
                $this->warn('Veículo ignorado por dados incompletos.');
                Log::warning('Veículo ignorado', ['data' => $data]);
                continue;
            }

            $this->vehicleService->import($data);
            $processed++;
            Log::info('Veículo importado', ['external_id' => $data['id']]);
        }

        $this->info('Importação concluída em: ' . now()->format('Y-m-d H:i:s'));
        $this->info("Veículos processados: $processed");
        $this->info("Veículos ignorados: $skipped");

        Log::info('Importação finalizada', [
            'processed' => $processed,
            'skipped' => $skipped,
            'executed_at' => now()->toDateTimeString(),
        ]);

        return 0;
    }
}
