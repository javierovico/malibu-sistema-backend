<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $barrio
 * @property mixed $nombre
 * @property mixed $ruc
 * @property mixed $telefono
 * @property mixed $ciudad
 * @method static self findOrFail(mixed $clienteId)
 */
class Cliente extends ModelRoot
{
    use SoftDeletes;
    const tableName = 'clientes';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_IMAGEN_ID = 'imagen_id';
    const COLUMNA_NOMBRE = 'nombre';
    const COLUMNA_RUC = 'ruc';
    const COLUMNA_TELEFONO = 'telefono';
    const COLUMNA_CIUDAD = 'ciudad';
    const COLUMNA_BARRIO = 'barrio';

    const RELACION_IMAGEN = 'imagen';

    public function imagen(): BelongsTo
    {
        return $this->belongsTo(Archivo::class, self::COLUMNA_IMAGEN_ID);
    }

    public function asociarImagen64($url)
    {
        $archivoNuevo = Archivo::nuevoArchivo(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$url)), ($this->nombre?:'sinNombre') . '.jpg');
        $this->imagen()->associate($archivoNuevo);
    }

    public function borrarImagen()
    {
        $this->imagen()->dissociate();
    }

}
