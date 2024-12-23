<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NamecardCollector extends AbstractResourceCollector {
    public function __construct(HttpClientInterface $httpClient, ResourceDownloader $resourceDownloader) {
        parent::__construct('.png', 'namecard', $httpClient, $resourceDownloader);
    }
}
