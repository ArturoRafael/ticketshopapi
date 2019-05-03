<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tribuna;
use App\Models\Auditorio;
use Illuminate\Support\Facades\Input;

class TribunaController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tribuna = Tribuna::paginate(15);

        return $this->sendResponse($tribuna->toArray(), 'Tribunas devueltas con éxito');
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
            'id_auditorio' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $auditorio = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio)) {
            return $this->sendError('El Auditorio indicado no existe');
        }

        $tribuna = Tribuna::create($request->all());        
        return $this->sendResponse($tribuna->toArray(), 'Tribuna creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $tribuna = Tribuna::find($id);

        if (is_null($tribuna)) {
            return $this->sendError('Tribuna no encontrada');
        }

        return $this->sendResponse($tribuna->toArray(), 'Tribuna devuelta con éxito');
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
            'id_auditorio' => 'required',           
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        $auditorio_search = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio_search)) {
            return $this->sendError('El Auditorio indicado no existe');
        }

        $tribuna_search = Tribuna::find($id);        
        if (is_null($tribuna_search)) {
            return $this->sendError('Tribuna no encontrada');
        }

        $tribuna_search->nombre = $input['nombre'];
        $tribuna_search->id_auditorio = $input['id_auditorio'];          
        $tribuna_search->save();

        return $this->sendResponse($tribuna_search->toArray(), 'Tribuna actualizada con éxito');
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

            $tribuna = Tribuna::find($id); 
            if (is_null($tribuna)) {
                return $this->sendError('Tribuna no encontrada');
            }
            $tribuna->delete();
            return $this->sendResponse($tribuna->toArray(), 'Tribuna eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La tribuna no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
        
    }
}
