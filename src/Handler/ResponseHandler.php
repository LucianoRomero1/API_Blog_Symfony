<?php

namespace App\Handler;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseHandler extends AbstractController{

    public function validateJson($json){
        if(is_null($json)){
            $response = $this->errorResponse("error", "400", "Params failed");
            return $response;
        }

        return true;
    }

    public function errorResponse($message){
        $response = new JsonResponse();
        $response->setData([
            "status"    => 'error',
            "code"      => 400,
            "message"   => $message
        ]);

        return $response;
    }

    public function successResponse($message, $data){
        $response = new JsonResponse();
        $response->setData([
            "status"    => 'success',
            "code"      => 200,
            "message"   => $message,
            "data"      => $data
        ]);

        return $response;
    }

}