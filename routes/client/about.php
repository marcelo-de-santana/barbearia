<?php

use \App\Http\Response;
use \App\Controller\Client;

//ROTA AGENDA
$obRouter->get('/agenda/about',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\About::getAbout($request));
    }
]);