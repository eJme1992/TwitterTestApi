<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\File;

class UserController extends Controller
{


  /* ###################### LOGIN #######################
       ###################################################
       ##################################################
       ################################################
       ###############################################
       ######################### */


       public function login(Request $request)
       {
           $json = $request->input('json', null);
           $params_array = json_decode($json, true);
           //return var_dump($params_array);
           if (empty($params_array))
           {
            $data = array(
                'status' => 'error',
                'code'   =>  404,
                'msj'    => 'El usuario no se a podido logear correctamente',
                'errors' => 'El Json no a sido escrito correctamente'
            );
            return response()->json($data, $data['code']);
           }
               $params_array = array_map('trim', $params_array);
               $validador = \Validator::make($params_array,
                   [
                    'email'      => 'required',
                    'password'   => 'required',
                   ]
               );
               //Segun la respuesta continuo o no
               if ($validador->fails())
               {
                   $data = array(
                       'status' => 'error',
                       'code' => 404,
                       'msj' => 'El usuario no se a podido logear correctamente',
                       'errors' => $validador->errors()
                   );
                   return response()->json($data, $data['code']);
               }

               // Inicio Logia Login
               // Decifro la contraseña
                $password = hash('sha256',$params_array['password']);
               // Llamo a mi clase Logeo por JwtAuth la cual hace la logica en db
                $JwtAuth = new \JwtAuth();
               // Paso los datos
                $user = $JwtAuth->signup($params_array['email'],$password);
               // Datos de salida
                $data = array(
                       'status' =>  $user['status'],
                       'code'   =>  200,
                       'msj'    =>  $user['msj'],
                       'data'   =>  $user
                );

           return response()->json($data, $data['code']);
       }

     /* ###################### REGISTRER ###################
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */
      public function register(Request $request)
       {
           // Recibe Json
           $json = $request->input('json', null);
           // Decodifica el json
           $params_array = json_decode($json, true);
           //return var_dump($params_array);
           //Varifico que los parametros esten llenos
           if (empty($params_array))
           {
            $data = array(
                'status' => 'error',
                'code'   =>  404,
                'msj'    => 'el usuario no a sido creado ',
                'errors' => 'El Json no a sido escrito correctamente'
            );
            return response()->json($data, $data['code']);
           }
               //Limpio los datos de front (Saca espacios)
               $params_array = array_map('trim', $params_array);
               //Valida datos de forma automatica y responde en una variable
               $validador = \Validator::make($params_array,
                   [
                    'first_name'  => 'required',
                    'last_name'   => 'required',
                    'email'       => 'required|email|unique:users',
                    'username'       => 'required|unique:users',
                     // unique:users valida automaticamente que usuario no esta repetido
                    'password'    => 'required',
                   ]
               );
               if ($validador->fails())
               {
                  $data = array(
                       'status' =>  'error',
                       'code'   =>  404,
                       'msj'    =>  'el usuario no a sido creado',
                       'errors' => $validador->errors()
                   );
                   return response()->json($data, $data['code']);
               }
                   // Cifrado de contraseñas
                   $password = hash('sha256',$params_array['password']);
                   $user     = new User;
                   // Datos recibidos por el front
                   $user->first_name            =  $params_array['first_name'];
                   $user->last_name             =  $params_array['last_name'];
                   $user->email                 =  $params_array['email'];
                   $user->username              =  $params_array['username'];
                   $user->password              =  $password;
                   $user->slug                  =  str_shuffle($user->name.date("Ymd").uniqid());
                   $user->save();
                   // Datos de salida
                   $data = array(
                       'status' => 'succes',
                       'code' => 200,
                       'msj' => 'el usuario ha sido creado',
                   );
                  return response()->json($data, $data['code']);
       }

       /* ###################### EDICION DEL PERFIL ##########
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */


   public function update(Request $request)
       {
           $json         = $request->input('json', null);
           $params_array = json_decode($json, true);
           if (empty($params_array))
           {
            $data = array(
                'status' => 'error',
                'code'   =>  404,
                'msj'    => 'Los datos de la peticion estan dañados',
            );
            return response()->json($data, $data['code']);
           }
               $params_array = array_map('trim', $params_array);

               // Sacamos el id del token
               $user = $jwtAuth->checkToken($token,true);

               $validador = \Validator::make($params_array,
                   [
                    'firt_name'     => 'alpha',
                    'last_name'   => 'alpha',
                    // unique:users valida automaticamente que usuario no esta repetido Si concateno con. hace un excecion con el mismo
                    'email'      => 'email|unique:users,email,'.$user->sub,
                    'username'     => 'numeric|unique:users,username,'.$user->sub,
                   ]
               );
               //Segun la respuesta continuo o no
               if ($validador->fails())
               {
                   $data = array(
                       'status' => 'error',
                       'code'   =>  404,
                       'msj'    => 'El Formulario no a sido llenado correctamente',
                       'errors' => $validador->errors()
                   );
                   return response()->json($data, $data['code']);
               }
               $user = User::where('id',$user->sub)->first();

               //  Descartos datos de la db que no quiero actualizar aunque vengan
               unset($params_array['id']);
               unset($params_array['email_verified_at']);
               unset($params_array['password']);
               unset($params_array['slug']);
               unset($params_array['remember_token']);
               unset($params_array['created_at']);

              $user->update($params_array);

              $data = array(
                       'status' => 'succes',
                       'code'   => 200,
                       'data'   =>  $user,
                       'msj'    => 'La edicion a sido hecha con exito',
                );

                return response()->json($data, $data['code']);
       }

       /* ########## SUBIDA DE ARCHIVOS DE USUARIOS #############
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */
   public function fileUser(Request $request, $tipo, $post='')
       {
        $validador = \Validator::make($request->all(),
                   [
                    'file0'     => 'required|image|mimes:jpg,png,jpeg,gif'
                   ]
         );
        // Optiene datos del usuario que uso el metodo
        $jwtAuth = new \JwtAuth();
        $token   = $request->header('Authorization');
        $user    = $jwtAuth->checkToken($token,true);
        // Contiene la imagen
        $imagen = $request->file('file0');
        // Verifico que la imagen sea treu osea exista y !$validador->fails() valida a la vez
        if($imagen AND !$validador->fails()){
            // Crea el nombre de la imagen
            $image_name = time().$imagen->getClientOriginalName();
            // Guarda en el disco user dentro de la carpeta storage/ user la imagen
            \Storage::disk('users')->put($image_name,\File::get($imagen));

            $File                    = new File();
            $File->user_id           = $user->sub;
            $File->slug              = str_shuffle($image_name.$user->sub.date("Ymd").uniqid());;
            $File->name              = $image_name;
            $File->url               = 'users';
            $File->type              = $tipo;
            $File->state             = 1;
            if($File->type=='post'){
                $File->tweet_id      = $post;
            }
            $File->save();

            $data = array(
                       'status' => 'succes',
                       'code'   => 200,
                       'data'   => $File,
                       'msj'    => 'El archivo ha sido subido correctamente',
            );
            return response()->json($data, $data['code']);
        }

       $data = array(
                   'status' => 'error',
                   'code'   =>  404,
                   'msj'    => 'El archivo no a sido subido',
        );
        return response()->json($data, $data['code']);
       }



       /* ##### PETICIOS GENERAL DE ARCHIVOS DE USUARIOS #############
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */

       public function getFile($filename)
       {

              $isset = \Storage::disk('users')->exists($filename);
              if($isset){
                 $file = \Storage::disk('users')->get($filename);
                 return new Response($file,200);
              }

             $data = array(
                   'status' => 'error',
                   'code'   =>  404,
                   'msj'    => 'El archivo no a sido subido',
             );
             return response()->json($data, $data['code']);
       }
          /* ##### Peticio de archivo DE ARCHIVOS DE USUARIOS #############
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */
        public function getFileUser($slug_user)
       {
            $filename = File::where('slug',$slug_user)->first();

            if(is_object($filename)){
              $isset = \Storage::disk('users')->exists($filename->name);
              if($isset){
                 $file = \Storage::disk('users')->get($filename->name);
                 return new Response($file,200);
              }
            }
             $data = array(
                   'status' => 'error',
                   'code'   =>  404,
                   'msj'    => 'El archivo no a sido subido',
             );
             return response()->json($data, $data['code']);
       }
         /* ##### GET USUARIO ################################
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */
       public function getUsuario($slug_user)
       {

        $user = User::where('slug',$slug_user)->first();
        if (is_object($user)) {
              $data = array(
                   'status' => 'success',
                   'code'   =>  202,
                   'data'    => $user,
             );
        }else{
              $data = array(
                   'status' => 'error',
                   'code'   =>  404,
                   'msj'    => 'El Usuario no ha ido encontrado',
             );
        }
             return response()->json($data, $data['code']);
       }
       /* ##### GET USUARIO ################################
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */
          public function ValidateEmail($email)
          {

           $user = User::where('email',$email)->first();
           if (is_object($user)) {
                 $data = array(
                      'status' => 'success',
                      'code'   =>  202,
                      'data'   => false
                );
           }else{
                 $data = array(
                      'status' => 'success',
                      'code'   =>  202,
                      'msj'    => 'El Usuario no ha ido encontrado',
                      'data'   => true
                );
           }
                return response()->json($data, $data['code']);
          }

          /* ##### GET USUARIO ################################
          ###################################################
          ##################################################
          ################################################
          ###############################################
          ######################### */
          public function ValidateUserName($username)
          {

           $user = User::where('username',$username)->first();
           if (is_object($user)) {
                 $data = array(
                      'status' => 'success',
                      'code'   =>  202,
                      'data'   => false
                );
           }else{
                 $data = array(
                      'status' => 'success',
                      'code'   =>  202,
                      'msj'    => 'El Usuario no ha ido encontrado',
                      'data'   => true
                );
           }
                return response()->json($data, $data['code']);
          }

}
