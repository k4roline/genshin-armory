<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:namecard',
    description: 'Downloads namecard data from external source'
)]
class ImportNamecardCommandAbstract extends AbstractResourceCommand {
    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params) {
        parent::__construct("namecard", $httpClient, $params);
    }
}