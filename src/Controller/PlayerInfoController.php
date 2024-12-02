<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlayerInfoController extends AbstractController {
    public function __construct(readonly HttpClientInterface $httpClient){}

    #[Route("/info/{uid}", "info_get", methods: ["GET"])]
    public function getPlayerInfo(int $uid): Response {
        try {
            $response = $this->httpClient->request("GET", "https://enka.network/api/uid/$uid/?info");
            return $this->render('web/index.html.twig', ['playerInfo' => $response->getContent()]);
        } catch (TransportExceptionInterface $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}