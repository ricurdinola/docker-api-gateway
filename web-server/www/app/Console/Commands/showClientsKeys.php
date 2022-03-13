<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class showClientsKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:showClientKeys {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra las Apis Keys asignadas a los clientes o al id especificado';

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

        $asignedServices = DB::table('clients')
            ->join('clients_keys', 'clients_keys.client_id','=','clients.id')
            ->leftJoin('api_keys', 'api_keys.client_key_id', '=', 'clients_keys.id')
            ->leftJoin('services', 'services.id', '=', 'api_keys.services_id')
            ->select('clients_keys.id','name', 'apikey', 'service_name', 'method', 'in_route', 'out_route','active');

        $client_id = $this->argument('client_id');
        if($client_id!= null){
            $asignedServices->where('clients.id', $client_id);
        }

        $asignedServices = $asignedServices->get();

        $data=array_map(function($item){
            return (array) $item;
        },$asignedServices->toArray());

        $this->table(
            ['ID Api Key','Cliente', 'Api Key Asignada', 'Servicio', 'Método', 'Endpoint de Ingreso', 'Endpoint de Salida','Habilitado'],
            $data
        );
    }
}
