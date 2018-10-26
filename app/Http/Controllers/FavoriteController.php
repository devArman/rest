<?php

namespace App\Http\Controllers;

use App\FavoriteMovies;
use GuzzleHttp;
use Response;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['results'] = [];

        //get all favorite movies from db
        $favorites = FavoriteMovies::get();

        if(!empty($favorites)){
            $data['total_results'] = count($favorites);

            //get favorite movies details
            foreach ($favorites as $favorite){
                $client = new GuzzleHttp\Client();

                $result =$client->get(
                    env('MOVIE_API_URL').'movie/'.$favorite->favorite_id.'?'.'api_key='.env('MOVIE_API_KEY'),
                    [
                        'headers' => ['Accept: application/json'],
                    ])->getBody();
                array_push($data['results'],json_decode($result));
            }

            return Response::make(json_encode([
                'success' => true,
                'data' => $data,
            ]), 200);
        }

        return Response::make(json_encode([
            'success' => false,
            'data' => json_decode(['message' => 'You have not favorite movies']),
        ]), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //checking is movie id exists in favorites table
        $favorite = FavoriteMovies::where('favorite_id',$id)->first();

        if (!empty($favorite))
            return Response::make(json_encode([
                'success' => false,
                'data' => ["message" => "You have already set this movie as favorite"],
            ]), 200);

        FavoriteMovies::create(['favorite_id' => $id]);

        $client = new GuzzleHttp\Client();

        $result =$client->get(
            env('MOVIE_API_URL').'movie/'.$id.'?'.'api_key='.env('MOVIE_API_KEY'),
            [
                'headers' => ['Accept: application/json'],
            ])->getBody();

        return Response::make(json_encode([
            'success' => false,
            'data' => json_decode($result),
        ]), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $favorite = FavoriteMovies::where('favorite_id',$id)->first();

        if (empty($favorite))
            return Response::make(json_encode([
                'success' => false,
                'data' => ["message" => "This movie is not your favorite"],
            ]), 200);


        return Response::make(json_encode([
            'success' => FavoriteMovies::where('favorite_id',$id)->delete(),
            'data' => ['message' => 'Successfully deleted'],
        ]), 200);
    }
}
