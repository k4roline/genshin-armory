<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PlayerDataExtension extends AbstractExtension {
    public function getFunctions(): array {
        return [
            new TwigFunction('getNameCard', [$this, 'getNameCard']),
        ];
    }

    private function getNameCard(int $nameCardId) {

    }
}