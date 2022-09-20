<?php

namespace App\Controller;

use App\Handler\ArticleHandler;
use App\Handler\ResponseHandler;
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
    public function create(Request $request){

        $json = $request->get('json', null);
        if(is_null($json)){
            $response = $this->responseHandler->errorResponse("error", "400", "Params failed");
            return $response;
        }

        $params = json_decode($json);
        if(!$this->articleHandler->validateParams($params)){
            $response = $this->responseHandler->errorResponse("error", "400", "Invalid params");
            return $response;
        }

        //Aca tengo que hacer el set de la entidad
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
