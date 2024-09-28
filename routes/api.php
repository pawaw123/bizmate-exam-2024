<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Secured the api key transfered it to env file.
Route::get('/weather', function() {
    $apiKey = config('services.openweathermap.key');
    $lat = request('lat');
    $lon = request('lon');
    $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$apiKey}");

    return $response->json();
});
