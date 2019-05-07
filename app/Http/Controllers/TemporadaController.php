<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Temporada
 *
 * APIs para la gestion de la tabla temporada
 */
class TemporadaController extends BaseController
{
    /**
     * Lista de la tabla temporada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $temporada = Temporada::paginate(15);

        return $this->sendResponse($temporada->toArray(), 'Temporadas devueltas con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla temporada
     * @response {      
     *  "nombre": "Temporada Gold", 
     *  "status": 1    
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(), [
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $temporada=Temporada::create($request->all());        
         return $this->sendResponse($temporada->toArray(), 'Temporada creada con éxito');
    }

   
     /**
     * Lista una temporada en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $temporada = Temporada::find($id);

        if (is_null($temporada)) {
            return $this->sendError('Temporada no encontrada');
        }
        return $this->sendResponse($temporada->toArray(), 'Temporada devuelta con éxito');
    }

    
 
    /**
     * Actualiza un elemeto de la tabla temporada 
     *
     * [Se filtra por el ID]
     * @response {
     *  "nombre": "Temporada Gold", 
     *  "status": 0    
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Temporada $temporada)
    {
        //
        $input = $request->all();

        $validator = Validator::make($input, [
            'nombre' => 'required',            
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $temporada->nombre = $input['nombre'];
        if (!is_null($request->input('status'))) 
            $temporada->status = $input['status'];
         $temporada->save();

        return $this->sendResponse($temporada->toArray(), 'Temporada actualizada con éxito');
    }

    /**
     * Elimina un elemento de la tabla temporada
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $temporada =Temporada::find($id);
         if (is_null($temporada)) {
            return $this->sendError('Temporada no encontrada');
        }
        $temporada->delete();


        return $this->sendResponse($temporada->toArray(), 'Temporada eliminada con éxito');
    }
}
