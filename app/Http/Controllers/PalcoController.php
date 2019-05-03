<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Palco;
use App\Models\Localidad;
use Illuminate\Support\Facades\Input;

class PalcoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $palco = Palco::paginate(15);
        return $this->sendResponse($palco->toArray(), 'Palcos devueltos con éxito');
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
            'id_localidad' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La Localidad indicada no existe');
        }

        $palco = Palco::create($request->all());        
        return $this->sendResponse($palco->toArray(), 'Palco creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $palco = Palco::find($id);

        if (is_null($palco)) {
            return $this->sendError('Palco no encontrado');
        }

        return $this->sendResponse($palco->toArray(), 'Palco devuelto con éxito');
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

        $palco_search = Palco::find($id);        
        if (is_null($palco_search)) {
            return $this->sendError('Palco no encontrado');
        }

        $localidad_search = Localidad::find($input['id_localidad']);
        if (is_null($localidad_search)) {
            return $this->sendError('La Localidad indicada no existe');
        }

        $palco_search->nombre = $input['nombre'];
        $palco_search->id_localidad = $input['id_localidad'];         
        $palco_search->save();

        return $this->sendResponse($palco_search->toArray(), 'Palco actualizado con éxito');
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

            $palco = Palco::find($id);
            if (is_null($palco)) {
                return $this->sendError('Palco no encontrado');
            }
            $palco->delete();
            return $this->sendResponse($palco->toArray(), 'Palco eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El palco no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
