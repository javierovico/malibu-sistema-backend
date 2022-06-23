<?php

namespace App\Models;



use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property mixed $tipo
 * @property mixed $path
 */
class Archivo extends ModelRoot
{
    const tableName = 'archivos';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_TIPO = 'tipo';
    const COLUMNA_PATH = 'path';

    protected $fillable = [
        self::COLUMNA_PATH,
        self::COLUMNA_TIPO
    ];

    protected $appends = [self::APPEND_URL];

    const APPEND_URL = 'url';

    const TIPO_ABSOLUTO = 1;
    const TIPO_RELATIVO_LOCAL = 10;     //SOLO PARA UPLOADS AL SERVIDOR

    public const INDICE_DISK = [
        self::TIPO_RELATIVO_LOCAL => 'uploadPrueba',
    ];

    public const INDICE_URL = [
        self::TIPO_RELATIVO_LOCAL => '#dir_local#/api/archivo/imagen/%%',
    ];

    public function getUrlAttribute()
    {
        if ($this->tipo == self::TIPO_ABSOLUTO) {
            return $this->path;
        } else {
            $url = str_replace('%%', $this->path, self::INDICE_URL[$this->tipo]);
            $url = str_replace('#dir_local#', config('app.url'), $url);
            return $url;
        }
    }
    /**
     * Crea un archivo nuevo desde base64
     * @param $contenido
     * @param null $nombreTentativo
     * @param int $tipoSubida
     * @return void
     */
    public static function nuevoArchivo($contenido, $nombreTentativo = null, int $tipoSubida = self::TIPO_RELATIVO_LOCAL): self
    {
        $nombreArchivoRandom = self::nombreAleatorioArchivo($nombreTentativo);
        $disco = self::INDICE_DISK[$tipoSubida];
        Storage::disk($disco)->put($nombreArchivoRandom,$contenido,'public');
        return Archivo::create([
            Archivo::COLUMNA_TIPO => $tipoSubida,
            Archivo::COLUMNA_PATH => $nombreArchivoRandom
        ]);
    }

    public static function nombreAleatorioArchivo($nombre = null, $extensionDefault = '.dat'): string
    {
        if (!$nombre) {
            $nombre = 'sin_nombre';
        }
        $nombre = str_replace(' ','_',$nombre);
        $array = explode('.',$nombre);
        if(count($array) == 1){
            $extension = '.dat';
        }else{
            $extension = '.' . $array[count($array)-1];
            $nombre = $array[0];
            for($i= 1 ;$i< (count($array)-1); $i++){
                $nombre .= '.' . $array[$i];
            }
        }
        return ((new Carbon('now'))->format('Y_m_d_H_i'))
            . '_' . Str::random()
            . '_' . substr($nombre,0,10)
            . substr($extension,0,5);
    }


}
