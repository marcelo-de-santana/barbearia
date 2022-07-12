<?php

namespace App\Controller\Admin;

use \App\Model\Entity\Agenda;
use Datetime;

class Date{

    /**
     * Método responsável por retornar a data atual
     *
     * @param Request $request
     * @return string
     */
    public static function getSystemDate(){
        $dateNow = new DateTime('now');
        return '"'.$dateNow->format('Y-m-d').'"';

    }

    /**
     * Método responsável por retornar a data atual sem aspas
     *
     * @param Request $request
     * @return string
     */
    public static function getSystemDate2(){
        $dateNow = new DateTime('now');
        return $dateNow->format('Y-m-d');

    }

    /**
     * Método responsável por retornar a data da busca
     *
     * @param array $queryParams
     * @return string
     */
    public static function getSearchDate($queryParams = null){
        return '"'.$queryParams['data'].'"';

    }

    /**
     * Método responsável por retornar o dia da próxima semana
     *
     * @return string
     */
    public static function getLastDay(){
        $lastDay = date('Y-m-d', strtotime('+6 days', strtotime(date('Y-m-d'))));
        return '"'.$lastDay.'"';

    }

    /**
     * Método responsável por retornar o dia da próxima semana sem aspas
     *
     * @return string
     */
    public static function getLastDay2(){
        $lastDay = date('Y-m-d', strtotime('+6 days', strtotime(date('Y-m-d'))));
        return $lastDay;

    }

}