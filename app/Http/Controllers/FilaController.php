<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fila;
use App\Models\Localidad;
use Validator;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Fila
 *
 * APIs para la gestion de fila
 */
class FilaController extends BaseController
{
    /**
     * Lista de la tabla fila.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $fila = Fila::paginate(15);

        return $this->sendResponse($fila->toArray(), 'Filas devueltas con éxito');
    }

    

     /**
     * Agrega un nuevo elemento a la tabla fila
     * @response {
     *  "id_localidad": 1,
     *  "nombre": "Fila 1",
     *  "numero": 1
     * }
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
     * Lista un fila en especifico 
     *
     * [Se filtra por el ID]
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
     * Actualiza un elemeto de la tabla fila 
     *
     * [Se filtra por el ID]
     *
     * @response {
     *  "id_localidad": 1,
     *  "nombre": "Fila 1",
     *  "numero": 1
     * }
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
     * Elimina un elemento de la tabla fila
     *
     * [Se filtra por el ID]
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
