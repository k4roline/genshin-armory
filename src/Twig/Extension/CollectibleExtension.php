<?php

namespace App\Twig\Extension;

use App\Service\NamecardCollector;
use App\Service\PfpCollector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CollectibleExtension extends AbstractExtension {
    public function __construct(
        private readonly NamecardCollector $namecardCollector,
        private readonly PfpCollector $pfpCollector
    ) {}
    public function getFunctions(): array {
        return [
            new TwigFunction('namecard', [$this, 'getNamecard']),
            new TwigFunction('pfp', [$this, 'getPfp'])
        ];
    }

    public function getNamecard(int $id): string {
        return $this->namecardCollector->get($id);
    }

    public function getPfp(int $id): string {
        return $this->pfpCollector->get($id);
    }
}