<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupsVendedore;
use Validator;

class GrupsVendedoreController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $grups_vendedores = GrupsVendedore::paginate(15);
        return $this->sendResponse($grups_vendedores->toArray(), 'Grupos de vendedores devueltos con éxito');
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
            'caracteristica' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
         $grup_vendedor = GrupsVendedore::create($request->all());        
         return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $grup_vendedor = GrupsVendedore::find($id);
        if (is_null($grup_vendedor)) {
            return $this->sendError('Grupo de vendedores no encontrado');
        }
        return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores devuelto con éxito');
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
            'caracteristica' => 'required',             
        ]);
        
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $grup_vendedor = GrupsVendedore::find($id);
        if (is_null($grup_vendedor)) {
            return $this->sendError('Grupo de vendedores no encontrado');
        }

        $grup_vendedor->nombre = $input['nombre'];
        $grup_vendedor->caracteristica = $input['caracteristica'];
        $grup_vendedor->save();

        return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores actualizado con éxito');
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

            $grup_vendedor = GrupsVendedore::find($id);
            if (is_null($grup_vendedor)) {
                return $this->sendError('Grupo de vendedores no encontrado');
            }
            $grup_vendedor->delete();
            return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Grupo de vendedores no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
