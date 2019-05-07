<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoVentum;
use Validator;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Puntos de Venta
 *
 * APIs para la gestion de la tabla punto_venta
 */
class PuntoVentumController extends BaseController
{
    /**
     * Lista de la tabla punto_venta.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $punto_venta = PuntoVentum::paginate(15);
        return $this->sendResponse($punto_venta->toArray(), 'Puntos de ventas devueltos con éxito');
    }

    /**
     * Agrega un nuevo elemento a la tabla punto_venta
     * @response {      
     *  "nombre_razon": "BBV", 
     *  "identificacion": "BBV",
     *  "tipo_identificacion": 1,
     *  "direccion" : "Address One"
     *  "telefono" : "311998333"     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_razon' => 'required',  
            'identificacion' => 'required',
            'tipo_identificacion' => 'required|boolean',  
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }      

        $punto_venta = PuntoVentum::create($request->all());        
        return $this->sendResponse($punto_venta->toArray(), 'Punto de venta creado con éxito');
    }

    /**
     * Lista de un punto de venta en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         
        $punto_venta = PuntoVentum::find($id);
        if (is_null($punto_venta)) {
            return $this->sendError('Punto de venta no encontrado');
        }
        return $this->sendResponse($punto_venta->toArray(), 'Punto de venta devuelto con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla punto_venta 
     *
     * [Se filtra por el ID]
     * @response {
     *  "nombre_razon": "BBV", 
     *  "identificacion": "BBV",
     *  "tipo_identificacion": 0,
     *  "direccion" : "Address Two"
     *  "telefono" : "311998333"   
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'nombre_razon' => 'required',  
            'identificacion' => 'required',
            'tipo_identificacion' => 'required|boolean',           
        ]);

        $punto_venta_search = PuntoVentum::find($id);        
        if (is_null($punto_venta_search)) {
            return $this->sendError('Punto de Venta no encontrado');
        } 

        $punto_venta_search->nombre_razon = $input['nombre_razon'];
        $punto_venta_search->identificacion = $input['identificacion'];
        $punto_venta_search->tipo_identificacion = $input['tipo_identificacion'];         
        $punto_venta_search->direccion = $input['direccion'];
        $punto_venta_search->telefono = $input['telefono'];
        $punto_venta_search->save();

        return $this->sendResponse($punto_venta_search->toArray(), 'Punto de Venta actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla punto_venta
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $punto_venta = PuntoVentum::find($id);
            if (is_null($punto_venta)) {
                return $this->sendError('Punto de Venta no encontrado');
            }
            $punto_venta->delete();
            return $this->sendResponse($punto_venta->toArray(), 'Punto de Venta eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Punto de Venta no se puedo eliminar, el registro es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
