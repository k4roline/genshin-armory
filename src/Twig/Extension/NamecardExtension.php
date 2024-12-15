<?php

namespace App\Twig\Extension;

use App\Service\NamecardCollector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NamecardExtension extends AbstractExtension {
    public function __construct(private readonly NamecardCollector $collector) {}
    public function getFunctions(): array {
        return [
            new TwigFunction('namecard', [$this, 'getNamecard']),
        ];
    }

    public function getNamecard(int $id): string {
        return $this->collector->getNamecard($id);
    }
}