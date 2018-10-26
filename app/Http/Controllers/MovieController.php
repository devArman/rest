<?php

namespace App\Http\Controllers;

use GuzzleHttp;
use Response;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($query)
    {
        //get movies by query from API
        $client = new GuzzleHttp\Client();

        $result =$client->get(
            env('MOVIE_API_URL').'search/movie?'.'api_key='.env('MOVIE_API_KEY')."&query=".$query,
            [
                'headers' => ['Accept: application/json'],
            ])->getBody();

        return Response::make(json_encode([
            'success' => true,
            'data' => json_decode($result),
        ]), 200);
    }

}
