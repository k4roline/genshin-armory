<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebController extends AbstractController {
    // for development purposes only
    private string $playerInfo = '{"playerInfo":{"nickname":"k4roline","level":54,"signature":"30% of the time I win 50/50 100% of the time","worldLevel":7,"nameCardId":210002,"finishAchievementNum":444,"towerFloorIndex":8,"towerLevelIndex":3,"showAvatarInfoList":[{"avatarId":10000091,"level":90,"energyType":8}],"profilePicture":{"id":8500},"isShowAvatarTalent":true,"fetterCount":2},"ttl":60,"uid":"772538064"}';

    #[Route('/', 'index')]
    public function index(): Response {
        return $this->render('web/index.html.twig', ['playerInfo' => json_decode($this->playerInfo, true)]);
    }
}