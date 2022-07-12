<?php

namespace App\Session\Client;

class Login{

    /**
     * Método responsável por iniciar a sessão
     *
     */
    private static function init(){
        //VERIFICA SE A SESSÃO NÃO ESTÁ ATIVA
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Método responsável por criar o login do cliente
     *
     * @param Client $obClient
     * @return boolean
     */
    public static function login($obClient){
        //INICIA A SESSÃO
        self::init();

        //DEFINE A SESSÃO DO CLIENTE
        $_SESSION['client'] = [
            'id'    => $obClient->id,
            'nome'  => $obClient->nome,
            'email' => $obClient->email
        ];
    }

    /**
     * Método responsável por verificar se o cliente está logado
     *
     * @return boolean
     */
    public static function isLogged(){
        //INICIA A SESSÃO
        self::init();

        //RETORNA A VERIFICAÇÃO
        return isset($_SESSION['client']['id']);
    }

    /**
     * Método responsável por executar o logout do client
     *
     * @return boolean
     */
    public static function logout(){
        //INICIA A SESSÃO
        self::init();

        //DESLOGA O client
        unset($_SESSION['client']);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por retornar os dados do cliente
     *
     * @return int
     */
    public static function getClient(){
        //INICIA A SESSÃO
        self::init();

        //RETORNA OS DADOS DA SESSÃO
        return $_SESSION['client'];

    }
}