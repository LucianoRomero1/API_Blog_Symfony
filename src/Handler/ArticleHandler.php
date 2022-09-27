<?php

namespace App\Handler;

use App\Entity\Article;
use App\Handler\ResponseHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

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
            return true;
        }

        return false; 
    }

    //Esta funcion la voy a usar tanto para editar como para crear
    public function setArticle($em, $data, $image, $article = null){
        $title      = $data['title'];
        $content    = $data['content'];
        $image_name = $this->setImageArticle($image);

        if(is_null($article)){
            $article    = new Article();
        }
        $article->setTitle($title);
        $article->setContent($content);
        $article->setDate(new \DateTime('now'));
        $article->setImage($image_name);

        $em->persist($article);
        $flush = $em->flush();

        if(!is_null($flush)){
            throw new Exception('Article can´t be created');
        }

        return $article;
    }

    public function setImageArticle($image){
        $ext        = $image->guessExtension();
        $image_name = "article_".time().'.'.$ext;
        $image->move("uploads/articles", $image_name);

        return $image_name;
    }

    public function findArticleById($em, $id){
        $article = $em->getRepository(Article::class)->findOneBy(["id"=>$id]);

        return $article;
    }

}