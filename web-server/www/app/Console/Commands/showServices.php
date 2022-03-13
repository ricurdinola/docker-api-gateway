<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class showServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:showServices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista los servicios disponibles para el consumo';

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
        $this->table(
            ['Id','Nombre del Servicio','Descripción','Método Aceptado','Endpoint de Ingreso','Endpoint de Salida'],
            Service::all(['id','service_name','description','method','in_route','out_route'])->toArray());
    }
}
