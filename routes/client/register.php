<?php

use \App\Http\Response;
use \App\Controller\Client;

//ROTA DE CADASTRO
$obRouter->get('/register',[
    'middlewares' => [
        'required-client-logout'
    ],
    function($request){
        return new Response(200,Client\Register::getRegister($request));
    }
]);

//ROTA DE CADASTRO (POST)
$obRouter->post('/register',[
    'middlewares' => [
        'required-client-logout'
    ],
    function($request){
        return new Response(200,Client\Register::setRegister($request));
    }
]);

//ROTA DE EDIÇÃO DE CADASTRO
$obRouter->get('/agenda/register',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\Register::getEditRegister($request));
    }
]);

//ROTA DE EDIÇÃO DE CADASTRO
$obRouter->post('/agenda/register',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\Register::setEditRegister($request));
    }
]);