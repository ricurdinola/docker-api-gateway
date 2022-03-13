<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\Service;
use Illuminate\Console\Command;

class assignService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:assignService';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Habilita un servicio a una Api Key de un Cliente';

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
        $services =(Service::all()->pluck('service_name', 'id')->toArray());
        foreach ($services as $key => $value) {
            $services[$key]=$key . ' => ' . $value;
        }

        $service = $this->choice('Seleccione un Servicio',$services,1);
        $service_id = (int)substr($service, 0, strpos($service, ' =>'));

        $clientApiKey = $this->ask('Indique el id de la Api Key del Cliente');

        $apiKey = new ApiKey();
        $apiKey->services_id = $service_id;
        $apiKey->client_key_id = $clientApiKey;
        $apiKey->active = 1;
        $apiKey->save();
    }
}
