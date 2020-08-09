<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Validation\ProductValidation;

class TestController extends AbstractController
{
     /**
      * @Route("/test")
      */
    public function number(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $v = new ProductValidation($request->query->all());
            $ret = $v->validate();

            $product = $v->setEntityValue(new Product());
            $product->setCreateAt(new \DateTime());
            $entityManager->persist($product);
            $entityManager->flush();

            return new JsonResponse([
                'code' => 'ok',
                'ret' => $ret,
            ]);
        } catch (\Exception $e) {
//            throw $e;
            return new JsonResponse([
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ]);
        }

    }
}