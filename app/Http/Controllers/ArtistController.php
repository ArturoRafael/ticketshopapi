<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistaEvento;
use App\Models\Genero;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Input;

class ArtistController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $artist = Artist::paginate(15);

        return $this->sendResponse($artist->toArray(), 'Artistas devueltos con éxito');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 

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
            'manager' => 'required',
            'id_genero' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $genero = Genero::find($request->input('id_genero'));
        if (is_null($genero)) {
            return $this->sendError('El Género indicado no existe');
        }

          $artist=Artist::create($request->all());        
         return $this->sendResponse($artist->toArray(), 'Artista creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $artist = Artist::find($id);


        if (is_null($artist)) {
            return $this->sendError('Artista no encontrado');
        }


        return $this->sendResponse($artist->toArray(), 'Artista devuelto con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Artist $artist)
    {
       
        $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',            
            'manager' => 'required',
            'id_genero' => 'required'           
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
         $genero = Genero::find($request->input('id_genero'));
        if (is_null($genero)) {
            return $this->sendError('El Género indicado no existe');
        }

        $artist2 = Artist::find($id);
        if (is_null($artist2)) {
                    return $this->sendError('Artista no encontrado');
                }
        $artist2->nombre = $input['nombre'];
        $artist2->manager = $input['manager'];
        $artist2->id_genero = $input['id_genero'];         
         $artist2->save();
         //$artist->update($input);

        return $this->sendResponse($artist2->toArray(), 'Artista actualizado con éxito');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $artist = Artist::find($id);
        $artist->delete();


        return $this->sendResponse($artist->toArray(), 'Artista eliminado con éxito');
    }

    public function listadoartistevento(){

        $artist_event = ArtistaEvento::all();
        $id_array = array();
        $artist_full = array();
        if(count($artist_event) != 0){
            foreach ($artist_event as $artist) {
                array_push($id_array, $artist->id_artista);
            }
           
           for ($i=0; $i < sizeof($id_array); $i++) { 
               
               $artist = \DB::table('artista_evento')
                      ->join('artist', 'artista_evento.id_artista', '=', 'artist.id')
                      ->join('genero', 'artist.id_genero', '=', 'genero.id')
                      ->where('artista_evento.id_artista','=', $id_array[$i])
                      ->select('artist.nombre AS nombre_artista', 'artist.manager', 'genero.nombre AS genero')
                      ->get();

                $artist_img = \DB::table('imagen_artist')
                      ->join('imagen', 'imagen_artist.id_imagen', '=', 'imagen.id')
                      ->where('imagen_artist.id_artista','=', $id_array[$i])
                      ->select('imagen.nombre', 'imagen.url')
                      ->get();

                $artist->first()->imagenes_artista = $artist_img->toArray();

                array_push($artist_full, $artist);
           }

           return $this->sendResponse($artist_full, 'Listado de artistas con eventos planificados devuelto con éxito');

        }else{
            return $this->sendError('No existen artistas con eventos planificados');
        }

        
    }
}
