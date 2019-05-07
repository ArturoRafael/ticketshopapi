<?php

namespace App\Http\Controllers;

use App\Models\TipoDescuento;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Tipo Descuento
 *
 * APIs para la gestion de la tabla tipo_descuento
 */
class TipoDescuentoController extends BaseController
{
    /**
     * Lista de la tabla tipo_descuento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $tipoDescuento = TipoDescuento::paginate(15);

        return $this->sendResponse($tipoDescuento->toArray(), 'Tipos de descuentos devueltos con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla tipo_descuento
     * @response {      
     *  "nombre": "Tipo 1"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $tipoDescuento=TipoDescuento::create($request->all());        
         return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento creado con éxito');
    }

    /**
     * Lista de un tipo de descuento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $tipoDescuento = TipoDescuento::find($id);
        if (is_null($tipoDescuento)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }
        return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento devuelto con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla tipo_descuento 
     *
     * [Se filtra por el ID]
     * @response {
     *  "nombre": "Tipo Descuento 1"     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, TipoDescuento $tipoDescuento)
    {
        
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',           
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

     $tipoDescuento = TipoDescuento::find($id);

        if (is_null($tipoDescuento)) {
                return $this->sendError('Tipo de descuento no encontrado');
            }

            $tipoDescuento->nombre = $input['nombre'];              
             $tipoDescuento->save();
             
            return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento actualizado con éxito');
        }

     /**
     * Elimina un elemento de la tabla tipo_descuento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipoDescuento= TipoDescuento::find($id);
        if (is_null($tipoDescuento)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }
        $tipoDescuento->delete();


        return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento eliminado con éxito');
    }
}
