<?php

namespace App\Controller\Client;

use \App\Utils\View;
use \App\Model\Entity\Agenda as EntityAgenda;
use \App\Model\Entity\Expediente;
use \App\Model\Entity\Barber;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Session\Client\Login as SessionClient;


class Agenda extends Page{

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
            case 'deleted':
                return Alert::getSuccess('Atendimento cancelado com sucesso!');
                break;
            case 'error':
                return Alert::getError('Não foi possível agendar!');
                break;
            case 'errorExists':
                return Alert::getError('Você já está agendado!');
                break;
        }
    }

    /**
     * Método responsável por obter a renderização dos itens de listagem da página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getHomeItems($request,&$obPagination){
        //ITENS DE AGENDAMENTO
        $itens = '';

        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //DATA PARA O SQL
        $dateNow      = Date::getSystemDate();
        $dateSearch   = isset($queryParams['data']) ? 'data = '.Date::getSearchDate($queryParams) : 'data = '.$dateNow;

        //BARBEIRO PARA O SQL
        $barberSearch = isset($queryParams['barbeiro']) ? ' and id_barbeiro = '.$queryParams['barbeiro'] : '';

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityAgenda::getAgenda($dateSearch.$barberSearch,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂNCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,10);

        //CONSULTA MULTI TABELAS
        $where  = $dateSearch.$barberSearch;
        $order  = 'horario';
        $limit  = $obPagination->getLimit();
        $fields = "a.id_agenda,a.id_cliente,a.id_barbeiro,
                    c.nome AS 'nome_cliente',b.nome AS 'nome_barbeiro',
                    a.data,a.horario";

        //RESULTADOS DA PÁGINA
        $results = EntityAgenda::getAgendamentos($where,$order,$limit,$fields);

        //RENDERIZA O ITEM
        while($obAgenda = $results->fetchObject(EntityAgenda::class)){
            $itens .= View::render('client/modules/agenda/itens',[
                'horario'   => date('H:i', strtotime($obAgenda->horario)),
                'cliente'   => $obAgenda->nome_cliente,
                'barbeiro'  => $obAgenda->nome_barbeiro,
                'estado'    => 'Agendado'
            ]);               
        }

        //RETORNA A LISTAGEM 
        return $itens;
    }

    /**
     * Método responsável por renderizar a caixa de pesquisa
     *
     * @param string $currentDate
     * @return string
     */
    private static function getSearch($request){
        //ITENS
        $itens = '';
        
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //DATAS
        $dateNow      = Date::getSystemDate();
        $dateSearch   = isset($queryParams['data']) ? Date::getSearchDate($queryParams) : $dateNow;
        $dateLast     = Date::getLastDay();

        //RENDERIZA A BUSCA
        $itens = View::render('client/search/calendar',[
            'data-hoje'     => $dateNow,
            'data-busca'    => $dateSearch,
            'data-max'      => $dateLast,
            'options'       => self::getBarbers()
        ]);
    
        //RETORNA A PÁGINA RENDERIZADA
        return $itens;
    }

    /**
     * Método responsável por retornar todos os barbeiros
     *
     * @param array $queryParams
     * @return string
     */
    private static function getBarbers(){
        //ITENS
        $itens = '';

        //RESULTADOS DOS BARBEIROS
        $results = Barber::getBarbers(null,null,null,'*');

        //RENDERIZA O ITEM
        while($obBarber= $results->fetchObject(Barber::class)){
            $itens .= View::render('client/search/options',[
                'id-barbeiro'   => $obBarber->id,
                'nome-barbeiro' => $obBarber->nome
            ]);               
        }

        //RETORNA A LISTAGEM 
        return $itens;
        
    }

        /**
     * Método responsável por retornar os horários de agendamento
     *
     * @param Request $request
     * @return string
     */
    public static function getTimes($request){
        //ITENS DA PÁGINA
        $itens = '';

        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        if(isset($queryParams['barbeiro'])){  
            //BUSCAS
            $searchBarber = $queryParams['barbeiro'];
            $searchDate = Date::getSearchDate($queryParams);
            $searchDates = $queryParams['data'];

            //RESULTADOS DOS HORÁRIOS
            $results = Expediente::getExpediente('e.id = '.$searchBarber,'horario',null,'*');

            //RENDERIZA OS ITENS
            while($obExped = $results->fetchObject(Expediente::class)){ 
                $itens .= View::render('client/modules/agenda/times',[
                    'id-barbeiro'  => $searchBarber,
                    'nome-barbeiro'=> $obExped->nome,
                    'data'         => $searchDates,
                    'horario'      => date('H:i', strtotime($obExped->horario)),
                    'status'       => self::getButtoms($searchDate,$searchBarber,$obExped->horario) == 0 ? 'primary"' : 'secondary" disabled',
                ]);
            }

        //RETORNA TODOS OS HORÁRIOS
        return $itens;
        }
    }

    /**
     * Método responsável por retornar os botões de agendamento
     *
     * @param string $searchDate
     * @param string $searchBarber
     * @param string $horario
     * @return string
     */
    private static function getButtoms($searchDate,$searchBarber,$horario){
        //COMANDO SQL
        $where = 'data = '.$searchDate.' and id_barbeiro = '.$searchBarber.' and horario = "'.$horario.'"';

        //RESULTADO
        $result = EntityAgenda::getAgenda($where,null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;

        //RETORNA A STATUS
        return $result; 
    }

    /**
     * Método responsável por retornar uma caixa de desagendamento
     *
     * @param Request $request
     * @return string
     */
    private static function getBox($request){
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //DADOS DO USUÁRIO
        $obClient = SessionClient::getClient();
        
        //DADOS DA PESQUISA
        $id = $obClient['id'];
        $data = isset($queryParams['data']) ? $queryParams['data'] : '';

        //CONFERE SE CLIENTE ESTÁ AGENDADO
        $result = EntityAgenda::getAgenda('id_cliente ='.$id.' and data ="'.$data.'"',null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;
        if($result == 1){
            //RETORNA O BOTÃO DE DESAGENDAMENTO
            return $content = View::render('client/modules/agenda/box',[
                'dia'     =>date('d/m', strtotime($data)),
                'data'    =>$data
            ]);
        }
        return '';
            
    }

    /**
     * Método responsável por renderizar a view da agenda
     *
     * @param string $request
     * @return string
     */
    public static function getAgenda($request){
        //COTEÚDO DA VIEW
        $content = View::render('client/modules/agenda/index',[
            'status'      => self::getStatus($request),
            'search'      => self::getSearch($request),
            'itens'       => self::getHomeItems($request,$obPagination),
            'pagination'  => parent::getPagination($request,$obPagination),
            'times'       => self::getTimes($request),
            'box'         => self::getBox($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Barbearia',$content,'agenda');
    }

    /**
     * Método responsável por renderizar a página de agendamento
     *
     * @param Request $request
     * @return string
     */
    public static function getNewAgenda($request){
        //POST VARS
        $postVars = $request->getPostVars();

        $id = $postVars['id'];
        $data = $postVars['data'];
        $horario = $postVars['horario'];

        //VERIFICA EXPEDIENTE DO BARBEIRO
        $obExped = Expediente::getExpedienteBarber('id ='.$id.' and horario ="'.$horario.'"',null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;
        if(!$obExped == true){
            $request->getRouter()->redirect('/agenda?status=error');
        }

        //CONFERE DISPONIBILIDADE NA AGENDA
        $obAgenda = EntityAgenda::getAgenda('id_barbeiro ='.$id.' and data ="'.$data.'" and horario ="'.$horario.'"',null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;
        if($obAgenda == true){
            $request->getRouter()->redirect('/agenda?status=error');
        }

        //CONFERE DATA
        if($data > Date::getLastDay2() or $data < Date::getSystemDate2()){
            $request->getRouter()->redirect('/agenda?status=error');
        }

        //DADOS DO CLIENTE
        $obClient = SessionClient::getClient();
  
        //CONFERE SE CLIENTE JÁ ESTÁ AGENDADO
        $obAgenda = EntityAgenda::getAgenda('id_cliente ='.$obClient['id'].' and data ="'.$data.'"',null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;
        if($obAgenda == true){
            $request->getRouter()->redirect('/agenda?status=errorExists');
        }

        //DADOS DO BARBEIRO
        $obBarber = Barber::getBarbers('id = '.$id,null,null,'*')->fetchObject(Barber::class);

            $content = View::render('client/modules/agenda/form',[
                'cod-cliente'   => $obClient['id'],
                'nome-cliente'  => $obClient['nome'],
                'email'         => $obClient['email'],
                'cod-barbeiro'  => $obBarber->id,
                'nome-barbeiro' => $obBarber->nome,
                'data'          => $data,
                'horario'       => $horario,
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Agendar',$content,'agenda');

    }

    /**
     * Método responsável por cadastrar o agendamento
     *
     * @param Request $request
     */
    public static function setNewAgenda($request,$id,$data,$horario){
        //VERIFICA EXPEDIENTE DO BARBEIRO
        $obExped = Expediente::getExpedienteBarber('id ='.$id.' and horario ="'.$horario.'"',null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;
        if(!$obExped == true){
            $request->getRouter()->redirect('/agenda?status=error');
        }

        //CONFERE DISPONIBILIDADE NA AGENDA
        $obAgenda = EntityAgenda::getAgenda('id_barbeiro ='.$id.' and data ="'.$data.'" and horario ="'.$horario.'"',null,null,'COUNT(*) AS qtd')->fetchObject()->qtd;
        if($obAgenda == true){
            $request->getRouter()->redirect('/agenda?status=error');
        }

        //CONFERE DATA
        if($data > Date::getLastDay2() or $data < Date::getSystemDate2()){
            $request->getRouter()->redirect('/agenda?status=error');
        }

        //DADOS DO CLIENTE
        $obClient = SessionClient::getClient();

        //NOVA INSTÂNCIA DE AGENDA
        $obAgenda = new EntityAgenda;
        $obAgenda->id_cliente = $obClient['id'];
        $obAgenda->id_barbeiro = $id;
        $obAgenda->data = $data;
        $obAgenda->horario = $horario;
        $obAgenda->cadastrar();

        //REDIRECIONA PARA A AGENDA
        $request->getRouter()->redirect('/agenda?status=created');

    }

    /**
     * Método responsável por retornar a página de desagendamento
     *
     * @param Request $request
     * @param string $data
     * @return string
     */
    public static function setDeleteAgenda($request,$data){
        //DADOS DO CLIENTE        
        $obClient = SessionClient::getClient();

        //NOVA INSTÂNCIA DE AGENDA
        $obAgenda = new EntityAgenda;
        $obAgenda->id_cliente = $obClient['id'];
        $obAgenda->data = $data;
        $obAgenda->excluir();

        //REDIRECIONA PARA A AGENDA
        $request->getRouter()->redirect('/agenda?status=deleted');
    }
}