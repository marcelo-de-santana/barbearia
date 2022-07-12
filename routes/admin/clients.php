<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE CLIENTES
$obRouter->get('/admin/clients',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Client::getClients($request));
    }
]);

//ROTA DE CADASTRO DE CLIENTES
$obRouter->get('/admin/clients/new',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Client::getNewClient($request));
    }
]);

//ROTA DE CADASTRO DE CLIENTES
$obRouter->post('/admin/clients/new',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Client::setNewClient($request));
    }
]);

//ROTA DE EDIÇÃO DE CLIENTES
$obRouter->get('/admin/clients/{id}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Client::getEditClient($request,$id));
    }
]);

//ROTA DE EDIÇÃO DE CLIENTES (POST)
$obRouter->post('/admin/clients/{id}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Client::setEditClient($request,$id));
    }
]);

//ROTA DE DELEÇÃO DE CLIENTES
$obRouter->get('/admin/clients/{id}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Client::getDeleteClient($request,$id));
    }
]);

//ROTA DE DELEÇÃO DE CLIENTES (POST)
$obRouter->post('/admin/clients/{id}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Client::setDeleteClient($request,$id));
    }
]);