<?php

namespace App\Http\Middleware;

use \App\Session\Client\Login as SessionClientLogin;

class RequireClientLogin{

    /**
     * Método responsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){
        //VERIFICA SE O CLIENTE ESTÁ LOGADO
        if(!SessionClientLogin::isLogged()){
            $request->getRouter()->redirect('/');
        }
        //CONTINUA A EXECUÇÃO
        return $next($request);
    }
}