<?php

namespace App\Utils;

class View{

    /**
     * Variáveis padrões da View
     *
     * @var array
     */
    private static $vars = [];

    /**
     * Método responsável por definir os dados iniciais da classe
     *
     * @param array $vars
     */
    public static function init($vars = []){
        self::$vars = $vars;
    }

    /**
     * Método responsável por retornar o conteúdo de uma view
     *
     * @param string $view
     * @return string
     */
    private static function getContentView($view){
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Método responsável por retornar o conteúdo renderizado de uma view
     *
     * @param string $view
     * @param array  $vars(string/numeric)
     * @return string
     */
    public static function render($view, $vars = []){
        //CONTEÚDO DA VIEW
        $contentView = self::getContentView($view);
        
        //MERGE DE VARIÁVEIS DA VIEW
        $vars = array_merge(self::$vars,$vars);

        //CHAVES DO ARRAY DE VARIÁVEIS
        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return '{{'.$item.'}}';
        }
        ,$keys);

        //RETORNA O CONTEÚDO RENDERIZADO
        return str_replace($keys,array_values($vars),$contentView);

    }

    }