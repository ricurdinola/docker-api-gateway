<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class createApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:createApiKey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera una ApiKey y le asigna a un cliente';

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
        $clients =(Client::all()->pluck('name', 'id')->toArray());
        foreach ($clients as $key => $value) {
            $clients[$key]=$key . ' => ' . $value;
        }

        $client = $this->choice('Seleccione un Cliente',$clients,1);
        $client_id = (int)substr($client, 0, strpos($client, ' =>'));
        $api_key = ClientKey::generate();

        $client_key = new ClientKey();
        $client_key->client_id = $client_id;
        $client_key->apikey = $api_key;
        $client_key->save();

        $this->info('Api Key generada.');
        $this->info('Id: ' . $client_key->id);
        $this->info('Api Key: ' . $client_key->apikey);
    }
}
