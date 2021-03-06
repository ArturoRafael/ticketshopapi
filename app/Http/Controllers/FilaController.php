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
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla fila paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $fila = Fila::with('localidad')->with('puestos')->paginate(15);

        return $this->sendResponse($fila->toArray(), 'Filas devueltas con éxito');
    }


    /**
     * Lista de la todas las filas.
     *
     * @return \Illuminate\Http\Response
     */
    public function fila_all()
    {
       
        $fila = Fila::with('localidad')->with('puestos')->get();

        return $this->sendResponse($fila->toArray(), 'Filas devueltas con éxito');
    }


    /**
     * Listado detallado de las filas.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_filas()
    {       
        
        $fila = Fila::with('localidad')                 
                  ->with('puestos')                 
                  ->paginate(15);
        
        return $this->sendResponse($fila, 'Filas devueltas con éxito');
    }


    /**
     * Buscar Filas por nombre.
     *@bodyParam nombre string Nombre del genero.
     *@response{
     *    "nombre" : "Fila 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarFila(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $filas = Fila::with('localidad')
                ->with('puestos')
                ->where('fila.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($filas->toArray(), 'Todos las Filas filtrados');
       }else{
            
            $filas = Fila::with('localidad')->with('puestos')
                ->get();
            return $this->sendResponse($filas->toArray(), 'Todos las Filas devueltos'); 
       }

        
    }


    /**
     * Buscar Filas por localidad.
     * [Se filtra por ID de la localidad]
     *
     * @return \Illuminate\Http\Response
     */
    public function filas_localidad($id_localidad)
    {
       
        $localidad = Localidad::find($id_localidad);

        if (!$localidad) {
            return $this->sendError('Localidad no encontrado');
        }       
            
        $filas = Fila::with('localidad')
                ->with('puestos')
                ->where('id_localidad', $id_localidad)
                ->get();
        return $this->sendResponse($filas->toArray(), 'Todas las Filas filtrados por localidad');
        
    }


    

     /**
     * Agrega un nuevo elemento a la tabla fila
     *@bodyParam id_localidad int required Id de la localidad de la fila.
     *@bodyParam nombre string Nombre de la fila.
     *@bodyParam numero int Numero de la fila.
     *@bodyParam alineacion int Aliniamiento de la fila. 
     *@response{
     *  "id_localidad": 1,
     *  "nombre": "Fila 1",
     *  "numero": 1,
     *  "alineacion": 1 
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'id_localidad' => 'required|integer',
            'nombre' => 'nullable|string',
            'numero' => 'nullable|integer',
            'alineacion' => 'nullable|integer'      
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
        $fila = Fila::with('localidad')->with('puestos')->find($id);


        if (is_null($fila)) {
            return $this->sendError('Fila no encontrado');
        }


        return $this->sendResponse($fila->toArray(), 'Fila devuelta con éxito');
    }

    /**
     * Actualiza un elemeto de la tabla fila 
     *
     * [Se filtra por el ID]
     *@bodyParam id_localidad int required Id de la localidad de la fila.
     *@bodyParam nombre string Nombre de la fila.
     *@bodyParam numero int Numero de la fila.
     *@bodyParam alineacion int Aliniamiento de la fila.
     * @response {
     *  "id_localidad": 2,
     *  "nombre": "Fila A1",
     *  "numero": 1,
     *  "alineacion": 1
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
            'nombre' => 'nullable|string',
            'numero' => 'nullable|integer',
            'alineacion' => 'nullable|integer'          
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
        $fila_search->alineacion = $input['alineacion'];
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
