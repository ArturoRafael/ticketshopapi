<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Input;

/**
 * @group Administración de Cliente
 *
 * APIs para la gestion de cliente
 */
class ClienteController extends BaseController
{
    /**
     * Lista de la tabla cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $cliente = Cliente::paginate(15);

        return $this->sendResponse($cliente->toArray(), 'Clientes devueltos con éxito');
    }

    /**
     * Agrega un nuevo elemento a la tabla cliente
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
       $validator = Validator::make($request->all(), [
            'Identificacion'=> 'required' ,
            'tipo_identificacion' => 'required|boolean',
            'nombrerazon' => 'required',
            'direccion' => 'required',
            'tipo_cliente' => 'required|boolean',
            'email' => 'required|email',
            'telefono' => 'required',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $cliente = Cliente::create($request->all());        
        return $this->sendResponse($cliente->toArray(), 'Cliente creado con éxito');
    }

    /**
     * Lista un cliente en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $cliente = Cliente::find($id);
        if (is_null($cliente)) {
            return $this->sendError('Cliente no encontrado');
        }
        return $this->sendResponse($cliente->toArray(), 'Cliente devuelto con éxito');
    }

     /**
     * Actualiza un elemeto de la tabla cliente 
     *
     * [Se filtra por el ID]
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'Identificacion'=> 'required' ,
            'tipo_identificacion' => 'required|boolean',
            'nombrerazon' => 'required',
            'direccion' => 'required',
            'tipo_cliente' => 'required|boolean',
            'email' => 'required|email',
            'telefono' => 'required',          
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $cliente = Cliente::find($id);
        if (is_null($cliente)) {
            return $this->sendError('Cliente no encontrado');
        }
        $cliente->Identificacion = $input['Identificacion'];
        $cliente->tipo_identificacion = $input['tipo_identificacion'];
        $cliente->nombrerazon = $input['nombrerazon'];
        $cliente->direccion = $input['direccion']; 
        $cliente->ciudad = $input['ciudad'];
        $cliente->departamento = $input['departamento'];   
        $cliente->tipo_cliente = $input['tipo_cliente'];
        $cliente->email = $input['email']; 
        $cliente->telefono = $input['telefono'];        
        $cliente->save();

        return $this->sendResponse($cliente->toArray(), 'Cliente actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla cliente
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {

            $cliente =Auditorio::find($id);
            if (is_null($cliente)) {
                return $this->sendError('Cliente no encontrado');
            }
            $cliente->delete();
            return $this->sendResponse($cliente->toArray(), 'Cliente eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Cliente no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }


        
    }
}
