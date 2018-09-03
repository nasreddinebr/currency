<?php

namespace App\Controller;

use App\Service\CurrencyGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CurenciesController extends AbstractController
{
    /**
     * @Route("/", name="curencies")
     */
    public function index(CurrencyGenerator $currencyGenerator)
    {
        $currecies = $currencyGenerator->getExchangeRate();

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent =  $serializer->serialize($currecies, 'json');

        //return new Response(dump(json_decode($jsonContent)));
        return new Response($jsonContent);

    }
}
