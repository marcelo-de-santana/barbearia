<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DA AGENDA
$obRouter->get('/admin/agenda',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Agenda::getAgenda($request));
    }
]);

//ROTA DE CADASTRO DE NOVO HORÁRIO NA AGENDA
$obRouter->get('/admin/agenda/new/{id}/{horario}/{data}',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id,$horario,$data){
        return new Response(200,Admin\Agenda::getNewAgenda($request,$id,$horario,$data));
    }
]);

//ROTA DE CADASTRO DE NOVO HORÁRIO NA AGENDA (POST)
$obRouter->post('/admin/agenda/new/{id}/{horario}/{data}',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id,$horario,$data){
        return new Response(200,Admin\Agenda::setNewAgenda($request,$id,$horario,$data));
    }
]);

//ROTA DE DELEÇÃO DE HORÁRIO NA AGENDA
$obRouter->get('/admin/agenda/delete/{id}/{horario}/{data}',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id,$horario,$data){
        return new Response(200,Admin\Agenda::getDeleteAgenda($request,$id,$horario,$data));
    }
]);

//ROTA DE DELEÇÃO DE HORÁRIO NA AGENDA (POST)
$obRouter->post('/admin/agenda/delete/{id}/{horario}/{data}',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id,$horario,$data){
        return new Response(200,Admin\Agenda::setDeleteAgenda($request,$id,$horario,$data));
    }
]);
