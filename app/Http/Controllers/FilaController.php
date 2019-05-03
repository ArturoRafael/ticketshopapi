<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fila;
use App\Models\Localidad;
use Validator;
use Illuminate\Support\Facades\Input;

class FilaController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $fila = Fila::paginate(15);

        return $this->sendResponse($fila->toArray(), 'Filas devueltas con éxito');
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

        $fila=Fila::create($request->all());        
        return $this->sendResponse($fila->toArray(), 'Fila creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         //
        $fila = Fila::find($id);


        if (is_null($fila)) {
            return $this->sendError('Fila no encontrado');
        }


        return $this->sendResponse($fila->toArray(), 'Fila devuelta con éxito');
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
        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicada no existe');
        }

        $fila_search = Fila::find($id);        
        if (is_null($fila_search)) {
            return $this->sendError('Fila no encontrada');
        }        
        $fila_search->id_localidad = $input['id_localidad'];
        $fila_search->nombre = $input['nombre'];
        $fila_search->numero = $input['numero'];         
        $fila_search->save();
         //$artist->update($input);

        return $this->sendResponse($fila_search->toArray(), 'Fila actualizada con éxito');
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

            $fila = Fila::find($id);
            if (is_null($fila)) {
                return $this->sendError('Fila no encontrada');
            }
            $fila->delete();
            return $this->sendResponse($fila->toArray(), 'Fila eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La Fila no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
