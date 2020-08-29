<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Jackai\Validator\Validator;

class TestController extends AbstractController
{
    /**
     * @Route("/test")
     * @Validator(
     *     emptyStringIsUndefined = true,
     *     requireQuery = {"name"},
     *     query = {
     *         {"name" = "name", "rule" = "Assert\Length", "ruleOption" = {"min" = 1, "max" = 30}, "errorCode" = "111", "errorMsg" = "Invalid name"},
     *         {"name" = "name", "rule" = "Assert\Length", "ruleOption" = {"min" = 1, "max" = 15}, "errorCode" = "117", "errorMsg" = "Invalid name2"},
     *         {"name" = "price", "rule" = "Assert\GreaterThan", "ruleOption" = "0", "errorCode" = "112", "default" = "99999", "errorMsg" = "Invalid price"},
     *         {"name" = "picture", "rule" = "Assert\Length", "ruleOption" = {"min" = 1, "max" = 30}, "errorCode" = "113", "errorMsg" = "Invalid picture"},
     *     }
     * )
     */
    public function number(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $query = $request->query->all();

            $product = new Product();
            $product->setName($query['name']);
            $product->setPrice($query['price']);
            $product->setPicture($query['picture']);
            $product->setCreateAt(new \DateTime());
            $entityManager->persist($product);
            $entityManager->flush();

            return new JsonResponse([
                'code' => 'ok',
                'ret' => $product->toArray(),
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