<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Expediente{

    /**
     * ID do barbeiro
     *
     * @var int
     */
    public $id;

    /**
     * Nome do barbeiro
     *
     * @var string
     */
    public $nome;

    /**
     * Horário do expediente do barbeiro
     *
     * @var string
     */
    public $horario;

    /**
     * Método responsável por cadastrar um horário do expediente do barbeiro
     * @return boolean
     */
    public function cadastrar(){
        //CADASTRA O HORÁRIO NO BANCO DE DADOS
        $this->id = (new Database('expediente'))->insert([
            'id'        => $this->id,
            'horario'   => $this->horario
        ]);
    }

    /**
     * Método responsável por excluir um horario do expediente do barbeiro no  banco de dados 
     * @return boolean
     */
    public function excluir(){
        //EXCLUI O HORÁRIO DO BANCO DE DADOS
        return (new Database('expediente'))->delete('horario ='.$this->horario.' and id ='.$this->id);
    }

    /**
     * Método responsável por retornar o expediênte do barbeiro
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getExpedienteBarber($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('expediente'))->select($where,$order,$limit,$fields);
    }
    
    /**
     * Método responsável por retornar o expediênte do barbeiro
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getExpediente($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('
                            expediente AS e
                            INNER JOIN barbeiro AS b ON b.id = e.id
        '))->select($where,$order,$limit,$fields);
    }

}