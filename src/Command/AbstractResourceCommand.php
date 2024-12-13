<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractResourceCommand extends Command {
    protected string $resourceName;
    public function __construct(
        string $resourceName,
        protected readonly HttpClientInterface $httpClient,
        protected readonly ParameterBagInterface $params,
    ) {
        $this->resourceName = $resourceName;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            $url = $_ENV['RESOURCE_' . strtoupper($this->resourceName)];
            $response = $this->httpClient->request('GET', $url);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $output->writeln("<error>Failed to import {$this->resourceName}. Status code:  $statusCode</error>");
                return Command::FAILURE;
            }
            $path = $this->params->get('kernel.project_dir') . "/public/resources/{$this->resourceName}.json";
            (new Filesystem())->dumpFile($path, $response->getContent());
        } catch (TransportExceptionInterface|
        ClientExceptionInterface|
        ServerExceptionInterface|
        RedirectionExceptionInterface $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
        $output->writeln("<info>Successfully imported {$this->resourceName}</info>");
        return Command::SUCCESS;
    }
}