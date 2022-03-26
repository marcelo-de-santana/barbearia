<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Client as EntityClient;
use \App\Model\Entity\Agenda as EntityAgenda;
use \App\Model\Entity\Barber as EntityBarber;
use \App\Model\Entity\Expediente as EntityExpediente;
use \WilliamCosta\DatabaseManager\Pagination;

class Agenda extends Page{

    /**
     * Método responsável por renderizar todos os agendamentos
     *
     * @param Request $request
     * @return string
     */
    public static function getAgenda($request){
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();
        
        //DATA DA PESQUISA
        $data = isset($queryParams['data']) ? $queryParams['data'] : Date::getSystemDate2(); 

        //RENDERIZAÇÃO DO CONTEÚDO DA PÁGINA AGENDA
        $content = View::render('admin/modules/agenda/index',[
            'status'        => self::getStatus($request),
            'dia'           => date('d/m/Y', strtotime($data)),
            'data'          => $data,
            'itens'         => self::getAgendaItens($request,$data),
            'pagination'    => '',

        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Agenda | Listagem',$content,'agenda');
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
                return Alert::getSuccess('Cliente agendado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Atendimento cancelado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O cliente já está agendado!');
                break;
        }
    }

    /**
     * Método responsável por retornar a listagem de agendamentos
     *
     * @param Request $request
     * @param string $data
     * @return string
     */
    private static function getAgendaItens($request,$data){
        //ITENS
        $itens = '';

        //QUANTIDADE TOTAL DE AGENDAS DE BARBEIROS
        $quantidadeTotal = EntityBarber::getBarbers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂNCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,7);

        //RESULTADOS DA PÁGINA
        $results = EntityBarber::getBarbers(null,'id ASC',$obPagination->getLimit());

        //RENDERIZA O CONTEÚDO DA PÁGINA
        while($obBarber = $results->fetchObject(EntityBarber::class)){
            $itens .= View::render('admin/modules/agenda/itens',[
                'nome'      => $obBarber->nome,
                'horarios'  => self::getHorarios($obBarber->id,$data),
            ]);
        }

        //RETORNA O CONTEÚDO
        return $itens;
    }

    /**
     * Método responsável por retornar os horários de atendimento
     *
     * @param int $id
     * @param string $data
     * @return string
     */
    private static function getHorarios($id,$data){
        //ITENS
        $itens = '';

        //OBTÉM OS HORÁRIOS DE ATENDIMENTO
        $results = EntityExpediente::getExpedienteBarber('id='.$id);

        //RENDERIZA O CONTEÚDO DA PÁGINA
        while($obExped = $results->fetchObject(EntityExpediente::class)){
            $itens .= View::render('admin/modules/agenda/horarios',[
                'horario'   => date('H:i', strtotime($obExped->horario)),
                'link'      => self::getStatusLink($obExped->id,$obExped->horario,$data),
                'status'    => self::getStatusButtom($obExped->id,$obExped->horario,$data)
            ]);
        }

        //RETORNA O CONTEÚDO
        return $itens;
    }

    /**
     * Método responsável por retornar o link do botão de agendamento
     *
     * @param int $id
     * @param string $horario
     * @param string $data
     * @return string
     */
    private static function getStatusLink($id,$horario,$data){
        //CONFERE SE O HORÁRIO ESTÁ AGENDADO
        $result = EntityAgenda::getAgendamentos('a.horario ="'.$horario.'" and a.data ="'.$data.'"and a.id_barbeiro ='.$id,null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;

        //TRATAMENTO DE VALORES
        $horario = date('H:i', strtotime($horario));

        //RETORNA O PARÂMETRO DO BOTÃO
        return $result == 0 ? '/new/'.$id.'/'.$horario.'/'.$data : '/delete/'.$id.'/'.$horario.'/'.$data;
    }

    /**
     * Método responsável por retornar o status do botão de agendamento
     *
     * @param int $id
     * @param string $horario
     * @param string $data
     * @return string
     */
    private static function getStatusButtom($id,$horario,$data){
        //CONFERE SE O HORÁRIO ESTÁ AGENDADO
        $result = EntityAgenda::getAgendamentos('a.horario ="'.$horario.'" and a.data ="'.$data.'"and a.id_barbeiro ='.$id,null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;

        //RETORNA O PARÂMETRO DO BOTÃO
        return $result == 0 ? 'success' : 'danger';
    }

    /**
     * Método responsável por retonar a página de novo agendamento
     *
     * @param Request $request
     * @param int $id
     * @param string $horario
     * @param string $data
     * @return string
     */
    public static function getNewAgenda($request,$id,$horario,$data){
        //DADOS DO BARBEIRO
        $obBarber = EntityBarber::getBarberById($id);

        //CONTEÚDO DA PÁGINA
        $content = View::render('admin/modules/agenda/form',[
            'cod-barbeiro'      => $obBarber->id,
            'nome-barbeiro'     => $obBarber->nome,
            'options'           => self::getOptions(),
            'horario'           => $horario,
            'data'              => $data,

        ]);
    
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Agenda | Novo atendimento',$content,'agenda');
    }

    /**
     * Método responsável por retornar as options de clientes
     *
     * @return string
     */
    private static function getOptions(){
        //ITENS
        $itens = '';

        //DADOS DOS CLIENTES
        $results = EntityClient::getClients(null,'email ASC',null,'*');

        //RENDERIZAÇÃO DE ITENS DA PÁGINA
        while($obClient = $results->fetchObject(EntityClient::class)){
            $itens .= View::render('admin/modules/agenda/options',[
                'id-cliente'        => $obClient->id,
                'email-cliente'     => $obClient->email
            ]);
        }
        //RETORNA OS ITENS
        return $itens;

    }

    /**
     * Método responsável por retornar a página de agendamento de cliente
     *
     * @param Request $request
     * @param int $id
     * @param string $horario
     * @param string $data
     */
    public static function setNewAgenda($request,$id,$horario,$data){
        //POST VARS
        $postVars = $request->getPostVars();
        $id_barbeiro = $postVars['cod-barbeiro'];
        $id_cliente = $postVars['cod-cliente'];

        //CONFERE SE CLIENTE JÁ ESTÁ CADASTRADO NO DIA
        $result = EntityAgenda::getAgenda('data ="'.$data.'" and id_cliente ='.$id_cliente.' and id_barbeiro='.$id_barbeiro,null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;

        if($result == 1){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/agenda?status=duplicated');
        }

        //NOVA INSTÂNCIA DE AGENDA
        $obAgenda = new EntityAgenda;
        $obAgenda->id_cliente = $id_cliente;
        $obAgenda->id_barbeiro = $id_barbeiro;
        $obAgenda->data = $data;
        $obAgenda->horario = $horario;
        $obAgenda->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/agenda?status=created');

    }

    /**
     * Método responsável por retornar a página de deleção de agendamento
     *
     * @param Request $request
     * @return string
     */
    public static function getDeleteAgenda($request,$id,$horario,$data){
        //DADOS DO BARBEIRO
        $id_barbeiro = $id;

        //DADOS DO CLIENTE
        $obClient = EntityAgenda::getAgendamentos('a.horario="'.$horario.'" and a.data="'.$data.'" and a.id_barbeiro='.$id_barbeiro,null,null,'a.id_agenda,a.id_cliente,c.nome')->fetchObject();

        //CONTEÚDO DA PÁGINA
        $content = View::render('admin/modules/agenda/delete',[
            'id-agenda'     => $obClient->id_agenda,
            'id-cliente'    => $obClient->id_cliente,
            'nome-cliente'  => $obClient->nome
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Agenda | Cancelar atendimento',$content,'agenda');

    }

    /**
     * Método responsável por cancelar um agendamento do cliente
     *
     * @param Request $request
     * @param int $id
     * @param string $horario
     * @param string $data
     */
    public static function setDeleteAgenda($request,$id,$horario,$data){
        //POST VARS
        $postVars = $request->getPostVars();
        $id_agenda = $postVars['id-agenda'];
        $id_cliente = $postVars['id-cliente'];

        //NOVA INSTÂNCIA DE AGENDA
        $obAgenda = new EntityAgenda;
        $obAgenda->id_agenda = $id_agenda;
        $obAgenda->excluirAgendamento();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/agenda?status=deleted');
    }
}
