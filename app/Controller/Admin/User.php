<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page{

    /**
     * Método responsável por obter a renderização dos itens de usuários para a página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request,&$obPagination){
        //USUÁRIOS
        $itens = '';
        
        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂNCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

        //RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers(null,'id',$obPagination->getLimit());
        
        //RENDERIZA O ITEM
        while($obUser = $results->fetchObject(EntityUser::class)){
            $itens .= View::render('admin/modules/users/item',[
                'id'        => $obUser->id,
                'nome'      => $obUser->nome,
                'email'  => $obUser->email
                ]
            );               
        }

        //RETORNA OS DEPOIMENTOS 
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de usuários
     *
     * @param string $request
     * @return string
     */
    public static function getUsers($request){
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/users/index',[
        'itens'      => self::getUserItems($request,$obPagination),
        'pagination' => parent::getPagination($request,$obPagination),
        'status'     => self::getStatus($request)
        ]);

            //RETORNA A PÁGINA COMPLETA
            return parent::getPanel('Usuários | Adm',$content,'users');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuário
     *
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request){
        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form',[
            'title'    => 'Cadastrar Adminstrador',
            'nome'     => '',
            'email' => '',
            'status'   => self::getStatus($request)
        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastro Usuário | Adm',$content,'users');
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request){
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUser = EntityUser::getUserByEmail($email);
        if($obUser instanceof EntityUser){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        //NOVA INSTÂNCIA DE USUÁRIO
        $obUser = new EntityUser;
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=created');

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
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluído com sucesso!');
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo utilizado por outro usuário');
                break;            
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request,$id){
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTÂNCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }        

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form',[
            'title'    => 'Editar usuário',
            'nome'     => $obUser->nome,
            'email'    => $obUser->email,
            'status'   => self::getStatus($request)
        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar usuário | Adm',$content,'users');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser($request,$id){
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTÂNCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }         

        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($email);
        if($obUserEmail instanceof EntityUser && $obUserEmail->id != $id){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }

        //ATUALIZA A INSTÂNCIA
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha,PASSWORD_DEFAULT);

        $obUser->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request,$id){
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTÂNCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }        

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/delete',[
            'nome'  => $obUser->nome,
            'email' => $obUser->email
        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Excluir usuário | Adm',$content,'users');
    }

    /**
     * Método responsável por excluir um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request,$id){
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTÂNCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }        

        //EXCLUI O USUÁRIO
        $obUser->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }

}