<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
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

#[AsCommand(name: 'app:import:namecard',
            description: 'Downloads namecard data from external source')]
class ImportNamecardCommand extends Command {
    public function __construct(private readonly HttpClientInterface   $httpClient,
                                private readonly ParameterBagInterface $params) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            $url = $_ENV['RESOURCE_NAMECARDS'];
            $response = $this->httpClient->request('GET', $url);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $output->writeln("<error>Failed to download the file. Status code:  $statusCode</error>");
                return Command::FAILURE;
            }
            $path = $this->params->get('kernel.project_dir') . '/public/resources/namecards.json';
            (new Filesystem())->dumpFile($path, $response->getContent());
        } catch (TransportExceptionInterface|
                ClientExceptionInterface|
                ServerExceptionInterface|
                RedirectionExceptionInterface $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
        $output->writeln("<info>Successfully imported namecards</info>");
        return Command::SUCCESS;
    }
}