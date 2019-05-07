<?php

namespace App\Http\Controllers;

use App\Models\Auditorio;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Auditorio
 *
 * APIs para la gestion de auditorio
 */
class AuditorioController extends BaseController
{
     /**
     * Lista de la tabla auditorio.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $auditorio = Auditorio::paginate(15);

         return $this->sendResponse($auditorio->toArray(), 'Auditorios devueltos con éxito');
    }

    /**
     * Agrega un nuevo elemento a la tabla auditorio
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(), [
            'nombre' => 'required',   
            'ciudad' => 'required',
            'departamento' => 'required',
            'pais' => 'required',
            'direccion' => 'required',
            'longitud' => 'numeric',
            'latitud' => 'numeric',
            'aforo' => 'integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $auditorio=Auditorio::create($request->all());        
         return $this->sendResponse($auditorio->toArray(), 'Auditorio creado con éxito');

    }

    /**
     * Lista un auditorio en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $auditorio = Auditorio::find($id);


        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }


        return $this->sendResponse($auditorio->toArray(), 'Auditorio devuelto con éxito');
    }

  
   /**
     * Actualiza un elemeto de la tabla auditorio 
     *
     * [Se filtra por el ID]
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Auditorio $auditorio)
    {
        //
         $input = $request->all();


        $validator = Validator::make($request->all(), [
           'nombre' => 'required',   
            'ciudad' => 'required',
            'departamento' => 'required',
            'pais' => 'required',
            'direccion' => 'required',
            'longitud' => 'numeric',
            'latitud' => 'numeric',
            'aforo' => 'integer'           
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }


        $auditorio->nombre = $input['nombre'];
        $auditorio->ciudad = $input['ciudad'];
        $auditorio->departamento = $input['departamento'];
        $auditorio->pais = $input['pais'];
        $auditorio->direccion = $input['direccion'];        
        if (!is_null($request->input('latitud'))) 
            $auditorio->latitud = $input['latitud'];
        if (!is_null($request->input('longitud'))) 
            $auditorio->longitud = $input['longitud'];
        if (!is_null($request->input('aforo'))) 
            $auditorio->aforo = $input['aforo'];
         $auditorio->save();

        return $this->sendResponse($auditorio->toArray(), 'Auditorio actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla auditorio
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $auditorio =Auditorio::find($id);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }
        $auditorio->delete();


        return $this->sendResponse($auditorio->toArray(), 'Auditorio eliminado con éxito');
    }
}
