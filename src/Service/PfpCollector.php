<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PfpCollector extends AbstractResourceCollector {
    public function __construct(HttpClientInterface $httpClient, ResourceDownloader $resourceDownloader) {
        parent::__construct('.png', 'pfp', $httpClient, $resourceDownloader);
    }
}