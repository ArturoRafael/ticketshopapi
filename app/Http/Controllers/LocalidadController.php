<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Localidad;
use App\Models\Tribuna;
use Illuminate\Support\Facades\Input;

class LocalidadController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localidad = Localidad::paginate(15);

        return $this->sendResponse($localidad->toArray(), 'Localidades devueltas con éxito');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',            
            'id_tribuna' => 'required',
            'puerta_acceso' => 'alpha_num|max:20',
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $tribuna = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna)) {
            return $this->sendError('La Tribuna indicada no existe');
        }

        $localidad = Localidad::create($request->all());        
        return $this->sendResponse($localidad->toArray(), 'Localidad creada con éxito');
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
        $localidad = Localidad::find($id);


        if (is_null($localidad)) {
            return $this->sendError('Localidad no encontrado');
        }


        return $this->sendResponse($localidad->toArray(), 'Localidad devuelta con éxito');
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
            'id_tribuna' => 'required',
            'puerta_acceso' => 'alpha_num|max:20',           
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        $tribuna_search = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna_search)) {
            return $this->sendError('La tribuna indicada no existe');
        }

        $localidad_search = Localidad::find($id);        
        if (is_null($localidad_search)) {
            return $this->sendError('Localidad no encontrada');
        }

        $localidad_search->nombre = $input['nombre'];
        $localidad_search->id_tribuna = $input['id_tribuna'];
        $localidad_search->puerta_acceso = $input['puerta_acceso'];         
        $localidad_search->save();

        return $this->sendResponse($localidad_search->toArray(), 'Localidad actualizada con éxito');

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

            $localidad = Localidad::find($id);
            if (is_null($localidad)) {
                return $this->sendError('Localidad no encontrada');
            }
            $localidad->delete();
            return $this->sendResponse($localidad->toArray(), 'Localidad eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La localidad no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }


        
    }
}
