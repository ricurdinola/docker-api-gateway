<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ClientKey extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'clients_keys';


    public static function keyExists($key)
    {
        return ClientKey::withTrashed()->where('apikey', $key)->first() instanceof self;
    }

    public static function generate(){
        do {
            $key = Str::random(64);
        } while (self::keyExists($key));

        return $key;
    }
}
