<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:pfp',
    description: 'Downloads character profile picture data from external source'
)]
class ImportPfpCommandAbstract extends AbstractResourceCommand {
    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params) {
        parent::__construct("pfp", $httpClient, $params);
    }
}