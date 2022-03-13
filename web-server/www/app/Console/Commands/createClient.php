<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class createClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:createClient {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Services Client';

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
        $client = new Client();
        $client->name =  $this->argument('name');
        $client->save();

        $this->info('Cliente Creado');
    }
}
