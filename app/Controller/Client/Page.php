<?php

namespace App\Controller\Client;

use \App\Utils\View;

class Page{

    /**
     * Módulos disponíveis no painel da página
     *
     * @param array $modules
     */
    private static $modules = [
        'home' => [
            'label' => 'Home',
            'link'  => URL.'/home'
        ],
        'agenda' => [
            'label' => 'Agenda',
            'link'  => URL.'/agenda'
        ],
        'client' => [
            'label' => 'Cadastro',
            'link'  => URL.'/agenda/register'
        ],
        'about' => [
            'label' => 'Suporte',
            'link'  => URL.'/agenda/about'
        ],

    ];

    /**
     * Método responsável por retonar o conteúdo (view) da estrutura genérioca de página do painel
     *
     * @param string $title
     * @param string $content
     * @return string
     */
    public static function getPage($title,$content){
        return View::render('client/page',[
            'title'     => $title,
            'content'   => $content 
        ]);
    }

    /**
     * Método responsável por renderizar a view do menu do painel
     *
     * @param string $currentModule
     * @return string
     */
    private static function getMenu($currentModule){
        //LINKS DO MENU
        $links = '';

        //ITERA OS MÓDULOS
        foreach(self::$modules as $hash=>$module){
            $links .= View::render('client/menu/link',[
                'label'     => $module['label'],
                'link'      => $module['link'], 
                'current'   => $hash == $currentModule ? 'text-danger' : ''
            ]);
        }

        //RETORNA A RENDERIZAÇÃO DO MENU
        return View::render('client/menu/box',[
            'links' => $links
        ]);
    }

    /**
     * Método responsável por renderizar a view do painel com conteúdos dinâmicos
     *
     * @param string $title
     * @param string $content
     * @param string $currentModule
     * @return string
     */
    public static function getPanel($title,$content,$currentModule){
        //RENDERIZA A VIEW DO PAINEL
        $contentPanel = View::render('client/panel',[
            'menu'      => self::getMenu($currentModule),
            'content'   => $content
        ]);

        //RETORNA A PÁGINA RENDERIZADA
        return self::getPage($title,$contentPanel);
    }

    /**
     * Método responsável por renderizar o layout da páginação
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    public static function getPagination($request,$obPagination){
        //PÁGINAS
        $pages = $obPagination->getPages();

        //VERIFICA A QUANTIDADE DE PÁGINAS
        if(count($pages) <= 1) return '';
        
        //LINKS
        $links = '';

        //URL ATUAL (SEM GETS)
        $url = $request->getRouter()->getCurrentUrl();

        //GET
        $queryParams = $request->getQueryParams();

        //RENDERIZ OS LINKS
        foreach($pages as $page){
            //ALTERA A PÁGINA
            $queryParams['page'] = $page['page'];

            //LINK
            $link = $url.'?'.http_build_query($queryParams);

            //VIEW
            $links .= View::render('client/pagination/link',[
                'page'   => $page['page'],
                'link'   => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);
        }

        //RENDERIZA BOX DE PAGINAÇÃO
        return View::render('client/pagination/box',[
            'links'  => $links
        ]);
    }
}