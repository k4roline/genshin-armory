<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/namecard')]
class NameCardController extends AbstractController {
    private array $cache = array();
    #[Route('/{nameCardId}', 'namecard_get', methods: ['GET'])]
    public function getNamecard(int $nameCardId) {
        
    }
}