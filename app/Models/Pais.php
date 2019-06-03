<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Pais
 * 
 * @property int $id
 * @property string $descripcion
 * 
 *
 * @package App\Models
 */
class Pais extends Model
{
    protected $table = 'pais';
	public $timestamps = false;

	protected $fillable = [
		'descripcion'
	];

	public function departamentos()
	{
		return $this->hasMany(\App\Models\Departameto::class, 'id_pais');
	}
}
