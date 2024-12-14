<?php

namespace App\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ResourceDownloader {
    public function __construct(private HttpClientInterface $httpClient) {}

    public function download(string $resource, string $path, OutputInterface $output): bool {
        try {
            $response = $this->httpClient->request('GET', $resource);
            $statusCode = $response->getStatusCode();
            if ($statusCode != 200) {
                $output->writeln("<error>Failed to import $resource. Status code:  $statusCode</error>");
                return false;
            }
            (new Filesystem())->dumpFile($path, $response->getContent());
        } catch (TransportExceptionInterface
                |ClientExceptionInterface
                |ServerExceptionInterface
                |RedirectionExceptionInterface $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return false;
        }
        $output->writeln("<info>Successfully imported $resource into $path</info>");
        return true;
    }
}