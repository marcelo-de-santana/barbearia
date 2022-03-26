<?php

use \App\Http\Response;
use \App\Controller\Client;

//ROTA DE LOGIN
$obRouter->get('/',[
    'middlewares' => [
        'required-client-logout'
    ],
    function($request){
        return new Response(200,Client\Login::getLogin($request));
    }
]);

//ROTA DE LOGIN (POST)
$obRouter->post('/',[
    'middlewares' => [
        'required-client-logout'
    ],
    function($request){
        return new Response(200,Client\Login::setLogin($request));
    }
]);

//ROTA DE LOGOUT
$obRouter->get('/logout',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\Login::setLogout($request));
    }
]);