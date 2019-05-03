<?php

namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Validator;
class TipoEventoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $tipoEventos = TipoEvento::paginate(15);
        return $this->sendResponse($tipoEventos->toArray(), 'Tipos de eventos devueltos con éxito');
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
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $tipoEvento=TipoEvento::create($request->all());        
         return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $tipoEvento = TipoEvento::find($id);

        if (is_null($tipoEvento)) {
            return $this->sendError('Tipo de evento no encontrado');
        }

        return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento devuelto con éxito');
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
            'nombre' => 'required',           
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

        $tipoEvento = TipoEvento::find($id);
        if (is_null($tipoEvento)) {
            return $this->sendError('Tipo de evento no encontrado');
        }

        $tipoEvento->nombre = $input['nombre'];              
        $tipoEvento->save();
         
        return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento actualizado con éxito');
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

            $tipoEvento = TipoEvento::find($id);
            if (is_null($tipoEvento)) {
                return $this->sendError('Tipo de evento no encontrado');
            }
            $tipoEvento->delete();
            return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El tipo de evento no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }

    
}
