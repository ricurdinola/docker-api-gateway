<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class createService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:createService';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('Nombre del Servicio');
        $description = $this->ask('Descripción del Servicio');
        $method = $this->choice('Método del Servicio', ['GET', 'POST', 'PUT', 'DELETE','PATCH'],0);
        $in_route = $this->ask('Endpoint de Entrada');
        $out_route = $this->ask('Endpoint de Salida');

        $service = new Service();
        $service->service_name = $name;
        $service->description = $description;
        $service->method = $method;
        $service->in_route = $in_route;
        $service->out_route = $out_route;
        $service->save();

        $this->info('Servicio Creado');
    }
}
