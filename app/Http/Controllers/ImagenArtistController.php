<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use App\Models\Artist;
use App\Models\ImagenArtist;
use Validator;

class ImagenArtistController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artist_img = ImagenArtist::paginate(15);
        return $this->sendResponse($artist_img->toArray(), 'Imagenes de artistas devueltas con éxito');
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
            'id_artista' => 'required|integer',
            'id_imagen' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $img_artist_search = ImagenArtistController::img_artist_search($request->input('id_artista'), $request->input('id_imagen'));

        if(count($img_artist_search) != 0){
           return $this->sendError('El artista ya posee la imagen asociada'); 
        }

        $artist = Artist::find($request->input('id_artista'));
        if (is_null($artist)) {
            return $this->sendError('El artista indicado no existe');
        }

        $imagen = Imagen::find($request->input('id_imagen'));
        if (is_null($imagen)) {
            return $this->sendError('La imagen indicada no existe');
        }

        $artist_img = ImagenArtist::create($request->all());        
        return $this->sendResponse($artist_img->toArray(), 'Imagen de artista creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $img_artist = ImagenArtist::where('id_artista','=',$id)->get();
        if (count($img_artist) == 0) {
            return $this->sendError('Imagenes por artista no encontradas');
        }
        return $this->sendResponse($img_artist->toArray(), 'Imagenes por artista devueltas con éxito');
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
        $img_artist_search = ImagenArtistController::img_artist_search($id, $input['id_imagen_old']);

        if(count($img_artist_search) != 0){

            $artist = Artist::find($id);
            if (is_null($artist)) {
                return $this->sendError('El artista indicado no existe');
            }

            $imagen = Imagen::find($input['id_imagen_new']);
            if (is_null($imagen)) {
                return $this->sendError('La imagen indicada no existe');
            }

            $img_artist_search2 = ImagenArtistController::img_artist_search($id, $input['id_imagen_new']);
            
            if(count($img_artist_search2) != 0){
                return $this->sendError('La imagen por artista ya se encuentra asociada'); 
            }
            
        }else{
           return $this->sendError('La imagen por artista no se encuentra'); 
        }

        ImagenArtist::where('id_artista','=',$id)
                            ->where('id_imagen','=', $input['id_imagen_old'])
                            ->update(['id_imagen' => $input['id_imagen_new']]);  
        
        $imagen_artist = ImagenArtistController::img_artist_search($id, $input['id_imagen_new']);
                            
        return $this->sendResponse($imagen_artist->toArray(), 'Imagen por artista actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imagen_artist = ImagenArtist::where('id_artista','=',$id)->get();
        if (count($imagen_artist) == 0) {
            return $this->sendError('Imagenes por artista no encontradas');
        }
        ImagenArtist::where('id_artista','=',$id)->delete();
        return $this->sendResponse($imagen_artist->toArray(), 'Imagenes por artista eliminadas con éxito');
    }

    public function img_artist_search($id_artista, $id_imagen){

        $search = ImagenArtist::where('id_imagen','=',$id_imagen)
                                ->where('id_artista','=', $id_artista)->get();
        return $search;
    }
}
