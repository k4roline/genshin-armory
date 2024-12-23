<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ResourceDownloader {
    public function __construct(private HttpClientInterface $httpClient, private LoggerInterface $logger) {}

    public function download(string $resource, string $path): bool {
        try {
            $response = $this->httpClient->request('GET', $resource);
            $statusCode = $response->getStatusCode();
            if ($statusCode >= 400) {
                $this->logger->error("<error>Failed to import $resource. Status code:  $statusCode</error>");
                return false;
            }
            (new Filesystem())->dumpFile($path, $response->getContent());
        } catch (TransportExceptionInterface
                |ClientExceptionInterface
                |ServerExceptionInterface
                |RedirectionExceptionInterface $e) {
            $this->logger->error("<error>{$e->getMessage()}</error>");
            return false;
        }
        $this->logger->info("<info>Successfully imported $resource into $path</info>");
        return true;
    }
}
