<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use App\Models\Localidad;
use App\Models\Fila;
use Illuminate\Support\Facades\Input;
use Validator;

class PuestoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $puesto = Puesto::paginate(15);
        return $this->sendResponse($puesto->toArray(), 'Puestos devueltos con éxito');
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
            'id_localidad' => 'required',      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicada no existe');
        }

        if($request->input('id_fila') != null){

            $fila = Fila::find($request->input('id_fila'));
            if (is_null($fila)) {
                return $this->sendError('La Fila indicada no existe');
            }

        }

        $puesto = Puesto::create($request->all());        
        return $this->sendResponse($puesto->toArray(), 'Puesto creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $puesto = Puesto::find($id);

        if (is_null($puesto)) {
            return $this->sendError('Puesto no encontrado');
        }

        return $this->sendResponse($puesto->toArray(), 'Puesto devuelto con éxito');
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
            'id_localidad' => 'required',           
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $puesto_search = Puesto::find($id);        
        if (is_null($puesto_search)) {
            return $this->sendError('Puesto no encontrado');
        }


        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicada no existe');
        }

        if($request->input('id_fila') != null){
            $fila = Fila::find($request->input('id_fila'));
            if (is_null($fila)) {
                return $this->sendError('La fila indicada no existe');
            }else{
                $puesto_search->id_fila = $input['id_fila']; 
            }             
        }
        
        $puesto_search->numero = $input['numero']; 
        $puesto_search->id_localidad = $input['id_localidad'];                    
        $puesto_search->save();

        return $this->sendResponse($puesto_search->toArray(), 'Puesto actualizado con éxito');
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

            $puesto = Puesto::find($id);
            if (is_null($puesto)) {
                return $this->sendError('Puesto no encontrado');
            }
            $puesto->delete();
            return $this->sendResponse($puesto->toArray(), 'Puesto eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Puesto no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
