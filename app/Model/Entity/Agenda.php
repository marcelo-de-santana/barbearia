<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Agenda{

    /**
     * ID do agendamento
     *
     * @var integer
     */
    public $id_agenda;

    /**
     * ID do cliente
     *
     * @var integer
     */
    public $id_cliente;

    /**
     * Nome do cliente
     *
     * @var string
     */
    public $nome_cliente;

    /**
     * ID do barbeiro
     *
     * @var integer
     */
    public $id_barbeiro;

    /**
     * Nome do barbeiro
     *
     * @var string
     */
    public $nome_barbeiro;

    /**
     * Data do agendamento
     *
     * @var string
     */
    public $data;

    /**
     * Horário do agendamento
     *
     * @var string
     */
    public $horario;

    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('agenda'))->insert([
            'id_cliente'      => $this->id_cliente,
            'id_barbeiro'     => $this->id_barbeiro,
            'data'            => $this->data,
            'horario'         => $this->horario,
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por excluir um agendamento com base no seu ID
     * @return boolean
     */
    public function excluirAgendamento(){
        //EXCLUI O AGENDAMENTO DO BANCO DE DADOS
        return (new Database('agenda'))->delete('id_agenda='.$this->id_agenda);
    }

    /**
     * Método responsável por excluir um agendamento 
     * @return boolean
     */
    public function excluir(){
        //EXCLUI O AGENDAMENTO DO BANCO DE DADOS
        return (new Database('agenda'))->delete('id_cliente='.$this->id_cliente,[
            'data'       => $this->data
        ]);
    }

    /**
     * Método responsável por retornar os agendamentos
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getAgenda($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('agenda'))->select($where,$order,$limit,$fields);
    }

    /**
     * Método responsável por retornar os agendamentos inteligando tabelas
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getAgendamentos($where = null, $order = null, $limit = null, $fields = '*'){
        return
        (new Database('
                    cliente AS c
                    INNER JOIN agenda   AS a on c.id = a.id_cliente
                    INNER JOIN barbeiro AS b on b.id = a.id_barbeiro
        '))->select($where,$order,$limit,$fields);
    }
}

