<?php
//for backend to call external apis 
namespace App\Services\Api;

class HttpClient
{
  public static function get(string $url): array
  {
    //to create a stream of context comming from the response of 3rd party api.
    $context = stream_context_create([
      'http' => [
        'method' => 'GET',
        'header' => 'Accept: application/json', // type of data you are asking back as
        'timeout' => 10, //if the api doesnt respond give up in 10 secs
      ]
    ]);
    $response =  @file_get_contents($url, false, $context);

    if($response === false) return ['error' => 'Request Failed'];

    //convert JSON sting to php array, true = associative array instead of object
    return json_decode($response, true);
  }
}