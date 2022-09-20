<?php

namespace App\Handler;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleHandler extends AbstractController{

    public function validateParams($params){
        $title      = isset($params->title) ? $params->title : null;
        $content    = isset($params->content) ? $params->content : null;

        //La imagen tiene otra validación
        if(is_null($title) || is_null($content)){
            return false;
        }

        if(strlen($title) > 50 || strlen($content) > 300){
            return false;
        }

        return true;

    }

    //Esta funcion la voy a usar tanto para editar como para crear
    public function setArticle(){

    }
}