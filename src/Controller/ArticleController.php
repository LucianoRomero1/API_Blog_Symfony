<?php

namespace App\Controller;

use App\Entity\Article;
use App\Handler\ArticleHandler;
use App\Handler\ResponseHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
*/
class ArticleController extends AbstractController
{

    private $responseHandler;
    private $articleHandler;

    public function __construct(ResponseHandler $responseHandler, ArticleHandler $articleHandler){
        $this->responseHandler  = $responseHandler;
        $this->articleHandler   = $articleHandler;
    }

    /**
     * @Route("/create", name="create_article")
     */
    public function create(ManagerRegistry $manager, Request $request){
        $em     = $manager->getManager();
        $data   = $request->request->all(); 
        
        $image  = $request->files->get('image');

        try {
            $this->articleHandler->validateParams($data, $image);
            $article = $this->articleHandler->setArticle($em, $data, $image);
            dd($article);
        } catch (\Exception $e) {
            $response = $this->responseHandler->errorResponse($e->getMessage());
            return $response;
        }

        $message = 'Article has been created successfully';
        return $this->responseHandler->successResponse($article, $message);
        
    }

    /**
     * @Route("/get_all", name="get_all_articles")
     */
    public function getAll(ManagerRegistry $manager){
        $em     = $manager->getManager();
        //Tendria que traerlas paginadas
    }

    /**
     * @Route("/get_one/{id}", name="get_one_article")
     */
    public function getOne(ManagerRegistry $manager, $id = null){
        $em         = $manager->getManager();
        $article    = $this->articleHandler->findArticleById($em, $id);
        if(is_null($article)){
            return $this->responseHandler->errorResponse("Article not found");
        }

        return $this->responseHandler->successResponse($article);
    }

    /**
     * @Route("/edit/{id}", name="edit_article")
     */
    public function edit(ManagerRegistry $manager, Request $request, $id = null){
        $em         = $manager->getManager();

        $article    = $this->articleHandler->findArticleById($em, $id);
        if(is_null($article)){
            return $this->responseHandler->errorResponse("Article not found");
        }
        
        $data   = $request->request->all(); 
        $image  = $request->files->get('image');

        try {
            $this->articleHandler->validateParams($data, $image);
            $article = $this->articleHandler->setArticle($em, $data, $image, $article);
        } catch (\Exception $e) {
            $response = $this->responseHandler->errorResponse($e->getMessage());
            return $response;
        }

        $message = 'Article has been edited successfully';
        return $this->responseHandler->successResponse($article, $message);
    }

    /**
     * @Route("/delete/{id}", name="delete_article")
     */
    public function delete(ManagerRegistry $manager, Request $request, $id = null){
        
    }
}
