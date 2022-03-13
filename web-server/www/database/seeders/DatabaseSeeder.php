<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clients')->insert(
            array(
                'id' => 1,
                'name' => "Administrador"
            )
        );
        DB::table('services')->insert(
            array(
                'id' => 1,
                'service_name' => "Registro de Usuario",
                'description' => "Permite enviar dar de alta un usuario al servicio de Autenticación",
                'method' => 'POST',
                'in_route' => 'api/v1/auth/register',
                'out_route' => '#'
            )
        );
        DB::table('services')->insert(
            array(
                'id' => 2,
                'service_name' => "Login de Usuario",
                'description' => "Permite validar las credenciales de un usuario",
                'method' => 'POST',
                'in_route' => 'api/v1/auth/login',
                'out_route' => '#'
            )
        );
        DB::table('services')->insert(
            array(
                'id' => 3,
                'service_name' => "Renovación de Token",
                'description' => "Permite renovar el token de un usuario con sesión iniciada",
                'method' => 'POST',
                'in_route' => 'api/v1/auth/refresh',
                'out_route' => '#'
            )
        );
        DB::table('services')->insert(
            array(
                'id' => 4,
                'service_name' => "Desconexión de Usuario",
                'description' => "Expira el token asignado al usuario",
                'method' => 'POST',
                'in_route' => 'api/v1/auth/logout',
                'out_route' => '#'
            )
        );
        DB::table('services')->insert(
            array(
                'id' => 5,
                'service_name' => "Check Token",
                'description' => "Permite recuperar los datos de un usuario con el token dado, verificando su validez",
                'method' => 'POST',
                'in_route' => 'api/v1/auth/me',
                'out_route' => '#'
            )
        );
        DB::table('services')->insert(
            array(
                'id' => 6,
                'service_name' => "Obtener Usuario",
                'description' => "Permite obtener un usuario a partir del id solicitado",
                'method' => 'POST',
                'in_route' => 'api/v1/auth/getUserById',
                'out_route' => '#'
            )
        );

        DB::insert('INSERT INTO clients_keys (`id`,`client_id`,`apikey`,`deleted_at`,`created_at`,`updated_at`) VALUES (1,1,"1234",NULL,NULL,NULL)');

        DB::insert('INSERT INTO api_keys (`id`,`client_key_id`,`services_id`,`active`,`deleted_at`,`created_at`,`updated_at`) VALUES (1,1,1,1,NULL,NULL,NULL)');
        DB::insert('INSERT INTO api_keys (`id`,`client_key_id`,`services_id`,`active`,`deleted_at`,`created_at`,`updated_at`) VALUES (2,1,2,1,NULL,NULL,NULL)');
        DB::insert('INSERT INTO api_keys (`id`,`client_key_id`,`services_id`,`active`,`deleted_at`,`created_at`,`updated_at`) VALUES (3,1,3,1,NULL,NULL,NULL)');
        DB::insert('INSERT INTO api_keys (`id`,`client_key_id`,`services_id`,`active`,`deleted_at`,`created_at`,`updated_at`) VALUES (4,1,4,1,NULL,NULL,NULL)');
        DB::insert('INSERT INTO api_keys (`id`,`client_key_id`,`services_id`,`active`,`deleted_at`,`created_at`,`updated_at`) VALUES (5,1,5,1,NULL,NULL,NULL)');
        DB::insert('INSERT INTO api_keys (`id`,`client_key_id`,`services_id`,`active`,`deleted_at`,`created_at`,`updated_at`) VALUES (6,1,6,1,NULL,NULL,NULL)');
    }
}
