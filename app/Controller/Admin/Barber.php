<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Barber as EntityBarber;
use \App\Model\Entity\Expediente as EntityExpediente;
use \WilliamCosta\DatabaseManager\Pagination;

class Barber extends Page{

    /**
     * Método responsável por retornar os horários de atendimento do barbeiro
     *
     * @param int $id
     * @return string
     */
    private static function getHours($id){
        //ITENS
        $itens = '';

        //RESULTADOS
        $results = EntityExpediente::getExpedienteBarber('id ='.$id,'horario',null,'horario');

        //RENDERIZA O ITEM
        while($obExped = $results->fetchObject(EntityBarber::class)){    
            $itens .= View::render('admin/modules/barbers/box',[
                'horarios'  => date('H:i', strtotime($obExped->horario))
            ]);
        }

        //RETORNA ITENS RENDERIZADOS
        return $itens;
    }

    /**
     * Método responsável por obter a renderização dos itens de barbeiros para a página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getBarberItems($request,&$obPagination){
        //ITENS
        $itens = '';
        
        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityBarber::getBarbers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂNCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

        //RESULTADOS DA PÁGINA
        $results = EntityBarber::getBarbers(null,'id ASC',$obPagination->getLimit());
        
        //RENDERIZA O ITEM
        while($obBarber = $results->fetchObject(EntityBarber::class)){
            $itens .= View::render('admin/modules/barbers/item',[
                'id'        => $obBarber->id,
                'nome'      => $obBarber->nome,
                'box'       => self::getHours($obBarber->id)
            ]
            );               
        }

        //RETORNA OS BARBEIROS 
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de barbeiros
     *
     * @param string $request
     * @return string
     */
    public static function getBarbers($request){
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/barbers/index',[
        'itens'      => self::getBarberItems($request,$obPagination),
        'pagination' => parent::getPagination($request,$obPagination),
        'status'     => self::getStatus($request)
        ]);

            //RETORNA A PÁGINA COMPLETA
            return parent::getPanel('Barbeiros | Listagem',$content,'barbers');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo barbeiro
     *
     * @param Request $request
     * @return string
     */
    public static function getNewBarber($request){
        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/barbers/form',[
            'title'    => 'Cadastrar barbeiro',
            'nome'     => '',
            'email'    => '',
            'status'   => ''
        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar barbeiro',$content,'barbers');
    }

    /**
     * Método responsável por cadastrar um barbeiro no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewBarber($request){
        //POST VARS
        $postVars = $request->getPostVars();

        //NOVA INSTÂNCIA DE BARBEIRO
        $obBarber = new EntityBarber;
        $obBarber->nome = $postVars['nome'] ?? '';
        $obBarber->mensagem = $postVars['mensagem'] ?? '';
        $obBarber->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/barbers?status=created');
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
                return Alert::getSuccess('Barbeiro criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Barbeiro atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Barbeiro excluído com sucesso!');
                break;
            case 'erro':
                return Alert::getError('Horário já existe!');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um barbeiro
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditBarber($request,$id){
        //OBTÉM O BARBEIRO DO BANCO DE DADOS
        $obBarber = EntityBarber::getBarberById($id);

        //VALIDA A INSTÂNCIA
        if(!$obBarber instanceof EntityBarber){
            $request->getRouter()->redirect('/admin/barbers');
        }        

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/barbers/form',[
            'title'    => 'Editar barbeiro',
            'nome'     => $obBarber->nome,
            'status'   => self::getStatus($request)
        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar barbeiro',$content,'barbers');
    }

    /**
     * Método responsável por gravar a atualização de um barbeiro
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditBarber($request,$id){
        //OBTÉM O BARBEIRO DO BANCO DE DADOS
        $obBarber = EntityBarber::getBarberById($id);

        //VALIDA A INSTÂNCIA
        if(!$obBarber instanceof EntityBarber){
            $request->getRouter()->redirect('/admin/barbers');
        }        

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTÂNCIA
        $obBarber->nome = $postVars['nome'] ?? $obBarber->nome;
        $obBarber->mensagem = $postVars['mensagem'] ?? $obBarber->mensagem;
        $obBarber->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/barbers?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um barbeiro
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteBarber($request,$id){
        //OBTÉM O BARBEIRO DO BANCO DE DADOS
        $obBarber = EntityBarber::getBarberById($id);

        //VALIDA A INSTÂNCIA
        if(!$obBarber instanceof EntityBarber){
            $request->getRouter()->redirect('/admin/barbers');
        }        

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/barbers/delete',[
            'nome'     => $obBarber->nome,
        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Excluir barbeiro',$content,'barbers');
    }

    /**
     * Método responsável por excluir um barbeiro
     *
     * @param Request $request
     * @param integer $id
     */
    public static function setDeleteBarber($request,$id){
        //OBTÉM O BARBEIRO DO BANCO DE DADOS
        $obBarber = EntityBarber::getBarberById($id);

        //VALIDA A INSTÂNCIA
        if(!$obBarber instanceof EntityBarber){
            $request->getRouter()->redirect('/admin/barbers');
        }        

        //EXCLUI O BARBEIRO
        $obBarber->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/barbers?status=deleted');
    }

    /**
     * Método responsável por retornar os horários do barbeiro
     *
     * @param int $id
     * @return string
     */
    private static function getItensHours($id){
        //ITENS
        $itens = '';

        //RESULTADOS
        $results = EntityExpediente::getExpedienteBarber('id ='.$id,'horario',null,'*');

        //ITENS DA PÁGINA
        while($obExped = $results->fetchObject(EntityExpediente::class)){    
            $itens .= View::render('admin/modules/barbers/hours/itens',[
                'id'        => $obExped->id,
                'horario'   => date('H:i', strtotime($obExped->horario))
            ]);
        }

        //RETORNA OS ITENS
        return $itens;
    }

    /**
     * Método responsável por retornar a página de edição de horários
     *
     * @param Request $request
     * @param int $id
     * @return string
     */
    public static function getEditHours($request,$id){
        //CONTEÚDO DA PÁGINA
        $content = View::render('admin/modules/barbers/hours/index',[
            'status'    => self::getStatus($request),
            'itens'     => self::getItensHours($id)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Barbeiro | Horários',$content,'barbers');        

    }

    /**
     * Método responsável por cadastrar um novo horário no expediênte do barbeiro
     *
     * @param Request $request
     * @param int $id
     */
    public static function setNewHour($request,$id){
        //POST VARS
        $postVars = $request->getPostVars();
        $horario = $postVars['horario'];

        //CONFERE A EXISTÊNCIA DO HORÁRIO
        $result = EntityExpediente::getExpedienteBarber('horario ="'.$horario.'" and id='.$id,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
        if($result == 1){
            $request->getRouter()->redirect('/admin/barbers/'.$id.'/hours?status=erro');
        }
        //OBTÉM NOVA INSTÂNCIA DE EXPEDIENTE
        $obExped = new EntityExpediente;
        $obExped->id = $id;
        $obExped->horario = $horario;
        $obExped->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/barbers/'.$id.'/hours');
    }

    /**
     * Método responsável por excluir um horário do barbeiro
     *
     * @param Request $request
     * @param int $id
     * @param string $hora
     */
    public static function setDeleteHour($request,$id,$horario){
        //OBTÉM NOVA INSTÂNCIA DE EXPEDIENTE
        $obExped = new EntityExpediente;
        $obExped->id = $id;
        $obExped->horario = '"'.$horario.'"';
        $obExped->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/barbers/'.$id.'/hours');
    }

}
