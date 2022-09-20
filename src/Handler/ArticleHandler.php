<?php

namespace App\Handler;

use App\Entity\Article;
use App\Handler\ResponseHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleHandler extends AbstractController{

    private $responseHandler;

    public function __construct(ResponseHandler $responseHandler){
        $this->responseHandler  = $responseHandler;
    }

    public function validateParams($data, $image){
        $title      = isset($data['title']) ? $data['title'] : null;
        $content    = isset($data['content']) ? $data['content'] : null;

        if(is_null($title) || is_null($content)){
            throw new Exception('Params not found');
        }

        if(strlen($title) > 50 || strlen($content) > 300){
            throw new Exception('Invalid params');
        }   

        if(!is_null($image)){
            if(!$this->validateImage($image)){
                throw new Exception('Invalid image format');
            }
        }
        
        return true;
    }

    public function validateImage($image){    
        $ext = $image->guessExtension();
        if($ext == "jpg" || $ext == "jpeg" || $ext == "png"){
            $image_name = "article_".time().'.'.$ext;
            $image->move("uploads/articles", $image_name);
        
            return true;
        }

        return false; 
    }

    //Esta funcion la voy a usar tanto para editar como para crear
    public function setArticle($em, $data){
        $title      = $data['title'];
        $content    = $data['content'];

        $article = new Article();
        $article->setTitle($title);
        $article->setContent($content);
        $article->setDate(new \DateTime('now'));

    }

    

}