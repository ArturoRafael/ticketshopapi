<?php

namespace App\Http\Controllers;

use App\Models\Preventum;
use App\Models\Evento;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Preventa
 *
 * APIs para la gestion de la tabla preventa
 */
class PreVentumController extends BaseController
{
    /**
     * Lista de la tabla preventa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preventa = Preventum::with("evento")->paginate(15);
        return $this->sendResponse($preventa->toArray(), 'Preventa devueltos con éxito');
    }


    /**
     * Agrega un nuevo elemento a la tabla preventa
     *
     *@bodyParam nombre string Nombre de la preventa.
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam fecha_inicio date Fecha de inicio.
     *@bodyParam fecha_fin date Fecha de finalización. 
     *@bodyParam activo int required Estado de la preventa. Defaults 0
     * @response {
     *  "nombre": "Palco New",
     *  "id_evento": 1,
     *  "fecha_inicio": null
     *  "fecha_fin": null
     *  "activo": 0           
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [                        
            'nombre' => 'required|max:200', 
            'id_evento' =>  'required|integer', 
            "fecha_inicio" =>'nullable|date|date_format:Y-m-d',
            "fecha_fin" =>'nullable|date|date_format:Y-m-d', 
            "activo" => 'required|integer'          
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        if(is_null($request->input('activo'))){
            Input::merge(['activo' => 0]);
        }

        $preventa = Preventum::create($request->all());        
        return $this->sendResponse($preventa->toArray(), 'Preventa creada con éxito');
    }

    /**
     * Lista una preventa en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Preventum  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $preventa = Preventum::with("evento")
                            ->where('id','=',$id)->get();
        if (count($preventa) == 0) {
            return $this->sendError('La preventa no se encuentra');
        }
        return $this->sendResponse($preventa->toArray(), 'Preventa devuelto con éxito');
    }


    /**
     * Actualiza un elemento a la tabla preventa
     *
     *@bodyParam nombre string Nombre de la preventa.
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam fecha_inicio date Fecha de inicio.
     *@bodyParam fecha_fin date Fecha de finalización. 
     *@bodyParam activo int required Estado de la preventa. Defaults 0
     * @response {
     *  "nombre": "Palco New",
     *  "id_evento": 3,
     *  "fecha_inicio": "2019-05-12"
     *  "fecha_fin": null
     *  "activo": 1           
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Preventum  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [                        
            'nombre' => 'required|max:200', 
            'id_evento' =>  'required|integer', 
            "fecha_inicio" =>'nullable|date|date_format:Y-m-d',
            "fecha_fin" =>'nullable|date|date_format:Y-m-d', 
            "activo" => 'required|integer'          
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($input['id_evento']);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $preventa_search = Preventum::find($id);
        if (is_null($preventa_search)) {
            return $this->sendError('Descuento no encontrado');
        }

        if(is_null($input['activo'])){
            $preventa_search->activo  = 0;
        }else{
            $preventa_search->activo  = $input['activo'];
        } 

        $preventa_search->nombre = $input['nombre'];
        $preventa_search->id_evento = $input['id_evento'];
        $preventa_search->fecha_inicio = $input['fecha_inicio'];
        $preventa_search->fecha_fin = $input['fecha_fin'];
        $preventa_search->save();
        return $this->sendResponse($preventa_search->toArray(), 'Preventa actualizada con éxito');
    }

    /**
     * Elimina un elemento de la tabla preventa
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Preventum  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $preventa = Preventum::find($id);
            if (is_null($preventa)) {
                return $this->sendError('Preventa no encontrado');
            }
            $preventa->delete();
            return $this->sendResponse($preventa->toArray(), 'Preventa eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La preventa no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
