<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Usuario;
use App\Models\Rol;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use App\Notifications\SignupActivate;
use Socialite;

class UsuarioController extends BaseController 
{

    public $successStatus = 200;
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Usuario  $user
     * @return \Illuminate\Http\Response
     **/
    public function updateprofile($id, Request $request)
    {
        //
         $input = $request->all();

         $validator = Validator::make($input, [ 
            'nombre' => 'required',             
            'clave' => 'required|min:3',
            'c_clave' => 'required|min:3|same:password', 
            'identificacion' => 'required',
            'tipo_identificacion' => 'required',
            'direccion' => 'required',
            'ciudad' => 'required',
            'departamento' => 'required',
            'telefono' => 'required', 
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }

        $users->nombre = $input['nombre'];
        $users->clave = $input['clave'];
        $users->identificacion = $input['identificacion'];
        $users->tipo_identificacion = $input['tipo_identificacion'];    
        $users->direccion = $input['direccion'];
        $users->ciudad = $input['ciudad'];
        $users->departamento = $input['departamento'];
        $users->telefono = $input['telefono'];             
        $users->save();
        

        return $this->sendResponse($users->toArray(), 'Usuario actualizado con éxito');

    }


    public function cambioclave(Request $request)
    {
        $validator  = Validator::make($request->all(), [
             'mypassword' => 'required|min:6',
             'password' => 'required|min:6',
             'c_password' => 'required|min:6|same:password',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $datos = $request->all();
        if(Hash::check($datos['mypassword'], Auth::user()->password)){
            $passhash = bcrypt($datos['password']); 
            \DB::table('usuario')
                 ->where('email', '=', Auth::user()->email)
                 ->update(['password' => $passhash]);
            $user = Usuario::find(Auth::user()->email);
            return $this->sendResponse($user->toArray(), 'Clave del usuario actualizada con éxito');

        }else{
            return $this->sendError('La contraseña actual no coincide.');
        }
    }

/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request){ 

        
        $validator  = Validator::make($request->all(), [
             'email' => 'email|required|string',
             'password' => 'required|min:6|string'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $datos = $request->all();

        $userdata = [
            'email'   => $datos['email'],
            'password' => $datos['password'],
            'active' => 1,
            'deleted_at' => null,
        ]; 
        
        if(Auth::attempt($userdata)){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApi')-> accessToken; 
            $success['token_type'] = 'Bearer';
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
       $validator = Validator::make($request->all(), [ 
            'nombre' => 'required', 
            'email' => 'required|email', 
            'password' => 'required|min:6',
            'c_password' => 'required|min:6|same:password', 
            'identificacion' => 'required',
            'tipo_identificacion' => 'required',
            'direccion' => 'required',
            'ciudad' => 'required',
            'departamento' => 'required',
            'telefono' => 'required',
            'id_rol' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $rol = Rol::find($request->input('id_rol'));
        if (is_null($rol)) {            
            return response()->json(['error'=>'El Rol indicado no existe'], 401);
        }

        $user_search = Usuario::find($request->input('email'));
        if (!is_null($user_search)) {            
            return response()->json(['error'=>'El Usuario ya se encuentra registrado'], 401);
        }


        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $input['activation_token'] = str_random(60);
        $user = Usuario::create($input); 
        $user->notify(new SignupActivate($user));
        $success['token'] =  $user->createToken('MyApi')-> accessToken; 
        $success['nombre'] =  $user->nombre;
        $success['token_type'] = 'Bearer';
        return response()->json(['success'=>$success], $this-> successStatus); 
    }


    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return $this->sendError('Ocurrio un error al validar el provider');
        }        
        
        $authUser = Usuario::find($user->email); 
        if($authUser){

            $userdata = [
                'email'   => $authUser['email'],
                'password' => $authUser['password'],
                'active' => 1,
                'deleted_at' => null,
            ]; 

            if(Auth::attempt($userdata)){ 
                $usuario = Auth::user(); 
                $success['token'] =  $usuario->createToken('MyApi')-> accessToken; 
                $success['token_type'] = 'Bearer';
                return response()->json(['success' => $success], $this-> successStatus); 
            }else{ 
                return response()->json(['error'=>'Unauthorised'], 401); 
            } 

        }else{

            $usuario = Usuario::create([
                'nombre' => $user->name, 
                'email' => $user->email, 
                'password' => bcrypt(123456789);,            
                'identificacion' => " ",
                'tipo_identificacion' => 1,
                'direccion' => " ",
                'ciudad' => " ",
                'departamento' => " ",
                'telefono' => " ",
                'id_rol' => 1,
                'active' => 1,
                'provider' => strtoupper($provider),
                'provider_id' => $user->id, 
            ]); 

            $success['token'] =  $usuario->createToken('MyApi')-> accessToken; 
            $success['nombre'] =  $usuario->nombre;
            $success['token_type'] = 'Bearer';
            return response()->json(['success'=>$success], $this-> successStatus);
        }

        


    }


    public function signupActivate($token)
    {
        $user = Usuario::where('activation_token', $token)->first();
        if (!$user) {
            return $this->sendError('Este token de activación no es válido.');            
        }
        $user->active = true;
        $user->activation_token = '';
        $user->save();
        return $this->sendResponse($user->toArray(), 'Cuenta activada con éxito');
    }
    
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function detailsuser() 
    { 
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus); 
    }


    /** 
     * lista usuarios api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function listausuarios(){
        $user = Usuario::with('rol')->paginate(15);
        $lista_usuarios = compact('user'); 

        return $this->sendResponse($lista_usuarios, 'Listado de usuarios devueltos con éxito');

    }


    /** 
     * compras realizadas usuarios api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function comprasrealizadas($id){

        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }


        $compras = Usuario::with('devolucions')
                        ->with('boleta_reservas')
                        ->with('palco_reservas')
                        ->where('usuario.email',$id)->get();                
        $compras_p = compact('compras'); 

        return $this->sendResponse($compras_p, 'Compras realizadas devueltas con éxito');

    }


     /** 
     * temporadas compradas usuarios api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function temporadascompradas($id){

        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }


        $temporadas = Usuario::with('venta_temporadas')
                        ->where('usuario.email',$id)->get();
        $temporadas_p = compact('temporadas'); 

        return $this->sendResponse($temporadas_p, 'Temporadas compradas devueltas con éxito');

    }


     /** 
     * reservas realizadas usuarios api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function reservas($id){

        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }

        $reservas = Usuario::with('vents')
                    ->with('devolucions')
                    ->where([ ['usuario.email','=',$id] ])
                    ->get();

        $reservas_p = compact('reservas'); 

        return $this->sendResponse($reservas_p, 'Reserevas realizadas devueltas con éxito');

    }



    public function logout(Request $request)
    {
       /*Logout all devices */ 
       $accessToken = Auth::user()->token();
        
       \DB::table('oauth_access_tokens')
            ->where('user_id', $accessToken->user_id)
            ->update([
                'revoked' => true
            ]);
        $accessToken->revoke();
        return response()->json(['data' => 'Usuario ha cerrado sesión.'], 200);       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $usuario = Usuario::find($request->input('email'));
        $usuario->delete();
        return $this->sendResponse($usuario->toArray(), 'Usuario eliminado con éxito');
    } 

}