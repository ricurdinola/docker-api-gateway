<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKeysEvents extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['in_route','method','apikey','ip_addres', 'params'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $table = 'api_keys_events';
}