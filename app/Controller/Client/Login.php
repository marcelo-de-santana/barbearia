<?php

namespace App\Controller\Client;

use \App\Utils\View;
use \App\Model\Entity\Client;
use \App\Session\Client\Login as SessionClientLogin;

class Login extends Page{

    /**
     * Método responsável por retornar a renderização da página de login cliente
     *
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request){
        //CONTEÚDO DA PÁGINA DE LOGIN
        return $content = View::render('client/login',[
            'status' => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        //return parent::getPage('Login | Cadastro',$content);
    }

    /**
     * Método responsável por retornar a mensagem de status da página register
     *
     * @param Request $request
     * @return string
     */
    private static function getStatus($request){
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if(!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch ($queryParams['status']){
            case 'created':
                return Alert::getSuccess('Cadastro efetuado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O e-mail já está sendo utilizado!');
                break;
            case 'error':
                return Alert::getError('E-mail ou senha inválidos!');
                break;
        }
    }

    /**
     * Método responsável por definir o login do cliente
     *
     * @param Request $request
     */
    public static function setLogin($request){
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //BUSCA O CLIENTE PELO E-MAIL
        $obClient = Client::getClientByEmail($email);
        if(!$obClient instanceof Client){
            $request->getRouter()->redirect('/?status=error');
        }

        //VERIFICA A SENHA DO CLIENTE
        if(!password_verify($senha,$obClient->senha)){
            $request->getRouter()->redirect('/?status=error');
        }

        //CRIA A SESSÃO DE LOGIN
        SessionClientLogin::login($obClient);

        //REDIRECIONA O CLIENTE PARA A AGENDA DE LISTAGEM
        $request->getRouter()->redirect('/home');
    }

    /**
     * Método responsável por deslogar o cliente
     *
     * @param Request $request
     */
    public static function setLogout($request){
        //DESTROI A SESSÃO DE LOGIN
        SessionClientLogin::logout();

        //REDIRECIONA O CLIENTE PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/');
    }

}