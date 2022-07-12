<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE BARBEIROS
$obRouter->get('/admin/barbers',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Barber::getBarbers($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO BARBEIRO
$obRouter->get('/admin/barbers/new',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Barber::getNewBarber($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO BARBEIRO (POST)
$obRouter->post('/admin/barbers/new',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Barber::setNewBarber($request));
    }
]);

//ROTA DE EDIÇÃO DE UM BARBEIRO
$obRouter->get('/admin/barbers/{id}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Barber::getEditBarber($request,$id));
    }
]);

//ROTA DE EDIÇÃO DE UM BARBEIRO (POST)
$obRouter->post('/admin/barbers/{id}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Barber::setEditBarber($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM BARBEIRO
$obRouter->get('/admin/barbers/{id}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Barber::getDeleteBarber($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM USUÁRIO (POST)
$obRouter->post('/admin/barbers/{id}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Barber::setDeleteBarber($request,$id));
    }
]);

//ROTA DE HORÁRIOS DOS BARBEIROS
$obRouter->get('/admin/barbers/{id}/hours',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Barber::getEditHours($request,$id));
    }
]);

//ROTA DE HORÁRIOS DOS BARBEIROS (POST)
$obRouter->post('/admin/barbers/{id}/hours',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Barber::setNewHour($request,$id));
    }
]);

//ROTA DE DELEÇÃO DOS HORÁRIOS DOS BARBEIROS
$obRouter->get('/admin/barbers/hours/delete/{id}/{horario}',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request,$id,$horario){
        return new Response(200,Admin\Barber::setDeleteHour($request,$id,$horario));
    }
]);

