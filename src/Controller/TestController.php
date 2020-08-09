<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Validation\testValidation;

class TestController
{
     /**
      * @Route("/test")
      */
    public function number(Request $request)
    {
        try {
            $v = new testValidation($request->query->all());

            return new JsonResponse([
                'code' => 'ok',
                'ret' => $v->validate(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ]);
        }

    }
}