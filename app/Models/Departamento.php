<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Departamento
 * 
 * @property int $id
 * @property int $id_pais
 * @property string $descripcion
 * 
 *
 * @package App\Models
 */
class Departamento extends Model
{
    protected $table = 'departamento';
	public $timestamps = false;

	protected $fillable = [
		'id_pais', 'descripcion'
	];

	public function pais()
	{
		return $this->belongsTo(\App\Models\Pais::class, 'id_pais');
	}
}
