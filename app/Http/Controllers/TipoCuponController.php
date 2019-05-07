<?php

namespace App\Http\Controllers;

use App\Models\TipoCupon;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Tipo de Cupon
 *
 * APIs para la gestion de la tabla tipo_cupon
 */
class TipoCuponController extends BaseController
{
    /**
     * Lista de la tabla tipo_cupon.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $tipoCupon = TipoCupon::paginate(15);

        return $this->sendResponse($tipoCupon->toArray(), 'Tipos de cupones devueltos con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla tipo_cupon
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
          $tipoCupon=TipoCupon::create($request->all());        
         return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón  creado con éxito');
    }

     /**
     * Lista de un tipo de cupon en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoCupon = TipoCupon::find($id);


        if (is_null($tipoCupon)) {
            return $this->sendError('Tipo de Cupón no encontrado');
        }


        return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón  devuelto con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla tipo_cupon 
     *
     * [Se filtra por el ID]
     * @response {
     *  "nombre": "Tipo Cupon 1"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, TipoCupon $tipoCupon)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',           
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

     $tipoCupon = TipoCupon::find($id);

 if (is_null($tipoCupon)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }

        $tipoCupon->nombre = $input['nombre'];              
         $tipoCupon->save();
         
        
        return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla tipo_cupon
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $tipoCupon = TipoCupon::find($id);
        if (is_null($tipoCupon)) {
            return $this->sendError('Tipo de cupón no encontrado');
        }
        $tipoCupon->delete();


        return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón eliminado con éxito');
    }
}
