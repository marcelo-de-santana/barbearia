<?php

namespace App\Controller\Client;

use \App\Utils\View;
use \App\Model\Entity\Client;

class About extends Page{

    /**
     * Método responsável por retornar a página de sobre
     *@param Request $request
     * 
     * @return string
     */
    public static function getAbout($request){
        $content = View::render('client/modules/about/index',[]);
    
        return parent::getPanel('Suporte',$content,'about');
    }

}