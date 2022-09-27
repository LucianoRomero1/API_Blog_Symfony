<?php

namespace App\Handler;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class ResponseHandler extends AbstractController{

    public function validateJson($json){
        if(is_null($json)){
            $response = $this->errorResponse("error", "400", "Params failed");
            return $response;
        }

        return true;
    }

    public function errorResponse($message = null){
        $response = new JsonResponse();
        $response->setData([
            "status"    => 'error',
            "code"      => 400,
            "message"   => $message
        ]);

        return $response;
    }

    public function successResponse($data, $message = null){
        $response = new JsonResponse();
        $response->setData([
            "status"    => 'success',
            "code"      => 200,
            "message"   => $message,
            "data"      => $this->serializer($data)
        ]);

        return $response;
    }

      /* 
    *   Convierta cualquier entidad a JSON.
    */
    public function serializer($entity){
        $encoders    = [new XmlEncoder(), new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($subEntities, $format, $context) {
                return $subEntities;
            },
        ];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
        $serializer   = new Serializer($normalizers, $encoders);
        return json_decode($serializer->serialize($entity, 'json'));

    }

    
}