<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Barber{

    /**
     * ID do barbeiro
     *
     * @var integer
     */
    public $id;
    
    /**
     * Nome do usuário que fez o barbeiro
     *
     * @var string
     */
    public $nome;

    /**
     * Método responsável por cadastrar a instância atual do banco de dados
     *
     * @return boolean
     */
    public function cadastrar(){

        //INSERE O BARBEIRO NO BANCO DE DADOS
        $this->id = (new Database('barbeiro'))->insert([
            'nome'      => $this->nome,
        ]);
            //SUCESSO
            return true;
    }

    /**
     * Método responsável por atualizar os dados do banco com a instância atual
     * @return boolean
     */
    public function atualizar(){
        //ATUALIZA O BARBEIRO NO BANCO DE DADOS
        return (new Database('barbeiro'))->update('id ='.$this->id,[
            'nome'      => $this->nome,
        ]);
    }

    /**
     * Método responsável por excluir um barbeiro do  banco de dados 
     * @return boolean
     */
    public function excluir(){
        //EXCLUI O BARBEIRO DO BANCO DE DADOS
        return (new Database('barbeiro'))->delete('id ='.$this->id,[
            'nome'      => $this->nome,
        ]);
    }

    /**
     * Método responsável por retornar um barbeiro com base no seu ID
     *
     * @param integer $id
     * @return Barber
     */
    public static function getBarberById($id){
        return self::getBarbers('id ='.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar Barbeiros
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getBarbers($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('barbeiro'))->select($where,$order,$limit,$fields);
        }

}
