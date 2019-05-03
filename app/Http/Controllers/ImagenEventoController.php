<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use App\Models\Evento;
use App\Models\ImagenEvento;
use Validator;

class ImagenEventoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $img_evento = ImagenEvento::paginate(15);
        return $this->sendResponse($img_evento->toArray(), 'Imagenes de eventos devueltas con éxito');
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
            'id_imagen' => 'required|integer',
            'id_evento' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $img_evento_search = ImagenEventoController::img_evento_search($request->input('id_evento'), $request->input('id_imagen'));

        if(count($img_evento_search) != 0){
           return $this->sendError('El evento ya posee esa imagen asociada'); 
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $imagen = Imagen::find($request->input('id_imagen'));
        if (is_null($imagen)) {
            return $this->sendError('La imagen indicada no existe');
        }


         $img_evento = ImagenEvento::create($request->all());        
         return $this->sendResponse($img_evento->toArray(), 'Imagenes de evento creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $img_evento = ImagenEvento::where('id_evento','=',$id)->get();
        if (count($img_evento) == 0) {
            return $this->sendError('Imagenes por evento no encontradas');
        }
        return $this->sendResponse($img_evento->toArray(), 'Imagenes por evento devueltas con éxito');
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
            'id_imagen_old' => 'required',
            'id_imagen_new' => 'required',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());      
        }
        $img_evento_search = ImagenEventoController::img_evento_search($id, $input['id_imagen_old']);

        if(count($img_evento_search) != 0){

            $evento = Evento::find($id);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            $imagen = Imagen::find($input['id_imagen_new']);
            if (is_null($imagen)) {
                return $this->sendError('La imagen indicada no existe');
            }

            $img_evento_search2 = ImagenEventoController::img_evento_search($id, $input['id_imagen_new']);
            
            if(count($img_evento_search2) != 0){
                return $this->sendError('La imagen por evento ya se encuentra asociada'); 
            }
            
        }else{
           return $this->sendError('La imagen por evento no se encuentra'); 
        }

        ImagenEvento::where('id_evento','=',$id)
                            ->where('id_imagen','=', $input['id_imagen_old'])
                            ->update(['id_imagen' => $input['id_imagen_new']]);  
        
        $imagen_evento = ImagenEventoController::img_evento_search($id, $input['id_imagen_new']);
                            
        return $this->sendResponse($imagen_evento->toArray(), 'Imagen por evento actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imagen_evento = ImagenEvento::where('id_evento','=',$id)->get();
        if (count($imagen_evento) == 0) {
            return $this->sendError('Imagenes por evento no encontradas');
        }
        ImagenEvento::where('id_evento','=',$id)->delete();
        return $this->sendResponse($grupo_vendors_pto->toArray(), 'Imagenes por evento eliminadas con éxito');
    }

     public function img_evento_search($id_evento, $id_imagen){

        $search = ImagenEvento::where('id_imagen','=',$id_imagen)
                                ->where('id_evento','=', $id_evento)->get();
        return $search;
    }
}
