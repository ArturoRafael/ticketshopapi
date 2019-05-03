<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoVentum;
use Validator;
use Illuminate\Support\Facades\Input;

class PuntoVentumController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $punto_venta = PuntoVentum::paginate(15);
        return $this->sendResponse($punto_venta->toArray(), 'Puntos de ventas devueltos con éxito');
    }

    /**
     * Store a newly created resource in storage.
     *
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
     *
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
     * Remove the specified resource from storage.
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
