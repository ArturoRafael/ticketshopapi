<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Ciudad
 * 
 * @property int $id
 * @property int $id_departamento
 * @property string $descripcion
 * 
 *
 * @package App\Models
 */
class Ciudad extends Model
{
    protected $table = 'ciudad';
	public $timestamps = false;

	protected $fillable = [
		'id_departamento', 'descripcion'
	];


	public function departamento()
	{
		return $this->belongsTo(\App\Models\Departamento::class, 'id_departamento');
	}

}
