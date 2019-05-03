<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use Validator;
class ImagenController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $imagen = Imagen::paginate(15);
        return $this->sendResponse($imagen->toArray(), 'Imagenes devueltas con éxito');
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
            'imagen' => 'required|mimes:jpeg,jpg,bmp,png',            
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

       
        $imagen = new Imagen();
        
        if($request->hasfile('imagen')){
            $file = $request->file('imagen');
            
            $name = $file->getClientOriginalName();
            $fileurl = 'http://api.ticketshop.com.co/storage/uploads/'.$name; 
            $file->move('uploads/', $name);
            
            $imagen->url = $fileurl; 
            $imagen->nombre = $name;
            $imagen->save();

        }

        return $this->sendResponse($imagen->toArray(), 'Imagen creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $imagen = Imagen::find($id);
        if (is_null($imagen)) {
            return $this->sendError('Imagen no encontrada');
        }

        return $this->sendResponse($imagen->toArray(), 'Imagen devuelta con éxito');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
               
        $validator = Validator::make($request->all(), [
            'imagen' => 'required|mimes:jpeg,jpg,bmp,png',            
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $imagen = Imagen::find($id);        
        if (is_null($imagen)) {
            return $this->sendError('Imagen no encontrada');
        }

        if($request->hasfile('imagen')){
            $file = $request->file('imagen');
            
            $name = $file->getClientOriginalName();
            $fileurl = 'http://api.ticketshop.com.co/storage/uploads/'.$name; 
            $file->move('uploads/', $name);
            
            $imagen->url = $fileurl; 
            $imagen->nombre = $name;
            $imagen->save();

        }  

        return $this->sendResponse($imagen->toArray(), 'Imagen actualizada con éxito');
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

            $imagen = Imagen::find($id);
            if (is_null($imagen)) {
                return $this->sendError('Imagen no encontrada');
            }
            $imagen->delete();
            return $this->sendResponse($imagen->toArray(), 'Imagen eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La imagen no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
