<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class showClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:showClients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista los clientes registrados';

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
            ['Id','Name'],
            Client::all(['id','name'])->toArray());
    }
}
