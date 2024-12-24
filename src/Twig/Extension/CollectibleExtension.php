<?php

namespace App\Twig\Extension;

use App\Service\NamecardCollector;
use App\Service\PfpCollector;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CollectibleExtension extends AbstractExtension {
    private readonly Package $asset;
    public function __construct(
        private readonly NamecardCollector $namecardCollector,
        private readonly PfpCollector $pfpCollector,
    ) {
        $this->asset = new Package(new EmptyVersionStrategy());
    }
    public function getFunctions(): array {
        return [
            new TwigFunction('namecard', [$this, 'getNamecard']),
            new TwigFunction('avatar', [$this, 'getAvatar'])
        ];
    }

    public function getNamecard(int $id): string {
        return $this->namecardCollector->get($id) ?? $this->asset->getUrl('/images/default_namecard.png');
    }

    public function getAvatar(int $id): string {
        return $this->pfpCollector->get($id) ?? $this->asset->getUrl('/images/default_avatar.png');
    }
}