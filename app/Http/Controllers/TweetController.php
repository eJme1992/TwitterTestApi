<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\Hashtag;
use App\Hashtag_tweet;
use Illuminate\Http\Request;
use Exception;

class TweetController extends Controller
{


    public function list(Request $request)
    {
        $Tweet = Tweet::orderBy('created_at')->paginate();
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
              'msj'    => 'el usuario no a sido creado ',
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
                     'msj'    =>  'el usuario no a sido creado',
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
                     'msj' => 'el usuario ha sido creado',
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
