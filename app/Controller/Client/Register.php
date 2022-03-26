<?php

namespace App\Controller\Client;

use \App\Utils\View;
use \App\Session\Client\Login as SessionClient;
use \App\Model\Entity\Client;

class Register extends Page{

    /**
     * Método responsável por retornar a renderização da página de cadastro
     *
     * @param Request $request
     * @return string
     */
    public static function getRegister($request){
        //CONTEÚDO DA PÁGINA DE CADASTRO
        return $content = View::render('client/modules/register/index',[]);
    }
    
    /**
     * Método responsável por definir o cadastro do cliente
     *
     * @param Request $request
     */
    public static function setRegister($request){
        //POST VARS
        $postVars = $request->getPostVars();
        $nome  = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';
        $tel   = $postVars['tel'] ?? '';
        $tel == '' ? $tel = 'nda' : $tel;

        //BUSCA O CLIENTE PELO E-MAIL
        $obClient = Client::getClientByEmail($email);
        if($obClient instanceof Client){
            $request->getRouter()->redirect('/?status=duplicated');
        }

        //NOVA INSTÂNCIA DE CLIENTE
        $obClient = new Client;
        $obClient->nome = $nome;
        $obClient->email = $email;
        $obClient->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obClient->telefone = $tel;
        $obClient->cadastrar();

        //REDIRECIONA O CLIENTE
        $request->getRouter()->redirect('/?status=created');
    }

    /**
     * Método responsável por retonar a mensagem de status
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
                return Alert::getSuccess('Atendimento marcado com sucesso!');
                break;
            case 'erroPassword':
                return Alert::getError('Senha atual incorreta!');
                break;
            case 'erroPasswordExist':
                return Alert::getError('Nova senha não pode ser igual a atual!');
                break;
            case 'erroPasswordNotConfer':
                return Alert::getError('Novas senha incorretas!');
                break;
        }
    }

    /**
     * Método responsável retornar a página de atualização de cadastro
     *
     * @param Request $request
     * @return string
     */
    public static function getEditRegister($request){
        //DADOS DO CLIENTE
        $obClient = SessionClient::getClient();
        $id = $obClient['id'];

        $obClient = Client::getClients('id ='.$id,null,null,'email,nome,telefone')->fetchObject();
                
        //CONTEÚDO DA PÁGINA
        $content = View::render('client/modules/register/form',[
            'status'    => self::getStatus($request),
            'nome'      => $obClient->nome,
            'email'     => $obClient->email,
            'telefone'  => $obClient->telefone

        ]);
            
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Meus dados',$content,'client');

    }

    /**
     * Método responsável por atualizar os dados do cliente
     *
     * @param Request $request
     */
    public static function setEditRegister($request){
        //DADOS DO CLIENTE
        $obClient = SessionClient::getClient();
        $id = $obClient['id'];
        $email = $obClient['email'];
        
        //POST VARS
        $postVars = $request->getPostVars();

        $nome = $postVars['nome'];
        $telefone = $postVars['telefone'] ?? 'nda';
        $senhaAtual = $postVars['senha'];
        $senhaNova = $postVars['nova-senha'] ?? ''; 
        $senhaNovaConfirma = $postVars['nova-senha-confirma'] ?? '';

        //CONFERE A SENHA ATUAL
        $obClient = Client::getClientByEmail($email);
        if(!password_verify($senhaAtual,$obClient->senha)){
            $request->getRouter()->redirect('/agenda/register?status=erroPassword');
        }

        //CONFERE SENHA ATUAL E NOVA
        if($senhaAtual == $senhaNova){
            $request->getRouter()->redirect('/agenda/register?status=erroPasswordExist');
        }

        //CONFERE SENHA NOVA E CONFIRMAÇÃO DE NOVA SENHA
        if($senhaNova != $senhaNovaConfirma){
            $request->getRouter()->redirect('/agenda/register?status=erroPasswordNotConfer');
        }
        
        //SENHAS
        $senha = $senhaNova != '' ? $senhaNova : $senhaAtual;

        //NOVA INSTÂNCIA DE CLIENTE
        $obClient = new Client;
        $obClient->id = $id;
        $obClient->nome = $nome;
        $obClient->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obClient->telefone = $telefone;        
        $obClient->atualizar();

        $request->getRouter()->redirect('/agenda?status=updated');
    }
}