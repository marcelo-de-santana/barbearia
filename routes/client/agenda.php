<?php

use \App\Http\Response;
use \App\Controller\Client;

//ROTA AGENDA
$obRouter->get('/agenda',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\Agenda::getAgenda($request));
    }
]);

//ROTA CADASTRAR AGENDAMENTO (POST)
$obRouter->post('/agenda/new',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request){
        return new Response(200,Client\Agenda::getNewAgenda($request));
    }
]);

//ROTA CADASTRAR AGENDAMENTO
$obRouter->get('/agenda/{id}/{data}/{horario}/new',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request,$id,$data,$horario){
        return new Response(200,Client\Agenda::setNewAgenda($request,$id,$data,$horario));
    }
]);

//ROTA DELETAR AGENDAMENTO
$obRouter->get('/agenda/{data}/delete',[
    'middlewares' => [
        'required-client-login'
    ],
    function($request,$data){
        return new Response(200,Client\Agenda::setDeleteAgenda($request,$data));
    }
]);



