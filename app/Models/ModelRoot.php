<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class ModelRoot extends Model
{
    use HasFactory;
    const CONNECTION_DB = 'conexion_db';
    protected $connection = self::CONNECTION_DB;
    const tableName = 'forge';
}
