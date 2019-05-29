<?php

namespace App\Http\Controllers;

use App\Models\Auditorio;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Auditorio
 *
 * APIs para la gestion de auditorio
 */
class AuditorioController extends BaseController
{
     /**
     * Lista de la tabla auditorio.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
         $auditorio = Auditorio::paginate(15);

         return $this->sendResponse($auditorio->toArray(), 'Auditorios devueltos con éxito');
    }


    /**
     * Buscar Auditorios por nombre.
     *@bodyParam nombre string Nombre del auditorio.
     *@response{
     *    "nombre" : "auditorio 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarAuditorio(Request $request)
    {       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $auditorio = \DB::table('auditorio')
                ->where('auditorio.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('auditorio.*')
                ->get();
            return $this->sendResponse($auditorio->toArray(), 'Todos los Auditorios filtrados');
       }else{
            
            $auditorio = \DB::table('auditorio')                
                ->select('auditorio.*')
                ->get();
            return $this->sendResponse($auditorio->toArray(), 'Todos los Auditorios devueltos'); 
       }
        
    }


    /**
     * Listado detallado de los auditorios.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_auditorios()
    {
        
        $auditorio = Auditorio::with('tribunas')
                    ->with('imagens')
                    ->paginate(15);
        $lista_auditorio = compact('auditorio');
        return $this->sendResponse($lista_auditorio, 'Auditorios devueltos con éxito');
    }

    /**
     * Agrega un nuevo elemento a la tabla auditorio
     *@bodyParam nombre string required Nombre del Auditorio.
     *@bodyParam ciudad string required Ciudad del auditorio.
     *@bodyParam departamento string required Departamento del auditorio.
     *@bodyParam pais string required Pais del auditorio.
     *@bodyParam direccion string Direccion del auditorio.
     *@bodyParam longitud int Coordenada: Longitud.
     *@bodyParam latitud int Coordenada: Latitud.
     *@bodyParam aforo int Aforo.
     *@response{
     *       "nombre" : "Auditorio 1",
     *       "ciudad" : "Raccon City",
     *       "departamento": "Departament 1",
     *       "pais": "US",
     *       "direccion": "Street 1-56",
     *       "longitud": null,
     *       "latitud": null,
     *       "aforo": null
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(), [
            'nombre' => 'required',   
            'ciudad' => 'required',
            'departamento' => 'required',
            'pais' => 'required',
            'direccion' => 'required',
            'longitud' => 'numeric',
            'latitud' => 'numeric',
            'aforo' => 'integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $auditorio=Auditorio::create($request->all());        
         return $this->sendResponse($auditorio->toArray(), 'Auditorio creado con éxito');

    }

    /**
     * Lista un auditorio en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $auditorio = Auditorio::find($id);


        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }


        return $this->sendResponse($auditorio->toArray(), 'Auditorio devuelto con éxito');
    }

  
   /**
     * Actualiza un elemeto de la tabla auditorio 
     *
     * [Se filtra por el ID]
     *
     *@bodyParam nombre string required Nombre del Auditorio.
     *@bodyParam ciudad string required Ciudad del auditorio.
     *@bodyParam departamento string required Departamento del auditorio.
     *@bodyParam pais string required Pais del auditorio.
     *@bodyParam direccion string Direccion del auditorio.
     *@bodyParam longitud int Coordenada: Longitud.
     *@bodyParam latitud int Coordenada: Latitud.
     *@bodyParam aforo int Aforo.
     *@response{
     *       "nombre" : "Auditorio GOLD",
     *       "ciudad" : "Raccon City",
     *       "departamento": "Departament 1",
     *       "pais": "US",
     *       "direccion": "Street 1-56",
     *       "longitud": 222,
     *       "latitud": 765,
     *       "aforo": 1000
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auditorio  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        //
         $input = $request->all();


        $validator = Validator::make($request->all(), [
           'nombre' => 'required',   
            'ciudad' => 'required',
            'departamento' => 'required',
            'pais' => 'required',
            'direccion' => 'required',
            'longitud' => 'numeric',
            'latitud' => 'numeric',
            'aforo' => 'integer'           
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $auditorio = Auditorio::find($id);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }
        
        $auditorio->nombre = $input['nombre'];
        $auditorio->ciudad = $input['ciudad'];
        $auditorio->departamento = $input['departamento'];
        $auditorio->pais = $input['pais'];
        $auditorio->direccion = $input['direccion'];        
        if (!is_null($request->input('latitud'))) 
            $auditorio->latitud = $input['latitud'];
        if (!is_null($request->input('longitud'))) 
            $auditorio->longitud = $input['longitud'];
        if (!is_null($request->input('aforo'))) 
            $auditorio->aforo = $input['aforo'];
         $auditorio->save();

        return $this->sendResponse($auditorio->toArray(), 'Auditorio actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla auditorio
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $auditorio =Auditorio::find($id);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }
        $auditorio->delete();


        return $this->sendResponse($auditorio->toArray(), 'Auditorio eliminado con éxito');
    }
}
