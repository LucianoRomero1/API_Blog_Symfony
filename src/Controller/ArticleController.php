<?php

namespace App\Controller;

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
        return $this->responseHandler->successResponse($message, $article);
        
    }

    /**
     * @Route("/get_all", name="get_all_articles")
     */
    public function getAll(){

    }

    /**
     * @Route("/get_one", name="get_one_article")
     */
    public function getOne(){

    }

    /**
     * @Route("/edit/{id}", name="edit_article")
     */
    public function edit(Request $request, $id = null){
        
    }

    /**
     * @Route("/delete/{id}", name="delete_article")
     */
    public function delete(Request $request, $id = null){
        
    }
}
