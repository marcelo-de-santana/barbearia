<?php

use \App\Http\Response;
use \App\Controller\Client;

//ROTA HOME
$obRouter->get('/home',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\Home::getHome($request));
    }
]);