<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Client{

    /**
     * ID do cliente
     *
     * @var integer
     */
    public $id;

    /**
     * Nome do cliente
     *
     * @var string
     */
    public $nome;

    /**
     * E-mail do cliente
     *
     * @var string
     */
    public $email;

    /**
     * Senha do cliente
     *
     * @var string
     */
    public $senha;

    /**
     * Telefone do cliente
     *
     * @var string
     */
    public $telefone;

    /**
     * Método responsável por cadastrar a instância atual do banco de dados
     *
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('cliente'))->insert([
            'nome'      => $this->nome,
            'email'     => $this->email,
            'senha'     => $this->senha,
            'telefone'  =>$this->telefone
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados do banco com a instância atual
     * @return boolean
     */
    public function atualizar(){
        //ATUALIZA O CADASTRO DO CLIENTE NO BANCO DE DADOS
        return (new Database('cliente'))->update('id ='.$this->id,[
            'nome'     => $this->nome,
            'senha'    => $this->senha,
            'telefone' => $this->telefone
        ]);
    }

    /**
     * Método responsável por excluir um client do banco de dados 
     * @return boolean
     */
    public function excluir(){
        //EXCLUI O CLIENTE DO BANCO DE DADOS
        return (new Database('cliente'))->delete('id ='.$this->id);
    }

    /**
     * Método responsável por retornar um cliente com base em seu e-mail
     *
     * @param string $email
     * @return Client
     */
    public static function getClientByEmail($email){
        return self::getClients('email = "'.$email.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar clientes
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getClients($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('cliente'))->select($where,$order,$limit,$fields);
    }
}