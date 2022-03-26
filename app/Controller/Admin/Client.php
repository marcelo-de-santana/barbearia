<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Client as EntityClient;
use \WilliamCosta\DatabaseManager\Pagination;

class Client extends Page{

    /**
     * Método responsável por retornar o conteúdo da página de clientes
     *
     * @param Request $request
     * @return string
     */
    public static function getClients($request){
        //RENDERIZAÇÃO DA PÁGINA
        $content = View::render('/admin/modules/clients/index',[
            'status'        => self::getStatus($request),
            'itens'         => self::getClientItems($request,$obPagination),
            'pagination'    => parent::getPagination($request,$obPagination),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Clientes | Listagem',$content,'clients');
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
                return Alert::getSuccess('Cliente cadastrado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Cliente atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Cliente excluído com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('Email já cadastrado!');
                break;
        }
    }

    /**
     * Método responsável por obter a renderização dos itens de clientes para a página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getClientItems($request,&$obPagination){
        //BARBEIROS
        $itens = '';
        
        //QUANTIDADE TODAL DE REGISTRO
        $quantidadeTotal = EntityClient::getClients(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂNCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,7);

        //RESULTADOS DA PÁGINA
        $results = EntityClient::getClients(null,'id ASC',$obPagination->getLimit());
        
        //RENDERIZA O ITEM
        while($obClient = $results->fetchObject(EntityClient::class)){
            $itens .= View::render('admin/modules/clients/item',[
                'id'        => $obClient->id,
                'nome'      => $obClient->nome,
                'email'     => $obClient->email,
                'telefone'  => $obClient->telefone,
            ]
            );               
        }

        //RETORNA OS BARBEIROS 
        return $itens;
    }

    /**
     * Método responsável por renderizar a página de cadastro de cliente
     *
     * @param Request $request
     * @return string
     */
    public static function getNewClient($request){
        //CONTEÚDO DA PÁGINA
        $content = View::render('admin/modules/clients/form',[
            'title'     => 'Cadastrar cliente',
            'status'    => '',
            'nome'      => '',
            'email'     => '',
            'telefone'  => ''
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar cliente',$content,'clients');
    }

    /**
     * Método responsável por cadastrar novo cliente
     *
     * @param Request $request
     */
    public static function setNewClient($request){
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $telefone = $postVars['telefone'] == '' ? 'nda' : $postVars['telefone'];
        $senha = $postVars['senha'] ?? '';


        //NOVA INSTÂNCIA DE CLIENTE
        $obClient = new EntityClient;
        $obClient->nome = $nome;
        $obClient->email = $email;
        $obClient->telefone = $telefone;
        $obClient->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obClient->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/clients?status=created');
    }

    /**
     * Método responsável por retornar a página de edição de um cliente
     *
     * @param Request $request
     * @return string
     */
    public static function getEditClient($request,$id){
        //OBTÉM OS DADOS CLIENTE NO BANCO DE DADOS
        $obClient = EntityClient::getClients('id='.$id,null,null,'*')->fetchObject();

        //CONTEÚDO DA PÁGINA DE EDIÇÃO
        $content = View::render('admin/modules/clients/form',[
            'title'    => 'Editar cliente',
            'status'   => self::getStatus($request),
            'nome'     => $obClient->nome,
            'email'    => $obClient->email,
            'telefone' => $obClient->telefone           
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar Cliente',$content,'clients');
    }

    /**
     * Método responsável por atualizar o cadastro do barbeiro
     *
     * @param Request $request
     * @param int $id
     */
    public static function setEditClient($request,$id){
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $telefone = $postVars['telefone'] == '' ? 'nda' : $postVars['telefone'];
        $senha = $postVars['senha'] ?? '';

        $obClient = EntityClient::getClientByEmail($email);
        if($obClient instanceof EntityClient){

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/clients?status=duplicated');

        }

        //NOVA INTÂNCIA DE CLIENTE
        $obClient = new EntityClient;
        $obClient->id = $id;
        $obClient->nome = $nome;
        $obClient->email = $email;
        $obClient->telefone = $telefone;
        $obClient->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obClient->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/clients?status=updated');
    }

    /**
     * Método responsável por retornar a página de deleção do cliente
     *
     * @param Request $request
     * @param int $id
     * @return string
     */
    public static function getDeleteClient($request,$id){
        //DADOS DO CLIENTE
        $obClient = EntityClient::getClients('id='.$id,null,null,'*')->fetchObject();

        //CONTEÚDO DA PÁGINA
        $content = View::render('admin/modules/clients/delete',[
            'nome'  =>$obClient->nome,
        ]);

        //RETORNA PÁGINA COMPLETA
        return parent::getPanel('Excluir cliente',$content,'clients');
    }

    /**
     * Método responsável por deletar um cliente
     *
     * @param Request $request
     * @param int $id
     */
    public static function setDeleteClient($request,$id){
        //OBTÉM NOVA INSTÂNCIA DE CLIENTE
        $obClient = new EntityClient;
        $obClient->id = $id;
        $obClient->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/clients?status=deleted');

    }

}