<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\Hashtag;
use App\Hashtag_tweet;
use App\File;
use Illuminate\Http\Request;
use Exception;

class TweetController extends Controller
{


    public function list(Request $request)
    {
        $Tweet = Tweet::orderBy('created_at', 'desc')->paginate(4);
        $data = array(
            'status' => 'succes',
            'code' => 200,
            'data' => $Tweet
        );
       return response()->json($data, $data['code']);
    }


    public function hashtagcreate($hashtags)
    {
        if(is_array($hashtags)){
        foreach($hashtags as $key){
           $hashtag = Hashtag::where('title',$key)->first();
           if($hashtag==null){
             $hashtag = new Hashtag();
             $hashtag->title = $key;
             $hashtag->save();
           }
        }
      }
    }

    public function hashtagassociate($hashtags,$tweet)
    {
        if(is_array($hashtags)){
        foreach($hashtags as $key){
           $hashtag = Hashtag::where('title',$key)->first();
           if($hashtag!==null){
             $Hashtag_tweet = new Hashtag_tweet();
             $Hashtag_tweet->hashtag_id = $hashtag->id;
             $Hashtag_tweet->tweet_id = $tweet->id;
             $Hashtag_tweet->save();
           }
        }
      }
    }


    public function store(Request $request)
    {

         $json = $request->input('json', null);
         $params_array = json_decode($json, true);
         if (empty($params_array))
         {
          $data = array(
              'status' => 'error',
              'code'   =>  404,
              'msj'    => 'el Tweet no a sido creado ',
              'errors' => 'El Json no a sido escrito correctamente'
          );
          return response()->json($data, $data['code']);
         }

             $validador = \Validator::make($params_array,
                 [
                  'tweet'  => 'required',
                 ]
             );
             if ($validador->fails())
             {
                $data = array(
                     'status' =>  'error',
                     'code'   =>  404,
                     'msj'    =>  'el Tweet no a sido creado',
                     'errors' => $validador->errors()
                 );
                 return response()->json($data, $data['code']);
             }

                 $jwtAuth = new \JwtAuth();
                 $token   = $request->header('Authorization');
                 $user    = $jwtAuth->checkToken($token,true);

                 $tweet     = new Tweet();
                 // Datos recibidos por el front
                 $tweet->content            =  $params_array['tweet'];
                 $tweet->user_id            =  $user->sub;
                 $tweet->slug               =  str_shuffle('tweet'.date("Ymd").uniqid());
                 $tweet->save();

                 $this->hashtagcreate($params_array['hashtag']);
                 $this->hashtagassociate($params_array['hashtag'],$tweet);
                 // Datos de salida
                 $data = array(
                     'status' => 'succes',
                     'code' => 200,
                     'msj' => 'el Tweet ha sido creado',
                     'id'  => $tweet->id
                 );
                return response()->json($data, $data['code']);
    }

    public function file(Request $request, $post)
    {
     $validador = \Validator::make($request->all(),
                [
                 'image'     => 'required|image|mimes:jpg,png,jpeg,gif'
                ]
      );
     // Optiene datos del usuario que uso el metodo
     $jwtAuth = new \JwtAuth();
     $token   = $request->header('Authorization');
     $user    = $jwtAuth->checkToken($token,true);
     // Contiene la imagen
     $imagen = $request->file('image');

     // Verifico que la imagen sea treu osea exista y !$validador->fails() valida a la vez
     if($imagen AND !$validador->fails()){
         // Crea el nombre de la imagen
         $image_name = time().$imagen->getClientOriginalName();
         // Guarda en el disco user dentro de la carpeta storage/ user la imagen
         \Storage::disk('users')->put($image_name,\File::get($imagen));

         $File                    = new File();
         $File->user_id           = $user->sub;
         $File->slug              = str_shuffle($image_name.$user->sub.date("Ymd").uniqid());
         $File->name              = $image_name;
         $File->url               = 'users';
         $File->type              = 'post';
         $File->state             = 1;
         $File->tweet_id          = $post;
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







    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
