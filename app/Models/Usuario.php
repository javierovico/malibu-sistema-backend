<?php

namespace App\Models;


class Usuario extends ModelRoot
{
    const tableName = 'usuarios';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
}
