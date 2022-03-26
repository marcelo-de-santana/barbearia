<?php

namespace App\Controller\Client;

use \App\Utils\View;

class Home extends Page{

    /**
     * Método responsável por renderizar a view da página de home
     *
     * @param string $request
     * @return string
     */
    public static function getHome($request){
        //CONTEÚDO DA HOME
        $content = View::render('client/modules/home/index',[]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Bem-Vindo',$content,'home');
    }

}